<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

/**
 * Generates the architectural imagery of the site as deterministic SVG —
 * facade studies, interiors, plans, sections, elevations and material boards.
 * Every image is derived from its key, so URLs are stable and cacheable.
 * Replace with real photography by swapping the <img> sources later.
 */
class ImageController extends Controller
{
    public function __invoke(string $key): Response
    {
        $response = new Response(
            $this->render($key),
            Response::HTTP_OK,
            [
                'Content-Type' => 'image/svg+xml',
                'Cache-Control' => 'public, max-age=604800',
            ]
        );

        return $response;
    }

    private function render(string $key): string
    {
        $n = 1;
        if (preg_match('/^p(\d+)-/', $key, $m)) {
            $n = (int) $m[1];
        }

        $pal = $this->palette($n);
        $seed = crc32($key) ?: 7;

        [$w, $h] = match (true) {
            $key === 'hero' || str_contains($key, 'cover') => [1600, 1000],
            str_starts_with($key, 'mat-') => [800, 1000],
            $key === 'portrait' => [900, 1100],
            str_starts_with($key, 'drawing-')
                || str_contains($key, '-plan') || str_contains($key, '-section')
                || str_contains($key, '-elevation') => [1500, 950],
            str_contains($key, '-render-2') => [1600, 1000],
            str_contains($key, '-ext-2') || str_contains($key, '-ext-3')
                || str_contains($key, '-int-2') || str_contains($key, '-int-3') => [950, 1200],
            default => [1400, 950],
        };

        $body = match (true) {
            $key === 'hero' => $this->facade($w, $h, $seed, $pal, 1),
            str_starts_with($key, 'mat-') => $this->material(substr($key, 4), $w, $h, $seed),
            str_starts_with($key, 'drawing-') => $this->drawing(substr($key, 8), $w, $h, $seed),
            str_starts_with($key, 'journal-') => $this->journal(substr($key, 8), $w, $h, $seed),
            $key === 'portrait' => $this->portrait($w, $h, $seed),
            $key === 'about-studio' => $this->studio($w, $h, $seed),
            str_contains($key, 'cover') => $this->structure($w, $h, $seed, $pal, $n),
            str_contains($key, 'render-1') => $this->renderView($w, $h, $seed, $pal, $n),
            str_contains($key, 'render-2') => $this->aerialView($w, $h, $seed, $pal, $n),
            str_contains($key, 'ext-1') => $this->facade($w, $h, $seed, $pal, 2),
            str_contains($key, 'ext-2') => $this->structure($w, $h, $seed, $pal, $n),
            str_contains($key, 'ext-3') => $this->facade($w, $h, $seed, $pal, 3),
            str_contains($key, 'int-1') => $this->interior($w, $h, $seed, $pal, 1),
            str_contains($key, 'int-2') => $this->stair($w, $h, $seed, $pal),
            str_contains($key, 'int-3') => $this->interior($w, $h, $seed, $pal, 2),
            str_contains($key, 'detail-1') => $this->detail($w, $h, $seed, $pal, 1),
            str_contains($key, 'detail-2') => $this->detail($w, $h, $seed, $pal, 2),
            str_contains($key, '-plan') => $this->plan($w, $h, $seed),
            str_contains($key, '-section') => $this->section($w, $h, $seed),
            str_contains($key, '-elevation') => $this->elevation($w, $h, $seed),
            default => $this->facade($w, $h, $seed, $pal, 1),
        };

        return $this->wrap($w, $h, $body, $this->isDrawing($key));
    }

    private function isDrawing(string $key): bool
    {
        return str_starts_with($key, 'drawing-')
            || str_contains($key, '-plan') || str_contains($key, '-section')
            || str_contains($key, '-elevation');
    }

    // ---------------------------------------------------------------- palettes

    /**
     * Material palettes per project: sky, glow, light block, mid block,
     * dark block, deep shadow, accent, ground.
     *
     * @return array<string, string>
     */
    private function palette(int $n): array
    {
        $sets = [
            1 => ['sky' => '#E9E4DA', 'glow' => '#F5F2EB', 'a' => '#B6B3AB', 'b' => '#8E8C84', 'c' => '#5E5C56', 'deep' => '#33322F', 'accent' => '#C8B9A3', 'ground' => '#D9D5CC'],
            2 => ['sky' => '#E5E7DF', 'glow' => '#F3F4EE', 'a' => '#AEAFA8', 'b' => '#84857E', 'c' => '#585952', 'deep' => '#2E2F2A', 'accent' => '#7A8065', 'ground' => '#CFD2C6'],
            3 => ['sky' => '#F0EDE6', 'glow' => '#FAF8F3', 'a' => '#DCD8CF', 'b' => '#C2BEB4', 'c' => '#98948A', 'deep' => '#3A3833', 'accent' => '#C8B9A3', 'ground' => '#E4E0D8'],
            4 => ['sky' => '#EAE2D6', 'glow' => '#F6F0E6', 'a' => '#C9987E', 'b' => '#B76E55', 'c' => '#8A5140', 'deep' => '#43302A', 'accent' => '#C8B9A3', 'ground' => '#E0D5C4'],
            5 => ['sky' => '#E8EAE0', 'glow' => '#F5F6EF', 'a' => '#B9BBA9', 'b' => '#93957F', 'c' => '#67695A', 'deep' => '#383A30', 'accent' => '#7A8065', 'ground' => '#D4D7C5'],
            6 => ['sky' => '#EDE5D8', 'glow' => '#F8F2E8', 'a' => '#D2BBA0', 'b' => '#AE9179', 'c' => '#7E6653', 'deep' => '#3E342B', 'accent' => '#B76E55', 'ground' => '#E4DACA'],
        ];

        return $sets[$n] ?? $sets[1];
    }

    // ---------------------------------------------------------------- helpers

    /**
     * Deterministic pseudo-random generator (linear congruential).
     */
    private function rng(int $seed): \Closure
    {
        $s = $seed & 0x7fffffff;

        return function () use (&$s): float {
            $s = ($s * 1103515245 + 12345) & 0x7fffffff;

            return $s / 0x7fffffff;
        };
    }

    private function wrap(int $w, int $h, string $body, bool $drawing): string
    {
        $open = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$w.'" height="'.$h
            .'" viewBox="0 0 '.$w.' '.$h.'" preserveAspectRatio="xMidYMid slice" role="img">';

        if ($drawing) {
            return $open.'<rect width="'.$w.'" height="'.$h.'" fill="#FFFFFF"/>'.$body.'</svg>';
        }

        return $open
            .'<defs>'
            .'<filter id="grain" x="0" y="0" width="100%" height="100%">'
            .'<feTurbulence type="fractalNoise" baseFrequency="0.8" numOctaves="2" stitchTiles="stitch" result="n"/>'
            .'<feColorMatrix in="n" type="matrix" values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 0.06 0"/>'
            .'</filter>'
            .'<radialGradient id="vig" cx="50%" cy="42%" r="80%">'
            .'<stop offset="55%" stop-color="#000000" stop-opacity="0"/>'
            .'<stop offset="100%" stop-color="#000000" stop-opacity="0.18"/>'
            .'</radialGradient>'
            .'</defs>'
            .'<rect width="'.$w.'" height="'.$h.'" fill="#F2F1ED"/>'
            .$body
            .'<rect width="'.$w.'" height="'.$h.'" filter="url(#grain)"/>'
            .'<rect width="'.$w.'" height="'.$h.'" fill="url(#vig)"/>'
            .'</svg>';
    }

    private function tree(float $x, float $y, float $s, string $color): string
    {
        return '<g opacity="0.92">'
            .'<line x1="'.$x.'" y1="'.$y.'" x2="'.$x.'" y2="'.($y - $s * 0.9).'" stroke="#4A463E" stroke-width="'.max(2, $s * 0.06).'" stroke-linecap="round"/>'
            .'<circle cx="'.$x.'" cy="'.($y - $s * 1.25).'" r="'.($s * 0.62).'" fill="'.$color.'"/>'
            .'<circle cx="'.($x - $s * 0.34).'" cy="'.($y - $s * 0.98).'" r="'.($s * 0.42).'" fill="'.$color.'"/>'
            .'<circle cx="'.($x + $s * 0.32).'" cy="'.($y - $s * 1.02).'" r="'.($s * 0.45).'" fill="'.$color.'" opacity="0.85"/>'
            .'</g>';
    }

    private function person(float $x, float $y, float $s, string $color = '#2E2C28'): string
    {
        return '<g fill="'.$color.'">'
            .'<circle cx="'.$x.'" cy="'.($y - $s).'" r="'.($s * 0.22).'"/>'
            .'<rect x="'.($x - $s * 0.16).'" y="'.($y - $s * 0.74).'" width="'.($s * 0.32).'" height="'.($s * 0.74).'" rx="'.($s * 0.14).'"/>'
            .'</g>';
    }

    private function label(float $x, float $y, string $text, int $size = 13, string $color = '#222222'): string
    {
        return '<text x="'.$x.'" y="'.$y.'" font-family="Inter, \'Helvetica Neue\', Arial, sans-serif" '
            .'font-size="'.$size.'" letter-spacing="'.($size * 0.14).'" fill="'.$color.'">'.htmlspecialchars($text).'</text>';
    }

    private function ground(float $y, int $w, int $h, array $pal): string
    {
        return '<rect x="0" y="'.$y.'" width="'.$w.'" height="'.($h - $y).'" fill="'.$pal['ground'].'"/>'
            .'<rect x="0" y="'.$y.'" width="'.$w.'" height="3" fill="'.$pal['deep'].'" opacity="0.55"/>';
    }

