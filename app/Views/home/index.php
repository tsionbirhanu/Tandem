<?php
// app/Views/home/index.php
include BASE_PATH . '/app/Views/layouts/header.php';

$heroReview = $latestReviews[0] ?? null;
?>

<main>
  <!-- Hero -->
  <section class="hero">
    <div class="wrap hero-grid">
      <div>

        <h1 class="mt-16">
          Good work,<br>
          done <span class="squiggle"><em>in tandem</em><svg viewBox="0 0 300 20" preserveAspectRatio="none"
              aria-hidden="true">
              <path d="M3 14 C 40 4, 70 18, 110 10 S 180 3, 220 11 S 280 15, 297 6" fill="none" stroke="#1d1b17"
                stroke-width="3" stroke-linecap="round" />
            </svg></span>.
        </h1>
        <p class="lead">
          Find a designer, developer or writer who actually gets the brief.
          Look at what they've made, read what past clients said, then reach out when you're ready.
        </p>

        <form action="/services" method="GET" class="hero-search mt-32" role="search">
          <svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"
            stroke-linecap="round" aria-hidden="true">
            <circle cx="9" cy="9" r="6" />
            <path d="M13.5 13.5L18 18" />
          </svg>
          <label for="hero-q" class="sr-only">Search services</label>
          <input id="hero-q" type="search" name="search" placeholder="Try “logo”, “Next.js” or “landing page”"
            data-hotkey="/" autocomplete="off">
          <span class="kbd hide-sm" aria-hidden="true">/</span>
          <button class="btn btn-ink" type="submit">Search</button>
        </form>

      </div>

      <figure class="hero-photo" aria-label="People working together">
        <span class="tape tape-a" aria-hidden="true"></span>
        <span class="tape tape-b" aria-hidden="true"></span>
        <img src="/assets/images/hero.jpg" alt="Three people laughing around a table with laptops" width="1200"
          height="800" fetchpriority="high">
        <?php if ($heroReview): ?>
          <figcaption class="hero-review">
            <?= stars((float) $heroReview["rating"]) ?>
            <p>“<?= e(mb_strimwidth($heroReview["comment"], 0, 96, "…")) ?>”</p>
            <span class="row small" style="gap: 8px;">
              <?= avatar($heroReview["client_avatar"], $heroReview["client_name"], "xs") ?>
              <span><b><?= e($heroReview["client_name"]) ?></b> <span class="muted">hired
                  <?= e(explode(" ", $heroReview["freelancer_name"])[0]) ?></span></span>
            </span>
          </figcaption>
        <?php endif; ?>
        <?php if ($serviceCount > 0): ?>
          <div class="sticker" aria-hidden="true"><span><b><?= (int) $serviceCount ?></b>services<br>open now</span></div>
        <?php endif; ?>
      </figure>
    </div>
  </section>

  <!-- Category ticker -->
  <?php if (!empty($categories)): ?>
    <div class="ticker" aria-hidden="true">
      <div class="ticker-track">
        <?php for ($loop = 0; $loop < 4; $loop++): ?>
          <?php foreach ($categories as $cat): ?>
            <a href="/services?category=<?= e($cat['slug']) ?>" tabindex="-1"><?= e($cat['name']) ?></a>
          <?php endforeach; ?>
        <?php endfor; ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- How it works -->
  <section class="section" id="how">
    <div class="wrap">
      <div class="section-head">
        <div>
          <!-- <span class="eyebrow">How it works</span> -->
          <h2>No bidding wars. <em>No mystery.</em></h2>
        </div>
      </div>
      <div class="steps reveal">
        <div class="step">
          <span class="step-no">01</span>
          <h3>Browse real offers</h3>
          <p>Each service has a fixed starting price, a description in the freelancer's own words, and reviews from
            people who hired them.</p>
        </div>
        <div class="step">
          <span class="step-no">02</span>
          <h3>Talk it through</h3>
          <p>Send a request with your brief. The freelancer replies, asks questions, and accepts when the scope is clear
            for both of you.</p>
        </div>
        <div class="step">
          <span class="step-no">03</span>
          <h3>Ship it, then say thanks</h3>
          <p>Track the project from your dashboard. When it's done, leave an honest review to help the next person
            decide.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Categories -->
  <?php if (!empty($categories)): ?>
    <section class="section" style="padding-top: 0;">
      <div class="wrap">
        <div class="section-head">
          <div>
            <span class="eyebrow">Start with a craft</span>
            <h2>What do you need made?</h2>
          </div>
        </div>
        <div class="cat-grid">
          <?php foreach ($categories as $i => $cat): ?>
            <a class="cat reveal" data-delay="<?= $i * 70 ?>" href="/services?category=<?= e($cat['slug']) ?>">
              <span class="cat-count"><?= (int) $cat['service_count'] ?>
                <?= (int) $cat['service_count'] === 1 ? 'service' : 'services' ?></span>
              <h3><?= e($cat['name']) ?></h3>
              <span class="cat-arrow" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8"
                  stroke-linecap="round">
                  <path d="M3 8h10M9 4l4 4-4 4" />
                </svg>
              </span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- Featured -->
  <section class="section" style="padding-top: 0;">
    <div class="wrap">
      <div class="section-head">
        <div>
          <!-- <span class="eyebrow">Well reviewed</span> -->
          <h2>People keep coming back to these.</h2>
        </div>
        <a href="/services?sort=rating_desc" class="arrow-link link">
          See everything
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8"
            stroke-linecap="round">
            <path d="M3 8h10M9 4l4 4-4 4" />
          </svg>
        </a>
      </div>

      <?php if (!empty($featuredServices)): ?>
        <div class="cards">
          <?php foreach ($featuredServices as $i => $service):
            $delay = ($i % 3) * 80; ?>
            <?php include BASE_PATH . '/app/Views/partials/service-card.php'; ?>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="empty">
          <h3>Nothing listed yet</h3>
          <p class="muted">Be the first — freelancers can list a service in a couple of minutes.</p>
          <a href="/register" class="btn btn-accent mt-8">List a service</a>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Reviews -->
  <?php if (!empty($latestReviews)): ?>
    <section class="section" style="padding-top: 0;">
      <div class="wrap">
        <div class="section-head">
          <div>
            <span class="eyebrow">In their words</span>
            <h2>Straight from clients.</h2>
          </div>
        </div>
        <div class="quotes">
          <?php foreach ($latestReviews as $i => $rev): ?>
            <blockquote class="quote reveal" data-delay="<?= $i * 90 ?>" style="margin: 0;">
              <span class="quote-mark" aria-hidden="true">“</span>
              <?= stars((float) $rev['rating']) ?>
              <p><?= e($rev['comment']) ?></p>
              <footer>
                <?= avatar($rev['client_avatar'], $rev['client_name'], 'sm') ?>
                <span>
                  <b><?= e($rev['client_name']) ?></b>
                  <span class="muted">hired <a class="link"
                      href="/freelancer/<?= (int) $rev['freelancer_id'] ?>"><?= e($rev['freelancer_name']) ?></a></span>
                </span>
              </footer>
            </blockquote>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- CTA -->
  <section class="section" style="padding-top: 24px;">
    <div class="wrap">
      <div class="cta-band reveal">
        <div>
          <span class="eyebrow" style="color: rgba(255,255,255,.75);">For freelancers</span>
          <h2 class="mt-16">Good at something? <em>Put it on the shelf.</em></h2>
        </div>
        <div>
          <p style="color: rgba(255,255,255,.88);">List what you do, set your price, add a few images of past work.
            Clients come to you with a brief.</p>
          <a href="<?= isLoggedIn() ? '/services/create' : '/register' ?>" class="btn btn-lg mt-8">
            List your first service
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8"
              stroke-linecap="round">
              <path d="M3 8h10M9 4l4 4-4 4" />
            </svg>
          </a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>