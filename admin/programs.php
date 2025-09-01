<?php
require_once '../config/db.php';
require_once '../includes/functions/programs.php';

// Check admin access


$action = $_GET['action'] ?? 'list';
$programId = $_GET['id'] ?? 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add' || $action === 'edit') {
        $programData = [
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'start_date' => $_POST['start_date'] ?: null,
            'end_date' => $_POST['end_date'] ?: null,
            'image' => $_POST['existing_image'] // Default to existing image
        ];

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/uploads/programs/';
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $programData['image'] = '/assets/uploads/programs/' . $fileName;
                
                // Delete old image if it exists
                if ($action === 'edit' && !empty($_POST['existing_image']) && $_POST['existing_image'] !== $programData['image']) {
                    $oldImage = '../' . ltrim($_POST['existing_image'], '/');
                    if (file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                }
            }
        }

        if ($action === 'add') {
            $result = createProgram($programData);
            if ($result['status'] === 'success') {
                $_SESSION['success_message'] = 'Program added successfully!';
                header('Location: programs.php');
                exit;
            } else {
                $error = $result['message'];
            }
        } elseif ($action === 'edit') {
            if (updateProgram($programId, $programData)) {
                $_SESSION['success_message'] = 'Program updated successfully!';
                header('Location: programs.php');
                exit;
            } else {
                $error = 'Failed to update program';
            }
        }
    }
}

// Handle delete action
if ($action === 'delete') {
    $program = getProgramById($programId);
    if ($program) {
        if (deleteProgram($programId)) {
            // Delete associated image
            if (!empty($program['image'])) {
                $imagePath = '../' . ltrim($program['image'], '/');
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $_SESSION['success_message'] = 'Program deleted successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to delete program';
        }
    }
    header('Location: programs.php');
    exit;
}

// Set page title and breadcrumb
$page_title = "Manage Programs";
$breadcrumb = [
    ['title' => 'Programs', 'active' => $action === 'list'],
    ['title' => 'Add New', 'active' => $action === 'add'],
    ['title' => 'Edit', 'active' => $action === 'edit']
];

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <?php if ($action === 'list'): ?>
        <!-- Programs List -->
        <div class="card admin-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">All Programs</h5>
                <a href="programs.php?action=add" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Add New
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 data-table">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">ID</th>
                                <th>Title</th>
                                <th>Dates</th>
                                <th>Registrations</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $programs = getAllPrograms()['programs'];
                            foreach ($programs as $program):
                                $startDate = $program['start_date'] ? date('M j, Y', strtotime($program['start_date'])) : 'TBD';
                                $endDate = $program['end_date'] ? date('M j, Y', strtotime($program['end_date'])) : 'Ongoing';
                                $regCount = fetchSingle("SELECT COUNT(*) as count FROM program_registrations WHERE program_id = ?", [$program['id']])['count'];
                            ?>
                            <tr>
                                <td><?= $program['id'] ?></td>
                                <td>
                                    <a href="programs.php?action=edit&id=<?= $program['id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($program['title']) ?>
                                    </a>
                                </td>
                                <td><?= "$startDate - $endDate" ?></td>
                                <td>
                                    <a href="program-registrations.php?program_id=<?= $program['id'] ?>">
                                        <?= $regCount ?> registration<?= $regCount != 1 ? 's' : '' ?>
                                    </a>
                                </td>
                                <td class="table-actions">
                                    <a href="programs.php?action=edit&id=<?= $program['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="programs.php?action=delete&id=<?= $program['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger confirm-delete" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($programs)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">No programs found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif ($action === 'add' || $action === 'edit'): ?>
        <!-- Program Form -->
        <?php
        $program = [];
        if ($action === 'edit') {
            $program = getProgramById($programId);
            if (!$program) {
                $_SESSION['error_message'] = 'Program not found';
                header('Location: programs.php');
                exit;
            }
        }
        ?>
        
        <div class="card admin-card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><?= $action === 'add' ? 'Add New' : 'Edit' ?> Program</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <!-- Title -->
                        <div class="col-md-12">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= htmlspecialchars($program['title'] ?? '') ?>" required>
                        </div>
                        
                        <!-- Description -->
                        <div class="col-md-12">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control summernote" id="description" name="description" 
                                      rows="5" required><?= htmlspecialchars($program['description'] ?? '') ?></textarea>
                        </div>
                        
                        <!-- Dates -->
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="<?= $program['start_date'] ?? '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="<?= $program['end_date'] ?? '' ?>">
                        </div>
                        
                        <!-- Image -->
                        <div class="col-md-12">
                            <label for="image" class="form-label">Featured Image</label>
                            <?php if (!empty($program['image'])): ?>
                                <div class="mb-3">
                                    <img src="<?= BASE_URL . $program['image'] ?>" alt="Current image" class="img-thumbnail" style="max-height: 200px;">
                                    <input type="hidden" name="existing_image" value="<?= $program['image'] ?>">
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                                    <label class="form-check-label" for="remove_image">Remove current image</label>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="existing_image" value="">
                            <?php endif; ?>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Recommended size: 800x450 pixels</div>
                        </div>
                        
                        <!-- Submit -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"><?= $action === 'add' ? 'Add Program' : 'Update Program' ?></button>
                            <a href="programs.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>