    // ------------------------------------------------------- photographic set

    /**
     * Facade study: finned concrete elevation against a pale sky.
     */
    private function facade(int $w, int $h, int $seed, array $pal, int $variant): string
    {
        $r = $this->rng($seed);
        $gy = $h * 0.66;

        $out = '<defs><linearGradient id="sky" x1="0" y1="0" x2="0" y2="1">'
            .'<stop offset="0%" stop-color="'.$pal['glow'].'"/>'
            .'<stop offset="100%" stop-color="'.$pal['sky'].'"/></linearGradient>'
            .'<linearGradient id="face" x1="0" y1="0" x2="1" y2="0">'
            .'<stop offset="0%" stop-color="'.$pal['a'].'"/>'
            .'<stop offset="100%" stop-color="'.$pal['b'].'"/></linearGradient></defs>'
            .'<rect width="'.$w.'" height="'.$h.'" fill="url(#sky)"/>'
            .'<circle cx="'.round($w * (0.18 + $r() * 0.55)).'" cy="'.round($h * 0.14).'" r="'.round($h * 0.15).'" fill="#FFFFFF" opacity="0.6"/>';

        // distant town silhouette
        $x = 0;
        while ($x < $w) {
            $bw = $w * (0.05 + $r() * 0.09);
            $bh = $h * (0.06 + $r() * 0.18);
            $out .= '<rect x="'.round($x).'" y="'.round($gy - $bh).'" width="'.round($bw).'" height="'.round($bh).'" fill="'.$pal['a'].'" opacity="0.45"/>';
            $x += $bw + $w * 0.012;
        }

        $out .= $this->ground($gy, $w, $h, $pal);

        // main volume
        $bx = $w * 0.2;
        $bw = $w * 0.6;
        $by = $h * 0.18;
        $bh = $gy - $by;

        $out .= '<polygon points="'.round($bx + 4).','.round($gy).' '.round($bx + $bw).','.round($gy).' '
            .round($bx + $bw + $bw * 0.22).','.round($h).' '.round($bx + $bw * 0.35).','.round($h).'" '
            .'fill="'.$pal['deep'].'" opacity="0.16"/>'
            .'<rect x="'.round($bx).'" y="'.round($by).'" width="'.round($bw).'" height="'.round($bh).'" fill="url(#face)"/>';

        // fin rhythm
        if ($variant === 3) {
            $step = $bw / 9;
            for ($i = 0; $i < 10; $i++) {
                $fx = $bx + $i * $step - $bw * 0.06;
                $out .= '<polygon points="'.round($fx).','.round($gy).' '.round($fx + $step * 0.5).','.round($by)
                    .' '.round($fx + $step * 0.78).','.round($by).' '.round($fx + $step * 0.28).','.round($gy).'" '
                    .'fill="'.$pal['glow'].'" opacity="0.5"/>';
            }
        } else {
            $count = $variant === 2 ? 7 : 11;
            $step = $bw / $count;
            $fw = $step * 0.42;
            for ($i = 0; $i <= $count; $i++) {
                $fill = $i % 2 === 0 ? $pal['glow'] : $pal['c'];
                if ($variant === 2) {
                    $y = $by + $i * ($bh / $count);
                    $out .= '<rect x="'.round($bx).'" y="'.round($y).'" width="'.round($bw).'" height="'.round($bh / $count * 0.5).'" fill="'.$fill.'" opacity="0.75"/>';
                } else {
                    $out .= '<rect x="'.round($bx + $i * $step).'" y="'.round($by).'" width="'.round($fw).'" height="'.round($bh).'" fill="'.$fill.'" opacity="0.8"/>';
                }
            }
        }

        // dark flank + roof slab
        $out .= '<rect x="'.round($bx + $bw * 0.86).'" y="'.round($by).'" width="'.round($bw * 0.14).'" height="'.round($bh).'" fill="'.$pal['c'].'" opacity="0.85"/>'
            .'<rect x="'.round($bx - $w * 0.012).'" y="'.round($by - $h * 0.022).'" width="'.round($bw + $w * 0.024).'" height="'.round($h * 0.022).'" fill="'.$pal['deep'].'"/>'
            .$this->tree($w * 0.1, $gy + $h * 0.1, $h * 0.16, $pal['accent'])
            .$this->tree($w * 0.9, $gy + $h * 0.12, $h * 0.2, $pal['accent'])
            .$this->person($bx + $bw * 0.12, $gy + $h * 0.06, $h * 0.05);

        return $out;
    }

    /**
     * Interior: one-point perspective with a curated window and light shaft.
     */
    private function interior(int $w, int $h, int $seed, array $pal, int $variant): string
    {
        $out = '<defs><linearGradient id="win" x1="0" y1="0" x2="0" y2="1">'
            .'<stop offset="0%" stop-color="'.$pal['glow'].'"/>'
            .'<stop offset="100%" stop-color="'.$pal['accent'].'"/></linearGradient></defs>'
            .'<rect width="'.$w.'" height="'.$h.'" fill="'.$pal['glow'].'"/>'
            .'<polygon points="0,0 '.round($w * 0.24).','.round($h * 0.22).' '.round($w * 0.24).','.round($h * 0.78).' 0,'.$h.'" fill="'.$pal['a'].'" opacity="0.6"/>'
            .'<polygon points="'.$w.',0 '.round($w * 0.76).','.round($h * 0.22).' '.round($w * 0.76).','.round($h * 0.78).' '.$w.','.$h.'" fill="'.$pal['b'].'" opacity="0.55"/>'
            .'<rect y="'.round($h * 0.78).'" width="'.$w.'" height="'.round($h * 0.22).'" fill="'.$pal['ground'].'"/>'
            .'<rect y="'.round($h * 0.78).'" width="'.$w.'" height="3" fill="'.$pal['c'].'" opacity="0.5"/>';

        // window on back wall
        $wx = $w * 0.34;
        $wy = $h * 0.24;
        $ww = $variant === 2 ? $w * 0.5 : $w * 0.32;
        $wh = $variant === 2 ? $h * 0.3 : $h * 0.42;

        $out .= '<rect x="'.round($wx).'" y="'.round($wy).'" width="'.round($ww).'" height="'.round($wh).'" fill="url(#win)" stroke="'.$pal['c'].'" stroke-width="4"/>'
            .'<rect x="'.round($wx).'" y="'.round($wy).'" width="'.round($ww).'" height="4" fill="'.$pal['c'].'"/>'
            .'<line x1="'.round($wx + $ww / 2).'" y1="'.round($wy).'" x2="'.round($wx + $ww / 2).'" y2="'.round($wy + $wh).'" stroke="'.$pal['c'].'" stroke-width="3"/>'
            .'<circle cx="'.round($wx + $ww * 0.3).'" cy="'.round($wy + $wh * 0.32).'" r="'.round($wh * 0.12).'" fill="#FFFFFF" opacity="0.75"/>'
            .'<polygon points="'.round($wx).','.round($wy + $wh).' '.round($wx + $ww).','.round($wy + $wh).' '
            .round($wx + $ww * 1.5).','.($h - 4).' '.round($wx - $ww * 0.45).','.($h - 4).'" fill="#FFFFFF" opacity="0.28"/>';

        if ($variant === 2) {
            $out .= '<rect x="'.round($w * 0.2).'" y="'.round($h * 0.62).'" width="'.round($w * 0.42).'" height="'.round($h * 0.17).'" rx="8" fill="'.$pal['a'].'"/>'
                .'<rect x="'.round($w * 0.2).'" y="'.round($h * 0.58).'" width="'.round($w * 0.42).'" height="'.round($h * 0.06).'" rx="8" fill="'.$pal['glow'].'" stroke="'.$pal['c'].'" stroke-width="2"/>'
                .'<rect x="'.round($w * 0.68).'" y="0" width="'.round($w * 0.08).'" height="'.$h.'" fill="'.$pal['accent'].'" opacity="0.35"/>';
        } else {
            $out .= '<rect x="'.round($w * 0.58).'" y="'.round($h * 0.66).'" width="'.round($w * 0.24).'" height="'.round($h * 0.11).'" rx="10" fill="'.$pal['b'].'"/>'
                .'<rect x="'.round($w * 0.58).'" y="'.round($h * 0.63).'" width="'.round($w * 0.06).'" height="'.round($h * 0.05).'" rx="8" fill="'.$pal['c'].'"/>'
                .'<rect x="'.round($w * 0.3).'" y="'.round($h * 0.72).'" width="'.round($w * 0.14).'" height="'.round($h * 0.035).'" rx="6" fill="'.$pal['c'].'" opacity="0.8"/>'
                .'<line x1="'.round($w * 0.37).'" y1="0" x2="'.round($w * 0.37).'" y2="'.round($h * 0.3).'" stroke="'.$pal['c'].'" stroke-width="3"/>'
                .'<circle cx="'.round($w * 0.37).'" cy="'.round($h * 0.32).'" r="'.round($h * 0.035).'" fill="'.$pal['deep'].'"/>'
                .$this->tree($w * 0.14, $h * 0.8, $h * 0.14, $pal['accent'])
                .$this->person($w * 0.47, $h * 0.78, $h * 0.05);
        }

        $out .= '<polygon points="0,0 '.$w.',0 '.round($w * 0.76).','.round($h * 0.2).' '.round($w * 0.24).','.round($h * 0.2).'" fill="'.$pal['c'].'" opacity="0.18"/>';

        return $out;
    }

