<?php
// app/Views/services/edit.php
include BASE_PATH . '/app/Views/layouts/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    <div style="max-width: 680px; margin: 0 auto;">
        
        <div style="margin-bottom: var(--space-24);">
            <a href="/services/<?php echo (int)($service['id'] ?? 0); ?>" style="color: var(--color-primary); text-decoration: none; font-weight: 500;">
                &larr; Back to Service Details
            </a>
        </div>

        <div style="margin-bottom: var(--space-32);">
            <h1>Edit Service Listing</h1>
            <p class="text-small" style="color: var(--color-text-muted);">Update your service details, pricing, and gallery images.</p>
        </div>

        <?php if ($dbError): ?>
            <div class="alert alert-error" style="margin-bottom: var(--space-24);">
                <strong>Database Error:</strong> <?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($service): ?>
            <div class="sg-card">
                <form action="/services/<?php echo (int)$service['id']; ?>/edit" method="POST" enctype="multipart/form-data" novalidate id="service-edit-form">
                    <input type="hidden" name="id" value="<?php echo (int)$service['id']; ?>">

                    <div class="form-group">
                        <label class="form-label" for="title">Service Title</label>
                        <input type="text" id="title" name="title" 
                               class="form-input <?php echo isset($errors['title']) ? 'form-input-error' : ''; ?>" 
                               value="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
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
                               value="<?php echo htmlspecialchars($price, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php if (isset($errors['price'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['price'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="summary">Service Summary & Scope</label>
                        <textarea id="summary" name="summary" rows="5" 
                                  class="form-input <?php echo isset($errors['summary']) ? 'form-input-error' : ''; ?>"><?php echo htmlspecialchars($summary, ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <?php if (isset($errors['summary'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['summary'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Existing Gallery Images -->
                    <?php if (!empty($galleryImages)): ?>
                        <div class="form-group" style="margin-top: var(--space-24);">
                            <label class="form-label">Current Gallery Images</label>
                            <p class="text-caption" style="color: var(--color-text-muted); margin-bottom: var(--space-12);">Check any image you wish to delete from the gallery.</p>
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: var(--space-12);">
                                <?php foreach ($galleryImages as $img): ?>
                                    <div style="position: relative; border: 1px solid var(--color-border); border-radius: var(--radius-sm); overflow: hidden; background-color: #fff;">
                                        <img src="<?php echo htmlspecialchars($img['image_path'], ENT_QUOTES, 'UTF-8'); ?>" 
                                             alt="Gallery Image" style="width: 100%; height: 90px; object-fit: cover;">
                                        <label style="display: flex; align-items: center; gap: 4px; padding: 4px 6px; font-size: 0.75rem; color: var(--color-error); cursor: pointer;">
                                            <input type="checkbox" name="delete_images[]" value="<?php echo (int)$img['id']; ?>"> Delete
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Add New Gallery Images via Drag & Drop -->
                    <div class="form-group" style="margin-top: var(--space-24);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-8);">
                            <label class="form-label" style="margin-bottom: 0;">Add New Gallery Images (Up to 5 total)</label>
                            <span id="file-count-badge" class="text-caption" style="color: var(--color-text-muted); font-weight: 500;">
                                0 new images selected
                            </span>
                        </div>

                        <!-- Dropzone Container -->
                        <div id="dropzone" style="border: 2px dashed var(--color-primary); background-color: rgba(44, 95, 93, 0.04); border-radius: var(--radius-md); padding: var(--space-24) var(--space-16); text-align: center; cursor: pointer; transition: all 0.2s ease;">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2" style="margin-bottom: var(--space-8);"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            <h4 style="margin: 0 0 var(--space-4) 0; font-size: var(--text-body); color: var(--color-primary);">Drag & drop new images, or <span style="text-decoration: underline;">click to browse</span></h4>
                            <p class="text-caption" style="margin: 0; color: var(--color-text-muted);">JPG, PNG, WebP up to 5MB.</p>
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
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Save Changes</button>
                        <a href="/services/<?php echo (int)$service['id']; ?>" class="btn btn-secondary">Cancel</a>
                    </div>

                </form>
            </div>
        <?php endif; ?>

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

    if (dropzone && fileInput) {
        dropzone.addEventListener('click', () => fileInput.click());

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

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            handleFiles(dt.files);
        });

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
                    rejectedMessages.push(`Maximum of ${maxFiles} new images allowed.`);
                    return;
                }
                if (!allowedTypes.includes(file.type)) {
                    rejectedMessages.push(`"${file.name}" rejected: Invalid file type (${file.type}).`);
                    return;
                }
                if (file.size > maxSizeBytes) {
                    const sizeMb = (file.size / (1024 * 1024)).toFixed(1);
                    rejectedMessages.push(`"${file.name}" rejected: Exceeds 5MB limit (${sizeMb}MB).`);
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
            countBadge.textContent = `${selectedFiles.length} new images selected`;

            const dataTransfer = new DataTransfer();
            selectedFiles.forEach((file, index) => {
                dataTransfer.items.add(file);

                const card = document.createElement('div');
                card.style.position = 'relative';
                card.style.border = '1px solid var(--color-border)';
                card.style.borderRadius = 'var(--radius-sm)';
                card.style.overflow = 'hidden';
                card.style.backgroundColor = '#fff';

                const img = document.createElement('img');
                img.style.width = '100%';
                img.style.height = '80px';
                img.style.objectFit = 'cover';
                img.style.display = 'block';
                img.src = URL.createObjectURL(file);

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

                removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectedFiles.splice(index, 1);
                    updateUI();
                });

                card.appendChild(img);
                card.appendChild(removeBtn);
                previewGrid.appendChild(card);
            });

            fileInput.files = dataTransfer.files;
        }
    }
});
</script>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
