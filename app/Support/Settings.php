<?php

namespace App\Support;

use App\Models\Setting as SettingModel;
use Illuminate\Support\Facades\Cache;

/**
 * Central key/value settings store with safe defaults, so the public
 * website works even before anything has been saved from the admin.
 */
final class Settings
{
    public const DEFAULTS = [
        // General
        'site_name' => 'STUDIO·VOLUME',
        'studio_name' => 'Studio Volume',
        'email' => 'studio@studiovolume.com',
        'phone' => '+855 12 345 678',
        'address' => 'Street 2404, Borey Peng Huoth, Phnom Penh, Cambodia',
        'instagram' => 'https://instagram.com',
        'facebook' => '',
        'linkedin' => 'https://linkedin.com',
        'youtube' => '',
        'logo' => '',
        'favicon' => '',

        // Homepage hero
        'hero_title' => 'SHAPING SPACE<br>FOR <em>HUMAN EXPERIENCE.</em>',
        'hero_subtitle' => 'ARCHITECTURE / INTERIOR / DESIGN',
        'hero_description' => 'Architecture that connects people, materials, light, and place.',
        'hero_image' => '',
        'hero_video' => '',
        'hero_button_text' => 'EXPLORE PROJECTS',
        'hero_button_link' => '/projects',

        // About page
        'about_title' => 'DESIGNING SPACES<br>WITH <em>PURPOSE.</em>',
        'about_description' => 'We are an architecture and design studio focused on creating thoughtful spaces that respond to people, context, climate, and material.',
        'about_quote' => '“A building should be a good citizen of its street, a good host to its visitors, and a good shelter from its climate.”',
        'about_body_1' => 'Founded in Phnom Penh in 2014, Studio Volume has grown from a two-person workshop into an eleven-person studio working across Cambodia and the wider region. We deliberately stay small: every project is led by a principal, drawn by hand first, and documented to the last bolt.',
        'about_body_2' => 'Our work spans private houses, interiors, cultural buildings and small master plans. What unites them is a slow regard for light, an honest use of local materials, and a belief that architecture is measured in daily comfort, not opening-day photographs.',
        'portrait_image' => '',
        'portrait_caption' => 'SOPEA CHAN — PRINCIPAL ARCHITECT',
        'stats' => '[{"value":"12+","label":"Years of Experience"},{"value":"48","label":"Completed Projects"},{"value":"12","label":"Awards & Recognitions"},{"value":"06","label":"Countries"}]',
        'philosophy' => '[{"title":"LIGHT","text":"We design spaces where natural light becomes part of the architectural experience — shaping movement, marking time, and giving every room its own atmosphere."},{"title":"MATERIAL","text":"Concrete, stone, wood, glass, and metal are carefully selected to create atmosphere and identity. Materials are never decoration — they are the building itself."},{"title":"CONTEXT","text":"Every building should respond to its environment, climate, culture, and surrounding landscape. Context is not a constraint — it is the beginning of the design."},{"title":"HUMAN","text":"Architecture ultimately exists for people. Comfort, movement, emotion, and experience guide every decision we make, from the first sketch to the final detail."}]',

        // SEO
        'seo_title' => 'Studio Volume — Architecture, Interiors & Design',
        'meta_description' => 'Studio Volume is an architecture and design studio in Phnom Penh creating thoughtful spaces shaped by light, material, context, and human experience.',
        'meta_keywords' => 'architecture, interior design, Phnom Penh, Cambodia, residential architecture',
        'og_image' => '',

        // Appearance
        'color_primary' => '#111111',
        'color_secondary' => '#6B6B62',
        'color_accent' => '#A65A3A',
        'color_background' => '#FFFFFF',
        'color_text' => '#1A1A1A',
        'font_heading' => 'Playfair Display',
        'font_body' => 'Inter',
        'button_style' => 'square',      // square | rounded | pill
        'button_animation' => 'fade',    // none | fade | slide | scale
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::all();

        return $all[$key] ?? $default;
    }

    public static function all(): array
    {
        $stored = Cache::remember('site.settings', 3600, function () {
            try {
                return SettingModel::query()->pluck('value', 'key')->all();
            } catch (\Throwable) {
                return [];
            }
        });

        return array_merge(static::DEFAULTS, $stored);
    }

    public static function set(array $values, string $group = 'general'): void
    {
        foreach ($values as $key => $value) {
            SettingModel::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group]
            );
        }

        Cache::forget('site.settings');
    }

    public static function json(string $key): array
    {
        $decoded = json_decode((string) static::get($key, '[]'), true);

        return is_array($decoded) ? $decoded : [];
    }
}
