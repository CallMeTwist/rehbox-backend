<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $exercises = [
            // Head & Neck
            ['title' => 'Chin Tuck',           'category' => 'head_neck',  'difficulty' => 'beginner',     'default_sets' => 3, 'default_reps' => 10, 'instructions_en' => 'Gently tuck your chin toward your chest, hold for 5 seconds.'],
            ['title' => 'Neck Side Stretch',   'category' => 'head_neck',  'difficulty' => 'beginner',     'default_sets' => 2, 'default_reps' => 8],
            ['title' => 'Neck Rotation',       'category' => 'head_neck',  'difficulty' => 'beginner',     'default_sets' => 3, 'default_reps' => 10],
            // Upper Limb
            ['title' => 'Shoulder Rolls',      'category' => 'upper_limb', 'difficulty' => 'beginner',     'default_sets' => 2, 'default_reps' => 15],
            ['title' => 'Wall Push-Up',        'category' => 'upper_limb', 'difficulty' => 'beginner',     'default_sets' => 3, 'default_reps' => 12],
            ['title' => 'Pendulum Exercise',   'category' => 'upper_limb', 'difficulty' => 'beginner',     'default_sets' => 3, 'default_reps' => 10],
            ['title' => 'Wrist Flexion Stretch','category' => 'upper_limb','difficulty' => 'beginner',     'default_sets' => 2, 'default_reps' => 10],
            // Back
            ['title' => 'Cat-Cow Stretch',     'category' => 'back',       'difficulty' => 'beginner',     'default_sets' => 3, 'default_reps' => 10],
            ['title' => 'Bird Dog',            'category' => 'back',       'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 8],
            ['title' => 'Pelvic Tilt',         'category' => 'back',       'difficulty' => 'beginner',     'default_sets' => 3, 'default_reps' => 12],
            ['title' => 'Cobra Stretch',       'category' => 'back',       'difficulty' => 'beginner',     'default_sets' => 2, 'default_reps' => 8],
            // Lower Limb
            ['title' => 'Quadriceps Stretch',  'category' => 'lower_limb', 'difficulty' => 'beginner',     'default_sets' => 2, 'default_reps' => 30],
            ['title' => 'Hamstring Stretch',   'category' => 'lower_limb', 'difficulty' => 'beginner',     'default_sets' => 2, 'default_reps' => 30],
            ['title' => 'Squatting',           'category' => 'lower_limb', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12],
            ['title' => 'One Leg Stance',      'category' => 'lower_limb', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10],
            ['title' => 'Hip ROM Exercise',    'category' => 'lower_limb', 'difficulty' => 'beginner',     'default_sets' => 3, 'default_reps' => 10],
            ['title' => 'Calf Raises',         'category' => 'lower_limb', 'difficulty' => 'beginner',     'default_sets' => 3, 'default_reps' => 15],
        ];

        foreach ($exercises as $ex) {
            Exercise::create(array_merge($ex, ['is_active' => true]));
        }

        $this->command->info('Seeded ' . count($exercises) . ' exercises.');
    }
}