    /**
     * Vertical stair study — steps climbing into light.
     */
    private function stair(int $w, int $h, int $seed, array $pal): string
    {
        $out = '<defs><linearGradient id="air" x1="0" y1="1" x2="0" y2="0">'
            .'<stop offset="0%" stop-color="'.$pal['c'].'"/>'
            .'<stop offset="100%" stop-color="'.$pal['glow'].'"/></linearGradient></defs>'
            .'<rect width="'.$w.'" height="'.$h.'" fill="url(#air)"/>';

        $steps = 12;
        $x0 = $w * 0.12;
        $y0 = $h * 0.82;
        $sw = $w * 0.072;
        $sh = $h * 0.062;

        for ($i = 0; $i < $steps; $i++) {
            $shade = round(0.35 + ($i / $steps) * 0.5, 2);
            $out .= '<rect x="'.round($x0 + $i * $sw).'" y="'.round($y0 - $i * $sh).'" width="'.round($sw).'" height="'.round($sh).'" fill="'.$pal['a'].'" opacity="'.$shade.'" stroke="'.$pal['deep'].'" stroke-width="1.5"/>';
        }

        $out .= '<line x1="'.round($x0 + $sw * 0.2).'" y1="'.round($y0 - $h * 0.075).'" x2="'.round($x0 + $steps * $sw * 0.86).'" y2="'.round($y0 - $h * 0.075 - $steps * $sh * 0.86).'" '
            .'stroke="'.$pal['deep'].'" stroke-width="7" stroke-linecap="round"/>'
            .'<line x1="'.round($x0 + $sw).'" y1="'.round($y0 - $h * 0.2).'" x2="'.round($x0 + $sw * 2.2).'" y2="0" stroke="#FFFFFF" stroke-width="'.round($w * 0.1).'" opacity="0.22"/>'
            .$this->person($x0 - $w * 0.01, $y0 + $h * 0.08, $h * 0.06);

        return $out;
    }

    /**
     * Hero composition per project — a small archipelago of building types.
     */
    private function structure(int $w, int $h, int $seed, array $pal, int $variant): string
    {
        $r = $this->rng($seed);
        $gy = $h * 0.68;

        $out = '<defs><linearGradient id="sky" x1="0" y1="0" x2="0" y2="1">'
            .'<stop offset="0%" stop-color="'.$pal['glow'].'"/>'
            .'<stop offset="100%" stop-color="'.$pal['sky'].'"/></linearGradient></defs>'
            .'<rect width="'.$w.'" height="'.$h.'" fill="url(#sky)"/>'
            .'<circle cx="'.round($w * (0.2 + $r() * 0.6)).'" cy="'.round($h * 0.12).'" r="'.round($h * 0.12).'" fill="#FFFFFF" opacity="0.55"/>'
            .$this->ground($gy, $w, $h, $pal);

        if ($variant === 2) {
            // monolith + reflecting pond
            $bx = $w * 0.28;
            $out .= '<rect x="'.round($bx).'" y="'.round($gy - $h * 0.5).'" width="'.round($w * 0.44).'" height="'.round($h * 0.5).'" fill="'.$pal['b'].'"/>'
                .'<rect x="'.round($bx).'" y="'.round($gy - $h * 0.5).'" width="'.round($w * 0.09).'" height="'.round($h * 0.5).'" fill="'.$pal['c'].'" opacity="0.8"/>'
                .'<rect x="'.round($bx + $w * 0.09).'" y="'.round($gy - $h * 0.38).'" width="'.round($w * 0.26).'" height="5" fill="'.$pal['glow'].'" opacity="0.9"/>'
                .'<rect x="0" y="'.round($gy + $h * 0.08).'" width="'.$w.'" height="'.round($h * 0.13).'" fill="'.$pal['sky'].'"/>'
                .'<rect x="'.round($bx).'" y="'.round($gy + $h * 0.09).'" width="'.round($w * 0.44).'" height="'.round($h * 0.11).'" fill="'.$pal['c'].'" opacity="0.35"/>'
                .$this->tree($w * 0.14, $gy + $h * 0.06, $h * 0.17, $pal['accent'])
                .$this->person($bx + $w * 0.5, $gy + $h * 0.045, $h * 0.048);
        } elseif ($variant === 4) {
            // brick arcade
            $bx = $w * 0.12;
            $bw = $w * 0.76;
            $out .= '<rect x="'.round($bx).'" y="'.round($gy - $h * 0.34).'" width="'.round($bw).'" height="'.round($h * 0.16).'" fill="'.$pal['b'].'"/>'
                .'<rect x="'.round($bx).'" y="'.round($gy - $h * 0.18).'" width="'.round($bw).'" height="'.round($h * 0.18).'" fill="'.$pal['c'].'"/>';
            $arches = 6;
            $aw = $bw / $arches;
            for ($i = 0; $i < $arches; $i++) {
                $ax = $bx + $i * $aw + $aw * 0.14;
                $aww = $aw * 0.72;
                $ay = $gy - $h * 0.14;
                $out .= '<path d="M '.round($ax).' '.round($ay + $h * 0.14).' L '.round($ax).' '.round($ay)
                    .' Q '.round($ax + $aww / 2).' '.round($ay - $h * 0.09).' '.round($ax + $aww).' '.round($ay)
                    .' L '.round($ax + $aww).' '.round($ay + $h * 0.14).' Z" fill="'.$pal['glow'].'" opacity="0.92"/>';
            }
            $out .= '<rect x="'.round($bx - $w * 0.02).'" y="'.round($gy - $h * 0.2).'" width="'.round($bw + $w * 0.04).'" height="8" fill="'.$pal['deep'].'" opacity="0.7"/>'
                .'<rect x="'.round($bx - $w * 0.02).'" y="'.round($gy - $h * 0.37).'" width="'.round($bw + $w * 0.04).'" height="10" fill="'.$pal['deep'].'" opacity="0.7"/>'
                .$this->tree($w * 0.06, $gy + $h * 0.08, $h * 0.15, $pal['accent'])
                .$this->person($bx + $bw * 0.5, $gy + $h * 0.05, $h * 0.05);
        }

        if ($variant === 5) {
            // stepped planted terraces
            for ($i = 0; $i < 3; $i++) {
                $tx = $w * (0.14 + $i * 0.22);
                $tw = $w * 0.3;
                $ty = $gy - $h * (0.42 - $i * 0.14);
                $out .= '<rect x="'.round($tx).'" y="'.round($ty).'" width="'.round($tw).'" height="'.round($h * 0.13).'" fill="'.$pal['b'].'"/>'
                    .'<rect x="'.round($tx).'" y="'.round($ty - $h * 0.02).'" width="'.round($tw).'" height="'.round($h * 0.02).'" fill="'.$pal['accent'].'"/>'
                    .'<rect x="'.round($tx + $tw * 0.15).'" y="'.round($ty + $h * 0.03).'" width="'.round($tw * 0.2).'" height="'.round($h * 0.07).'" fill="'.$pal['glow'].'" opacity="0.85"/>';
            }
            $out .= $this->tree($w * 0.1, $gy + $h * 0.1, $h * 0.16, $pal['accent'])
                .$this->tree($w * 0.9, $gy + $h * 0.12, $h * 0.18, $pal['accent'])
                .$this->person($w * 0.52, $gy + $h * 0.07, $h * 0.05);
        } elseif ($variant === 6) {
            // three pavilions on plinths
            for ($i = 0; $i < 3; $i++) {
                $px = $w * (0.08 + $i * 0.3);
                $pw = $w * 0.22;
                $py = $gy - $h * (0.24 - $i * 0.02);
                $out .= '<rect x="'.round($px).'" y="'.round($py + $h * 0.09).'" width="'.round($pw).'" height="8" fill="'.$pal['deep'].'" opacity="0.7"/>'
                    .'<rect x="'.round($px).'" y="'.round($py).'" width="'.round($pw).'" height="'.round($h * 0.09).'" fill="'.$pal['a'].'" stroke="'.$pal['deep'].'" stroke-width="2"/>'
                    .'<rect x="'.round($px - $w * 0.012).'" y="'.round($py - $h * 0.025).'" width="'.round($pw + $w * 0.024).'" height="7" fill="'.$pal['c'].'"/>'
                    .'<line x1="'.round($px + $pw * 0.3).'" y1="'.round($py + $h * 0.09).'" x2="'.round($px + $pw * 0.3).'" y2="'.round($py + $h * 0.13).'" stroke="'.$pal['deep'].'" stroke-width="5"/>';
            }
            $out .= $this->tree($w * 0.05, $gy + $h * 0.12, $h * 0.2, $pal['accent'])
                .$this->tree($w * 0.95, $gy + $h * 0.1, $h * 0.22, $pal['accent'])
                .$this->person($w * 0.47, $gy + $h * 0.08, $h * 0.05);
        } elseif ($variant !== 2 && $variant !== 4) {
            // stacked shifted volumes (default / variants 1 & 3)
            $bx = $w * 0.22;
            $out .= '<rect x="'.round($bx).'" y="'.round($gy - $h * 0.26).'" width="'.round($w * 0.52).'" height="'.round($h * 0.26).'" fill="'.$pal['b'].'"/>'
                .'<rect x="'.round($bx + $w * 0.1).'" y="'.round($gy - $h * 0.5).'" width="'.round($w * 0.42).'" height="'.round($h * 0.25).'" fill="'.$pal['a'].'"/>'
                .'<rect x="'.round($bx + $w * 0.1).'" y="'.round($gy - $h * 0.5).'" width="'.round($w * 0.08).'" height="'.round($h * 0.25).'" fill="'.$pal['c'].'" opacity="0.75"/>'
                .'<rect x="'.round($bx + $w * 0.22).'" y="'.round($gy - $h * 0.4).'" width="'.round($w * 0.2).'" height="'.round($h * 0.12).'" fill="'.$pal['glow'].'" opacity="0.9"/>'
                .'<rect x="'.round($bx + $w * 0.04).'" y="'.round($gy - $h * 0.18).'" width="'.round($w * 0.09).'" height="'.round($h * 0.18).'" fill="'.$pal['deep'].'" opacity="0.8"/>'
                .'<rect x="'.round($bx - $w * 0.02).'" y="'.round($gy - $h * 0.52).'" width="'.round($w * 0.6).'" height="8" fill="'.$pal['deep'].'"/>'
                .'<rect x="'.round($bx - $w * 0.04).'" y="'.round($gy - $h * 0.28).'" width="'.round($w * 0.66).'" height="8" fill="'.$pal['deep'].'" opacity="0.9"/>'
                .$this->tree($w * 0.1, $gy + $h * 0.1, $h * 0.16, $pal['accent'])
                .$this->person($bx + $w * 0.28, $gy + $h * 0.05, $h * 0.05);
        }

        return $out;
    }

