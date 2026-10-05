<?php
// app/Views/services/index.php
// Services directory. Filters update the results live via htmx (the whole page is
// fetched and only #results is swapped); without JS it is a plain GET form.
$pageTitle = 'Browse work';
include BASE_PATH . '/app/Views/layouts/header.php';

$sort = $sort ?? 'newest';
$ratingOptions = ['' => 'Any rating', '3' => '3+ stars', '4' => '4+ stars', '4.5' => '4.5+ stars'];
$currentRating = ($minRating ?? null) ? rtrim(rtrim(number_format((float)$minRating, 1, '.', ''), '0'), '.') : '';
$canList = isLoggedIn() && in_array($_SESSION['user_role'] ?? '', ['freelancer', 'admin'], true);
?>

<main class="wrap">
  <header class="page-head row between row-wrap" style="align-items: flex-end;">
    <div>
      <span class="eyebrow">The directory</span>
      <h1>Browse <em>work</em></h1>
      <p class="muted mb-0">Fixed starting prices, real reviews. Narrow it down on the left.</p>
    </div>
    <?php if ($canList): ?>
      <a href="/services/create" class="btn btn-accent">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M7 2v10M2 7h10"/></svg>
        List a service
      </a>
    <?php endif; ?>
  </header>

  <?php if (!empty($dbError)): ?>
    <div class="alert"><strong>Couldn't load services.</strong> <?= e($dbError) ?></div>
  <?php endif; ?>

  <form id="directory" class="directory" action="/services" method="GET"
        x-data="{
          filtersOpen: false,
          clear(key) {
            const f = this.$root;
            if (key === 'search') f.querySelector('#q').value = '';
            if (key === 'category') f.querySelector('input[name=category][value=\'\']').checked = true;
            if (key === 'price') { f.querySelector('#min_price').value = ''; f.querySelector('#max_price').value = ''; }
            if (key === 'min_rating') f.querySelector('input[name=min_rating][value=\'\']').checked = true;
            htmx.trigger(f, 'change');
          }
        }"
        hx-get="/services"
        hx-trigger="submit, change, keyup changed delay:300ms from:#q, keyup changed delay:500ms from:.price-input"
        hx-target="#results" hx-select="#results" hx-swap="outerHTML"
        hx-push-url="true" hx-indicator="#loading-bar">

    <!-- Filters -->
    <aside class="filters" :class="{ 'is-open': filtersOpen }" aria-label="Filters">
      <button type="button" class="btn btn-sm filters-toggle" @click="filtersOpen = !filtersOpen" :aria-expanded="filtersOpen">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M2 4h12M4 8h8M6 12h4"/></svg>
        <span x-text="filtersOpen ? 'Hide filters' : 'Filters'">Filters</span>
      </button>

      <div class="filters-body">
        <div class="filter-group">
          <p class="filter-title">Category</p>
          <div class="chips">
            <label class="chip">
              <input type="radio" name="category" value="" <?= empty($selectedCategory) ? 'checked' : '' ?>>
              <span>Everything</span>
            </label>
            <?php foreach ($categories as $cat): ?>
              <label class="chip">
                <input type="radio" name="category" value="<?= e($cat['slug']) ?>" <?= ($selectedCategory ?? '') === $cat['slug'] ? 'checked' : '' ?>>
                <span><?= e($cat['name']) ?> <span class="count"><?= (int)($cat['service_count'] ?? 0) ?></span></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="filter-group">
          <p class="filter-title">Budget</p>
          <div class="price-pair">
            <div class="input-prefix">
              <span>$</span>
              <label for="min_price" class="sr-only">Minimum price</label>
              <input class="input price-input" type="number" id="min_price" name="min_price" min="0" step="50" placeholder="Min" inputmode="numeric"
                     value="<?= $minPrice !== null ? e((int)$minPrice) : '' ?>">
            </div>
            <span class="muted">–</span>
            <div class="input-prefix">
              <span>$</span>
              <label for="max_price" class="sr-only">Maximum price</label>
              <input class="input price-input" type="number" id="max_price" name="max_price" min="0" step="50" placeholder="Max" inputmode="numeric"
                     value="<?= $maxPrice !== null ? e((int)$maxPrice) : '' ?>">
            </div>
          </div>
        </div>

        <div class="filter-group">
          <p class="filter-title">Rating</p>
          <div class="chips">
            <?php foreach ($ratingOptions as $value => $label): ?>
              <label class="chip">
                <input type="radio" name="min_rating" value="<?= e($value) ?>" <?= (string)$value === $currentRating ? 'checked' : '' ?>>
                <span><?= $value !== '' ? '★ ' : '' ?><?= e($label) ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <noscript><button type="submit" class="btn btn-block mt-16">Apply filters</button></noscript>
      </div>
    </aside>

    <!-- Results -->
    <section aria-label="Results" style="min-width: 0;">
      <div class="search-bar">
        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="9" cy="9" r="6"/><path d="M13.5 13.5L18 18"/></svg>
        <label for="q" class="sr-only">Search</label>
        <input type="search" id="q" name="search" placeholder="Search titles and descriptions…" value="<?= e($search ?? '') ?>" data-hotkey="/" autocomplete="off">
        <span class="kbd hide-sm" aria-hidden="true">/</span>
        <label for="sort" class="sr-only">Sort by</label>
        <select id="sort" name="sort" class="select">
          <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
          <option value="rating_desc" <?= $sort === 'rating_desc' ? 'selected' : '' ?>>Best rated</option>
          <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price ↑</option>
          <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price ↓</option>
        </select>
      </div>

      <div id="results">
        <div class="results-meta">
          <p class="mb-0">
            <b style="font-family: var(--font-display); font-size: 1.25rem; font-weight: 500;"><?= (int)$total ?></b>
            <span class="muted"><?= (int)$total === 1 ? 'service' : 'services' ?><?= ($totalPages ?? 1) > 1 ? ' · page ' . (int)$page . ' of ' . (int)$totalPages : '' ?></span>
          </p>
          <?php if (!empty($activeChips)): ?>
            <div class="active-chips">
              <?php foreach ($activeChips as $chip): ?>
                <?php /* labels are escaped by the controller */ ?>
                <a class="active-chip" href="<?= e($chip['remove_url']) ?>" @click.prevent="clear('<?= e($chip['key']) ?>')" title="Remove filter">
                  <?= $chip['label'] ?> <i aria-hidden="true">×</i>
                </a>
              <?php endforeach; ?>
              <a href="/services" class="small link" style="align-self: center;">Clear all</a>
            </div>
          <?php endif; ?>
        </div>

        <div class="cards">
          <?php if (!empty($services)): ?>
            <?php foreach ($services as $i => $service): $delay = ($i % 3) * 60; ?>
              <?php include BASE_PATH . '/app/Views/partials/service-card.php'; ?>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty">
              <svg width="64" height="64" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
                <circle cx="28" cy="28" r="16"/><path d="M40 40l12 12"/><path d="M22 26c2-3 6-4 9-2" stroke="#e0532f"/>
              </svg>
              <h3>Nothing matches that — yet.</h3>
              <p class="muted">Try a broader search, or loosen the budget a little.</p>
              <a href="/services" class="btn btn-sm mt-8">Clear filters</a>
            </div>
          <?php endif; ?>
        </div>

        <?php if (($totalPages ?? 1) > 1): ?>
          <nav class="pager" aria-label="Pagination"
               hx-boost="true" hx-target="#results" hx-select="#results" hx-swap="outerHTML show:#directory:top" hx-indicator="#loading-bar">
            <?php if ($page > 1): ?>
              <a href="<?= e($buildPageUrl($page - 1)) ?>" aria-label="Previous page">←</a>
            <?php else: ?>
              <span class="is-disabled">←</span>
            <?php endif; ?>

            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
              <?php if ($p === $page): ?>
                <span class="is-current" aria-current="page"><?= $p ?></span>
              <?php else: ?>
                <a href="<?= e($buildPageUrl($p)) ?>"><?= $p ?></a>
              <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
              <a href="<?= e($buildPageUrl($page + 1)) ?>" aria-label="Next page">→</a>
            <?php else: ?>
              <span class="is-disabled">→</span>
            <?php endif; ?>
          </nav>
        <?php endif; ?>
      </div>
    </section>
  </form>
</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
