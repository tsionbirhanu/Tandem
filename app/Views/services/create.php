<?php
// app/Views/services/create.php
include BASE_PATH . '/app/Views/layouts/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    <div style="max-width: 680px; margin: 0 auto;">
        
        <div style="margin-bottom: var(--space-24);">
            <a href="/services" style="color: var(--color-primary); text-decoration: none; font-weight: 500;">
                &larr; Back to Services
            </a>
        </div>

        <div style="margin-bottom: var(--space-32);">
            <h1>Offer a New Service</h1>
            <p class="text-small" style="color: var(--color-text-muted);">Publish a service listing with a rich gallery on the Tandem network.</p>
        </div>

        <?php if ($dbError): ?>
            <div class="alert alert-error" style="margin-bottom: var(--space-24);">
                <strong>Database Error:</strong> <?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div class="sg-card">
            <form action="/services/create" method="POST" enctype="multipart/form-data" novalidate id="service-create-form">
                
                <div class="form-group">
                    <label class="form-label" for="title">Service Title</label>
                    <input type="text" id="title" name="title" 
                           class="form-input <?php echo isset($errors['title']) ? 'form-input-error' : ''; ?>" 
                           value="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="e.g. Modern Brand Identity & Logo Package">
                    <?php if (isset($errors['title'])): ?>
                        <div class="form-error-msg"><?php echo htmlspecialchars($errors['title'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="form-select <?php echo isset($errors['category_id']) ? 'form-input-error' : ''; ?>">
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo (int)$cat['id']; ?>" <?php echo ($categoryId === (int)$cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['category_id'])): ?>
                        <div class="form-error-msg"><?php echo htmlspecialchars($errors['category_id'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="price">Starting Price ($ USD)</label>
                    <input type="number" step="0.01" min="1" id="price" name="price" 
                           class="form-input <?php echo isset($errors['price']) ? 'form-input-error' : ''; ?>" 
                           value="<?php echo htmlspecialchars($price, ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="e.g. 750.00">
                    <?php if (isset($errors['price'])): ?>
                        <div class="form-error-msg"><?php echo htmlspecialchars($errors['price'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="summary">Service Summary & Scope</label>
                    <textarea id="summary" name="summary" rows="5" 
                              class="form-input <?php echo isset($errors['summary']) ? 'form-input-error' : ''; ?>"
                              placeholder="Describe what is included in this service offer..."><?php echo htmlspecialchars($summary, ENT_QUOTES, 'UTF-8'); ?></textarea>
                    <?php if (isset($errors['summary'])): ?>
                        <div class="form-error-msg"><?php echo htmlspecialchars($errors['summary'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>

                <!-- Drag and Drop Multi-Image Upload Section -->
                <div class="form-group" style="margin-top: var(--space-32);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-8);">
                        <label class="form-label" style="margin-bottom: 0;">Service Gallery Images (Up to 5)</label>
                        <span id="file-count-badge" class="text-caption" style="color: var(--color-text-muted); font-weight: 500;">
                            0 / 5 images selected
                        </span>
                    </div>

                    <!-- Dropzone Container -->
                    <div id="dropzone" style="border: 2px dashed var(--color-primary); background-color: rgba(44, 95, 93, 0.04); border-radius: var(--radius-md); padding: var(--space-32) var(--space-16); text-align: center; cursor: pointer; transition: all 0.2s ease;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2" style="margin-bottom: var(--space-12);"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        <h4 style="margin: 0 0 var(--space-4) 0; font-size: var(--text-body); color: var(--color-primary);">Drag & drop images here, or <span style="text-decoration: underline;">click to browse</span></h4>
                        <p class="text-caption" style="margin: 0; color: var(--color-text-muted);">Supports JPG, PNG, and WebP up to 5MB each.</p>
                        <input type="file" id="service_images" name="service_images[]" multiple accept="image/jpeg,image/png,image/webp" style="display: none;">
                    </div>

                    <!-- Validation Feedback Box -->
                    <div id="upload-error-box" class="alert alert-error" style="display: none; margin-top: var(--space-12);"></div>

                    <!-- Live Thumbnail Previews Container -->
                    <div id="preview-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: var(--space-12); margin-top: var(--space-16);"></div>
                    
                    <?php if (isset($errors['service_images'])): ?>
                        <div class="form-error-msg" style="margin-top: var(--space-8);"><?php echo htmlspecialchars($errors['service_images'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>

                <div style="display: flex; gap: var(--space-16); margin-top: var(--space-32);">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Publish Service</button>
                    <a href="/services" class="btn btn-secondary">Cancel</a>
                </div>

            </form>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('service_images');
    const previewGrid = document.getElementById('preview-grid');
    const errorBox = document.getElementById('upload-error-box');
    const countBadge = document.getElementById('file-count-badge');

    const maxFiles = 5;
    const maxSizeBytes = 5242880; // 5MB
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

    let selectedFiles = [];

    // Trigger file dialog on dropzone click
    dropzone.addEventListener('click', () => fileInput.click());

    // Highlight dropzone on dragover
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.backgroundColor = 'rgba(44, 95, 93, 0.12)';
            dropzone.style.borderColor = 'var(--color-primary-dark)';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.backgroundColor = 'rgba(44, 95, 93, 0.04)';
            dropzone.style.borderColor = 'var(--color-primary)';
        }, false);
    });

    // Handle dropped files
    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    });

    // Handle selected files via input
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });

    function handleFiles(files) {
        errorBox.style.display = 'none';
        errorBox.textContent = '';

        if (!files || files.length === 0) return;

        let rejectedMessages = [];

        Array.from(files).forEach(file => {
            if (selectedFiles.length >= maxFiles) {
                rejectedMessages.push(`Maximum of ${maxFiles} images allowed. Additional files ignored.`);
                return;
            }

            if (!allowedTypes.includes(file.type)) {
                rejectedMessages.push(`"${file.name}" rejected: Invalid file type (${file.type}). JPG, PNG, and WebP only.`);
                return;
            }

            if (file.size > maxSizeBytes) {
                const sizeMb = (file.size / (1024 * 1024)).toFixed(1);
                rejectedMessages.push(`"${file.name}" rejected: File size (${sizeMb}MB) exceeds 5MB limit.`);
                return;
            }

            selectedFiles.push(file);
        });

        if (rejectedMessages.length > 0) {
            errorBox.style.display = 'block';
            errorBox.innerHTML = rejectedMessages.join('<br>');
        }

        updateUI();
    }

    function updateUI() {
        previewGrid.innerHTML = '';
        countBadge.textContent = `${selectedFiles.length} / ${maxFiles} images selected`;

        // Sync files array to DataTransfer for form submission
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach((file, index) => {
            dataTransfer.items.add(file);

            // Render live thumbnail preview
            const card = document.createElement('div');
            card.style.position = 'relative';
            card.style.border = '1px solid var(--color-border)';
            card.style.borderRadius = 'var(--radius-sm)';
            card.style.overflow = 'hidden';
            card.style.backgroundColor = '#fff';
            card.style.boxShadow = 'var(--shadow-subtle)';

            const img = document.createElement('img');
            img.style.width = '100%';
            img.style.height = '85px';
            img.style.objectFit = 'cover';
            img.style.display = 'block';
            img.src = URL.createObjectURL(file);

            const meta = document.createElement('div');
            meta.style.padding = '4px 6px';
            meta.style.fontSize = '0.7rem';
            meta.style.color = 'var(--color-text-muted)';
            meta.style.whiteSpace = 'nowrap';
            meta.style.overflow = 'hidden';
            meta.style.textOverflow = 'ellipsis';
            meta.textContent = file.name;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.innerHTML = '&times;';
            removeBtn.style.position = 'absolute';
            removeBtn.style.top = '4px';
            removeBtn.style.right = '4px';
            removeBtn.style.backgroundColor = 'rgba(229, 62, 62, 0.9)';
            removeBtn.style.color = '#fff';
            removeBtn.style.border = 'none';
            removeBtn.style.borderRadius = '50%';
            removeBtn.style.width = '20px';
            removeBtn.style.height = '20px';
            removeBtn.style.cursor = 'pointer';
            removeBtn.style.fontSize = '14px';
            removeBtn.style.lineHeight = '18px';
            removeBtn.style.textAlign = 'center';

            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                selectedFiles.splice(index, 1);
                updateUI();
            });

            card.appendChild(img);
            card.appendChild(meta);
            card.appendChild(removeBtn);
            previewGrid.appendChild(card);
        });

        fileInput.files = dataTransfer.files;
    }
});
</script>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
