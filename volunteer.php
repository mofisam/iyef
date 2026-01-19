<?php
// Start session at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Become a Volunteer";
require_once 'includes/header.php';

// Initialize variables
$success = false;
$errors = [];
$formData = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $formData = [
        'full_name' => trim($_POST['full_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'gender' => $_POST['gender'] ?? '',
        'dob' => $_POST['dob'] ?? '',
        'age' => $_POST['age'] ?? '',
        'marital_status' => $_POST['marital_status'] ?? '',
        'state_of_origin' => trim($_POST['state_of_origin'] ?? ''),
        'nationality' => trim($_POST['nationality'] ?? ''),
        'residential_address' => trim($_POST['residential_address'] ?? ''),
        'phone_number' => trim($_POST['phone_number'] ?? ''),
        'whatsapp_number' => trim($_POST['whatsapp_number'] ?? ''),
        'education_level' => $_POST['education_level'] ?? '',
        'education_other' => trim($_POST['education_other'] ?? ''),
        'work_school_ppa' => trim($_POST['work_school_ppa'] ?? ''),
        'occupation_course' => trim($_POST['occupation_course'] ?? ''),
        'level_class' => trim($_POST['level_class'] ?? ''),
        'last_cgpa' => trim($_POST['last_cgpa'] ?? ''),
        'hobbies' => trim($_POST['hobbies'] ?? ''),
        'born_again' => $_POST['born_again'] ?? '',
        'salvation_experience' => trim($_POST['salvation_experience'] ?? ''),
        'holy_spirit_baptism' => $_POST['holy_spirit_baptism'] ?? '',
        'discovered_purpose' => $_POST['discovered_purpose'] ?? '',
        'god_given_purpose' => trim($_POST['god_given_purpose'] ?? ''),
        'denomination' => trim($_POST['denomination'] ?? ''),
        'gifts_talents' => trim($_POST['gifts_talents'] ?? ''),
        'social_media' => $_POST['social_media'] ?? [],
        'social_media_other' => trim($_POST['social_media_other'] ?? ''),
        'passionate_about_youth' => $_POST['passionate_about_youth'] ?? '',
        'motivation' => trim($_POST['motivation'] ?? ''),
        'questions' => trim($_POST['questions'] ?? ''),
        'terms_accepted' => isset($_POST['terms_accepted']) ? 1 : 0
    ];

    // Validation
    if (empty($formData['full_name'])) {
        $errors['full_name'] = 'Full name is required';
    } elseif (strlen($formData['full_name']) < 3) {
        $errors['full_name'] = 'Full name must be at least 3 characters';
    }

    if (empty($formData['email'])) {
        $errors['email'] = 'Email address is required';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }

    if (empty($formData['gender'])) {
        $errors['gender'] = 'Please select your gender';
    }

    if (empty($formData['dob'])) {
        $errors['dob'] = 'Date of birth is required';
    } elseif (!validateDate($formData['dob'])) {
        $errors['dob'] = 'Please enter a valid date (dd/mm/yyyy)';
    }

    if (empty($formData['age']) || !is_numeric($formData['age']) || $formData['age'] < 13 || $formData['age'] > 100) {
        $errors['age'] = 'Please enter a valid age (13-100)';
    }

    if (empty($formData['marital_status'])) {
        $errors['marital_status'] = 'Please select your marital status';
    }

    if (empty($formData['state_of_origin'])) {
        $errors['state_of_origin'] = 'State of origin is required';
    }

    if (empty($formData['nationality'])) {
        $errors['nationality'] = 'Nationality is required';
    }

    if (empty($formData['residential_address'])) {
        $errors['residential_address'] = 'Residential address is required';
    }

    if (empty($formData['phone_number'])) {
        $errors['phone_number'] = 'Phone number is required';
    } elseif (!preg_match('/^[0-9+\-\s()]{10,20}$/', $formData['phone_number'])) {
        $errors['phone_number'] = 'Please enter a valid phone number';
    }

    if (empty($formData['education_level'])) {
        $errors['education_level'] = 'Please select your education level';
    }

    if ($formData['education_level'] === 'Other' && empty($formData['education_other'])) {
        $errors['education_other'] = 'Please specify your education level';
    }

    if (empty($formData['born_again'])) {
        $errors['born_again'] = 'Please answer this question';
    }

    if ($formData['born_again'] === 'Yes' && empty($formData['salvation_experience'])) {
        $errors['salvation_experience'] = 'Please share your salvation experience';
    }

    if (empty($formData['holy_spirit_baptism'])) {
        $errors['holy_spirit_baptism'] = 'Please answer this question';
    }

    if (empty($formData['discovered_purpose'])) {
        $errors['discovered_purpose'] = 'Please answer this question';
    }

    if ($formData['discovered_purpose'] === 'Yes' && empty($formData['god_given_purpose'])) {
        $errors['god_given_purpose'] = 'Please share your God-given purpose';
    }

    if (empty($formData['denomination'])) {
        $errors['denomination'] = 'Please specify your denomination/church';
    }

    if (empty($formData['gifts_talents'])) {
        $errors['gifts_talents'] = 'Please share your gifts and talents';
    }

    if (empty($formData['social_media'])) {
        $errors['social_media'] = 'Please select at least one social media platform';
    }

    if (empty($formData['passionate_about_youth'])) {
        $errors['passionate_about_youth'] = 'Please answer this question';
    }

    if (empty($formData['motivation'])) {
        $errors['motivation'] = 'Please share your motivation';
    }

    if (empty($formData['terms_accepted'])) {
        $errors['terms_accepted'] = 'You must accept the terms and conditions';
    }

    // If no errors, save to database
    if (empty($errors)) {
        require_once 'config/db.php';
        require_once 'config/db_functions.php';
        
        // Check if email already exists
        $existingVolunteer = fetchSingle("SELECT id FROM volunteers WHERE email = ?", [$formData['email']]);
        if ($existingVolunteer) {
            $errors['email'] = 'This email is already registered as a volunteer';
        } else {
            // Prepare data for database
            $volunteerData = [
                'full_name' => $formData['full_name'],
                'email' => $formData['email'], // Added email field
                'gender' => $formData['gender'],
                'dob' => date('Y-m-d', strtotime(str_replace('/', '-', $formData['dob']))),
                'age' => (int)$formData['age'],
                'marital_status' => $formData['marital_status'],
                'state_of_origin' => $formData['state_of_origin'],
                'nationality' => $formData['nationality'],
                'residential_address' => $formData['residential_address'],
                'phone_number' => $formData['phone_number'],
                'whatsapp_number' => $formData['whatsapp_number'],
                'education_level' => $formData['education_level'] === 'Other' ? $formData['education_other'] : $formData['education_level'],
                'work_school_ppa' => $formData['work_school_ppa'],
                'occupation_course' => $formData['occupation_course'],
                'level_class' => $formData['level_class'],
                'last_cgpa' => $formData['last_cgpa'],
                'hobbies' => $formData['hobbies'],
                'born_again' => $formData['born_again'],
                'salvation_experience' => $formData['salvation_experience'],
                'holy_spirit_baptism' => $formData['holy_spirit_baptism'],
                'discovered_purpose' => $formData['discovered_purpose'],
                'god_given_purpose' => $formData['god_given_purpose'],
                'denomination' => $formData['denomination'],
                'gifts_talents' => $formData['gifts_talents'],
                'social_media' => json_encode($formData['social_media']),
                'social_media_other' => $formData['social_media_other'],
                'passionate_about_youth' => $formData['passionate_about_youth'],
                'motivation' => $formData['motivation'],
                'questions' => $formData['questions'],
                'submitted_at' => date('Y-m-d H:i:s'),
                'ip_address' => $_SERVER['REMOTE_ADDR']
            ];

            try {
                // Update volunteers table to include email
                $sql = "CREATE TABLE IF NOT EXISTS volunteers (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    full_name VARCHAR(100) NOT NULL,
                    email VARCHAR(100) NOT NULL, -- Added email field
                    gender ENUM('Male', 'Female') NOT NULL,
                    dob DATE NOT NULL,
                    age INT NOT NULL,
                    marital_status ENUM('Single', 'Engaged', 'Married') NOT NULL,
                    state_of_origin VARCHAR(50) NOT NULL,
                    nationality VARCHAR(50) NOT NULL,
                    residential_address TEXT NOT NULL,
                    phone_number VARCHAR(20) NOT NULL,
                    whatsapp_number VARCHAR(20),
                    education_level VARCHAR(100) NOT NULL,
                    work_school_ppa VARCHAR(255),
                    occupation_course VARCHAR(255),
                    level_class VARCHAR(50),
                    last_cgpa VARCHAR(10),
                    hobbies TEXT,
                    born_again ENUM('Yes', 'No') NOT NULL,
                    salvation_experience TEXT,
                    holy_spirit_baptism ENUM('Yes', 'Not yet') NOT NULL,
                    discovered_purpose ENUM('Yes', 'No') NOT NULL,
                    god_given_purpose TEXT,
                    denomination VARCHAR(100),
                    gifts_talents TEXT NOT NULL,
                    social_media JSON,
                    social_media_other VARCHAR(100),
                    passionate_about_youth ENUM('Yes', 'No', 'Somewhat', 'Not sure') NOT NULL,
                    motivation TEXT NOT NULL,
                    questions TEXT,
                    submitted_at DATETIME NOT NULL,
                    viewed TINYINT(1) DEFAULT 0,
                    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
                    ip_address VARCHAR(45),
                    UNIQUE KEY unique_email (email),
                    INDEX idx_status (status),
                    INDEX idx_submitted_at (submitted_at),
                    INDEX idx_email (email)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                
                executeQuery($sql);
                
                $volunteerId = insertRecord('volunteers', $volunteerData);
                
                if ($volunteerId) {
                    $success = true;
                    
                    // Send confirmation email
                    try {
                        require_once 'includes/functions/email.php';
                        $emailResult = sendEmail(
                            $formData['email'],
                            'Volunteer Application Received - IYEF',
                            '<!DOCTYPE html>
                            <html>
                            <head>
                                <meta charset="UTF-8">
                                <style>
                                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                                    .header { background-color: #007bff; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                                    .content { padding: 30px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-top: none; }
                                    .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                                    .btn { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
                                </style>
                            </head>
                            <body>
                                <div class="container">
                                    <div class="header">
                                        <h1>Indefatigable Youth Empowerment Foundation</h1>
                                    </div>
                                    <div class="content">
                                        <h2>Thank You for Your Volunteer Application!</h2>
                                        <p>Dear ' . htmlspecialchars($formData['full_name']) . ',</p>
                                        <p>We have received your volunteer application for the Indefatigable Youth Empowerment Foundation.</p>
                                        <p><strong>Application Details:</strong></p>
                                        <ul>
                                            <li><strong>Name:</strong> ' . htmlspecialchars($formData['full_name']) . '</li>
                                            <li><strong>Email:</strong> ' . htmlspecialchars($formData['email']) . '</li>
                                            <li><strong>Phone:</strong> ' . htmlspecialchars($formData['phone_number']) . '</li>
                                            <li><strong>Submitted:</strong> ' . date('F j, Y') . '</li>
                                        </ul>
                                        <p>Our team will review your application and get back to you within 5-7 business days.</p>
                                        <p>If you have any urgent questions, please contact us at support@iyef.org or call our support line.</p>
                                        <div style="text-align: center; margin: 30px 0;">
                                            <a href="' . BASE_URL . '/contact.php" class="btn">Contact Support</a>
                                        </div>
                                        <p>Thank you for your interest in empowering youth through Christ-centered mentorship.</p>
                                        <p>Blessings,<br><strong>The IYEF Team</strong></p>
                                    </div>
                                    <div class="footer">
                                        <p>© ' . date('Y') . ' Indefatigable Youth Empowerment Foundation. All rights reserved.</p>
                                        <p>This is an automated message, please do not reply to this email.</p>
                                    </div>
                                </div>
                            </body>
                            </html>',
                            "Thank you for your volunteer application to Indefatigable Youth Empowerment Foundation.\n\n" .
                            "We have received your application and will review it within 5-7 business days.\n\n" .
                            "Application Details:\n" .
                            "Name: " . $formData['full_name'] . "\n" .
                            "Email: " . $formData['email'] . "\n" .
                            "Phone: " . $formData['phone_number'] . "\n" .
                            "Submitted: " . date('F j, Y') . "\n\n" .
                            "If you have questions, contact support@iyef.org\n\n" .
                            "© " . date('Y') . " Indefatigable Youth Empowerment Foundation."
                        );
                    } catch (Exception $e) {
                        // Email failed, but registration succeeded
                        error_log('Volunteer confirmation email failed: ' . $e->getMessage());
                    }
                    
                    // Clear form data on success
                    $formData = [];
                } else {
                    $errors['database'] = 'Sorry, there was an error submitting your application. Please try again1.';
                }
            } catch (Exception $e) {
                error_log('Database error: ' . $e->getMessage());
                $errors['database'] = 'Sorry, there was an error submitting your application. Please try again2.';
            }
        }
    }
}

function validateDate($date, $format = 'd/m/Y') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
?>

<!-- Success Message -->
<?php if ($success): ?>
<div class="container mt-4">
    <div class="alert alert-success alert-dismissible fade show shadow-lg" role="alert">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <h4 class="alert-heading mb-2">Application Submitted Successfully!</h4>
                <p class="mb-2">Thank you for your interest in volunteering with IYEF. Your application has been received.</p>
                <p class="mb-1"><strong>Check your email</strong> for a confirmation message.</p>
                <p class="mb-0">Our team will review your application and contact you within 5-7 business days.</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php endif; ?>

<!-- Application Form -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4 p-md-5">
                        <?php if (isset($errors['database'])): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= $errors['database'] ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" id="volunteerForm" novalidate>
                            <!-- Progress Indicator -->
                            <div class="mb-5">
                                <div class="d-none d-md-flex justify-content-between align-items-center mb-3">
                                    <small class="text-muted">Step 1 of 5: Personal Information</small>
                                    <small class="text-primary"><span id="progressPercent">0</span>% Complete</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" id="formProgress" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                            
                            <!-- Section 1: Personal Information -->
                            <div class="form-section" id="section1">
                                <div class="section-header mb-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary text-white rounded-circle p-2 me-3">
                                            <i class="fas fa-user fa-lg"></i>
                                        </div>
                                        <div>
                                            <h3 class="h4 fw-bold mb-0">Personal Information</h3>
                                            <small class="text-muted">Basic details about yourself</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="full_name" class="form-label fw-bold">
                                            Full Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" 
                                               id="full_name" name="full_name" 
                                               value="<?= htmlspecialchars($formData['full_name'] ?? '') ?>" 
                                               required>
                                        <?php if (isset($errors['full_name'])): ?>
                                            <div class="invalid-feedback"><?= $errors['full_name'] ?></div>
                                        <?php endif; ?>
                                        <div class="form-text">As it appears on your official documents</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-bold">
                                            Email Address <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                               id="email" name="email" 
                                               value="<?= htmlspecialchars($formData['email'] ?? '') ?>" 
                                               required>
                                        <?php if (isset($errors['email'])): ?>
                                            <div class="invalid-feedback"><?= $errors['email'] ?></div>
                                        <?php endif; ?>
                                        <div class="form-text">We'll send confirmation to this email</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            Gender <span class="text-danger">*</span>
                                        </label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="gender" 
                                                       id="gender_male" value="Male" 
                                                       <?= ($formData['gender'] ?? '') === 'Male' ? 'checked' : '' ?> required>
                                                <label class="form-check-label" for="gender_male">
                                                    Male
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="gender" 
                                                       id="gender_female" value="Female"
                                                       <?= ($formData['gender'] ?? '') === 'Female' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="gender_female">
                                                    Female
                                                </label>
                                            </div>
                                        </div>
                                        <?php if (isset($errors['gender'])): ?>
                                            <div class="text-danger small mt-1"><?= $errors['gender'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="dob" class="form-label fw-bold">
                                            Date of Birth <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control <?= isset($errors['dob']) ? 'is-invalid' : '' ?>" 
                                               id="dob" name="dob" 
                                               value="<?= htmlspecialchars($formData['dob'] ?? '') ?>" 
                                               placeholder="dd/mm/yyyy" required>
                                        <?php if (isset($errors['dob'])): ?>
                                            <div class="invalid-feedback"><?= $errors['dob'] ?></div>
                                        <?php endif; ?>
                                        <div class="form-text">Format: Day/Month/Year</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="age" class="form-label fw-bold">
                                            Age <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control <?= isset($errors['age']) ? 'is-invalid' : '' ?>" 
                                               id="age" name="age" min="13" max="100"
                                               value="<?= htmlspecialchars($formData['age'] ?? '') ?>" required>
                                        <?php if (isset($errors['age'])): ?>
                                            <div class="invalid-feedback"><?= $errors['age'] ?></div>
                                        <?php endif; ?>
                                        <div class="form-text">Must be 13 years or older</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            Marital Status <span class="text-danger">*</span>
                                        </label>
                                        <div>
                                            <?php 
                                            $maritalOptions = ['Single', 'Engaged', 'Married'];
                                            foreach ($maritalOptions as $option): ?>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="marital_status" 
                                                           id="marital_<?= strtolower($option) ?>" value="<?= $option ?>"
                                                           <?= ($formData['marital_status'] ?? '') === $option ? 'checked' : '' ?> required>
                                                    <label class="form-check-label" for="marital_<?= strtolower($option) ?>">
                                                        <?= $option ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php if (isset($errors['marital_status'])): ?>
                                            <div class="text-danger small mt-1"><?= $errors['marital_status'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="state_of_origin" class="form-label fw-bold">
                                            State of Origin <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control <?= isset($errors['state_of_origin']) ? 'is-invalid' : '' ?>" 
                                               id="state_of_origin" name="state_of_origin" 
                                               value="<?= htmlspecialchars($formData['state_of_origin'] ?? '') ?>" required>
                                        <?php if (isset($errors['state_of_origin'])): ?>
                                            <div class="invalid-feedback"><?= $errors['state_of_origin'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="nationality" class="form-label fw-bold">
                                            Nationality <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control <?= isset($errors['nationality']) ? 'is-invalid' : '' ?>" 
                                               id="nationality" name="nationality" 
                                               value="<?= htmlspecialchars($formData['nationality'] ?? '') ?>" required>
                                        <?php if (isset($errors['nationality'])): ?>
                                            <div class="invalid-feedback"><?= $errors['nationality'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="phone_number" class="form-label fw-bold">
                                            Phone Number <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" class="form-control <?= isset($errors['phone_number']) ? 'is-invalid' : '' ?>" 
                                               id="phone_number" name="phone_number" 
                                               value="<?= htmlspecialchars($formData['phone_number'] ?? '') ?>" required>
                                        <?php if (isset($errors['phone_number'])): ?>
                                            <div class="invalid-feedback"><?= $errors['phone_number'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="whatsapp_number" class="form-label fw-bold">
                                            WhatsApp Number
                                        </label>
                                        <input type="tel" class="form-control" 
                                               id="whatsapp_number" name="whatsapp_number" 
                                               value="<?= htmlspecialchars($formData['whatsapp_number'] ?? '') ?>">
                                        <div class="form-text">If different from phone number</div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="residential_address" class="form-label fw-bold">
                                            Present Residential Address <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control <?= isset($errors['residential_address']) ? 'is-invalid' : '' ?>" 
                                                  id="residential_address" name="residential_address" 
                                                  rows="3" required><?= htmlspecialchars($formData['residential_address'] ?? '') ?></textarea>
                                        <?php if (isset($errors['residential_address'])): ?>
                                            <div class="invalid-feedback"><?= $errors['residential_address'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-5">
                                    <div></div>
                                    <button type="button" class="btn btn-primary next-section" data-next="section2">
                                        Next: Education & Career <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Section 2: Education & Career -->
                            <div class="form-section d-none" id="section2">
                                <div class="section-header mb-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-success text-white rounded-circle p-2 me-3">
                                            <i class="fas fa-graduation-cap fa-lg"></i>
                                        </div>
                                        <div>
                                            <h3 class="h4 fw-bold mb-0">Education & Career</h3>
                                            <small class="text-muted">Your academic and professional background</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            Level of Education <span class="text-danger">*</span>
                                        </label>
                                        <?php 
                                        $educationOptions = [
                                            'Secondary School',
                                            'Undergraduate', 
                                            'Graduate/Corp Member',
                                            'Jambite/Aspirant',
                                            'Working Class',
                                            'Other'
                                        ];
                                        foreach ($educationOptions as $option): ?>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input education-level" type="radio" 
                                                       name="education_level" id="edu_<?= strtolower(str_replace('/', '_', $option)) ?>" 
                                                       value="<?= $option ?>"
                                                       <?= ($formData['education_level'] ?? '') === $option ? 'checked' : '' ?> required>
                                                <label class="form-check-label" for="edu_<?= strtolower(str_replace('/', '_', $option)) ?>">
                                                    <?= $option ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                        <?php if (isset($errors['education_level'])): ?>
                                            <div class="text-danger small mt-1"><?= $errors['education_level'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div id="education_other_container" class="d-none">
                                            <label for="education_other" class="form-label fw-bold">
                                                Specify Education Level <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control <?= isset($errors['education_other']) ? 'is-invalid' : '' ?>" 
                                                   id="education_other" name="education_other" 
                                                   value="<?= htmlspecialchars($formData['education_other'] ?? '') ?>">
                                            <?php if (isset($errors['education_other'])): ?>
                                                <div class="invalid-feedback"><?= $errors['education_other'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div id="undergraduate_fields" class="d-none">
                                            <label for="last_cgpa" class="form-label fw-bold mt-3">
                                                Last CGPA
                                            </label>
                                            <input type="text" class="form-control" 
                                                   id="last_cgpa" name="last_cgpa" 
                                                   value="<?= htmlspecialchars($formData['last_cgpa'] ?? '') ?>">
                                            <div class="form-text">For undergraduate students only</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="work_school_ppa" class="form-label fw-bold">
                                            Place of Work/School/PPA
                                        </label>
                                        <input type="text" class="form-control" 
                                               id="work_school_ppa" name="work_school_ppa" 
                                               value="<?= htmlspecialchars($formData['work_school_ppa'] ?? '') ?>">
                                        <div class="form-text">Company, School, or PPA name</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="occupation_course" class="form-label fw-bold">
                                            Occupation / Course of Study
                                        </label>
                                        <input type="text" class="form-control" 
                                               id="occupation_course" name="occupation_course" 
                                               value="<?= htmlspecialchars($formData['occupation_course'] ?? '') ?>">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="level_class" class="form-label fw-bold">
                                            Level / Class
                                        </label>
                                        <input type="text" class="form-control" 
                                               id="level_class" name="level_class" 
                                               value="<?= htmlspecialchars($formData['level_class'] ?? '') ?>">
                                        <div class="form-text">E.g., 300 Level, Year 2, etc.</div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="hobbies" class="form-label fw-bold">
                                            Your Hobbies
                                        </label>
                                        <textarea class="form-control" id="hobbies" name="hobbies" 
                                                  rows="2"><?= htmlspecialchars($formData['hobbies'] ?? '') ?></textarea>
                                        <div class="form-text">What do you enjoy doing in your free time?</div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-5">
                                    <button type="button" class="btn btn-outline-primary prev-section" data-prev="section1">
                                        <i class="fas fa-arrow-left me-2"></i> Previous
                                    </button>
                                    <button type="button" class="btn btn-primary next-section" data-next="section3">
                                        Next: Spiritual Life <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Section 3: Spiritual Life -->
                            <div class="form-section d-none" id="section3">
                                <div class="section-header mb-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-warning text-dark rounded-circle p-2 me-3">
                                            <i class="fas fa-pray fa-lg"></i>
                                        </div>
                                        <div>
                                            <h3 class="h4 fw-bold mb-0">Spiritual Life</h3>
                                            <small class="text-muted">Your faith journey and spiritual walk</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-bold">
                                            Are you Born Again? <span class="text-danger">*</span>
                                        </label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input born-again" type="radio" name="born_again" 
                                                       id="born_again_yes" value="Yes"
                                                       <?= ($formData['born_again'] ?? '') === 'Yes' ? 'checked' : '' ?> required>
                                                <label class="form-check-label" for="born_again_yes">
                                                    Yes
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input born-again" type="radio" name="born_again" 
                                                       id="born_again_no" value="No"
                                                       <?= ($formData['born_again'] ?? '') === 'No' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="born_again_no">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                        <?php if (isset($errors['born_again'])): ?>
                                            <div class="text-danger small mt-1"><?= $errors['born_again'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-12" id="salvation_experience_container">
                                        <label for="salvation_experience" class="form-label fw-bold">
                                            Briefly share your Salvation Experience <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control <?= isset($errors['salvation_experience']) ? 'is-invalid' : '' ?>" 
                                                  id="salvation_experience" name="salvation_experience" 
                                                  rows="4"><?= htmlspecialchars($formData['salvation_experience'] ?? '') ?></textarea>
                                        <?php if (isset($errors['salvation_experience'])): ?>
                                            <div class="invalid-feedback"><?= $errors['salvation_experience'] ?></div>
                                        <?php endif; ?>
                                        <div class="form-text">Share your personal salvation testimony</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            Have you been baptized with the Holy Spirit with evidence of speaking in tongues? <span class="text-danger">*</span>
                                        </label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="holy_spirit_baptism" 
                                                       id="holy_spirit_yes" value="Yes"
                                                       <?= ($formData['holy_spirit_baptism'] ?? '') === 'Yes' ? 'checked' : '' ?> required>
                                                <label class="form-check-label" for="holy_spirit_yes">
                                                    Yes
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="holy_spirit_baptism" 
                                                       id="holy_spirit_not_yet" value="Not yet"
                                                       <?= ($formData['holy_spirit_baptism'] ?? '') === 'Not yet' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="holy_spirit_not_yet">
                                                    Not yet
                                                </label>
                                            </div>
                                        </div>
                                        <?php if (isset($errors['holy_spirit_baptism'])): ?>
                                            <div class="text-danger small mt-1"><?= $errors['holy_spirit_baptism'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            Have you discovered your God-given purpose? <span class="text-danger">*</span>
                                        </label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input discovered-purpose" type="radio" name="discovered_purpose" 
                                                       id="purpose_yes" value="Yes"
                                                       <?= ($formData['discovered_purpose'] ?? '') === 'Yes' ? 'checked' : '' ?> required>
                                                <label class="form-check-label" for="purpose_yes">
                                                    Yes
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input discovered-purpose" type="radio" name="discovered_purpose" 
                                                       id="purpose_no" value="No"
                                                       <?= ($formData['discovered_purpose'] ?? '') === 'No' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="purpose_no">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                        <?php if (isset($errors['discovered_purpose'])): ?>
                                            <div class="text-danger small mt-1"><?= $errors['discovered_purpose'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-12" id="god_given_purpose_container">
                                        <label for="god_given_purpose" class="form-label fw-bold">
                                            Give a summary of your God-given purpose <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control <?= isset($errors['god_given_purpose']) ? 'is-invalid' : '' ?>" 
                                                  id="god_given_purpose" name="god_given_purpose" 
                                                  rows="4"><?= htmlspecialchars($formData['god_given_purpose'] ?? '') ?></textarea>
                                        <?php if (isset($errors['god_given_purpose'])): ?>
                                            <div class="invalid-feedback"><?= $errors['god_given_purpose'] ?></div>
                                        <?php endif; ?>
                                        <div class="form-text">What do you believe God has called you to do?</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="denomination" class="form-label fw-bold">
                                            Denomination (Church) <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control <?= isset($errors['denomination']) ? 'is-invalid' : '' ?>" 
                                               id="denomination" name="denomination" 
                                               value="<?= htmlspecialchars($formData['denomination'] ?? '') ?>" required>
                                        <?php if (isset($errors['denomination'])): ?>
                                            <div class="invalid-feedback"><?= $errors['denomination'] ?></div>
                                        <?php endif; ?>
                                        <div class="form-text">E.g., Baptist, Anglican, Pentecostal, etc.</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="gifts_talents" class="form-label fw-bold">
                                            Your gifts, talents, or what you're really good at doing <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control <?= isset($errors['gifts_talents']) ? 'is-invalid' : '' ?>" 
                                                  id="gifts_talents" name="gifts_talents" 
                                                  rows="3" required><?= htmlspecialchars($formData['gifts_talents'] ?? '') ?></textarea>
                                        <?php if (isset($errors['gifts_talents'])): ?>
                                            <div class="invalid-feedback"><?= $errors['gifts_talents'] ?></div>
                                        <?php endif; ?>
                                        <div class="form-text">E.g., Teaching, Counseling, Music, Writing, etc.</div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-5">
                                    <button type="button" class="btn btn-outline-primary prev-section" data-prev="section2">
                                        <i class="fas fa-arrow-left me-2"></i> Previous
                                    </button>
                                    <button type="button" class="btn btn-primary next-section" data-next="section4">
                                        Next: Social Media & Passion <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Section 4: Social Media & Passion -->
                            <div class="form-section d-none" id="section4">
                                <div class="section-header mb-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-info text-white rounded-circle p-2 me-3">
                                            <i class="fas fa-share-alt fa-lg"></i>
                                        </div>
                                        <div>
                                            <h3 class="h4 fw-bold mb-0">Social Media & Passion</h3>
                                            <small class="text-muted">Your online presence and motivation</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-bold">
                                            Which social media platform do you belong to? <span class="text-danger">*</span>
                                        </label>
                                        <?php 
                                        $socialMediaOptions = [
                                            'Facebook', 'Twitter', 'Instagram', 'LinkedIn', 
                                            'YouTube', 'TikTok', 'WhatsApp', 'Telegram'
                                        ];
                                        ?>
                                        <div class="row">
                                            <?php foreach ($socialMediaOptions as $platform): ?>
                                                <div class="col-md-6">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input social-media" type="checkbox" 
                                                               name="social_media[]" id="social_<?= strtolower($platform) ?>" 
                                                               value="<?= $platform ?>"
                                                               <?= in_array($platform, $formData['social_media'] ?? []) ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="social_<?= strtolower($platform) ?>">
                                                            <i class="fab fa-<?= strtolower($platform) ?> me-2"></i><?= $platform ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php if (isset($errors['social_media'])): ?>
                                            <div class="text-danger small mt-1"><?= $errors['social_media'] ?></div>
                                        <?php endif; ?>
                                        
                                        <div class="mt-3">
                                            <label for="social_media_other" class="form-label fw-bold">
                                                Other Social Media Platform
                                            </label>
                                            <input type="text" class="form-control" 
                                                   id="social_media_other" name="social_media_other" 
                                                   value="<?= htmlspecialchars($formData['social_media_other'] ?? '') ?>">
                                            <div class="form-text">If not listed above</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label fw-bold">
                                            Are you passionate about impacting weak and vulnerable adolescents and youths positively? <span class="text-danger">*</span>
                                        </label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="passionate_about_youth" 
                                                       id="passionate_yes" value="Yes"
                                                       <?= ($formData['passionate_about_youth'] ?? '') === 'Yes' ? 'checked' : '' ?> required>
                                                <label class="form-check-label" for="passionate_yes">
                                                    Yes, absolutely
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="passionate_about_youth" 
                                                       id="passionate_somewhat" value="Somewhat"
                                                       <?= ($formData['passionate_about_youth'] ?? '') === 'Somewhat' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="passionate_somewhat">
                                                    Somewhat
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="passionate_about_youth" 
                                                       id="passionate_not_sure" value="Not sure"
                                                       <?= ($formData['passionate_about_youth'] ?? '') === 'Not sure' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="passionate_not_sure">
                                                    Not sure yet
                                                </label>
                                            </div>
                                        </div>
                                        <?php if (isset($errors['passionate_about_youth'])): ?>
                                            <div class="text-danger small mt-1"><?= $errors['passionate_about_youth'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="motivation" class="form-label fw-bold">
                                            What is motivating you to partner with Indefatigable Youth Empowerment Foundation? <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control <?= isset($errors['motivation']) ? 'is-invalid' : '' ?>" 
                                                  id="motivation" name="motivation" 
                                                  rows="5" required><?= htmlspecialchars($formData['motivation'] ?? '') ?></textarea>
                                        <?php if (isset($errors['motivation'])): ?>
                                            <div class="invalid-feedback"><?= $errors['motivation'] ?></div>
                                        <?php endif; ?>
                                        <div class="form-text">Share what draws you to volunteer with IYEF</div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="questions" class="form-label fw-bold">
                                            Do you have any questions for us?
                                        </label>
                                        <textarea class="form-control" id="questions" name="questions" 
                                                  rows="3"><?= htmlspecialchars($formData['questions'] ?? '') ?></textarea>
                                        <div class="form-text">Any questions about volunteering with IYEF?</div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-5">
                                    <button type="button" class="btn btn-outline-primary prev-section" data-prev="section3">
                                        <i class="fas fa-arrow-left me-2"></i> Previous
                                    </button>
                                    <button type="button" class="btn btn-primary next-section" data-next="section5">
                                        Next: Terms & Submit <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Section 5: Terms & Submit -->
                            <div class="form-section d-none" id="section5">
                                <div class="section-header mb-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-purple text-white rounded-circle p-2 me-3">
                                            <i class="fas fa-file-signature fa-lg"></i>
                                        </div>
                                        <div>
                                            <h3 class="h4 fw-bold mb-0">Terms & Submission</h3>
                                            <small class="text-muted">Review and submit your application</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <h5 class="alert-heading mb-3">
                                        <i class="fas fa-info-circle me-2"></i>Important Information
                                    </h5>
                                    <ul class="mb-0">
                                        <li>All fields marked with <span class="text-danger">*</span> are required</li>
                                        <li>Your information will be kept confidential and used only for volunteer coordination</li>
                                        <li>You will receive a confirmation email at <strong><?= htmlspecialchars($formData['email'] ?? '') ?></strong></li>
                                        <li>You will be contacted within 5-7 business days after submission</li>
                                        <li>Volunteers are expected to uphold Christian values and principles</li>
                                    </ul>
                                </div>
                                
                                <div class="form-check mb-4">
                                    <input class="form-check-input <?= isset($errors['terms_accepted']) ? 'is-invalid' : '' ?>" 
                                           type="checkbox" id="terms_accepted" name="terms_accepted" 
                                           <?= ($formData['terms_accepted'] ?? 0) ? 'checked' : '' ?> required>
                                    <label class="form-check-label fw-bold" for="terms_accepted">
                                        I agree to the <a href="terms.php" target="_blank">Terms and Conditions</a> and 
                                        <a href="privacy.php" target="_blank">Privacy Policy</a> of IYEF. <span class="text-danger">*</span>
                                    </label>
                                    <?php if (isset($errors['terms_accepted'])): ?>
                                        <div class="invalid-feedback d-block"><?= $errors['terms_accepted'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Application Summary (Read-only preview) -->
                                <div class="card border-0 bg-light mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold mb-3">
                                            <i class="fas fa-eye me-2"></i>Application Preview
                                        </h5>
                                        <div id="applicationPreview" class="small text-muted">
                                            Your application details will appear here...
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-5">
                                    <button type="button" class="btn btn-outline-primary prev-section" data-prev="section4">
                                        <i class="fas fa-arrow-left me-2"></i> Previous
                                    </button>
                                    <button type="submit" class="btn btn-success btn-lg px-5">
                                        <i class="fas fa-paper-plane me-2"></i> Submit Application
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Volunteer Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">BENEFITS</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">Why Volunteer With IYEF?</h2>
                <p class="lead text-muted">Make a difference while growing personally and spiritually</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                            <i class="fas fa-hands-helping fa-2x"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Make Real Impact</h4>
                        <p class="text-muted">Directly influence the lives of vulnerable youth through mentorship and support programs.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                            <i class="fas fa-heart fa-2x"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Spiritual Growth</h4>
                        <p class="text-muted">Grow in your faith while serving others in a Christ-centered environment.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Community</h4>
                        <p class="text-muted">Join a community of like-minded believers passionate about youth empowerment.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for form functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form sections and navigation
    const sections = document.querySelectorAll('.form-section');
    const nextButtons = document.querySelectorAll('.next-section');
    const prevButtons = document.querySelectorAll('.prev-section');
    const formProgress = document.getElementById('formProgress');
    const progressPercent = document.getElementById('progressPercent');
    const applicationPreview = document.getElementById('applicationPreview');
    
    // Initialize step text
    updateStepText(1);
    
    // Date picker for DOB
    const dobInput = document.getElementById('dob');
    if (dobInput) {
        // Add date picker attribute for better UX
        dobInput.setAttribute('title', 'Click to select date or enter as dd/mm/yyyy');
        
        dobInput.addEventListener('focus', function() {
            // Show HTML5 date picker on mobile devices
            if ('ontouchstart' in window || navigator.maxTouchPoints) {
                this.type = 'date';
            }
        });
        
        dobInput.addEventListener('blur', function() {
            // Convert HTML5 date format to dd/mm/yyyy if needed
            if (this.value && this.type === 'date') {
                const date = new Date(this.value);
                const day = date.getDate().toString().padStart(2, '0');
                const month = (date.getMonth() + 1).toString().padStart(2, '0');
                const year = date.getFullYear();
                this.value = `${day}/${month}/${year}`;
            }
            this.type = 'text'; // Always show as text for dd/mm/yyyy format
        });
        
        // Add input masking for dd/mm/yyyy format
        dobInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            
            if (value.length > 2 && value.length <= 4) {
                value = value.substring(0, 2) + '/' + value.substring(2);
            } else if (value.length > 4) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4) + '/' + value.substring(4, 8);
            }
            
            // Limit to 10 characters (dd/mm/yyyy)
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            
            this.value = value;
        });
    }
    
    // Education level conditional fields
    const educationLevelRadios = document.querySelectorAll('.education-level');
    const educationOtherContainer = document.getElementById('education_other_container');
    const undergraduateFields = document.getElementById('undergraduate_fields');
    
    educationLevelRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'Other') {
                educationOtherContainer.classList.remove('d-none');
                undergraduateFields.classList.add('d-none');
            } else if (this.value === 'Undergraduate') {
                educationOtherContainer.classList.add('d-none');
                undergraduateFields.classList.remove('d-none');
            } else {
                educationOtherContainer.classList.add('d-none');
                undergraduateFields.classList.add('d-none');
            }
            updateProgress();
        });
    });
    
    // Trigger initial state
    const selectedEducation = document.querySelector('.education-level:checked');
    if (selectedEducation) {
        selectedEducation.dispatchEvent(new Event('change'));
    }
    
    // Born again conditional field
    const bornAgainRadios = document.querySelectorAll('.born-again');
    const salvationExperienceContainer = document.getElementById('salvation_experience_container');
    
    bornAgainRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'Yes') {
                salvationExperienceContainer.classList.remove('d-none');
                salvationExperienceContainer.querySelector('textarea').required = true;
            } else {
                salvationExperienceContainer.classList.add('d-none');
                salvationExperienceContainer.querySelector('textarea').required = false;
            }
        });
    });
    
    // Discovered purpose conditional field
    const discoveredPurposeRadios = document.querySelectorAll('.discovered-purpose');
    const godGivenPurposeContainer = document.getElementById('god_given_purpose_container');
    
    discoveredPurposeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'Yes') {
                godGivenPurposeContainer.classList.remove('d-none');
                godGivenPurposeContainer.querySelector('textarea').required = true;
            } else {
                godGivenPurposeContainer.classList.add('d-none');
                godGivenPurposeContainer.querySelector('textarea').required = false;
            }
        });
    });
    
    // Social media validation
    const socialMediaCheckboxes = document.querySelectorAll('.social-media');
    socialMediaCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateProgress();
        });
    });
    
    // Navigation between sections
    nextButtons.forEach(button => {
        button.addEventListener('click', function() {
            const currentSectionId = this.closest('.form-section').id;
            const nextSectionId = this.getAttribute('data-next');
            
            // Validate current section before proceeding
            if (validateSection(currentSectionId)) {
                showSection(nextSectionId);
            }
        });
    });
    
    prevButtons.forEach(button => {
        button.addEventListener('click', function() {
            const prevSectionId = this.getAttribute('data-prev');
            showSection(prevSectionId);
        });
    });
    
    // Show specific section
    function showSection(sectionId) {
        // Find the index of the target section
        let targetIndex = 0;
        sections.forEach((section, index) => {
            if (section.id === sectionId) {
                targetIndex = index;
            }
        });
        
        sections.forEach(section => {
            section.classList.add('d-none');
        });
        
        const targetSection = document.getElementById(sectionId);
        if (targetSection) {
            targetSection.classList.remove('d-none');
            
            // Scroll to top of section
            targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Update progress
            updateProgress();
            
            // Update application preview if on last section
            if (sectionId === 'section5') {
                updateApplicationPreview();
            }
        }
    }
    
    // Email validation function
    function isValidEmail(email) {
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return pattern.test(email);
    }
    
    // Validate section fields
    function validateSection(sectionId) {
        const section = document.getElementById(sectionId);
        const requiredFields = section.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            // Skip hidden fields
            if (field.closest('.d-none')) return;
            
            // Skip radio/checkbox groups if at least one is checked
            if (field.type === 'radio' || field.type === 'checkbox') {
                const groupName = field.name;
                const groupChecked = section.querySelectorAll(`[name="${groupName}"]:checked`).length > 0;
                if (!groupChecked) {
                    isValid = false;
                    showFieldError(field, 'This field is required');
                }
            } else if (!field.value.trim()) {
                isValid = false;
                showFieldError(field, 'This field is required');
            } else {
                clearFieldError(field);
                
                // Additional validation for specific fields
                if (field.id === 'dob' && !isValidDate(field.value)) {
                    isValid = false;
                    showFieldError(field, 'Please enter a valid date (dd/mm/yyyy)');
                }
                
                if (field.id === 'age' && (field.value < 13 || field.value > 100)) {
                    isValid = false;
                    showFieldError(field, 'Age must be between 13 and 100');
                }
                
                if (field.id === 'phone_number' && !isValidPhone(field.value)) {
                    isValid = false;
                    showFieldError(field, 'Please enter a valid phone number');
                }
                
                if (field.id === 'email' && !isValidEmail(field.value)) {
                    isValid = false;
                    showFieldError(field, 'Please enter a valid email address');
                }
            }
        });
        
        return isValid;
    }
    
    // Show field error
    function showFieldError(field, message) {
        const formGroup = field.closest('.mb-3') || field.parentElement;
        field.classList.add('is-invalid');
        
        let errorDiv = formGroup.querySelector('.invalid-feedback');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            formGroup.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
    }
    
    // Clear field error
    function clearFieldError(field) {
        field.classList.remove('is-invalid');
        const formGroup = field.closest('.mb-3') || field.parentElement;
        const errorDiv = formGroup.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }
    
    // Validate date format
    function isValidDate(dateString) {
        if (!dateString) return false;
        
        // Try dd/mm/yyyy format
        const pattern = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;
        if (!pattern.test(dateString)) return false;
        
        const [, day, month, year] = dateString.match(pattern);
        
        // Check if the date is valid
        const date = new Date(year, month - 1, day);
        return date.getFullYear() == year && 
               date.getMonth() == month - 1 && 
               date.getDate() == day &&
               dateString === `${day.padStart(2, '0')}/${month.padStart(2, '0')}/${year}`;
    }
    
    // Validate phone number
    function isValidPhone(phone) {
        const pattern = /^[0-9+\-\s()]{10,20}$/;
        return pattern.test(phone.replace(/\s+/g, ''));
    }
    
    // Update progress bar
    function updateProgress() {
        let completedFields = 0;
        let totalRelevantFields = 0;
        
        // Get current visible section
        let currentSection = null;
        let currentSectionIndex = 0;
        
        sections.forEach((section, index) => {
            if (!section.classList.contains('d-none')) {
                currentSection = section;
                currentSectionIndex = index;
            }
        });
        
        if (!currentSection) return;
        
        // Count fields in current section and all previous sections
        sections.forEach((section, index) => {
            if (index <= currentSectionIndex) {
                const fields = section.querySelectorAll('[required]');
                
                fields.forEach(field => {
                    // Skip hidden fields
                    if (field.closest('.d-none')) return;
                    
                    totalRelevantFields++;
                    
                    if (field.type === 'radio' || field.type === 'checkbox') {
                        const groupName = field.name;
                        const groupChecked = section.querySelectorAll(`[name="${groupName}"]:checked`).length > 0;
                        if (groupChecked) completedFields++;
                    } else if (field.value.trim()) {
                        completedFields++;
                    }
                });
            }
        });
        
        // Calculate progress
        const totalSections = sections.length;
        const sectionsCompleted = currentSectionIndex;
        const sectionWeight = 20; // Each section is worth 20% (100% / 5 sections)
        const baseProgress = sectionsCompleted * sectionWeight;
        
        // Calculate field completion within current section
        let currentSectionProgress = 0;
        if (totalRelevantFields > 0) {
            currentSectionProgress = (completedFields / totalRelevantFields) * sectionWeight;
        }
        
        const progress = Math.min(baseProgress + currentSectionProgress, 100);
        
        // Update progress bar
        formProgress.style.width = `${progress}%`;
        progressPercent.textContent = Math.round(progress);
        
        // Update step text
        updateStepText(currentSectionIndex + 1);
    }
    
    // Update step text
    function updateStepText(currentStep) {
        const stepTitles = [
            "Step 1 of 5: Personal Information",
            "Step 2 of 5: Education & Career", 
            "Step 3 of 5: Spiritual Life",
            "Step 4 of 5: Social Media & Passion",
            "Step 5 of 5: Terms & Submit"
        ];
        
        const stepText = document.querySelector('.d-none.d-md-flex small.text-muted');
        if (stepText && stepTitles[currentStep - 1]) {
            stepText.textContent = stepTitles[currentStep - 1];
        }
    }
    
    // Update application preview
    function updateApplicationPreview() {
        const form = document.getElementById('volunteerForm');
        const formData = new FormData(form);
        let previewHTML = '<dl class="row mb-0">';
        
        // Collect all form data
        const previewData = {};
        formData.forEach((value, key) => {
            if (Array.isArray(previewData[key])) {
                previewData[key].push(value);
            } else if (previewData[key]) {
                previewData[key] = [previewData[key], value];
            } else {
                previewData[key] = value;
            }
        });
        
        // Create preview display
        const fieldLabels = {
            'full_name': 'Full Name',
            'email': 'Email Address',
            'gender': 'Gender',
            'dob': 'Date of Birth',
            'age': 'Age',
            'marital_status': 'Marital Status',
            'state_of_origin': 'State of Origin',
            'nationality': 'Nationality',
            'residential_address': 'Residential Address',
            'phone_number': 'Phone Number',
            'whatsapp_number': 'WhatsApp Number',
            'education_level': 'Education Level',
            'work_school_ppa': 'Place of Work/School',
            'occupation_course': 'Occupation/Course',
            'level_class': 'Level/Class',
            'last_cgpa': 'Last CGPA',
            'hobbies': 'Hobbies',
            'born_again': 'Born Again',
            'salvation_experience': 'Salvation Experience',
            'holy_spirit_baptism': 'Holy Spirit Baptism',
            'discovered_purpose': 'Discovered Purpose',
            'god_given_purpose': 'God-given Purpose',
            'denomination': 'Denomination',
            'gifts_talents': 'Gifts & Talents',
            'social_media': 'Social Media',
            'passionate_about_youth': 'Passionate About Youth',
            'motivation': 'Motivation',
            'questions': 'Questions'
        };
        
        Object.keys(fieldLabels).forEach(key => {
            if (previewData[key] && previewData[key].toString().trim()) {
                let value = previewData[key];
                
                // Handle arrays (social media)
                if (Array.isArray(value)) {
                    value = value.join(', ');
                }
                
                // Truncate long text
                if (value.length > 100) {
                    value = value.substring(0, 100) + '...';
                }
                
                previewHTML += `
                    <dt class="col-sm-4 text-muted">${fieldLabels[key]}:</dt>
                    <dd class="col-sm-8">${escapeHtml(value)}</dd>
                `;
            }
        });
        
        previewHTML += '</dl>';
        applicationPreview.innerHTML = previewHTML;
    }
    
    // HTML escaping for preview
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Initialize progress
    updateProgress();
    
    // Initialize conditional fields
    bornAgainRadios.forEach(radio => {
        if (radio.checked) radio.dispatchEvent(new Event('change'));
    });
    
    discoveredPurposeRadios.forEach(radio => {
        if (radio.checked) radio.dispatchEvent(new Event('change'));
    });
    
    // Form submission validation
    const volunteerForm = document.getElementById('volunteerForm');
    if (volunteerForm) {
        volunteerForm.addEventListener('submit', function(e) {
            // Validate all sections before submission
            let allValid = true;
            
            for (let i = 0; i < sections.length; i++) {
                if (!validateSection(sections[i].id)) {
                    allValid = false;
                    // Show first invalid section
                    if (sections[i].classList.contains('d-none')) {
                        showSection(sections[i].id);
                    }
                    break;
                }
            }
            
            if (!allValid) {
                e.preventDefault();
                alert('Please fill in all required fields correctly before submitting.');
            } else {
                // Add loading state to submit button
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Submitting...';
                    submitBtn.disabled = true;
                }
            }
        });
    }
    
    // Real-time validation on field blur
    const formFields = volunteerForm.querySelectorAll('input, textarea, select');
    formFields.forEach(field => {
        field.addEventListener('blur', function() {
            if (this.hasAttribute('required')) {
                validateField(this);
            }
            updateProgress();
        });
        
        // Also update progress on input for text fields
        if (field.type === 'text' || field.type === 'email' || field.type === 'tel' || field.type === 'number') {
            field.addEventListener('input', function() {
                updateProgress();
            });
        }
    });
    
    function validateField(field) {
        if (field.type === 'radio' || field.type === 'checkbox') {
            // Validate the entire group
            const groupName = field.name;
            const group = document.querySelectorAll(`[name="${groupName}"]`);
            const groupChecked = Array.from(group).some(f => f.checked);
            
            if (!groupChecked) {
                showFieldError(field, 'This field is required');
                return false;
            }
        } else if (!field.value.trim()) {
            showFieldError(field, 'This field is required');
            return false;
        }
        
        clearFieldError(field);
        
        // Additional field-specific validation
        if (field.id === 'dob' && !isValidDate(field.value)) {
            showFieldError(field, 'Please enter a valid date (dd/mm/yyyy or select from calendar)');
            return false;
        }
        
        if (field.id === 'age' && (field.value < 13 || field.value > 100)) {
            showFieldError(field, 'Age must be between 13 and 100');
            return false;
        }
        
        if (field.id === 'phone_number' && !isValidPhone(field.value)) {
            showFieldError(field, 'Please enter a valid phone number');
            return false;
        }
        
        if (field.id === 'email' && !isValidEmail(field.value)) {
            showFieldError(field, 'Please enter a valid email address');
            return false;
        }
        
        return true;
    }
});
</script>

<style>
.min-vh-60 {
    min-height: 60vh;
}

.form-section {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.section-header {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 1rem;
}

.dot {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(76, 217, 100, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(76, 217, 100, 0); }
    100% { box-shadow: 0 0 0 0 rgba(76, 217, 100, 0); }
}

.bg-purple {
    background-color: #6f42c1 !important;
}

/* Form styling */
.form-label.required::after {
    content: " *";
    color: #dc3545;
}

/* Custom checkbox/radio styling */
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .display-2 {
        font-size: 2.5rem;
    }
    
    .lead {
        font-size: 1.1rem;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem !important;
        font-size: 1rem !important;
    }
}

@media (max-width: 576px) {
    .display-2 {
        font-size: 2rem;
    }
    
    .form-check-inline {
        display: block;
        margin-right: 0;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>