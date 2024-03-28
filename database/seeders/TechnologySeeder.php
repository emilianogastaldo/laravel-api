<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Technology;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TechnologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creo l'array delle tecnologie su cui creero le istanze(tuple) per riempire la tabella
        $techs = [
            ['label' => 'HTML', 'color' => 'danger'],
            ['label' => 'ES6', 'color' => 'secondary'],
            ['label' => 'CSS', 'color' => 'success'],
            ['label' => 'Bootstrap', 'color' => 'primary'],
            ['label' => 'Vue', 'color' => 'warning'],
            ['label' => 'PHP', 'color' => 'info'],
            ['label' => 'SQL', 'color' => 'warning'],
            ['label' => 'Laravel', 'color' => 'dark']
        ];
        foreach ($techs as $tech) {
            $new_tech = new Technology();
            $new_tech->label = $tech['label'];
            $new_tech->color = $tech['color'];
            $new_tech->save();

            // Riempio le tecnologie di progetti, funziona solo se creo le tecnologie DOPO i progetti
            // $project_ids = Project::pluck('id')->toArray();
            // $tech_projects = array_filter($project_ids, fn () => rand(0, 1));
            // $new_tech->projects()->attach($tech_projects);
        }
    }
}