    /**
     * Dusk render view — warm sky, lit interiors, long shadows.
     */
    private function renderView(int $w, int $h, int $seed, array $pal, int $variant): string
    {
        $gy = $h * 0.7;

        $out = '<defs><linearGradient id="dusk" x1="0" y1="0" x2="0" y2="1">'
            .'<stop offset="0%" stop-color="'.$pal['glow'].'"/>'
            .'<stop offset="70%" stop-color="'.$pal['accent'].'"/>'
            .'<stop offset="100%" stop-color="'.$pal['b'].'"/></linearGradient></defs>'
            .'<rect width="'.$w.'" height="'.$h.'" fill="url(#dusk)"/>'
            .'<circle cx="'.round($w * 0.76).'" cy="'.round($h * 0.3).'" r="'.round($h * 0.09).'" fill="#FFFFFF" opacity="0.8"/>'
            .$this->ground($gy, $w, $h, $pal)
            .$this->structure($w, $h * 0.86, $seed + 11, $pal, $variant);

        // lit windows
        $r = $this->rng($seed);
        for ($i = 0; $i < 7; $i++) {
            $lx = $w * (0.26 + $r() * 0.44);
            $ly = $h * (0.4 + $r() * 0.24);
            $out .= '<rect x="'.round($lx).'" y="'.round($ly).'" width="'.round($w * 0.035).'" height="'.round($h * 0.045).'" fill="'.$pal['glow'].'" opacity="0.95"/>';
        }

        return $out;
    }

    /**
     * Aerial render — roofs, gardens and soft diagonal shadows.
     */
    private function aerialView(int $w, int $h, int $seed, array $pal, int $variant): string
    {
        $r = $this->rng($seed);

        $out = '<rect width="'.$w.'" height="'.$h.'" fill="'.$pal['ground'].'"/>';

        // field parcels
        for ($i = 0; $i < 9; $i++) {
            $out .= '<rect x="0" y="'.round($h * $i / 9).'" width="'.$w.'" height="'.round($h / 9).'" fill="'.$pal['accent'].'" opacity="'.round(0.1 + $r() * 0.16, 2).'"/>';
        }

        $dx = $w * 0.05; // shadow offset
        // roofs
        $roofs = $variant === 6 ? [[0.12, 0.2, 0.2, 0.12], [0.4, 0.24, 0.18, 0.12], [0.66, 0.2, 0.2, 0.12]] : [[0.22, 0.24, 0.34, 0.22], [0.32, 0.5, 0.28, 0.18]];
        foreach ($roofs as $i => $ro) {
            $rx = $w * $ro[0];
            $ry = $h * $ro[1];
            $rw = $w * $ro[2];
            $rh = $h * $ro[3];
            $out .= '<polygon points="'.round($rx + $dx).','.round($ry + $dx * 0.6).' '.round($rx + $rw + $dx).','.round($ry + $dx * 0.6).' '
                .round($rx + $rw + $dx).','.round($ry + $rh + $dx * 0.6).' '.round($rx + $dx).','.round($ry + $rh + $dx * 0.6).'" fill="'.$pal['deep'].'" opacity="0.18"/>'
                .'<rect x="'.round($rx).'" y="'.round($ry).'" width="'.round($rw).'" height="'.round($rh).'" fill="'.$pal['b'].'" stroke="'.$pal['c'].'" stroke-width="3"/>'
                .'<rect x="'.round($rx).'" y="'.round($ry).'" width="'.round($rw * 0.4).'" height="'.round($rh).'" fill="'.$pal['a'].'" opacity="0.7"/>';
        }

        // pool / pond
        $out .= '<rect x="'.round($w * 0.62).'" y="'.round($h * 0.58).'" width="'.round($w * 0.2).'" height="'.round($h * 0.14).'" rx="6" fill="'.$pal['sky'].'" stroke="'.$pal['c'].'" stroke-width="2"/>';

        // trees from above
        for ($i = 0; $i < 10; $i++) {
            $tx = $w * (0.05 + $r() * 0.9);
            $ty = $h * (0.05 + $r() * 0.9);
            $ts = $w * (0.015 + $r() * 0.02);
            $out .= '<circle cx="'.round($tx + $ts * 0.4).'" cy="'.round($ty + $ts * 0.4).'" r="'.round($ts).'" fill="'.$pal['deep'].'" opacity="0.15"/>'
                .'<circle cx="'.round($tx).'" cy="'.round($ty).'" r="'.round($ts).'" fill="'.$pal['accent'].'"/>';
        }

        return $out;
    }

    // ------------------------------------------------------- technical set

    private function drawing(string $type, int $w, int $h, int $seed): string
    {
        return match ($type) {
            'section' => $this->section($w, $h, $seed),
            'elevation' => $this->elevation($w, $h, $seed),
            'site' => $this->sitePlan($w, $h, $seed),
            default => $this->plan($w, $h, $seed),
        };
    }

    private const INK = '#222222';

