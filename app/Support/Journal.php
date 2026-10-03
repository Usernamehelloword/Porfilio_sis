<?php

namespace App\Support;

final class Journal
{
    public static function all(): array
    {
        return [
            [
                'slug' => 'light-and-architecture',
                'category' => 'Theory',
                'date' => 'July 12, 2026',
                'title' => 'The Relationship Between Light and Architecture',
                'excerpt' => 'Light is the one material every building shares. How orientation, shadow, and rhythm decide whether a space feels alive.',
                'body' => [
                    'Every material we specify — concrete, timber, steel — is inert until light touches it. Light is the first and last material of architecture: it reveals texture, gives depth to plane, and turns enclosure into atmosphere.',
                    'In the tropics, we design with light the way northern architects design with warmth. Deep overhangs filter the harsh sun into a soft, indirect glow. Slit windows catch the low morning angle and throw long bars of shadow across a polished floor, marking the hours like a sundial.',
                    'The question is never how much glass a facade can hold, but how much light a room can absorb without losing its shadow. A good building keeps both.',
                ],
            ],
            [
                'slug' => 'why-concrete-remains-timeless',
                'category' => 'Materials',
                'date' => 'June 03, 2026',
                'title' => 'Why Concrete Remains Timeless',
                'excerpt' => 'From the Pantheon to Brion Cemetery — the quiet permanence of the world\'s most misunderstood material.',
                'body' => [
                    'Concrete is the most honest of materials. It carries the memory of its making: every board mark, every pour line, every small imperfection records the hands and weather of the day it was cast.',
                    'Its bad reputation comes from bad buildings, not bad material. When concrete is left raw and considered — not painted, not clad, not apologised for — it ages the way stone does, slowly gaining colour and dignity.',
                    'For us it is the ground material of Southeast Asian modernism: affordable, local, and able to hold a shade in a way no other material can.',
                ],
            ],
            [
                'slug' => 'designing-for-tropical-climates',
                'category' => 'Practice',
                'date' => 'April 21, 2026',
                'title' => 'Designing for Tropical Climates',
                'excerpt' => 'Breeze, monsoon, and shade. Why the best air-conditioning in Phnom Penh is still a well-placed window.',
                'body' => [
                    'A tropical building has three jobs: block the sun, invite the breeze, and survive the rain. Everything else — plan, section, facade — follows from these three.',
                    'Cross-ventilation is geometry, not luck: inlets low and shaded, outlets high and generous, corridors aligned with the southwest monsoon. Deep verandas keep rain off walls and sun off glass.',
                    'The irony of contemporary tropical architecture is that it often imitates deserts — sealed boxes of glass fighting the climate with machinery. We prefer buildings that negotiate.',
                ],
            ],
            [
                'slug' => 'minimalism-in-contemporary-architecture',
                'category' => 'Theory',
                'date' => 'March 08, 2026',
                'title' => 'Minimalism in Contemporary Architecture',
                'excerpt' => 'Minimalism is not the absence of things — it is the presence of attention. On reduction as generosity.',
                'body' => [
                    'Minimalism is widely mistaken for emptiness. In practice, it is density of decision: every joint resolved, every sightline considered, every material given a reason.',
                    'A white room with one perfect window can hold more life than a decorated hall. Reduction removes noise so that light, proportion, and the movement of a body through space become legible again.',
                    'The risk of minimalism is coldness. The cure is material warmth — timber, linen, terracotta — measured like spices.',
                ],
            ],
            [
                'slug' => 'future-of-sustainable-buildings',
                'category' => 'Practice',
                'date' => 'February 14, 2026',
                'title' => 'The Future of Sustainable Buildings',
                'excerpt' => 'Sustainability is not a technology you add. It is a decision you make at the first stroke of the plan.',
                'body' => [
                    'The most sustainable building is the one already standing. The second most sustainable is the one that needs no machines to be comfortable.',
                    'Passive design — orientation, mass, shade, ventilation — remains undefeated by any gadget. Embodied carbon argues for timber and earth; operational carbon argues for the humble window.',
                    'The future building is local: local labour, local material, local climate intelligence, dressed in the quiet confidence of contemporary form.',
                ],
            ],
            [
                'slug' => 'natural-materials-in-modern-interiors',
                'category' => 'Interiors',
                'date' => 'January 09, 2026',
                'title' => 'Natural Materials in Modern Interiors',
                'excerpt' => 'Lime, oak, linen, stone. Why the modern interior is learning to be soft again.',
                'body' => [
                    'After a decade of lacquer and grey laminate, interiors are returning to materials that age with their owners: lime plaster that records light, oak that silvers, linen that creases.',
                    'Natural materials do something no surface can simulate — they change. A room finished in living materials is never photographed the same way twice, because it is never the same room twice.',
                    'The modern interior is not a showroom. It is a climate for people, and people prefer wood to plastic.',
                ],
            ],
        ];
    }

    public static function find(string $slug): ?array
    {
        foreach (static::all() as $article) {
            if ($article['slug'] === $slug) {
                return $article;
            }
        }

        return null;
    }
}