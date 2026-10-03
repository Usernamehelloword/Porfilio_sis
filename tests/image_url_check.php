<?php

// Diagnose image serving: fetch rendered pages, extract <img> URLs,
// request each one, and validate every SVG as strict XML.

$base = 'http://127.0.0.1:8088';

$pages = ['/', '/projects', '/projects/casa-forma', '/about', '/services', '/journal', '/contact'];
$urls = [];

foreach ($pages as $p) {
    $html = file_get_contents($base.$p);
    preg_match_all('/<img[^>]+src="([^"]+)"/', $html, $m);
    foreach ($m[1] as $src) {
        $urls[html_entity_decode($src)] = $p;
    }
    // also collect CSS url() refs if any
    preg_match_all('/url\(([^)]+\.svg)\)/', $html, $m2);
    foreach ($m2[1] as $src) {
        $urls[trim(html_entity_decode($src), '\'"')] = $p;
    }
}

echo count($urls), " unique image URLs found\n";

$bad = 0;
foreach ($urls as $src => $from) {
    $full = str_starts_with($src, 'http') ? $src : $base.$src;

    $ctx = stream_context_create(['http' => ['ignore_errors' => true, 'timeout' => 15]]);
    $raw = @file_get_contents($full, false, $ctx);
    $status = 0;
    foreach ($http_response_header ?? [] as $h) {
        if (preg_match('#HTTP/\S+\s+(\d+)#', $h, $sm)) {
            $status = (int) $sm[1];
        }
    }

    $valid = false;
    $why = '';
    if ($raw === false) {
        $why = 'request failed';
    } else {
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($raw);
        if ($doc === false) {
            $err = libxml_get_errors();
            $why = 'XML ERROR: '.trim($err[0]->message ?? 'unknown');
            libxml_clear_errors();
        } else {
            $valid = true;
        }
    }

    if (! $valid || $status !== 200) {
        $bad++;
        echo "BAD [$status] $why — $full (on $from)\n";
    }
}

echo $bad === 0 ? "ALL IMAGES OK — served and valid XML\n" : "$bad PROBLEM IMAGES\n";