    /**
     * Ground floor plan with poché walls, door swings and furniture.
     */
    private function plan(int $w, int $h, int $seed): string
    {
        $ink = self::INK;
        $m = $w * 0.07;
        $top = $h * 0.14;
        $bot = $h * 0.86;
        $wt = 10;
        $px = $m;
        $pw = $w - 2 * $m;
        $ph = $bot - $top;

        $out = $this->label($m, $top - 14, 'GROUND FLOOR PLAN — 1:100', 15)
            .'<rect x="'.round($px).'" y="'.round($top).'" width="'.round($pw).'" height="'.$wt.'" fill="'.$ink.'"/>'
            .'<rect x="'.round($px).'" y="'.round($bot - $wt).'" width="'.round($pw).'" height="'.$wt.'" fill="'.$ink.'"/>'
            .'<rect x="'.round($px).'" y="'.round($top).'" width="'.$wt.'" height="'.round($ph).'" fill="'.$ink.'"/>'
            .'<rect x="'.round($px + $pw - $wt).'" y="'.round($top).'" width="'.$wt.'" height="'.round($ph).'" fill="'.$ink.'"/>';

        $v1 = $px + $pw * 0.42;
        $v2 = $px + $pw * 0.68;
        $hm = $top + $ph * 0.55;

        $out .= '<rect x="'.round($v1).'" y="'.round($top).'" width="'.$wt.'" height="'.round($ph * 0.55).'" fill="'.$ink.'"/>'
            .'<rect x="'.round($v2).'" y="'.round($hm).'" width="'.$wt.'" height="'.round($ph * 0.45).'" fill="'.$ink.'"/>'
            .'<rect x="'.round($v2).'" y="'.round($hm).'" width="'.round($px + $pw - $v2).'" height="'.$wt.'" fill="'.$ink.'"/>'
            .'<rect x="'.round($px + $pw * 0.18).'" y="'.round($bot - $wt).'" width="'.round($pw * 0.16).'" height="'.$wt.'" fill="#FFFFFF"/>'
            .'<path d="M '.round($px + $pw * 0.18).' '.round($bot - $wt).' A '.round($pw * 0.16).' '.round($pw * 0.16).' 0 0 1 '.round($px + $pw * 0.34).' '.round($bot - $wt).'" fill="none" stroke="'.$ink.'" stroke-width="1.5" stroke-dasharray="4 4"/>'
            .'<rect x="'.round($px + $pw * 0.46).'" y="'.round($top + $ph * 0.08).'" width="'.round($pw * 0.13).'" height="'.round($ph * 0.3).'" fill="none" stroke="'.$ink.'" stroke-width="1.5"/>';

        for ($i = 1; $i < 8; $i++) {
            $out .= '<line x1="'.round($px + $pw * 0.46).'" y1="'.round($top + $ph * 0.08 + $ph * 0.3 * $i / 8).'" x2="'.round($px + $pw * 0.59).'" y2="'.round($top + $ph * 0.08 + $ph * 0.3 * $i / 8).'" stroke="'.$ink.'" stroke-width="1.2"/>';
        }

        $out .= '<line x1="'.round($px + $pw * 0.46).'" y1="'.round($top + $ph * 0.08).'" x2="'.round($px + $pw * 0.59).'" y2="'.round($top + $ph * 0.38).'" stroke="'.$ink.'" stroke-width="2" stroke-dasharray="6 4"/>';

        $out .= '<rect x="'.round($px + $pw * 0.08).'" y="'.round($top + $ph * 0.12).'" width="'.round($pw * 0.1).'" height="'.round($ph * 0.22).'" fill="none" stroke="'.$ink.'" stroke-width="1.5"/>'
            .'<rect x="'.round($px + $pw * 0.055).'" y="'.round($top + $ph * 0.4).'" width="'.round($pw * 0.15).'" height="'.round($ph * 0.08).'" fill="none" stroke="'.$ink.'" stroke-width="1.5"/>'
            .'<circle cx="'.round($px + $pw * 0.27).'" cy="'.round($top + $ph * 0.25).'" r="'.round($pw * 0.035).'" fill="none" stroke="'.$ink.'" stroke-width="1.5"/>'
            .'<rect x="'.round($v2 + $pw * 0.05).'" y="'.round($hm + $ph * 0.1).'" width="'.round($pw * 0.14).'" height="'.round($ph * 0.22).'" fill="none" stroke="'.$ink.'" stroke-width="1.5"/>'
            .'<rect x="'.round($v2 + $pw * 0.05).'" y="'.round($hm + $ph * 0.1).'" width="'.round($pw * 0.14).'" height="'.round($ph * 0.05).'" fill="'.$ink.'" opacity="0.85"/>'
            .'<rect x="'.round($v1 + $wt).'" y="'.round($hm - $ph * 0.32).'" width="'.round($v2 - $v1 - $wt).'" height="'.round($ph * 0.3).'" fill="none" stroke="'.$ink.'" stroke-width="1.5" stroke-dasharray="8 5"/>'
            .'<circle cx="'.round(($v1 + $v2) / 2).'" cy="'.round($hm - $ph * 0.17).'" r="'.round($pw * 0.05).'" fill="none" stroke="'.$ink.'" stroke-width="1.5"/>'
            .'<circle cx="'.round(($v1 + $v2) / 2).'" cy="'.round($hm - $ph * 0.17).'" r="3" fill="'.$ink.'"/>'
            .'<line x1="'.round($px).'" y1="'.round($top - 34).'" x2="'.round($px + $pw).'" y2="'.round($top - 34).'" stroke="'.$ink.'" stroke-width="1"/>'
            .'<line x1="'.round($px).'" y1="'.round($top - 42).'" x2="'.round($px).'" y2="'.round($top - 26).'" stroke="'.$ink.'" stroke-width="1"/>'
            .'<line x1="'.round($px + $pw).'" y1="'.round($top - 42).'" x2="'.round($px + $pw).'" y2="'.round($top - 26).'" stroke="'.$ink.'" stroke-width="1"/>'
            .$this->label($px + $pw * 0.09, $top + $ph * 0.52, 'LIVING')
            .$this->label($px + $pw * 0.47, $top + $ph * 0.44, 'COURT', 11)
            .$this->label($v2 + $pw * 0.05, $top + $ph * 0.35, 'BED 01', 11)
            .$this->label($v2 + $pw * 0.05, $hm + $ph * 0.3, 'BED 02', 11)
            .$this->label($px + $pw * 0.46, $top + $ph * 0.08 + 22, 'STAIR', 9)
            .$this->label($px + $pw * 0.46, $bot - 14, 'KITCHEN', 10)
            .'<circle cx="'.round($px + $pw + 40).'" cy="'.round($top + 6).'" r="20" fill="none" stroke="'.$ink.'" stroke-width="1.5"/>'
            .'<line x1="'.round($px + $pw + 40).'" y1="'.round($top + 20).'" x2="'.round($px + $pw + 40).'" y2="'.round($top - 10).'" stroke="'.$ink.'" stroke-width="2"/>'
            .'<polygon points="'.round($px + $pw + 40).','.round($top - 14).' '.round($px + $pw + 35).','.round($top - 2).' '.round($px + $pw + 45).','.round($top - 2).'" fill="'.$ink.'"/>'
            .'<rect x="'.round($px).'" y="'.round($bot + 24).'" width="50" height="5" fill="'.$ink.'"/>'
            .'<rect x="'.round($px + 50).'" y="'.round($bot + 24).'" width="50" height="5" fill="none" stroke="'.$ink.'" stroke-width="1"/>'
            .'<rect x="'.round($px + 100).'" y="'.round($bot + 24).'" width="50" height="5" fill="'.$ink.'"/>'
            .$this->label($px + 160, $bot + 32, '0 — 5 — 10 m', 10);

        return $out;
    }

    /**
     * South elevation — glazed grid on a solid base.
     */
    private function elevation(int $w, int $h, int $seed): string
    {
        $ink = self::INK;
        $m = $w * 0.1;
        $gy = $h * 0.8;

        $out = $this->label($m, $h * 0.12, 'SOUTH ELEVATION — 1:100', 15)
            .'<line x1="0" y1="'.round($gy).'" x2="'.$w.'" y2="'.round($gy).'" stroke="'.$ink.'" stroke-width="4"/>'
            .'<rect x="'.round($m).'" y="'.round($h * 0.2).'" width="'.round($w - 2 * $m).'" height="'.round($gy - $h * 0.2).'" fill="none" stroke="'.$ink.'" stroke-width="3"/>'
            .'<rect x="'.round($m - 14).'" y="'.round($h * 0.17).'" width="'.round($w - 2 * $m + 28).'" height="10" fill="'.$ink.'"/>';

        $cols = 8;
        $cw = ($w - 2 * $m) / $cols;
        $r = $this->rng($seed);
        for ($i = 1; $i < $cols; $i++) {
            $out .= '<line x1="'.round($m + $i * $cw).'" y1="'.round($h * 0.2).'" x2="'.round($m + $i * $cw).'" y2="'.round($gy).'" stroke="'.$ink.'" stroke-width="1.5"/>';
        }
        for ($i = 0; $i < $cols; $i++) {
            if ($r() > 0.45) {
                $out .= '<rect x="'.round($m + $i * $cw + 4).'" y="'.round($h * 0.26).'" width="'.round($cw - 8).'" height="'.round(($gy - $h * 0.2) * 0.42).'" fill="'.$ink.'" opacity="0.14"/>';
            }
            if ($r() > 0.6) {
                $out .= '<rect x="'.round($m + $i * $cw + 4).'" y="'.round($gy - ($gy - $h * 0.2) * 0.3).'" width="'.round($cw - 8).'" height="'.round(($gy - $h * 0.2) * 0.3 - 6).'" fill="'.$ink.'" opacity="0.2"/>';
            }
        }

        $out .= $this->tree($m * 0.55, $gy, $h * 0.17, '#FFFFFF')
            .$this->tree($w - $m * 0.55, $gy, $h * 0.15, '#FFFFFF')
            .$this->person($m + ($w - 2 * $m) * 0.5, $gy, $h * 0.05)
            .$this->label($m, $gy + 40, '0.00', 10)
            .$this->label($m, $h * 0.2 + 30, '+3.50', 10)
            .'<line x1="'.round($m - 40).'" y1="'.round($gy).'" x2="'.round($m - 40).'" y2="'.round($h * 0.2).'" stroke="'.$ink.'" stroke-width="1"/>'
            .'<line x1="'.round($m - 46).'" y1="'.round($gy).'" x2="'.round($m - 34).'" y2="'.round($gy).'" stroke="'.$ink.'" stroke-width="1"/>'
            .'<line x1="'.round($m - 46).'" y1="'.round($h * 0.2).'" x2="'.round($m - 34).'" y2="'.round($h * 0.2).'" stroke="'.$ink.'" stroke-width="1"/>';

        return $out;
    }

    /**
     * Site plan — boundary, road, plots and north point.
     */
    private function sitePlan(int $w, int $h, int $seed): string
    {
        $ink = self::INK;
        $m = $w * 0.06;

        $out = $this->label($m, $h * 0.1, 'SITE PLAN — 1:500', 15)
            .'<rect x="'.round($m).'" y="'.round($h * 0.14).'" width="'.round($w - 2 * $m).'" height="'.round($h * 0.72).'" fill="none" stroke="'.$ink.'" stroke-width="2" stroke-dasharray="12 6"/>'
            // road
            .'<path d="M 0 '.round($h * 0.94).' C '.round($w * 0.3).' '.round($h * 0.9).', '.round($w * 0.55).' '.round($h * 0.98).', '.$w.' '.round($h * 0.88).'" fill="none" stroke="'.$ink.'" stroke-width="2"/>'
            .'<path d="M 0 '.round($h * 0.98).' C '.round($w * 0.3).' '.round($h * 0.94).', '.round($w * 0.55).' '.round($h * 1.02).', '.$w.' '.round($h * 0.93).'" fill="none" stroke="'.$ink.'" stroke-width="2"/>'
            // contours
            .'<path d="M '.round($m).' '.round($h * 0.3).' Q '.round($w * 0.4).' '.round($h * 0.2).', '.round($w - $m).' '.round($h * 0.34).'" fill="none" stroke="'.$ink.'" stroke-width="1" stroke-dasharray="3 5" opacity="0.6"/>'
            .'<path d="M '.round($m).' '.round($h * 0.42).' Q '.round($w * 0.4).' '.round($h * 0.32).', '.round($w - $m).' '.round($h * 0.46).'" fill="none" stroke="'.$ink.'" stroke-width="1" stroke-dasharray="3 5" opacity="0.6"/>'
            // buildings
            .'<rect x="'.round($w * 0.3).'" y="'.round($h * 0.3).'" width="'.round($w * 0.26).'" height="'.round($h * 0.2).'" fill="'.$ink.'" opacity="0.85"/>'
            .'<rect x="'.round($w * 0.62).'" y="'.round($h * 0.36).'" width="'.round($w * 0.14).'" height="'.round($h * 0.14).'" fill="'.$ink.'" opacity="0.5"/>'
            .'<rect x="'.round($w * 0.16).'" y="'.round($h * 0.52).'" width="'.round($w * 0.2).'" height="'.round($h * 0.14).'" fill="'.$ink.'" opacity="0.5"/>'
            .$this->label($w * 0.31, $h * 0.41, 'PROPOSED', 10, '#FFFFFF')
            .$this->label($w * 0.63, $h * 0.44, 'EXISTING', 9)
            .$this->label($w * 0.17, $h * 0.6, 'ANNEX', 9);

        // trees
        $r = $this->rng($seed);
        for ($i = 0; $i < 12; $i++) {
            $tx = $m + 30 + $r() * ($w - 2 * $m - 60);
            $ty = $h * (0.18 + $r() * 0.62);
            $out .= '<circle cx="'.round($tx).'" cy="'.round($ty).'" r="9" fill="none" stroke="'.$ink.'" stroke-width="1.2"/>'
                .'<circle cx="'.round($tx).'" cy="'.round($ty).'" r="2" fill="'.$ink.'"/>';
        }

        $out .= '<circle cx="'.round($w - $m - 50).'" cy="'.round($h * 0.22).'" r="22" fill="none" stroke="'.$ink.'" stroke-width="1.5"/>'
            .'<line x1="'.round($w - $m - 50).'" y1="'.round($h * 0.22 + 16).'" x2="'.round($w - $m - 50).'" y2="'.round($h * 0.22 - 14).'" stroke="'.$ink.'" stroke-width="2"/>'
            .'<polygon points="'.round($w - $m - 50).','.round($h * 0.22 - 18).' '.round($w - $m - 56).','.round($h * 0.22 - 5).' '.round($w - $m - 44).','.round($h * 0.22 - 5).'" fill="'.$ink.'"/>'
            .$this->label($w - $m - 58, $h * 0.22 - 26, 'N', 12);

        return $out;
    }

