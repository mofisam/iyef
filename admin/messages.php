<?php
require_once '../config/db.php';
require_once '../includes/functions/users.php';

// Check admin access

$action = $_GET['action'] ?? 'list';
$messageId = $_GET['id'] ?? 0;

// Handle message actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'reply') {
        $message = getContactMessage($messageId);
        
        // In a real implementation, you would send an email here
        $to = $message['email'];
        $subject = "Re: " . $message['subject'];
        $replyMessage = $_POST['reply_message'];
        $headers = "From: " . $contactInfo['contact_email'] . "\r\n";
        
        // mail($to, $subject, $replyMessage, $headers);
        
        // Mark as replied
        updateRecord('contact_messages', ['is_read' => 1], 'id = ?', [$messageId]);
        
        $_SESSION['success_message'] = 'Reply sent successfully!';
        header('Location: messages.php');
        exit;
    }
}

// Handle delete action
if ($action === 'delete') {
    if (deleteRecord('contact_messages', 'id = ?', [$messageId])) {
        $_SESSION['success_message'] = 'Message deleted successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to delete message';
    }
    header('Location: messages.php');
    exit;
}

// Handle mark as read/unread
if ($action === 'mark-read') {
    updateRecord('contact_messages', ['is_read' => 1], 'id = ?', [$messageId]);
    header('Location: messages.php');
    exit;
}

if ($action === 'mark-unread') {
    updateRecord('contact_messages', ['is_read' => 0], 'id = ?', [$messageId]);
    header('Location: messages.php');
    exit;
}

// Get all contact messages
$messages = fetchAll("
    SELECT * FROM contact_messages 
    ORDER BY is_read ASC, created_at DESC
");

// Get single message for view/reply
$message = [];
if ($action === 'view' || $action === 'reply') {
    $message = getContactMessage($messageId);
    if (!$message) {
        $_SESSION['error_message'] = 'Message not found';
        header('Location: messages.php');
        exit;
    }
    
    // Mark as read when viewing
    if ($action === 'view' && !$message['is_read']) {
        updateRecord('contact_messages', ['is_read' => 1], 'id = ?', [$messageId]);
    }
}

// Page setup
$page_title = "Contact Messages";
$breadcrumb = [
    ['title' => 'Messages', 'active' => $action === 'list'],
    ['title' => 'View', 'active' => $action === 'view'],
    ['title' => 'Reply', 'active' => $action === 'reply']
];

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <?php if ($action === 'list'): ?>
        <!-- Messages List -->
        <div class="card admin-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Contact Messages</h5>
                <div class="d-flex gap-2">
                    <a href="messages.php?action=mark-all-read" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-check-circle me-1"></i> Mark All Read
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">ID</th>
                                <th>From</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $msg): ?>
                                <tr class="<?= !$msg['is_read'] ? 'table-primary' : '' ?>">
                                    <td><?= $msg['id'] ?></td>
                                    <td>
                                        <div><?= htmlspecialchars($msg['name']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($msg['email']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($msg['subject']) ?></td>
                                    <td>
                                        <?= date('M j, Y', strtotime($msg['created_at'])) ?>
                                        <small class="text-muted d-block"><?= date('g:i A', strtotime($msg['created_at'])) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($msg['is_read']): ?>
                                            <span class="badge bg-success">Read</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Unread</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="table-actions">
                                        <a href="messages.php?action=view&id=<?= $msg['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="messages.php?action=reply&id=<?= $msg['id'] ?>" 
                                           class="btn btn-sm btn-outline-info" title="Reply">
                                            <i class="fas fa-reply"></i>
                                        </a>
                                        <a href="messages.php?action=delete&id=<?= $msg['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger confirm-delete" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($messages)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">No messages found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif ($action === 'view'): ?>
        <!-- View Message -->
        <div class="card admin-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Message Details</h5>
                <div>
                    <a href="messages.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Messages
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Sender Information</h6>
                        <table class="table table-sm table-bordered">
                            <tr>
                                <th width="30%">Name:</th>
                                <td><?= htmlspecialchars($message['name']) ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><a href="mailto:<?= htmlspecialchars($message['email']) ?>"><?= htmlspecialchars($message['email']) ?></a></td>
                            </tr>
                            <tr>
                                <th>IP Address:</th>
                                <td><?= htmlspecialchars($message['ip_address']) ?></td>
                            </tr>
                            <tr>
                                <th>Date Sent:</th>
                                <td><?= date('M j, Y g:i A', strtotime($message['created_at'])) ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Quick Actions</h6>
                        <div class="d-grid gap-2">
                            <a href="mailto:<?= htmlspecialchars($message['email']) ?>?subject=Re: <?= urlencode($message['subject']) ?>" 
                               class="btn btn-outline-primary text-start">
                                <i class="fas fa-reply me-2"></i> Reply via Email
                            </a>
                            <a href="messages.php?action=reply&id=<?= $message['id'] ?>" 
                               class="btn btn-outline-info text-start">
                                <i class="fas fa-comment-dots me-2"></i> Save Reply
                            </a>
                            <?php if ($message['is_read']): ?>
                                <a href="messages.php?action=mark-unread&id=<?= $message['id'] ?>" 
                                   class="btn btn-outline-warning text-start">
                                    <i class="fas fa-envelope me-2"></i> Mark as Unread
                                </a>
                            <?php else: ?>
                                <a href="messages.php?action=mark-read&id=<?= $message['id'] ?>" 
                                   class="btn btn-outline-success text-start">
                                    <i class="fas fa-envelope-open me-2"></i> Mark as Read
                                </a>
                            <?php endif; ?>
                            <a href="messages.php?action=delete&id=<?= $message['id'] ?>" 
                               class="btn btn-outline-danger text-start confirm-delete">
                                <i class="fas fa-trash-alt me-2"></i> Delete Message
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Subject: <?= htmlspecialchars($message['subject']) ?></h6>
                    </div>
                    <div class="card-body">
                        <?= nl2br(htmlspecialchars($message['message'])) ?>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($action === 'reply'): ?>
        <!-- Reply to Message -->
        <div class="card admin-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Reply to Message</h5>
                <div>
                    <a href="messages.php?action=view&id=<?= $message['id'] ?>" 
                       class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Message
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-4">
                        <h6>Original Message</h6>
                        <div class="card bg-light border-0 p-3">
                            <p><strong>From:</strong> <?= htmlspecialchars($message['name']) ?> &lt;<?= htmlspecialchars($message['email']) ?>&gt;</p>
                            <p><strong>Subject:</strong> <?= htmlspecialchars($message['subject']) ?></p>
                            <p><strong>Message:</strong></p>
                            <div class="bg-white p-3 rounded">
                                <?= nl2br(htmlspecialchars($message['message'])) ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reply_message" class="form-label">Your Reply <span class="text-danger">*</span></label>
                        <textarea class="form-control summernote" id="reply_message" name="reply_message" rows="8" required></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="messages.php?action=view&id=<?= $message['id'] ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Send Reply</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>

<?php
// Helper function to get a single contact message
function getContactMessage($id) {
    return fetchSingle("SELECT * FROM contact_messages WHERE id = ?", [$id]);
}
?>