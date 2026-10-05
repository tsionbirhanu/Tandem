<?php
// app/Views/partials/service-form.php
// Shared fields for creating and editing a service, with a live preview card.
// Expects: $categories, $title, $categoryId, $price, $summary, $errors; optional $galleryImages, $service.

$errors        = $errors ?? [];
$existing      = [];
foreach ($galleryImages ?? [] as $img) {
    $existing[] = ['id' => (int)$img['id'], 'url' => assetUrl($img['image_path'])];
}
$categoryNames = array_column($categories, 'name', 'id');
$me            = currentUser();
$previewId     = (int)($service['id'] ?? 0);
?>
<div class="detail"
     x-data="{
       title: <?= e(json_encode($title ?? '')) ?>,
       category: <?= e(json_encode((string)($categoryId ?? ''))) ?>,
       price: <?= e(json_encode((string)($price ?? ''))) ?>,
       names: <?= e(json_encode($categoryNames)) ?>,
       removing: [],
       get kept() { return <?= count($existing) ?> - this.removing.length; },
       toggle(id) { this.removing.includes(id) ? this.removing = this.removing.filter(x => x !== id) : this.removing.push(id); },
       get priceLabel() { const n = parseFloat(this.price); return n > 0 ? '$' + n.toLocaleString(undefined, { maximumFractionDigits: 2 }) : '$—'; }
     }">

  <div class="panel">
    <div class="field <?= isset($errors['title']) ? 'has-error' : '' ?>" x-data="charCount(0, 120)" x-effect="text = title">
      <label class="label" for="title">Title <span class="counter" :class="state" x-text="label"></span></label>
      <input class="input" type="text" id="title" name="title" x-model="title" maxlength="255"
             placeholder="e.g. A complete brand identity for a new café">
      <p class="field-help">Say what the client gets, not what you are. “Logo + brand guide” beats “Creative designer”.</p>
      <?= fieldError($errors, 'title') ?>
    </div>

    <div class="row row-wrap" style="align-items: flex-start; gap: 16px;">
      <div class="field grow <?= isset($errors['category_id']) ? 'has-error' : '' ?>" style="min-width: 200px;">
        <label class="label" for="category_id">Category</label>
        <select class="select" id="category_id" name="category_id" x-model="category">
          <option value="">Pick one…</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>" <?= (int)($categoryId ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <?= fieldError($errors, 'category_id') ?>
      </div>

      <div class="field <?= isset($errors['price']) ? 'has-error' : '' ?>" style="width: 180px;">
        <label class="label" for="price">Starting price</label>
        <div class="input-prefix">
          <span>$</span>
          <input class="input" type="number" id="price" name="price" min="1" step="1" inputmode="decimal" x-model="price" placeholder="500">
        </div>
        <?= fieldError($errors, 'price') ?>
      </div>
    </div>

    <div class="field <?= isset($errors['summary']) ? 'has-error' : '' ?>" x-data="charCount(40, 2000, <?= e(json_encode($summary ?? '')) ?>)">
      <label class="label" for="summary">What's included <span class="counter" :class="state" x-text="label"></span></label>
      <textarea class="textarea" id="summary" name="summary" rows="8" x-model="text"
                placeholder="What will you deliver? How many revisions? Roughly how long does it take? Leave a blank line between paragraphs."><?= e($summary ?? '') ?></textarea>
      <?= fieldError($errors, 'summary') ?>
    </div>

    <?php if ($existing): ?>
      <div class="field">
        <span class="label">Current images <span class="hint">click × to remove on save</span></span>
        <div class="thumbs" style="margin-top: 0;">
          <?php foreach ($existing as $n => $img): ?>
            <div class="thumb" :class="{ 'is-marked': removing.includes(<?= $img['id'] ?>) }">
              <?php if ($img['url']): ?>
                <img src="<?= e($img['url']) ?>" alt="">
              <?php else: ?>
                <span class="small muted" style="display: grid; place-items: center; height: 100%;">missing file</span>
              <?php endif; ?>
              <?php if ($n === 0): ?><span class="tag tag-butter badge-first">Cover</span><?php endif; ?>
              <button type="button" class="x" @click="toggle(<?= $img['id'] ?>)" :aria-label="removing.includes(<?= $img['id'] ?>) ? 'Keep image' : 'Remove image'"
                      x-text="removing.includes(<?= $img['id'] ?>) ? '↺' : '×'">×</button>
              <template x-if="removing.includes(<?= $img['id'] ?>)">
                <input type="hidden" name="delete_images[]" value="<?= $img['id'] ?>">
              </template>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="field <?= isset($errors['service_images']) ? 'has-error' : '' ?>" x-data="dropzone(5, 5)" x-effect="limit = Math.max(0, 5 - kept)">
      <span class="label"><?= $existing ? 'Add more images' : 'Images' ?> <span class="hint" x-text="files.length + ' / ' + Math.max(0, 5 - kept) + ' added'"></span></span>
      <label class="dropzone" :class="{ 'is-over': over }"
             @dragover.prevent="over = true" @dragleave.prevent="over = false" @drop.prevent="drop($event)">
        <span>
          <svg class="dz-icon" width="44" height="44" viewBox="0 0 44 44" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect x="5" y="9" width="34" height="26" rx="3"/><circle cx="15" cy="18" r="3" stroke="#e0532f"/><path d="M5 30l9-8 7 6 6-5 12 9"/>
          </svg>
          <b style="display: block;">Drop images here, or click to choose</b>
          <span class="small muted">Up to 5 in total · JPG, PNG, WebP · 5 MB each. The first one becomes the cover.</span>
        </span>
        <input type="file" name="service_images[]" multiple accept="image/jpeg,image/png,image/webp" class="sr-only" x-ref="input"
               @change="add(Array.from($event.target.files))">
      </label>
      <p class="field-error" x-show="error" x-text="error" x-cloak></p>
      <div class="thumbs" x-show="files.length" x-cloak>
        <template x-for="(f, n) in files" :key="f.id">
          <div class="thumb" x-transition>
            <img :src="f.url" alt="">
            <span class="tag tag-butter badge-first" x-show="n === 0 && kept === 0">Cover</span>
            <button type="button" class="x" @click="remove(f.id)" aria-label="Remove image">×</button>
          </div>
        </template>
      </div>
      <?= fieldError($errors, 'service_images') ?>
    </div>

    <?php if (isset($formActions)) { $formActions(); } ?>
  </div>

  <!-- Live preview -->
  <aside>
    <div class="buy-box">
      <p class="filter-title">Preview</p>
      <div class="card" style="pointer-events: none;">
        <div class="cover cover-grid" x-show="true">
          <span class="cover-no">№ <?= $previewId ? str_pad((string)$previewId, 3, '0', STR_PAD_LEFT) : 'new' ?></span>
          <span class="cover-cat" x-text="names[category] || 'Category'"></span>
        </div>
        <div class="card-body">
          <h3 class="card-title" x-text="title.trim() || 'Your service title'"></h3>
          <div class="card-by">
            <?= avatar($me?->getAvatarUrl(), $me?->getName() ?? 'You', 'xs') ?>
            <span><?= e($me?->getName() ?? 'You') ?></span>
          </div>
          <div class="card-foot">
            <span class="tag">New</span>
            <span class="price"><small>from</small><span x-text="priceLabel"></span></span>
          </div>
        </div>
      </div>
      <p class="small muted mt-16">This is roughly how your listing appears in the directory.</p>
    </div>
  </aside>
</div>
