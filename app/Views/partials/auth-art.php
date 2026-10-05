<?php
// app/Views/partials/auth-art.php
// Left-hand illustrated panel for the login / register pages. Expects $artHeading (HTML) and $artQuote.
?>
<div class="auth-art">
  <div style="position: relative; z-index: 1;">
    <span class="eyebrow">tandem</span>
    <h2 class="mt-16"><?= $artHeading ?></h2>
  </div>

  <svg class="auth-art-doodle" viewBox="0 0 400 300" fill="none" stroke="#f4efe5" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <!-- a tandem bicycle, drawn loosely -->
    <circle cx="90" cy="210" r="62"/>
    <circle cx="310" cy="210" r="62"/>
    <circle cx="90" cy="210" r="5" fill="#f4efe5"/>
    <circle cx="310" cy="210" r="5" fill="#f4efe5"/>
    <path d="M90 210 L150 110 L262 112 L310 210"/>
    <path d="M150 110 L180 210 L90 210"/>
    <path d="M180 210 L230 112"/>
    <path d="M230 112 L262 112 L232 210 L180 210"/>
    <path d="M140 92 h26 M222 94 h26"/>
    <path d="M262 112 l12 -30 h18"/>
    <circle cx="205" cy="210" r="12" stroke="#f3cd5b"/>
    <path d="M30 278 C 120 268, 280 286, 380 274" stroke-dasharray="2 10" opacity=".6"/>
  </svg>

  <?php if (!empty($artQuote)): ?>
    <figure class="auth-quote">
      <p>“<?= e($artQuote['comment']) ?>”</p>
      <figcaption class="small" style="opacity: .75;">— <?= e($artQuote['client_name']) ?>, on working with <?= e($artQuote['freelancer_name']) ?></figcaption>
    </figure>
  <?php endif; ?>
</div>