    /**
     * Material board — procedural texture per material key.
     */
    private function material(string $key, int $w, int $h, int $seed): string
    {
        $r = $this->rng($seed);
        $out = '<defs><linearGradient id="sheen" x1="0" y1="0" x2="1" y2="1">'
            .'<stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.14"/>'
            .'<stop offset="50%" stop-color="#FFFFFF" stop-opacity="0"/>'
            .'<stop offset="100%" stop-color="#111111" stop-opacity="0.14"/></linearGradient></defs>';

        switch ($key) {
            case 'concrete':
                $out .= '<rect width="'.$w.'" height="'.$h.'" fill="#B8B8B2"/>';
                for ($i = 0; $i < 12; $i++) {
                    $ly = $h * $i / 12;
                    $out .= '<line x1="0" y1="'.round($ly).'" x2="'.$w.'" y2="'.round($ly + 6).'" stroke="#9C9C95" stroke-width="2.5" opacity="0.6"/>';
                }
                for ($i = 0; $i < 40; $i++) {
                    $out .= '<circle cx="'.round($r() * $w).'" cy="'.round($r() * $h).'" r="'.round(1 + $r() * 2.5).'" fill="#8E8E86" opacity="0.3"/>';
                }
                for ($i = 0; $i < 6; $i++) {
                    $out .= '<circle cx="'.round($w * (0.15 + $r() * 0.7)).'" cy="'.round($h * (0.1 + $r() * 0.8)).'" r="6" fill="#7A7A72" opacity="0.5"/>';
                }
                break;

            case 'wood':
                $out .= '<rect width="'.$w.'" height="'.$h.'" fill="#A98963"/>';
                for ($i = 0; $i < 30; $i++) {
                    $gx = $w * $i / 30;
                    $out .= '<line x1="'.round($gx).'" y1="0" x2="'.round($gx - 10).'" y2="'.$h.'" stroke="#8A6C48" stroke-width="'.round(2 + $r() * 4).'" opacity="'.round(0.2 + $r() * 0.35, 2).'"/>';
                }
                for ($i = 0; $i < 4; $i++) {
                    $kx = round($w * $r());
                    $ky = round($h * $r());
                    $out .= '<ellipse cx="'.$kx.'" cy="'.$ky.'" rx="8" ry="16" fill="#6E543A" opacity="0.5"/>'
                        .'<ellipse cx="'.$kx.'" cy="'.$ky.'" rx="3" ry="7" fill="#54402C" opacity="0.6"/>';
                }
                break;

            case 'stone':
                $out .= '<rect width="'.$w.'" height="'.$h.'" fill="#C4C0B6"/>';
                $cw = $w / 4;
                $ch = $h / 7;
                for ($row = 0; $row < 7; $row++) {
                    $offset = $row % 2 === 0 ? 0 : $cw / 2;
                    for ($col = -1; $col < 4; $col++) {
                        $sx = $col * $cw + $offset;
                        $sy = $row * $ch;
                        $out .= '<rect x="'.round($sx + 2).'" y="'.round($sy + 2).'" width="'.round($cw - 4).'" height="'.round($ch - 4).'" fill="#B0ABA0" opacity="'.round(0.82 + $r() * 0.18, 2).'" stroke="#9A958A" stroke-width="1.5"/>';
                    }
                }
                break;

            case 'glass':
                $out .= '<defs><linearGradient id="gl" x1="0" y1="0" x2="1" y2="1">'
                    .'<stop offset="0%" stop-color="#EDEFF0"/><stop offset="45%" stop-color="#C9CFD2"/>'
                    .'<stop offset="55%" stop-color="#E5E9EB"/><stop offset="100%" stop-color="#B7BEC2"/></linearGradient></defs>'
                    .'<rect width="'.$w.'" height="'.$h.'" fill="url(#gl)"/>'
                    .'<polygon points="0,'.$h.' '.$w.',0 '.$w.','.round($h * 0.35).' 0,'.$h.'" fill="#FFFFFF" opacity="0.35"/>'
                    .'<polygon points="0,0 '.round($w * 0.4).',0 0,'.round($h * 0.7).'" fill="#FFFFFF" opacity="0.25"/>';
                for ($i = 1; $i < 5; $i++) {
                    $out .= '<line x1="'.round($w * $i / 5).'" y1="0" x2="'.round($w * $i / 5).'" y2="'.$h.'" stroke="#9BA4A9" stroke-width="3" opacity="0.7"/>';
                }
                for ($i = 1; $i < 7; $i++) {
                    $out .= '<line x1="0" y1="'.round($h * $i / 7).'" x2="'.$w.'" y2="'.round($h * $i / 7).'" stroke="#9BA4A9" stroke-width="1.5" opacity="0.5"/>';
                }
                break;

            case 'steel':
                $out .= '<rect width="'.$w.'" height="'.$h.'" fill="#7E848A"/>';
                for ($i = 0; $i < 60; $i++) {
                    $ly = $r() * $h;
                    $out .= '<line x1="0" y1="'.round($ly).'" x2="'.$w.'" y2="'.round($ly - 3).'" stroke="#9AA0A6" stroke-width="1" opacity="'.round(0.1 + $r() * 0.25, 2).'"/>';
                }
                $out .= '<rect x="'.round($w * 0.2).'" y="0" width="14" height="'.$h.'" fill="#5C6167"/>'
                    .'<rect x="'.round($w * 0.2).'" y="0" width="4" height="'.$h.'" fill="#A8AEB4"/>'
                    .'<rect x="'.round($w * 0.62).'" y="0" width="14" height="'.$h.'" fill="#5C6167"/>'
                    .'<rect x="'.round($w * 0.62).'" y="0" width="4" height="'.$h.'" fill="#A8AEB4"/>';
                for ($i = 0; $i < 10; $i++) {
                    $by = $h * $i / 10 + 10;
                    $out .= '<circle cx="'.round($w * 0.2 + 7).'" cy="'.round($by).'" r="2.5" fill="#44484D"/>'
                        .'<circle cx="'.round($w * 0.62 + 7).'" cy="'.round($by).'" r="2.5" fill="#44484D"/>';
                }
                break;

            case 'brick':
                $out .= '<rect width="'.$w.'" height="'.$h.'" fill="#B76E55"/>';
                $bh = $h / 16;
                for ($row = 0; $row < 16; $row++) {
                    $offset = $row % 2 === 0 ? 0 : ($w / 6) / 2;
                    for ($col = -1; $col < 7; $col++) {
                        $bx = $col * ($w / 6) + $offset;
                        $out .= '<rect x="'.round($bx + 1.5).'" y="'.round($row * $bh + 1.5).'" width="'.round($w / 6 - 3).'" height="'.round($bh - 3).'" rx="2" fill="#C9836B" opacity="'.round(0.75 + $r() * 0.35, 2).'" stroke="#9A5844" stroke-width="1"/>';
                    }
                }
                break;

            case 'marble':
                $out .= '<defs><linearGradient id="mb" x1="0" y1="0" x2="1" y2="1">'
                    .'<stop offset="0%" stop-color="#FAF9F7"/><stop offset="100%" stop-color="#EDEAE4"/></linearGradient></defs>'
                    .'<rect width="'.$w.'" height="'.$h.'" fill="url(#mb)"/>';
                for ($i = 0; $i < 9; $i++) {
                    $g = round(0.15 + $r() * 0.3, 2);
                    $out .= '<path d="M '.round($r() * $w).' -20 C '.round($r() * $w).' '.round($h * 0.33).', '.round($r() * $w).' '.round($h * 0.66).', '.round($r() * $w).' '.($h + 20).'" fill="none" stroke="#B4AFA5" stroke-width="'.round(1 + $r() * 3).'" opacity="'.$g.'"/>';
                }
                for ($i = 0; $i < 4; $i++) {
                    $x0 = round($r() * $w);
                    $out .= '<path d="M '.$x0.' 0 C '.round($x0 + 60).' '.round($h * 0.3).', '.round($x0 - 60).' '.round($h * 0.7).', '.round($x0 + 20).' '.$h.'" fill="none" stroke="#CFC9BE" stroke-width="1" opacity="0.6"/>';
                }
                break;

            default:
                $out .= '<rect width="'.$w.'" height="'.$h.'" fill="#B8B8B2"/>';
        }

        $out .= '<rect width="'.$w.'" height="'.$h.'" fill="url(#sheen)"/>';

        return $out;
    }

