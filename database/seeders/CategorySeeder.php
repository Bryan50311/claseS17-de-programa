<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            [
                'nombre' => 'Novela / Ficción',
                'descripcion' => 'Obras literarias narrativas en prosa que relatan hechos ficticios o basados en la realidad.'
            ],
            [
                'nombre' => 'Fantasía',
                'descripcion' => 'Historias llenas de elementos mágicos, mundos imaginarios y criaturas míticas.'
            ],
            [
                'nombre' => 'Ciencia Ficción',
                'descripcion' => 'Narraciones basadas en futuros posibles, avances científicos y viajes espaciales.'
            ],
            [
                'nombre' => 'Historia',
                'descripcion' => 'Libros que analizan e investigan acontecimientos históricos del pasado de la humanidad.'
            ],
            [
                'nombre' => 'Tecnología & Programación',
                'descripcion' => 'Guías, manuales y teorías sobre el desarrollo de software, informática y nuevas tecnologías.'
            ],
            [
                'nombre' => 'Desarrollo Personal',
                'descripcion' => 'Libros de autoayuda, hábitos, crecimiento individual y superación personal.'
            ],
            [
                'nombre' => 'Poesía',
                'descripcion' => 'Obras escritas en verso que expresan emociones, belleza y reflexiones artísticas.'
            ]
        ];

        foreach ($categorias as $categoria) {
            Category::create($categoria);
        }
    }
}
