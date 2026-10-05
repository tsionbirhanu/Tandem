<?php
// app/Helpers/ui.php
// Small presentation helpers shared by the view templates.

if (!function_exists('e')) {
    /** HTML-escape a value for output. */
    function e(mixed $value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('money')) {
    /** "$1,250" — drops the cents when they are zero. */
    function money(mixed $amount): string {
        $amount = (float)$amount;
        $decimals = fmod($amount, 1.0) === 0.0 ? 0 : 2;
        return '$' . number_format($amount, $decimals);
    }
}

if (!function_exists('assetUrl')) {
    /**
     * Normalises a stored image path into a usable URL.
     * Returns null for local files that do not exist, so views can fall back to cover art.
     */
    function assetUrl(?string $path): ?string {
        if (empty($path)) {
            return null;
        }
        if (preg_match('#^https?://#', $path)) {
            return $path;
        }
        $path = '/' . ltrim($path, '/');
        return is_file(BASE_PATH . '/public' . $path) ? $path : null;
    }
}

if (!function_exists('initials')) {
    function initials(string $name): string {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $letters = array_map(fn($p) => mb_substr($p, 0, 1), array_slice($parts, 0, 2));
        return mb_strtoupper(implode('', $letters)) ?: '?';
    }
}

if (!function_exists('avatar')) {
    /** Renders a round avatar image, or an initials disc when there is no photo. */
    function avatar(?string $url, string $name, string $size = 'md'): string {
        $src = assetUrl($url);
        if ($src) {
            return sprintf('<img class="avatar avatar-%s" src="%s" alt="%s" loading="lazy">', e($size), e($src), e($name));
        }
        // Pick a stable tint from the name so the same person always gets the same colour
        $tint = abs(crc32($name)) % 4;
        return sprintf('<span class="avatar avatar-%s avatar-tint-%d" aria-hidden="true">%s</span>', e($size), $tint, e(initials($name)));
    }
}

if (!function_exists('stars')) {
    /** Inline SVG star row for a 0–5 rating (rounded to the nearest half). */
    function stars(float $rating, string $class = ''): string {
        $rounded = round($rating * 2) / 2;
        $html = '<span class="stars ' . e($class) . '" role="img" aria-label="' . e(number_format($rating, 1)) . ' out of 5">';
        for ($i = 1; $i <= 5; $i++) {
            $state = $rounded >= $i ? 'full' : ($rounded >= $i - 0.5 ? 'half' : 'empty');
            $path = 'M10 1.8l2.5 5.3 5.7.7-4.2 3.9 1.1 5.7L10 14.6l-5.1 2.8 1.1-5.7L1.8 7.8l5.7-.7z';
            $html .= '<svg class="star star-' . $state . '" viewBox="0 0 20 20" aria-hidden="true">'
                   . '<path class="star-bg" d="' . $path . '"/>'
                   . ($state !== 'empty' ? '<path class="star-fg" d="' . $path . '"/>' : '')
                   . '</svg>';
        }
        return $html . '</span>';
    }
}

if (!function_exists('timeAgo')) {
    function timeAgo(?string $datetime): string {
        if (!$datetime) {
            return '';
        }
        $seconds = time() - strtotime($datetime);
        if ($seconds < 60)        return 'just now';
        if ($seconds < 3600)      return floor($seconds / 60) . 'm ago';
        if ($seconds < 86400)     return floor($seconds / 3600) . 'h ago';
        if ($seconds < 86400 * 7) return floor($seconds / 86400) . 'd ago';
        return date('M j, Y', strtotime($datetime));
    }
}

if (!function_exists('statusPill')) {
    /** Coloured pill for a project request status. */
    function statusPill(string $status): string {
        $label = ucwords(str_replace('_', ' ', $status));
        return '<span class="pill pill-' . e($status) . '"><i></i>' . e($label) . '</span>';
    }
}

if (!function_exists('coverArt')) {
    /**
     * Generative cover for a service without photos: a patterned panel whose
     * pattern and colour come from the category, with the service number stamped on.
     */
    function coverArt(array $service, string $class = ''): string {
        $slug = $service['category_slug'] ?? $service['category_name'] ?? '';
        $variant = match (true) {
            str_contains($slug, 'web'), str_contains($slug, 'Web')         => 'grid',
            str_contains($slug, 'ui'),  str_contains($slug, 'UI')          => 'dots',
            str_contains($slug, 'brand'), str_contains($slug, 'Brand')     => 'rings',
            str_contains($slug, 'content'), str_contains($slug, 'Content') => 'lines',
            default => ['grid', 'dots', 'rings', 'lines'][abs(crc32($slug)) % 4],
        };
        $number = str_pad((string)(int)($service['id'] ?? 0), 3, '0', STR_PAD_LEFT);
        return '<div class="cover cover-' . $variant . ' ' . e($class) . '" aria-hidden="true">'
             . '<span class="cover-no">№ ' . $number . '</span>'
             . '<span class="cover-cat">' . e($service['category_name'] ?? '') . '</span>'
             . '</div>';
    }
}

if (!function_exists('serviceCover')) {
    /** Photo if the service has a usable one, generative cover art otherwise. */
    function serviceCover(array $service, ?string $imagePath = null, string $class = ''): string {
        $src = assetUrl($imagePath ?? ($service['primary_image'] ?? null));
        if ($src) {
            return '<div class="cover cover-photo ' . e($class) . '"><img src="' . e($src) . '" alt="" loading="lazy"></div>';
        }
        return coverArt($service, $class);
    }
}

if (!function_exists('fieldError')) {
    function fieldError(array $errors, string $field): string {
        return isset($errors[$field]) ? '<p class="field-error">' . e($errors[$field]) . '</p>' : '';
    }
}

if (!function_exists('navActive')) {
    /** Returns ' is-active' when the current path starts with $prefix. */
    function navActive(string $prefix): string {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        if ($prefix === '/') {
            return $path === '/' ? ' is-active' : '';
        }
        return str_starts_with($path, $prefix) ? ' is-active' : '';
    }
}
