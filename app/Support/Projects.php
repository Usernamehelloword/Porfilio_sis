<?php

namespace App\Support;

final class Projects
{
    private static array $projects = [];

    public static function all(): array
    {
        if (static::$projects === []) {
            static::$projects = [
                [
                    'slug' => 'casa-forma',
                    'index' => '01',
                    'name' => 'Casa Forma',
                    'category' => 'Residential Architecture',
                    'location' => 'Phnom Penh, Cambodia',
                    'year' => '2026',
                    'area' => '420 m²',
                    'client' => 'Private Residence',
                    'type' => 'Residential',
                    'status' => 'Completed',
                    'excerpt' => 'A courtyard house composed of stacked concrete volumes, opened to light, air, and the monsoon season.',
                    'concept' => [
                        'The project explores the relationship between solid concrete volumes and natural light. Rather than treating light as an element added to the architecture, the design uses light as a material that defines movement and atmosphere.',
                        'Three shifted volumes organise the house around a shaded courtyard. Deep reveals protect the openings from rain and sun, while a single pencil-thin staircase stitches the levels together, ending in a roof terrace framed by the city skyline.',
                        'Board-formed concrete carries the memory of its timber shuttering; teak screens and terrazzo floors soften the mineral palette with warmth and craft.',
                    ],
                    'materials' => ['Board-formed concrete', 'Teak screens', 'Terrazzo', 'Brushed brass'],
                ],
                [
                    'slug' => 'concrete-house',
                    'index' => '02',
                    'name' => 'Concrete House',
                    'category' => 'Residential Architecture',
                    'location' => 'Siem Reap, Cambodia',
                    'year' => '2025',
                    'area' => '380 m²',
                    'client' => 'Private Client',
                    'type' => 'Residential',
                    'status' => 'Completed',
                    'excerpt' => 'A monolithic house for the tropics — thick walls, deep shade, and rooms that breathe.',
                    'concept' => [
                        'Set among the rice fields outside Siem Reap, the house is conceived as a heavy, monolithic object that shelters a light, open interior. Mass is used as a climatic device: thick walls delay the heat of the day, and voids pull the evening breeze through the plan.',
                        'A single long pond anchors the garden and mirrors the facade, cooling the air before it enters the living spaces.',
                    ],
                    'materials' => ['Fair-faced concrete', 'Local sandstone', 'Black steel', 'Polished screed'],
                ],
                [
                    'slug' => 'light-and-shadow',
                    'index' => '03',
                    'name' => 'Light & Shadow',
                    'category' => 'Interior Architecture',
                    'location' => 'Phnom Penh, Cambodia',
                    'year' => '2025',
                    'area' => '260 m²',
                    'client' => 'Gallery Residence',
                    'type' => 'Interior',
                    'status' => 'Completed',
                    'excerpt' => 'An apartment transformed into a sequence of lit rooms, where daylight is curated like an exhibition.',
                    'concept' => [
                        'The renovation treats daylight as a curatorial act. Each room is tuned to a different quality of light — a diffused north glow for the gallery, a sharp western shaft for the stair, a soft reflected light for the library.',
                        'Existing structure was kept and exposed; new interventions are reduced to a few precise elements in oak and plaster.',
                    ],
                    'materials' => ['White plaster', 'White oak', 'Lime plaster', 'Linen'],
                ],
                [
                    'slug' => 'atelier-nord',
                    'index' => '04',
                    'name' => 'Atelier Nord',
                    'category' => 'Cultural Architecture',
                    'location' => 'Battambang, Cambodia',
                    'year' => '2024',
                    'area' => '1,150 m²',
                    'client' => 'Municipality of Battambang',
                    'type' => 'Cultural',
                    'status' => 'Under Construction',
                    'excerpt' => 'A community art centre that reinterprets the region\'s riverside heritage in brick and shade.',
                    'concept' => [
                        'The centre occupies a disused riverside warehouse quarter. Rather than demolishing, the design inserts a new brick arcade between the old walls — a colonnaded public room that works as gallery, market, and stage.',
                        'Pitched roof volumes float above the arcades, ventilated by the river breeze and lit through clerestories tuned to the tropical sun.',
                    ],
                    'materials' => ['Exposed brick', 'Reclaimed timber', 'Galvanised steel', 'Rammed earth'],
                ],
                [
                    'slug' => 'terrace-garden-house',
                    'index' => '05',
                    'name' => 'Terrace Garden House',
                    'category' => 'Residential Architecture',
                    'location' => 'Kampot, Cambodia',
                    'year' => '2024',
                    'area' => '290 m²',
                    'client' => 'Private Residence',
                    'type' => 'Residential',
                    'status' => 'Completed',
                    'excerpt' => 'A stepped hillside house where every level opens onto its own planted terrace.',
                    'concept' => [
                        'Following the slope of a river hill, the house steps down in three planted terraces. Living spaces alternate with gardens, so that the boundary between inside and outside dissolves room by room.',
                        'The material palette is kept deliberately quiet — rendered concrete, roof tiles, and the green of the landscape doing the talking.',
                    ],
                    'materials' => ['Rendered concrete', 'Clay tiles', 'Bamboo screens', 'River stone'],
                ],
                [
                    'slug' => 'pavilion-nine',
                    'index' => '06',
                    'name' => 'Pavilion Nine',
                    'category' => 'Hospitality Architecture',
                    'location' => 'Kep, Cambodia',
                    'year' => '2023',
                    'area' => '640 m²',
                    'client' => 'Boutique Hotel',
                    'type' => 'Hospitality',
                    'status' => 'Completed',
                    'excerpt' => 'Nine timber pavilions scattered along the coast like driftwood between the palms.',
                    'concept' => [
                        'Nine guest pavilions are placed loosely along the shoreline, each rotated toward its own view and breeze. Prefabricated timber frames rest on slender concrete plinths, touching the ground lightly.',
                        'Deep verandas, operable louvres, and open bathrooms blur the limit between shelter and landscape.',
                    ],
                    'materials' => ['Glulam timber', 'Coconut wood', 'Copper mesh', 'Terracotta'],
                ],
            ];
        }

        return static::$projects;
    }

