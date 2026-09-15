<?php
// app/Views/messages/contact.php
include BASE_PATH . '/app/Views/layouts/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    
    <div style="max-width: 600px; margin: 0 auto;">
        
        <?php if ($isSuccess): ?>
            <!-- Polished Success State -->
            <div class="sg-card" style="text-align: center; padding: var(--space-48) var(--space-32);">
                <div style="width: 72px; height: 72px; background-color: rgba(72, 187, 120, 0.12); color: var(--color-success); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-24);">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                
                <h1 style="font-size: var(--text-h2); margin-bottom: var(--space-12);">Request Received!</h1>
                <p style="font-size: var(--text-body); color: var(--color-text-muted); margin-bottom: var(--space-24); line-height: 1.6;">
                    Thank you, <strong><?php echo htmlspecialchars($submittedData['name'], ENT_QUOTES, 'UTF-8'); ?></strong>! We've received your project request and sent a confirmation to <strong><?php echo htmlspecialchars($submittedData['email'], ENT_QUOTES, 'UTF-8'); ?></strong>.
                </p>

                <div style="background-color: var(--color-bg-base); border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: var(--space-20); text-align: left; margin-bottom: var(--space-32);">
                    <div style="font-size: var(--text-caption); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-muted); margin-bottom: var(--space-8);">
                        Submitted Message Excerpt
                    </div>
                    <p style="margin: 0; font-size: var(--text-small); color: var(--color-text-neutral); white-space: pre-wrap; font-style: italic;">
                        "<?php echo htmlspecialchars($submittedData['message'], ENT_QUOTES, 'UTF-8'); ?>"
                    </p>
                </div>

                <div style="display: flex; gap: var(--space-16); justify-content: center; flex-wrap: wrap;">
                    <a href="/services" class="btn btn-primary">Browse Services</a>
                    <a href="/contact" class="btn btn-secondary">Send Another Request</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Form State -->
            <div style="text-align: center; margin-bottom: var(--space-32);">
                <h1>Start a Project</h1>
                <p class="text-small" style="color: var(--color-text-muted);">Tell us what you need, and we'll connect you with the right talent.</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error" style="margin-bottom: var(--space-24);">
                    <strong>Please correct the errors below to continue.</strong>
                </div>
            <?php endif; ?>

            <div class="sg-card">
                <form action="/contact" method="POST" novalidate>
                    
                    <div class="form-group">
                        <label class="form-label" for="name">Your Name</label>
                        <input type="text" id="name" name="name" 
                               class="form-input <?php echo isset($errors['name']) ? 'form-input-error' : ''; ?>" 
                               value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
                               placeholder="e.g. Jane Doe">
                        <?php if (isset($errors['name'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" 
                               class="form-input <?php echo isset($errors['email']) ? 'form-input-error' : ''; ?>" 
                               value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
                               placeholder="e.g. jane@example.com">
                        <?php if (isset($errors['email'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-8);">
                            <label class="form-label" for="message" style="margin-bottom: 0;">Project Details</label>
                            <span id="char-counter" class="text-caption" style="transition: color 0.2s ease;">
                                0 / 1000 characters
                            </span>
                        </div>
                        <textarea id="message" name="message" rows="5" 
                                  class="form-input <?php echo isset($errors['message']) ? 'form-input-error' : ''; ?>"
                                  placeholder="Describe your project, requirements, or scope (min 20 characters)..."><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <?php if (isset($errors['message'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['message'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Request</button>
                    
                </form>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const messageInput = document.getElementById('message');
                const charCounter = document.getElementById('char-counter');
                
                if (messageInput && charCounter) {
                    const minLength = 20;
                    const maxLength = 1000;
                    
                    function updateCounter() {
                        const count = messageInput.value.length;
                        charCounter.textContent = `${count} / ${maxLength} characters`;
                        
                        if (count > maxLength) {
                            charCounter.style.color = 'var(--color-error)';
                            charCounter.style.fontWeight = '600';
                        } else if (count > 0 && count < minLength) {
                            charCounter.style.color = '#d69e2e'; // warning tone
                            charCounter.style.fontWeight = '500';
                        } else {
                            charCounter.style.color = 'var(--color-text-muted)';
                            charCounter.style.fontWeight = 'normal';
                        }
                    }
                    
                    ['input', 'keyup', 'paste', 'change'].forEach(function(eventType) {
                        messageInput.addEventListener(eventType, updateCounter);
                    });
                    
                    updateCounter();
                }
            });
            </script>
        <?php endif; ?>
        
    </div>

</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
