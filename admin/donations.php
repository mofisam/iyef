<?php
require_once '../config/db.php';
require_once '../includes/functions/donations.php';

// Check admin access


$action = $_GET['action'] ?? 'list';
$donationId = $_GET['id'] ?? 0;

// Handle send receipt action
if ($action === 'send-receipt' && $donationId) {
    $donation = fetchSingle("SELECT * FROM donations WHERE id = ?", [$donationId]);
    if ($donation) {
        // In a real application, you would send an email receipt here
        updateRecord('donations', ['receipt_sent' => 1], 'id = ?', [$donationId]);
        $_SESSION['success_message'] = 'Receipt has been marked as sent!';
    }
    header('Location: donations.php');
    exit;
}

// Handle delete action
if ($action === 'delete') {
    if (deleteRecord('donations', 'id = ?', [$donationId])) {
        $_SESSION['success_message'] = 'Donation record deleted successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to delete donation record';
    }
    header('Location: donations.php');
    exit;
}

// Set page title and breadcrumb
$page_title = "Donations";
$breadcrumb = [
    ['title' => 'Donations', 'active' => true]
];

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <div class="card admin-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0">Donation Records</h5>
            <div>
                <a href="donations.php?action=export" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-file-export me-1"></i> Export
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 data-table">
                    <thead class="bg-light">
                        <tr>
                            <th width="50">ID</th>
                            <th>Donor</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $donations = getAllDonations(1, 100)['donations'];
                        foreach ($donations as $donation):
                            $donatedAt = new DateTime($donation['donated_at']);
                        ?>
                        <tr>
                            <td><?= $donation['id'] ?></td>
                            <td>
                                <?php if ($donation['user_id']): ?>
                                    <strong><?= htmlspecialchars($donation['full_name']) ?></strong>
                                    <div class="text-muted small"><?= $donation['email'] ?></div>
                                <?php else: ?>
                                    <em>Anonymous Donor</em>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= $donation['currency'] ?> <?= number_format($donation['amount'], 2) ?></strong>
                            </td>
                            <td><?= $donation['payment_method'] ?? 'N/A' ?></td>
                            <td>
                                <?php if ($donation['receipt_sent']): ?>
                                    <span class="badge bg-success">Receipt Sent</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $donatedAt->format('M j, Y') ?></td>
                            <td class="table-actions">
                                <button type="button" class="btn btn-sm btn-outline-primary view-details" 
                                        data-bs-toggle="modal" data-bs-target="#detailsModal"
                                        data-name="<?= $donation['user_id'] ? htmlspecialchars($donation['full_name']) : 'Anonymous Donor' ?>"
                                        data-email="<?= $donation['user_id'] ? htmlspecialchars($donation['email']) : 'N/A' ?>"
                                        data-amount="<?= $donation['currency'] ?> <?= number_format($donation['amount'], 2) ?>"
                                        data-method="<?= $donation['payment_method'] ?? 'N/A' ?>"
                                        data-transaction="<?= $donation['transaction_id'] ?? 'N/A' ?>"
                                        data-status="<?= $donation['receipt_sent'] ? 'Receipt Sent' : 'Pending' ?>"
                                        data-date="<?= $donatedAt->format('M j, Y g:i A') ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if (!$donation['receipt_sent']): ?>
                                    <a href="donations.php?action=send-receipt&id=<?= $donation['id'] ?>" 
                                       class="btn btn-sm btn-outline-success" title="Mark Receipt Sent">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="donations.php?action=delete&id=<?= $donation['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger confirm-delete">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($donations)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">No donations found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Donation Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm">
                    <tr>
                        <th width="40%">Donor Name:</th>
                        <td id="detail-name"></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td id="detail-email"></td>
                    </tr>
                    <tr>
                        <th>Amount:</th>
                        <td id="detail-amount"></td>
                    </tr>
                    <tr>
                        <th>Payment Method:</th>
                        <td id="detail-method"></td>
                    </tr>
                    <tr>
                        <th>Transaction ID:</th>
                        <td id="detail-transaction"></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td id="detail-status"></td>
                    </tr>
                    <tr>
                        <th>Donated On:</th>
                        <td id="detail-date"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// View details modal
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('detailsModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            document.getElementById('detail-name').textContent = button.getAttribute('data-name');
            document.getElementById('detail-email').textContent = button.getAttribute('data-email');
            document.getElementById('detail-amount').textContent = button.getAttribute('data-amount');
            document.getElementById('detail-method').textContent = button.getAttribute('data-method');
            document.getElementById('detail-transaction').textContent = button.getAttribute('data-transaction');
            document.getElementById('detail-status').textContent = button.getAttribute('data-status');
            document.getElementById('detail-date').textContent = button.getAttribute('data-date');
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>