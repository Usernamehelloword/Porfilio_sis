<?php

// Smoke test for the SVG image generator.
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../app/Http/Controllers/ImageController.php';

use App\Http\Controllers\ImageController;

$c = new ImageController;

$keys = [
    'hero', 'mat-concrete', 'mat-wood', 'mat-stone', 'mat-glass', 'mat-steel', 'mat-brick', 'mat-marble',
    'drawing-plan', 'drawing-section', 'drawing-elevation', 'drawing-site',
    'p1-ext-1', 'p1-ext-2', 'p1-ext-3', 'p1-int-1', 'p1-int-2', 'p1-int-3',
    'p1-plan', 'p1-section', 'p1-elevation', 'p1-render-1', 'p1-render-2', 'p1-detail-1', 'p1-detail-2',
    'p2-cover', 'p3-cover', 'p4-cover', 'p5-cover', 'p6-cover',
    'journal-light-and-architecture', 'journal-why-concrete-remains-timeless',
    'journal-designing-for-tropical-climates', 'journal-minimalism-in-contemporary-architecture',
    'journal-future-of-sustainable-buildings', 'journal-natural-materials-in-modern-interiors',
    'portrait', 'about-studio',
];

$fail = 0;
foreach ($keys as $k) {
    $svg = $c($k)->getContent();
    $ok = str_starts_with($svg, '<svg') && str_ends_with($svg, '</svg>');
    if (! $ok) {
        $fail++;
    }
    echo str_pad($k, 48), str_pad((string) strlen($svg), 9), $ok ? 'OK' : 'INVALID', PHP_EOL;
}

echo $fail === 0 ? 'ALL IMAGES VALID' : $fail.' IMAGES FAILED', PHP_EOL;