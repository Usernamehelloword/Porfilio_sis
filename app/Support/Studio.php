<?php

namespace App\Support;

final class Studio
{
    public const NAME = 'STUDIO VOLUME';

    public const TAGLINE = 'Architecture / Interior / Design';

    public const EMAIL = 'studio@studiovolume.com';

    public const PHONE = '+855 12 345 678';

    public const ADDRESS = 'Street 2404, Borey Peng Huoth, Phnom Penh, Cambodia';

    public const INSTAGRAM = 'https://instagram.com';

    public const LINKEDIN = 'https://linkedin.com';

    public const QUOTE = '“Architecture is the art of shaping how people experience space.”';

    public static function stats(): array
    {
        return [
            ['value' => '12+', 'label' => 'Years of Experience'],
            ['value' => '48', 'label' => 'Completed Projects'],
            ['value' => '12', 'label' => 'Awards & Recognitions'],
            ['value' => '06', 'label' => 'Countries'],
        ];
    }

    public static function philosophy(): array
    {
        return [
            ['index' => '01', 'title' => 'LIGHT', 'text' => 'We design spaces where natural light becomes part of the architectural experience — shaping movement, marking time, and giving every room its own atmosphere.'],
            ['index' => '02', 'title' => 'MATERIAL', 'text' => 'Concrete, stone, wood, glass, and metal are carefully selected to create atmosphere and identity. Materials are never decoration — they are the building itself.'],
            ['index' => '03', 'title' => 'CONTEXT', 'text' => 'Every building should respond to its environment, climate, culture, and surrounding landscape. Context is not a constraint — it is the beginning of the design.'],
            ['index' => '04', 'title' => 'HUMAN', 'text' => 'Architecture ultimately exists for people. Comfort, movement, emotion, and experience guide every decision we make, from the first sketch to the final detail.'],
        ];
    }

    public static function services(): array
    {
        return [
            ['index' => '01', 'title' => 'Architectural Design', 'text' => 'Concept development, planning, design development, and construction documentation.'],
            ['index' => '02', 'title' => 'Interior Architecture', 'text' => 'Interior spaces designed as an extension of the architectural concept.'],
            ['index' => '03', 'title' => 'Master Planning', 'text' => 'Large-scale planning and development strategies.'],
            ['index' => '04', 'title' => 'Landscape Design', 'text' => 'Integration of architecture, landscape, vegetation, and outdoor environments.'],
            ['index' => '05', 'title' => 'Visualization', 'text' => 'High-quality architectural visualization, 3D rendering, and presentation imagery.'],
            ['index' => '06', 'title' => 'Consultation', 'text' => 'Architectural consultation, feasibility studies, and design strategy.'],
        ];
    }

    public static function materials(): array
    {
        return [
            ['key' => 'concrete', 'name' => 'CONCRETE', 'tags' => 'Raw / Structural / Minimal'],
            ['key' => 'wood', 'name' => 'WOOD', 'tags' => 'Warm / Natural / Human'],
            ['key' => 'stone', 'name' => 'STONE', 'tags' => 'Solid / Timeless / Grounded'],
            ['key' => 'glass', 'name' => 'GLASS', 'tags' => 'Light / Transparent / Precise'],
            ['key' => 'steel', 'name' => 'STEEL', 'tags' => 'Slim / Honest / Engineered'],
            ['key' => 'brick', 'name' => 'BRICK', 'tags' => 'Tactile / Local / Rhythmic'],
            ['key' => 'marble', 'name' => 'MARBLE', 'tags' => 'Quiet / Elegant / Permanent'],
        ];
    }

    public static function projectTypes(): array
    {
        return ['Residential', 'Interior', 'Commercial', 'Cultural', 'Hospitality', 'Master Plan'];
    }

    public static function budgets(): array
    {
        return ['Under $50,000', '$50,000 – $150,000', '$150,000 – $500,000', '$500,000+', 'Not sure yet'];
    }
}