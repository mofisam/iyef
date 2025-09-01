<?php
require_once 'config/db.php';
require_once 'includes/functions/events.php';

// Check if event ID is provided
$eventId = $_GET['event_id'] ?? 0;
$event = getEventById($eventId);

if (!$event) {
    header('Location: events.php');
    exit;
}

$page_title = "Register for " . htmlspecialchars($event['title']);
require_once 'includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $formData = [
        'full_name' => trim($_POST['full_name']),
        'email' => trim($_POST['email']),
        'age_group' => $_POST['age_group'],
        'gender' => $_POST['gender'],
        'marital_status' => $_POST['marital_status'] ?? null,
        'religion' => $_POST['religion'] ?? null,
        'country' => $_POST['country'] ?? null,
        'state_of_residence' => $_POST['state_of_residence'] ?? null,
        'phone_number' => $_POST['phone_number'] ?? null,
        'telegram_number' => $_POST['telegram_number'] ?? null,
        'hear_about' => $_POST['hear_about'] ?? null,
        'current_occupation' => $_POST['current_occupation'] ?? null,
        'affiliation' => $_POST['affiliation'] ?? null,
        'participated_before' => $_POST['participated_before'] ?? null,
        'expectations' => $_POST['expectations'] ?? null,
        'speaker_questions' => $_POST['speaker_questions'] ?? null,
        'event_id' => $eventId
    ];

    $errors = [];

    // Validate required fields
    if (empty($formData['full_name'])) {
        $errors['full_name'] = 'Full name is required';
    }

    if (empty($formData['email'])) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }

    if (empty($formData['age_group'])) {
        $errors['age_group'] = 'Age group is required';
    }

    if (empty($formData['gender'])) {
        $errors['gender'] = 'Gender is required';
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        try {
            // Insert registration into database
            $stmt = $conn->prepare("
                INSERT INTO event_registrations (
                    full_name, email, age_group, gender, marital_status, religion, 
                    country, state_of_residence, phone_number, telegram_number, 
                    hear_about, current_occupation, affiliation, participated_before, 
                    expectations, speaker_questions, event_id
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )
            ");

            $stmt->bind_param(
                "ssssssssssssssssi",
                $formData['full_name'],
                $formData['email'],
                $formData['age_group'],
                $formData['gender'],
                $formData['marital_status'],
                $formData['religion'],
                $formData['country'],
                $formData['state_of_residence'],
                $formData['phone_number'],
                $formData['telegram_number'],
                $formData['hear_about'],
                $formData['current_occupation'],
                $formData['affiliation'],
                $formData['participated_before'],
                $formData['expectations'],
                $formData['speaker_questions'],
                $formData['event_id']
            );

            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Registration successful! We look forward to seeing you at the event.';
                header("Location: event.php?slug=" . $event['slug']);
                exit;
            } else {
                $errors['general'] = 'There was an error processing your registration. Please try again.';
            }
        } catch (Exception $e) {
            $errors['general'] = 'Registration error: ' . $e->getMessage();
        }
    }
}

// Get Nigerian states for dropdown
$nigerianStates = [
    'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 
    'Benue', 'Borno', 'Cross River', 'Delta', 'Ebonyi', 'Edo', 
    'Ekiti', 'Enugu', 'FCT', 'Gombe', 'Imo', 'Jigawa', 
    'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 
    'Lagos', 'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 
    'Oyo', 'Plateau', 'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara'
];
?>