    /**
     * Journal cover images keyed by article slug.
     */
    private function journal(string $slug, int $w, int $h, int $seed): string
    {
        $ink = '#222222';
        $r = $this->rng($seed);

        $out = '<defs><linearGradient id="jg" x1="0" y1="0" x2="0" y2="1">'
            .'<stop offset="0%" stop-color="#FAF9F6"/><stop offset="100%" stop-color="#E9E9E6"/></linearGradient></defs>'
            .'<rect width="'.$w.'" height="'.$h.'" fill="url(#jg)"/>';

        switch ($slug) {
            case 'light-and-architecture':
                $out .= '<rect width="'.$w.'" height="'.$h.'" fill="#E9E4DA"/>'
                    .'<rect x="'.round($w * 0.55).'" y="0" width="70" height="'.$h.'" fill="#F7F7F5"/>'
                    .'<polygon points="'.round($w * 0.55 + 70).',0 '.$w.',0 '.$w.','.round($h * 0.8).' '.round($w * 0.55 + 70).','.round($h * 0.62).'" fill="#FFFFFF" opacity="0.55"/>'
                    .'<rect x="0" y="'.round($h * 0.8).'" width="'.$w.'" height="'.round($h * 0.2).'" fill="#B8B8B2"/>'
                    .'<polygon points="0,'.round($h * 0.8).' '.$w.',0 '.$w.',0 '.round($w * 0.5).','.round($h * 0.8).'" fill="#F7F7F5" opacity="0.25"/>';
                break;

            case 'why-concrete-remains-timeless':
                $out .= '<rect width="'.$w.'" height="'.$h.'" fill="#B8B8B2"/>';
                for ($i = 0; $i < 10; $i++) {
                    $out .= '<line x1="0" y1="'.round($h * $i / 10).'" x2="'.$w.'" y2="'.round($h * $i / 10 + 8).'" stroke="#8E8E86" stroke-width="3" opacity="0.5"/>';
                }
                for ($i = 0; $i < 8; $i++) {
                    $out .= '<circle cx="'.round($w * (0.12 + $r() * 0.76)).'" cy="'.round($h * (0.1 + $r() * 0.8)).'" r="7" fill="#6E6E66" opacity="0.6"/>';
                }
                break;

            case 'designing-for-tropical-climates':
                $out .= '<rect x="0" y="'.round($h * 0.62).'" width="'.$w.'" height="'.round($h * 0.38).'" fill="#7A8065"/>'
                    .'<rect x="0" y="'.round($h * 0.62).'" width="'.$w.'" height="4" fill="#111111" opacity="0.2"/>';
                for ($i = 0; $i < 7; $i++) {
                    $bx = $w * (0.06 + $i * 0.14);
                    $out .= '<rect x="'.round($bx).'" y="'.round($h * 0.2).'" width="'.round($w * 0.09).'" height="'.round($h * 0.42).'" fill="#C8B9A3" opacity="0.9"/>'
                        .'<rect x="'.round($bx - 6).'" y="'.round($h * 0.15).'" width="'.round($w * 0.09 + 12).'" height="10" fill="#575C49"/>';
                }
                break;

            case 'minimalism-in-contemporary-architecture':
                $out .= '<rect width="'.$w.'" height="'.$h.'" fill="#F7F7F5"/>'
                    .'<circle cx="'.round($w * 0.38).'" cy="'.round($h * 0.4).'" r="'.round($h * 0.18).'" fill="none" stroke="'.$ink.'" stroke-width="2"/>'
                    .'<line x1="0" y1="'.round($h * 0.7).'" x2="'.$w.'" y2="'.round($h * 0.7).'" stroke="'.$ink.'" stroke-width="1.5"/>'
                    .'<rect x="'.round($w * 0.62).'" y="'.round($h * 0.24).'" width="'.round($w * 0.2).'" height="'.round($h * 0.46).'" fill="#E9E9E6" stroke="'.$ink.'" stroke-width="1.5"/>';
                break;

            case 'future-of-sustainable-buildings':
                $out .= '<rect x="0" y="'.round($h * 0.7).'" width="'.$w.'" height="'.round($h * 0.3).'" fill="#C8B9A3"/>'
                    .'<circle cx="'.round($w * 0.7).'" cy="'.round($h * 0.24).'" r="'.round($h * 0.13).'" fill="#F7F7F5" stroke="'.$ink.'" stroke-width="1.5"/>'
                    .'<rect x="'.round($w * 0.14).'" y="'.round($h * 0.34).'" width="'.round($w * 0.4).'" height="'.round($h * 0.36).'" fill="#FFFFFF" stroke="'.$ink.'" stroke-width="2"/>';
                for ($i = 0; $i < 4; $i++) {
                    $out .= '<line x1="'.round($w * 0.14 + $w * 0.1 * $i).'" y1="'.round($h * 0.34).'" x2="'.round($w * 0.14 + $w * 0.1 * $i).'" y2="'.round($h * 0.7).'" stroke="'.$ink.'" stroke-width="1.2"/>';
                }
                $out .= $this->tree($w * 0.85, $h * 0.72, $h * 0.14, '#7A8065');
                break;

            default:
                // natural materials: wood + stone composition
                $out .= '<rect width="'.$w.'" height="'.$h.'" fill="#ECE9E2"/>'
                    .'<rect x="0" y="0" width="'.round($w * 0.55).'" height="'.$h.'" fill="#A98963"/>';
                for ($i = 0; $i < 16; $i++) {
                    $gx = $w * 0.55 * $i / 16;
                    $out .= '<line x1="'.round($gx).'" y1="0" x2="'.round($gx - 8).'" y2="'.$h.'" stroke="#8A6C48" stroke-width="2" opacity="0.5"/>';
                }
                $out .= '<rect x="'.round($w * 0.62).'" y="'.round($h * 0.12).'" width="'.round($w * 0.3).'" height="'.round($h * 0.3).'" fill="#C4C0B6" stroke="#9A958A" stroke-width="1.5"/>'
                    .'<rect x="'.round($w * 0.62).'" y="'.round($h * 0.5).'" width="'.round($w * 0.3).'" height="'.round($h * 0.38).'" fill="#B76E55" opacity="0.9"/>';
                break;
        }

        return $out;
    }

    /**
     * Macro material detail — board marks, seams and accent strips.
     */
    private function detail(int $w, int $h, int $seed, array $pal, int $variant): string
    {
        $r = $this->rng($seed);
        $out = '<rect width="'.$w.'" height="'.$h.'" fill="'.$pal['b'].'"/>';

        if ($variant === 2) {
            $out .= '<rect x="0" y="0" width="'.round($w * 0.62).'" height="'.$h.'" fill="'.$pal['a'].'"/>';
            // vertical wood grain
            for ($i = 0; $i < 26; $i++) {
                $gx = $w * 0.62 + ($i / 26) * $w * 0.38;
                $out .= '<line x1="'.round($gx).'" y1="0" x2="'.round($gx - 6).'" y2="'.$h.'" stroke="'.$pal['c'].'" stroke-width="2" opacity="'.round(0.25 + $r() * 0.4, 2).'"/>';
            }
        } else {
            // horizontal board marks
            for ($i = 0; $i < 14; $i++) {
                $ly = $h * $i / 14;
                $out .= '<line x1="0" y1="'.round($ly).'" x2="'.$w.'" y2="'.round($ly + 4).'" stroke="'.$pal['c'].'" stroke-width="2.5" opacity="0.5"/>';
            }
        }

        // tie holes
        for ($i = 0; $i < 8; $i++) {
            $tx = $w * (0.1 + $r() * 0.8);
            $ty = $h * (0.08 + $r() * 0.84);
            $out .= '<circle cx="'.round($tx).'" cy="'.round($ty).'" r="7" fill="'.$pal['c'].'" opacity="0.7"/>';
        }

        // accent strip + raking light
        $out .= '<rect x="'.round($w * 0.3).'" y="0" width="10" height="'.$h.'" fill="'.$pal['accent'].'" opacity="0.9"/>'
            .'<polygon points="0,0 '.$w.',0 '.$w.','.round($h * 0.2).' 0,'.round($h * 0.5).'" fill="#FFFFFF" opacity="0.12"/>'
            .'<polygon points="0,'.$h.' '.$w.','.$h.' '.$w.','.round($h * 0.86).' 0,'.round($h * 0.6).'" fill="'.$pal['deep'].'" opacity="0.16"/>';

        return $out;
    }