    public static function find(string $slug): ?array
    {
        foreach (static::all() as $project) {
            if ($project['slug'] === $slug) {
                return $project;
            }
        }

        return null;
    }

    public static function next(string $slug): ?array
    {
        $all = static::all();
        foreach ($all as $i => $project) {
            if ($project['slug'] === $slug) {
                return $all[($i + 1) % count($all)];
            }
        }

        return null;
    }

    /**
     * Gallery definition for a project page: photographs, drawings, renders, details.
     * Each entry is [imageKey, caption, kind, span] — span is a layout hint.
     */
    public static function gallery(int $n): array
    {
        $captions = [
            'ext-1' => 'North elevation — street facade',
            'ext-2' => 'Approach at dusk',
            'ext-3' => 'Garden elevation',
            'int-1' => 'Living space, afternoon light',
            'int-2' => 'Stair detail',
            'int-3' => 'Bedroom, morning',
            'render-1' => 'Courtyard render',
            'render-2' => 'Aerial render',
            'detail-1' => 'Material assembly — facade',
            'detail-2' => 'Concrete & brass junction',
        ];

        $k = 'p' . $n;

        return [
            [$k . '-ext-1', $captions['ext-1'], 'photo', 'full'],
            [$k . '-int-1', $captions['int-1'], 'photo', 'wide'],
            [$k . '-ext-2', $captions['ext-2'], 'photo', 'narrow'],
            [$k . '-render-1', $captions['render-1'], 'render', 'full'],
            [$k . '-plan', 'Ground floor plan — 1:100', 'drawing', 'wide'],
            [$k . '-section', 'Section A–A — 1:100', 'drawing', 'narrow'],
            [$k . '-elevation', 'South elevation — 1:100', 'drawing', 'wide'],
            [$k . '-int-2', $captions['int-2'], 'photo', 'narrow'],
            [$k . '-detail-1', $captions['detail-1'], 'detail', 'wide'],
            [$k . '-ext-3', $captions['ext-3'], 'photo', 'narrow'],
            [$k . '-int-3', $captions['int-3'], 'photo', 'wide'],
            [$k . '-render-2', $captions['render-2'], 'render', 'full'],
            [$k . '-detail-2', $captions['detail-2'], 'detail', 'wide'],
        ];
    }
}