<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold mb-3">Register for<br><?= htmlspecialchars($event['title']) ?></h1>
                <p class="lead mb-4">Complete this form to secure your spot at our upcoming event</p>
                <div class="d-flex align-items-center">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <span><?= date('l, F j, Y', strtotime($event['event_date'])) ?></span>
                </div>
                <div class="d-flex align-items-center mt-2">
                    <i class="fas fa-clock me-2"></i>
                    <span><?= date('g:i A', strtotime($event['event_date'])) ?></span>
                </div>
                <div class="d-flex align-items-center mt-2">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    <span><?= htmlspecialchars($event['location']) ?></span>
                </div>
            </div>
            <div class="col-lg-6">
                <?php if (!empty($event['image'])): ?>
                    <img src="<?= $event['image'] ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="img-fluid rounded shadow">
                <?php else: ?>
                    <div class="bg-secondary rounded shadow d-flex align-items-center justify-content-center" style="height: 250px;">
                        <i class="fas fa-calendar-alt fa-5x text-white"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Registration Form -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger">
                                <?= $errors['general'] ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <input type="hidden" name="event_id" value="<?= $eventId ?>">
                            
                            <h2 class="h4 mb-4">Personal Information</h2>
                            
                            <div class="row g-3 mb-4">
                                <!-- Full Name -->
                                <div class="col-md-6">
                                    <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" 
                                           id="full_name" name="full_name" 
                                           value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                                    <?php if (isset($errors['full_name'])): ?>
                                        <div class="invalid-feedback"><?= $errors['full_name'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                           id="email" name="email" 
                                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                    <?php if (isset($errors['email'])): ?>
                                        <div class="invalid-feedback"><?= $errors['email'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Age Group -->
                                <div class="col-md-6">
                                    <label for="age_group" class="form-label">Age Group <span class="text-danger">*</span></label>
                                    <select class="form-select <?= isset($errors['age_group']) ? 'is-invalid' : '' ?>" 
                                            id="age_group" name="age_group" required>
                                        <option value="">Select your age group</option>
                                        <option value="Under 18" <?= (($_POST['age_group'] ?? '') === 'Under 18') ? 'selected' : '' ?>>Under 18</option>
                                        <option value="18-24" <?= (($_POST['age_group'] ?? '') === '18-24') ? 'selected' : '' ?>>18-24</option>
                                        <option value="25-30" <?= (($_POST['age_group'] ?? '') === '25-30') ? 'selected' : '' ?>>25-30</option>
                                        <option value="31-40" <?= (($_POST['age_group'] ?? '') === '31-40') ? 'selected' : '' ?>>31-40</option>
                                        <option value="41 and above" <?= (($_POST['age_group'] ?? '') === '41 and above') ? 'selected' : '' ?>>41 and above</option>
                                        <option value="Other" <?= (($_POST['age_group'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                                    </select>
                                    <?php if (isset($errors['age_group'])): ?>
                                        <div class="invalid-feedback"><?= $errors['age_group'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Gender -->
                                <div class="col-md-6">
                                    <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select class="form-select <?= isset($errors['gender']) ? 'is-invalid' : '' ?>" 
                                            id="gender" name="gender" required>
                                        <option value="">Select your gender</option>
                                        <option value="Male" <?= (($_POST['gender'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= (($_POST['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                                    </select>
                                    <?php if (isset($errors['gender'])): ?>
                                        <div class="invalid-feedback"><?= $errors['gender'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Marital Status -->
                                <div class="col-md-6">
                                    <label for="marital_status" class="form-label">Marital Status</label>
                                    <select class="form-select" id="marital_status" name="marital_status">
                                        <option value="">Select marital status</option>
                                        <option value="Single" <?= (($_POST['marital_status'] ?? '') === 'Single') ? 'selected' : '' ?>>Single</option>
                                        <option value="Married" <?= (($_POST['marital_status'] ?? '') === 'Married') ? 'selected' : '' ?>>Married</option>
                                        <option value="Divorced" <?= (($_POST['marital_status'] ?? '') === 'Divorced') ? 'selected' : '' ?>>Divorced</option>
                                        <option value="Widowed" <?= (($_POST['marital_status'] ?? '') === 'Widowed') ? 'selected' : '' ?>>Widowed</option>
                                    </select>
                                </div>
                                
                                <!-- Religion -->
                                <div class="col-md-6">
                                    <label for="religion" class="form-label">Religion</label>
                                    <select class="form-select" id="religion" name="religion">
                                        <option value="">Select religion</option>
                                        <option value="Christian" <?= (($_POST['religion'] ?? '') === 'Christian') ? 'selected' : '' ?>>Christian</option>
                                        <option value="Muslim" <?= (($_POST['religion'] ?? '') === 'Muslim') ? 'selected' : '' ?>>Muslim</option>
                                        <option value="Other" <?= (($_POST['religion'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                                
                                <!-- Country -->
                                <div class="col-md-6">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text" class="form-control" id="country" name="country" 
                                           value="<?= htmlspecialchars($_POST['country'] ?? 'Nigeria') ?>">
                                </div>
                                
                                <!-- State of Residence -->
                                <div class="col-md-6">
                                    <label for="state_of_residence" class="form-label">State of Residence</label>
                                    <select class="form-select" id="state_of_residence" name="state_of_residence">
                                        <option value="">Select state</option>
                                        <?php foreach ($nigerianStates as $state): ?>
                                            <option value="<?= $state ?>" <?= (($_POST['state_of_residence'] ?? '') === $state) ? 'selected' : '' ?>>
                                                <?= $state ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <!-- Phone Number -->
                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                                           value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>">
                                </div>
                                
                                <!-- Telegram Number -->
                                <div class="col-md-6">
                                    <label for="telegram_number" class="form-label">Telegram Username (optional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"></span>
                                        <input type="text" class="form-control" id="telegram_number" name="telegram_number" 
                                               value="<?= htmlspecialchars($_POST['telegram_number'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <h2 class="h4 mb-4 mt-5">Additional Information</h2>
                            
                            <div class="row g-3 mb-4">
                                <!-- How did you hear about us? -->
                                <div class="col-md-6">
                                    <label for="hear_about" class="form-label">How did you hear about this event?</label>
                                    <select class="form-select" id="hear_about" name="hear_about">
                                        <option value="">Select an option</option>
                                        <option value="Facebook" <?= (($_POST['hear_about'] ?? '') === 'Facebook') ? 'selected' : '' ?>>Facebook</option>
                                        <option value="Instagram" <?= (($_POST['hear_about'] ?? '') === 'Instagram') ? 'selected' : '' ?>>Instagram</option>
                                        <option value="Twitter" <?= (($_POST['hear_about'] ?? '') === 'Twitter') ? 'selected' : '' ?>>Twitter</option>
                                        <option value="WhatsApp" <?= (($_POST['hear_about'] ?? '') === 'WhatsApp') ? 'selected' : '' ?>>WhatsApp</option>
                                        <option value="Email" <?= (($_POST['hear_about'] ?? '') === 'Email') ? 'selected' : '' ?>>Email</option>
                                        <option value="Friend/Family" <?= (($_POST['hear_about'] ?? '') === 'Friend/Family') ? 'selected' : '' ?>>Friend/Family</option>
                                        <option value="IYEF Website" <?= (($_POST['hear_about'] ?? '') === 'IYEF Website') ? 'selected' : '' ?>>IYEF Website</option>
                                        <option value="Other" <?= (($_POST['hear_about'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                                
                                <!-- Current Occupation -->
                                <div class="col-md-6">
                                    <label for="current_occupation" class="form-label">Current Occupation</label>
                                    <input type="text" class="form-control" id="current_occupation" name="current_occupation" 
                                           value="<?= htmlspecialchars($_POST['current_occupation'] ?? '') ?>">
                                </div>
                                
                                <!-- Affiliation -->
                                <div class="col-12">
                                    <label for="affiliation" class="form-label">Organization/School/Church Affiliation (if any)</label>
                                    <input type="text" class="form-control" id="affiliation" name="affiliation" 
                                           value="<?= htmlspecialchars($_POST['affiliation'] ?? '') ?>">
                                </div>
                                
                                <!-- Participated Before -->
                                <div class="col-md-6">
                                    <label for="participated_before" class="form-label">Have you participated in an IYEF event before?</label>
                                    <select class="form-select" id="participated_before" name="participated_before">
                                        <option value="">Select an option</option>
                                        <option value="Yes" <?= (($_POST['participated_before'] ?? '') === 'Yes') ? 'selected' : '' ?>>Yes</option>
                                        <option value="No" <?= (($_POST['participated_before'] ?? '') === 'No') ? 'selected' : '' ?>>No</option>
                                        <option value="Not sure" <?= (($_POST['participated_before'] ?? '') === 'Not sure') ? 'selected' : '' ?>>Not sure</option>
                                    </select>
                                </div>
                            </div>
                            
                            <h2 class="h4 mb-4 mt-5">Your Expectations</h2>
                            
                            <div class="row g-3 mb-4">
                                <!-- Expectations -->
                                <div class="col-12">
                                    <label for="expectations" class="form-label">What are you hoping to gain from this event?</label>
                                    <textarea class="form-control" id="expectations" name="expectations" rows="3"><?= htmlspecialchars($_POST['expectations'] ?? '') ?></textarea>
                                </div>
                                
                                <!-- Speaker Questions -->
                                <div class="col-12">
                                    <label for="speaker_questions" class="form-label">Do you have any specific questions for the speaker(s)?</label>
                                    <textarea class="form-control" id="speaker_questions" name="speaker_questions" rows="3"><?= htmlspecialchars($_POST['speaker_questions'] ?? '') ?></textarea>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="d-grid mt-5">
                                <button type="submit" class="btn btn-primary btn-lg">Complete Registration</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>