    /**
     * The studio — desk, models and pinned drawings.
     */
    private function studio(int $w, int $h, int $seed): string
    {
        $r = $this->rng($seed);

        $out = '<defs><linearGradient id="win" x1="0" y1="0" x2="0" y2="1">'
            .'<stop offset="0%" stop-color="#FAF8F3"/><stop offset="100%" stop-color="#E9E4DA"/></linearGradient></defs>'
            .'<rect width="'.$w.'" height="'.$h.'" fill="#F0EDE6"/>'
            .'<rect y="'.round($h * 0.8).'" width="'.$w.'" height="'.round($h * 0.2).'" fill="#E4E0D8"/>'
            .'<rect y="'.round($h * 0.8).'" width="'.$w.'" height="3" fill="#8E8C84" opacity="0.5"/>'
            // pinned drawings on the wall
            .'<rect x="'.round($w * 0.06).'" y="'.round($h * 0.1).'" width="'.round($w * 0.2).'" height="'.round($h * 0.3).'" fill="#FFFFFF" stroke="#B8B8B2" stroke-width="3" transform="rotate(-1.2 '.round($w * 0.16).' '.round($h * 0.25).')"/>'
            .'<rect x="'.round($w * 0.29).'" y="'.round($h * 0.12).'" width="'.round($w * 0.17).'" height="'.round($h * 0.26).'" fill="#FFFFFF" stroke="#B8B8B2" stroke-width="3" transform="rotate(1.4 '.round($w * 0.375).' '.round($h * 0.25).')"/>'
            .'<line x1="'.round($w * 0.1).'" y1="'.round($h * 0.22).'" x2="'.round($w * 0.22).'" y2="'.round($h * 0.34).'" stroke="#8E8C84" stroke-width="2"/>'
            .'<line x1="'.round($w * 0.1).'" y1="'.round($h * 0.34).'" x2="'.round($w * 0.22).'" y2="'.round($h * 0.22).'" stroke="#8E8C84" stroke-width="2"/>'
            .'<rect x="'.round($w * 0.33).'" y="'.round($h * 0.2).'" width="'.round($w * 0.09).'" height="'.round($h * 0.1).'" fill="none" stroke="#8E8C84" stroke-width="2"/>'
            // window with light
            .'<rect x="'.round($w * 0.7).'" y="'.round($h * 0.08).'" width="'.round($w * 0.22).'" height="'.round($h * 0.5).'" fill="url(#win)" stroke="#8E8C84" stroke-width="4"/>'
            .'<polygon points="'.round($w * 0.7).','.round($h * 0.58).' '.round($w * 0.92).','.round($h * 0.58).' '.$w.','.round($h * 0.94).' '.round($w * 0.62).','.round($h * 0.94).'" fill="#FFFFFF" opacity="0.3"/>'
            // desk with model blocks
            .'<rect x="'.round($w * 0.12).'" y="'.round($h * 0.62).'" width="'.round($w * 0.5).'" height="12" fill="#C2BEB4"/>'
            .'<rect x="'.round($w * 0.16).'" y="'.round($h * 0.63).'" width="14" height="'.round($h * 0.17).'" fill="#B8B8B2"/>'
            .'<rect x="'.round($w * 0.54).'" y="'.round($h * 0.63).'" width="14" height="'.round($h * 0.17).'" fill="#B8B8B2"/>';

        for ($i = 0; $i < 4; $i++) {
            $mx = $w * (0.2 + $i * 0.09);
            $my = $h * (0.55 + $r() * 0.03);
            $out .= '<rect x="'.round($mx).'" y="'.round($my).'" width="'.round($w * 0.05).'" height="'.round($h * 0.07).'" fill="#8E8C84"/>'
                .'<rect x="'.round($mx).'" y="'.round($my).'" width="'.round($w * 0.02).'" height="'.round($h * 0.07).'" fill="#5E5C56"/>';
        }

        return $out;
    }

    /**
     * Abstract portrait of the principal architect.
     */
    private function portrait(int $w, int $h, int $seed): string
    {
        $cx = $w * 0.5;

        return '<rect width="'.$w.'" height="'.$h.'" fill="#ECE9E2"/>'
            .'<circle cx="'.round($cx).'" cy="'.round($h * 0.42).'" r="'.round($w * 0.34).'" fill="#C8B9A3" opacity="0.5"/>'
            .'<path d="M '.round($cx - $w * 0.24).' '.$h.' L '.round($cx - $w * 0.24).' '.round($h * 0.78)
            .' Q '.round($cx).' '.round($h * 0.6).' '.round($cx + $w * 0.24).' '.round($h * 0.78)
            .' L '.round($cx + $w * 0.24).' '.$h.' Z" fill="#2A2926"/>'
            .'<ellipse cx="'.round($cx).'" cy="'.round($h * 0.44).'" rx="'.round($w * 0.15).'" ry="'.round($h * 0.185).'" fill="#33322F"/>'
            .'<rect x="'.round($cx - $w * 0.075).'" y="'.round($h * 0.575).'" width="'.round($w * 0.15).'" height="'.round($h * 0.05).'" fill="#F7F7F5"/>'
            .'<line x1="0" y1="'.round($h * 0.16).'" x2="'.$w.'" y2="'.round($h * 0.16).'" stroke="#B8B8B2" stroke-width="2" opacity="0.6"/>';
    }

    /**
     * Building section — slabs, columns, ground hatch and figures.
     */
    private function section(int $w, int $h, int $seed): string
    {
        $ink = self::INK;
        $m = $w * 0.08;
        $gy = $h * 0.82;

        return '<defs><pattern id="hatch" width="9" height="9" patternTransform="rotate(45)" patternUnits="userSpaceOnUse">'
            .'<line x1="0" y1="0" x2="0" y2="9" stroke="'.$ink.'" stroke-width="1.2" opacity="0.55"/></pattern></defs>'
            .$this->label($m, $h * 0.09, 'SECTION A–A — 1:100', 15)
            .'<rect x="0" y="'.round($gy).'" width="'.$w.'" height="'.round($h - $gy).'" fill="url(#hatch)"/>'
            .'<rect x="'.round($m).'" y="'.round($gy).'" width="'.round($w - 2 * $m).'" height="5" fill="'.$ink.'"/>'
            .'<rect x="'.round($m).'" y="'.round($h * 0.42).'" width="'.round($w - 2 * $m).'" height="8" fill="'.$ink.'"/>'
            .'<rect x="'.round($m).'" y="'.round($h * 0.2).'" width="'.round($w * 0.7).'" height="8" fill="'.$ink.'"/>'
            .'<rect x="'.round($m).'" y="'.round($h * 0.2).'" width="7" height="'.round($gy - $h * 0.2).'" fill="'.$ink.'"/>'
            .'<rect x="'.round($w * 0.5).'" y="'.round($h * 0.2).'" width="7" height="'.round($gy - $h * 0.2).'" fill="'.$ink.'"/>'
            .'<rect x="'.round($m).'" y="'.round($h * 0.42).'" width="7" height="'.round($gy - $h * 0.42).'" fill="'.$ink.'"/>'
            .'<rect x="'.round($w * 0.5).'" y="'.round($h * 0.42).'" width="7" height="'.round($gy - $h * 0.42).'" fill="'.$ink.'"/>'
            .'<rect x="'.round($m - 4).'" y="'.round($h * 0.17).'" width="10" height="'.round($h * 0.05).'" fill="'.$ink.'"/>'
            .'<rect x="'.round($w * 0.7 - 2).'" y="'.round($h * 0.17).'" width="10" height="'.round($h * 0.05).'" fill="'.$ink.'"/>'
            .'<path d="M '.round($w * 0.56).' '.round($gy - 5).' L '.round($w * 0.66).' '.round($gy - 5)
            .' L '.round($w * 0.66).' '.round($h * 0.42 + 8).' L '.round($w * 0.76).' '.round($h * 0.42 + 8)
            .' L '.round($w * 0.76).' '.round($h * 0.2 + 8).' L '.round($w * 0.86).' '.round($h * 0.2 + 8).'" '
            .'fill="none" stroke="'.$ink.'" stroke-width="2"/>'
            .'<line x1="0" y1="0" x2="'.round($w * 0.36).'" y2="'.round($h * 0.42).'" stroke="'.$ink.'" stroke-width="1" stroke-dasharray="2 6" opacity="0.5"/>'
            .'<line x1="'.round($w * 0.1).'" y1="0" x2="'.round($w * 0.46).'" y2="'.round($h * 0.42).'" stroke="'.$ink.'" stroke-width="1" stroke-dasharray="2 6" opacity="0.5"/>'
            .$this->person($w * 0.22, $gy - 2, $h * 0.045)
            .$this->person($w * 0.32, $h * 0.42, $h * 0.045)
            .$this->person($w * 0.62, $h * 0.2, $h * 0.045)
            .$this->label($w * 0.78, $h * 0.19, '+3.50', 11)
            .$this->label($w * 0.78, $h * 0.41, '+0.00', 11)
            .$this->label($w * 0.8, $gy + 34, '−0.90', 11)
            .'<line x1="'.round($w * 0.7).'" y1="'.round($h * 0.2).'" x2="'.round($w * 0.86).'" y2="'.round($h * 0.2).'" stroke="'.$ink.'" stroke-width="1"/>'
            .'<line x1="'.round($w * 0.7).'" y1="'.round($h * 0.42).'" x2="'.round($w * 0.86).'" y2="'.round($h * 0.42).'" stroke="'.$ink.'" stroke-width="1"/>'
            .'<circle cx="'.round($m + 26).'" cy="'.round($h * 0.13).'" r="16" fill="none" stroke="'.$ink.'" stroke-width="1.5"/>'
            .$this->label($m + 21, $h * 0.135 + 5, 'A', 12);
    }
}