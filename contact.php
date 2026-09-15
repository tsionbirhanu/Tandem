<?php
// contact.php
// This single file handles both rendering the contact form (HTTP GET) and processing its submission (HTTP POST).

// Check if the form was submitted.
// $_SERVER['REQUEST_METHOD'] tells us if the browser sent a GET (navigated to page) or POST (submitted form) request.
$isSubmitted = ($_SERVER['REQUEST_METHOD'] === 'POST');

// Variables to store form data and errors
$name = '';
$email = '';
$message = '';
$errors = [];
$successMessage = '';

if ($isSubmitted) {
    // 1. Sanitize and retrieve POST data
    // It's important to trim whitespace and prevent XSS (though htmlspecialchars is better on output).
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // 2. Validate data
    if (empty($name)) {
        $errors['name'] = "Please enter your name.";
    }
    
    if (empty($email)) {
        $errors['email'] = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }
    
    if (empty($message)) {
        $errors['message'] = "Please tell us about your project.";
    }

    // 3. Process success
    if (empty($errors)) {
        // Normally you would save to a database or send an email here.
        // For our demo, we just set a success message.
        $successMessage = "Thank you, {$name}! Your project request has been received. We'll be in touch soon.";
        
        // Reset form fields after successful submission so the user doesn't double-submit
        $name = '';
        $email = '';
        $message = '';
    }
}

include 'includes/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    
    <div style="max-width: 600px; margin: 0 auto;">
        
        <div style="text-align: center; margin-bottom: var(--space-32);">
            <h1>Start a Project</h1>
            <p class="text-small" style="color: var(--color-text-muted);">Tell us what you need, and we'll connect you with the right talent.</p>
        </div>

        <?php if (!empty($successMessage)): ?>
            <!-- Success State -->
            <div class="alert alert-success">
                <strong>Success:</strong> <?php echo htmlspecialchars($successMessage); ?>
            </div>
            <div style="text-align: center; margin-top: var(--space-24);">
                <a href="services.php" class="btn btn-secondary">Browse More Services</a>
            </div>
        <?php else: ?>
            <!-- Form State -->
            
            <?php if (!empty($errors)): ?>
                <!-- Global error summary (optional, but good for UX) -->
                <div class="alert alert-error">
                    <strong>Please correct the errors below to continue.</strong>
                </div>
            <?php endif; ?>

            <div class="sg-card">
                <!-- 
                   The form action is empty, which defaults to submitting to itself (contact.php).
                   The method is POST so data is sent securely in the request body, not the URL.
                -->
                <form action="" method="POST">
                    
                    <div class="form-group">
                        <label class="form-label" for="name">Your Name</label>
                        <!-- We re-populate the value attribute so the user doesn't lose typed data if there's an error -->
                        <input type="text" id="name" name="name" 
                               class="form-input <?php echo isset($errors['name']) ? 'form-input-error' : ''; ?>" 
                               value="<?php echo htmlspecialchars($name); ?>">
                        <?php if (isset($errors['name'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['name']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" 
                               class="form-input <?php echo isset($errors['email']) ? 'form-input-error' : ''; ?>" 
                               value="<?php echo htmlspecialchars($email); ?>">
                        <?php if (isset($errors['email'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['email']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="message">Project Details</label>
                        <textarea id="message" name="message" rows="5" 
                                  class="form-input <?php echo isset($errors['message']) ? 'form-input-error' : ''; ?>"><?php echo htmlspecialchars($message); ?></textarea>
                        <?php if (isset($errors['message'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['message']); ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Request</button>
                    
                </form>
            </div>
        <?php endif; ?>
        
    </div>

</main>

<?php include 'includes/footer.php'; ?>
