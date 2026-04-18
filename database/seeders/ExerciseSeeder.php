<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\ExercisePlan;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        // Remove all exercise plans (cascades to plan_exercises pivot and exercise_sessions)
        ExercisePlan::query()->delete();

        // Remove all exercises
        Exercise::query()->delete();

        $exercises = [

            // ─────────────────────────────────────────────────────────────
            // NECK — Strengthening
            // ─────────────────────────────────────────────────────────────
            ['title' => 'Prone Head Lifts',                               'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/24ZNQPy8LAo'],
            ['title' => 'Supine Head Lifts',                              'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/RivatOTwtz0'],
            ['title' => 'Head Lift with Neck Side Bend',                  'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/WIWh3Xo0duM'],
            ['title' => 'Neck Rotation (Strengthening)',                   'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/dVjexqdvWqk'],
            ['title' => 'Lateral Flexion / Sideways Flexion',             'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/KX2OpgLVvVk'],
            ['title' => 'Neck Retraction',                                'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/VYcifC6BFgc'],
            ['title' => 'Neck Protraction',                               'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/VkCd4hO0UfI'],
            ['title' => 'Dumbbell Shrug',                                 'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/xDt6qbKgLkY'],
            ['title' => 'Shoulder Shrug (Neck)',                          'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/YT6qn6HVQyE'],
            ['title' => 'One-Arm Row',                                    'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/HTkZljz5oFQ'],
            ['title' => 'Upright Row',                                    'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/VIoihl5ZZzM'],
            ['title' => 'Reverse Fly (Neck)',                             'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/QICCsyKVP-g'],
            ['title' => 'Lateral Raise (Neck)',                           'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/3VcKaXpzqRo'],
            ['title' => 'Isometric Neck Rotation',                        'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/tbNtU66KzmE'],
            ['title' => 'Isometric Lateral Flexion',                      'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/3Owy1hurobA'],
            ['title' => 'Neck Flexion',                                   'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/6k9VQNN8B5U'],
            ['title' => 'Neck Extension',                                 'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 10],
            ['title' => 'Neck Flexion and Extension in 4-Point Kneeling', 'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/oFq-FRb6Keg'],
            ['title' => 'Theraband Rows',                                 'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/ygakoWpuOwU'],
            ['title' => 'Chin Tuck',                                      'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/7rnlAVhAK-8'],
            ['title' => 'Prone Cobra',                                    'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/2Z7uGBbsvf8'],
            ['title' => 'Back Burn',                                      'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/ECxsw3Dhr84'],
            ['title' => 'Scapular Retraction (Neck)',                     'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/WklUZWulQao'],
            ['title' => 'Wall Push-Ups (Neck)',                           'area' => 'neck', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/8LCCE-nNaUs'],

            // ─────────────────────────────────────────────────────────────
            // NECK — Stretching
            // ─────────────────────────────────────────────────────────────
            ['title' => 'Levator Scapula Stretch',                        'area' => 'neck', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/GSoXPJRnR6E'],
            ['title' => 'Lateral Neck Flexion Stretch',                   'area' => 'neck', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/KX2OpgLVvVk'],
            ['title' => 'Trapezius Muscle Stretch',                       'area' => 'neck', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/-r0eoFS7_5Q'],
            ['title' => 'Neck Flexion & Extension Stretch',               'area' => 'neck', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/6k9VQNN8B5U'],
            ['title' => 'Diagonal Neck Stretch',                          'area' => 'neck', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/wnfO7tUBB28'],
            ['title' => 'Scalene Stretch',                                'area' => 'neck', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/K4RT5VWhptg'],
            ['title' => 'Pectoral Stretch / Doorway Stretch',             'area' => 'neck', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/B9uY01NoqBg'],
            ['title' => 'Neck Rotation Stretch',                          'area' => 'neck', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/P_8MoxK-hOE'],
            ['title' => 'Neck Protraction and Retraction Stretch',        'area' => 'neck', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/JfJtzdG7GdA'],
            ['title' => 'Neck Flexion & Extension in 4-Point Kneeling Stretch', 'area' => 'neck', 'category' => 'stretching', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/0LXafSS_jo8'],
            ['title' => 'Bridge',                                         'area' => 'neck', 'category' => 'stretching', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/_leI4qFfPVw'],
            ['title' => 'Upper Trapezius Stretch',                        'area' => 'neck', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/z4jptepVuq4'],
            ['title' => 'Thread the Needle (Neck)',                       'area' => 'neck', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/uenqxybA9kU'],
            ['title' => 'Behind the Back Drill',                          'area' => 'neck', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/IA9eqs9ByB8'],
            ['title' => 'Backward Shoulder Rolls',                        'area' => 'neck', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 15, 'video_url' => 'https://www.youtube.com/embed/IKJZL4hvppw'],
            ['title' => 'Banded Pull-Apart (Theraband)',                  'area' => 'neck', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/osRimvxXlKQ'],
            ['title' => 'Seated Clasped Neck Stretch',                    'area' => 'neck', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/HFl16zGR4Dk'],

            // ─────────────────────────────────────────────────────────────
            // NECK — Range of Motion
            // ─────────────────────────────────────────────────────────────
            ['title' => 'Neck Flexion & Extension ROM',                   'area' => 'neck', 'category' => 'rom', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/6k9VQNN8B5U'],
            ['title' => 'Lateral Flexion ROM',                            'area' => 'neck', 'category' => 'rom', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/KX2OpgLVvVk'],
            ['title' => 'Cervical Rotation',                              'area' => 'neck', 'category' => 'rom', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/6YAH9eLCFh4'],
            ['title' => 'Cervical Retraction',                            'area' => 'neck', 'category' => 'rom', 'difficulty' => 'beginner', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/41q4gNuufkk'],
            ['title' => 'Cervical Retraction + Rotation',                 'area' => 'neck', 'category' => 'rom', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/B9nBmUnbqeE'],
            ['title' => 'Cervical Retraction + Extension',                'area' => 'neck', 'category' => 'rom', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/eznjIHMTi_U'],

            // ─────────────────────────────────────────────────────────────
            // NECK — Functional
            // ─────────────────────────────────────────────────────────────
            ['title' => 'Smooth Pursuit Neck Torsion Test (Right Side)',  'area' => 'neck', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/7IvOuC6fbr4'],
            ['title' => 'Smooth Pursuit Test in Neutral',                 'area' => 'neck', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/YwQvcgY0A_k'],
            ['title' => 'Gaze Stability Test (Rotation)',                 'area' => 'neck', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/RzPjESAVWfk'],
            ['title' => 'Mirror Twist',                                   'area' => 'neck', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'On the spot maintain gaze and head stability whilst rotating body beneath.', 'video_url' => 'https://www.youtube.com/embed/BG2r2eQkD8Y'],
            ['title' => 'The Pedestrian',                                 'area' => 'neck', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Walk forwards maintaining gaze and head stability whilst rotating body beneath.'],
            ['title' => 'Crossing the Road',                              'area' => 'neck', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Walking the length of a room, alternately focusing upon the left and right side walls.'],
            ['title' => 'Walk Past',                                      'area' => 'neck', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Look at a point on the wall, maintain eye and head stability whilst walking to comfortable end range cervical rotation, then turn and walk back.'],
            ['title' => 'Washing Hair — Extension',                       'area' => 'neck', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Extend head and neck and touch back of head with both hands.'],
            ['title' => 'Sit and Reach',                                  'area' => 'neck', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Reach forward in sitting.', 'video_url' => 'https://www.youtube.com/embed/NlYe26OXDQI'],
            ['title' => 'Stand and Reach',                                'area' => 'neck', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Reach forward in standing.', 'video_url' => 'https://www.youtube.com/embed/4jTd-kBSAmA'],
            ['title' => 'Walk Past — Extension',                          'area' => 'neck', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Look at a point where the wall and ceiling meet, maintain eye and head stability whilst walking to comfortable end range combined cervical extension and rotation, then turn and walk back.'],
            ['title' => 'Walk Past — Flexion',                            'area' => 'neck', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Look at an object on the ground, maintain eye and head stability whilst walking to comfortable end range combined cervical flexion and rotation, then turn and walk back.'],
            ['title' => 'Washing Hair — Flexion',                         'area' => 'neck', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Flex head and neck and touch back of head with both hands.', 'video_url' => 'https://www.youtube.com/embed/mTZ2L3K1FKs'],
            ['title' => 'Smell the Coffee',                               'area' => 'neck', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Sitting cervical protraction.', 'video_url' => 'https://www.youtube.com/embed/aGTz0MkYrYI'],
            ['title' => 'Avoid',                                          'area' => 'neck', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Standing cervical retraction.', 'video_url' => 'https://www.youtube.com/embed/Vg4iSulJStI'],
            ['title' => 'Crossing the Road with Step',                    'area' => 'neck', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Walking the length of a room, alternately focusing upon the left and right sidewalls, stepping over a step.'],

            // ─────────────────────────────────────────────────────────────
            // SHOULDER — Strengthening
            // ─────────────────────────────────────────────────────────────
            ['title' => 'Supine Press (One Hand)',                        'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/rCbtnTIZXRQ'],
            ['title' => 'Supine Press (Two Hand)',                        'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/TthobUszevM'],
            ['title' => 'Sitting Press',                                  'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/b5JzUH8gsOg'],
            ['title' => 'Press Plus',                                     'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/9lSjbxkjCqU'],
            ['title' => 'Isometric Contractions (Shoulder)',              'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/gFGmNiczjKc'],
            ['title' => 'Free Weight (Shoulder)',                         'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/xHxxKIgbvso'],
            ['title' => 'Tubing Pulls',                                   'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/ZlH7wgJzLnQ'],
            ['title' => 'Internal Rotation (Shoulder)',                   'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/xDDsCnJllIU'],
            ['title' => 'External Rotation (Shoulder)',                   'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/dy9DL7Pr7P0'],
            ['title' => 'Shoulder Shrug',                                 'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/cJRVVxmytaM'],
            ['title' => 'Auto-Assisted Shoulder Flexion',                 'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/lbXQcFF5J6M'],
            ['title' => 'Arm Circles',                                    'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 15, 'video_url' => 'https://www.youtube.com/embed/3STTSi_jdHk'],
            ['title' => 'Rhomboid Exercise',                              'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/8HLhv3vekwc'],
            ['title' => 'Flies',                                          'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/uBXpJGidDn4'],
            ['title' => 'Stiff Arm Pull',                                 'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/SUEvPczIlWA'],
            ['title' => 'Table Push-Ups',                                 'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/vPojzSvc-58'],
            ['title' => 'Two Hand Catch',                                 'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 15, 'video_url' => 'https://www.youtube.com/embed/PLUlbimeV7M'],
            ['title' => 'Lateral Raises',                                 'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/3VcKaXpzqRo'],
            ['title' => 'Anterior Shoulder Raises / Shoulder Flexion',    'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/NF1uwqc9VwU'],
            ['title' => "Standing Lateral Scap Pinches (T's)",            'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/MjWm9ZujQg8'],
            ['title' => 'Serratus Push-Ups',                              'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/4ye2_HMmNbs'],
            ['title' => 'Wall Angels',                                    'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/YO87HFVgsGo'],
            ['title' => 'Dumbbell Front Raise',                           'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/-t7fuZ0KhDA'],
            ['title' => 'Dumbbell Lateral Raise',                         'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/HcuY34Vz8WQ'],
            ['title' => 'Reverse Fly (Shoulder)',                         'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/QICCsyKVP-g'],
            ['title' => 'Seated Military Press',                          'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/g83mWGS0Zl4'],
            ['title' => 'Standing Dumbbell Shoulder Press',               'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/OOe_HrNnQWw'],
            ['title' => 'One-Arm Dumbbell Push Press',                    'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'advanced',      'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/XCErz4Rwwm0'],
            ['title' => 'Plank Dumbbell Shoulder Raise',                  'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'advanced',      'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/pudpTAl9ye8'],
            ['title' => 'Push-Ups',                                       'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/IODxDxX7oi4'],
            ['title' => 'Bent-Over Rows',                                 'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/vR9KcvzLqVo'],
            ['title' => 'Scaption Exercise',                              'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/5GjXe9HNthE'],
            ['title' => 'Windmill',                                       'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/5i79T19L2gE'],
            ['title' => 'Bench Press',                                    'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/4Y2ZdHCOXok'],
            ['title' => 'Wall / Finger Climbing',                         'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/kuJjYd-rdww'],
            ['title' => 'Finger Climbing Sideways',                       'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/B1w-pRvAicI'],
            ['title' => 'Squeeze Shoulder Blades',                        'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/9tJTdqUXW14'],
            ['title' => 'Shoulder Rows',                                  'area' => 'shoulder', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/_Czzlq62D1Q'],

            // ─────────────────────────────────────────────────────────────
            // SHOULDER — Stretching
            // ─────────────────────────────────────────────────────────────
            ['title' => 'Elbow-Out Rotator Stretch',                      'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/z63ZM9VdpWk'],
            ['title' => 'Cross Body Shoulder Stretch',                    'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/aIq0fLi8iak'],
            ['title' => 'Bent-Arm Shoulder Stretch',                      'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/mFXx9Fc-kUk'],
            ['title' => 'Overhead Triceps & Shoulder Stretch',            'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/zzvDO56B0HE'],
            ['title' => 'Reverse Shoulder Stretch',                       'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/BgXNUCjpCMw'],
            ['title' => 'Downward Facing Dog',                            'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/j97SSGsnCAQ'],
            ['title' => "Child's Pose",                                   'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/qYvYsFrTI0U'],
            ['title' => 'Hand Behind the Head Stretch',                   'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/Vm8xjOHijek'],
            ['title' => 'Eagle Arms Spinal Rolls',                        'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/vYWSiBnkUPg'],
            ['title' => 'Seated Twist',                                   'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/4faLXO2bLFU'],
            ['title' => 'Pectoral / Doorway Shoulder Stretch',            'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/w3I3d1LuqOU'],
            ['title' => 'Thread the Needle (Shoulder)',                   'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/MfUx9FCOb1E'],
            ['title' => 'Reverse Prayer Pose',                            'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/KFIJcEs_F2E'],
            ['title' => 'Towel Stretch',                                  'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/-YFWrYkJVBs'],
            ['title' => 'Cat Cow Pose',                                   'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/tT00XNqJ3uA'],
            ['title' => 'Upper Trapezius Stretch (Shoulder)',             'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/z4jptepVuq4'],
            ['title' => 'Wide-Legged Standing Forward Bend',              'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/xO65_FHbEr0'],
            ['title' => 'Hand Cuffs Drill',                               'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/C9DKGrMoFhA'],
            ['title' => 'T-Spine Windmill Stretch',                       'area' => 'shoulder', 'category' => 'stretching', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/63DoI8EYvRI'],

            // ─────────────────────────────────────────────────────────────
            // SHOULDER — Range of Motion
            // ─────────────────────────────────────────────────────────────
            ['title' => 'Across the Chest Stretch',                       'area' => 'shoulder', 'category' => 'rom', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/-1K0m5ywRcY'],
            ['title' => 'Shoulder Circles',                               'area' => 'shoulder', 'category' => 'rom', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 15, 'video_url' => 'https://www.youtube.com/embed/shcSlZEnNp0'],
            ['title' => 'Cane Exercises',                                 'area' => 'shoulder', 'category' => 'rom', 'difficulty' => 'beginner', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/6UPOad14RBY'],
            ['title' => 'Arm Elevation',                                  'area' => 'shoulder', 'category' => 'rom', 'difficulty' => 'beginner', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/dvemgOeXvzE'],
            ['title' => 'Ball Squeeze Exercise',                          'area' => 'shoulder', 'category' => 'rom', 'difficulty' => 'beginner', 'default_sets' => 3, 'default_reps' => 15, 'video_url' => 'https://www.youtube.com/embed/8h0tSMxLNG4'],
            ['title' => 'Pendulum Exercise',                              'area' => 'shoulder', 'category' => 'rom', 'difficulty' => 'beginner', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/wD3jQJ-dGnY'],
            ['title' => 'Supine External Rotation',                       'area' => 'shoulder', 'category' => 'rom', 'difficulty' => 'beginner', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/MXD-XyQj64E'],
            ['title' => 'Supine Passive Forward Flexion',                 'area' => 'shoulder', 'category' => 'rom', 'difficulty' => 'beginner', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/7UvmUd1FYC4'],
            ['title' => 'Behind the Back Internal Rotation',              'area' => 'shoulder', 'category' => 'rom', 'difficulty' => 'beginner', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/z7x1gZnFi5I'],
            ['title' => 'Wall Side Stretch',                              'area' => 'shoulder', 'category' => 'rom', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/cW5WuyfICjA'],
            ['title' => 'Assisted Abduction',                             'area' => 'shoulder', 'category' => 'rom', 'difficulty' => 'beginner', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/l-pHAMdJOJM'],

            // ─────────────────────────────────────────────────────────────
            // SHOULDER — Functional
            // ─────────────────────────────────────────────────────────────
            ['title' => 'Scapula Upward Rotation',                        'area' => 'shoulder', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Raising arm above head to change the batteries in a smoke detector.', 'video_url' => 'https://www.youtube.com/embed/NIK1COmHrro'],
            ['title' => 'Scapula Downward Rotation',                      'area' => 'shoulder', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Moving a box of pasta from the top shelf in the cabinet to the kitchen counter.', 'video_url' => 'https://www.youtube.com/embed/uzwdRBZYSEY'],
            ['title' => 'Scapular Elevation',                             'area' => 'shoulder', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Carrying a pocketbook on one shoulder.', 'video_url' => 'https://www.youtube.com/embed/NNK1H2aAzhM'],
            ['title' => 'Scapular Depression',                            'area' => 'shoulder', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => "Reaching into jacket's pocket for a cell phone.", 'video_url' => 'https://www.youtube.com/embed/maKLqBSn_Vo'],
            ['title' => 'Scapular Protraction',                           'area' => 'shoulder', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Reaching forward to turn on the sink faucet.', 'video_url' => 'https://www.youtube.com/embed/RMLua5woHRg'],
            ['title' => 'Scapular Retraction',                            'area' => 'shoulder', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => "Reaching into pant's back pocket for wallet.", 'video_url' => 'https://www.youtube.com/embed/WklUZWulQao'],
            ['title' => 'Shoulder Flexion (Functional)',                  'area' => 'shoulder', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Reaching for the doorknob when opening a door.', 'video_url' => 'https://www.youtube.com/embed/NF1uwqc9VwU'],
            ['title' => 'Shoulder Extension / Hyperextension',            'area' => 'shoulder', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => "Reaching behind the driver's seat in the car for a pocketbook.", 'video_url' => 'https://www.youtube.com/embed/vEvJoV1z6Q8'],
            ['title' => 'Shoulder Abduction (Functional)',                'area' => 'shoulder', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Extending arm out to the side in order to put arm through the arm hole in jacket.', 'video_url' => 'https://www.youtube.com/embed/KXQA0wpKvhU'],
            ['title' => 'Shoulder Adduction (Functional)',                'area' => 'shoulder', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Holding pocketbook in place under arm.', 'video_url' => 'https://www.youtube.com/embed/2N6WhuGDZRs'],
            ['title' => 'Overhead Toss',                                  'area' => 'shoulder', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 15, 'video_url' => 'https://www.youtube.com/embed/6CzPR8T-XgI'],
            ['title' => 'Chest Pass Shoulder Exercise',                   'area' => 'shoulder', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 15, 'video_url' => 'https://www.youtube.com/embed/1Dpv_CJsa-g'],
            ['title' => 'Shoulder Height Task',                           'area' => 'shoulder', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'While sitting on a wooden chair, lift and place object on near edge of height-adjustable desk at shoulder height.', 'video_url' => 'https://www.youtube.com/embed/fWrVRQwcxXg'],
            ['title' => 'Sliding Task',                                   'area' => 'shoulder', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'While sitting on a wooden chair, slide box across table at desk height by pushing it away from you.'],
            ['title' => 'Reaching for Salt Shaker',                       'area' => 'shoulder', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/ztpyQSI1eWw'],
            ['title' => 'Hand Walking',                                   'area' => 'shoulder', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/omQcFoMuZXg'],

            // ─────────────────────────────────────────────────────────────
            // ELBOW, FOREARM & WRIST — Strengthening
            // ─────────────────────────────────────────────────────────────
            ['title' => 'Elbow Curl',                                     'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/p9zAf87T-es'],
            ['title' => 'Bicep Curl',                                     'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/ykJmrZ5v0Oo'],
            ['title' => 'Resisted Forearm Pronation',                     'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/jPdyXJjZAAw'],
            ['title' => 'Resisted Supination',                            'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/9Qy53eyz1Hw'],
            ['title' => 'Supination & Pronation with Dumbbell',           'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/-UC0nCcP67c'],
            ['title' => 'Towel Twist',                                    'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/8N-fMQk90ao'],
            ['title' => 'Triceps Dips',                                   'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/6kALZikXxLc'],
            ['title' => 'Triceps Kick-Backs',                             'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/ZO81bExngMI'],
            ['title' => 'Isometric Elbow Flexion',                        'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/WsMyJXo92f0'],
            ['title' => 'Theraband Flex Bar Reverse Tyler Twist',         'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 15, 'video_url' => 'https://www.youtube.com/embed/vZsa0bBCAf0'],
            ['title' => 'Theraband Wrist Flexion',                        'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 15, 'video_url' => 'https://www.youtube.com/embed/rSpoq7hmQ-k'],
            ['title' => 'Pull-Ups',                                       'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'advanced',      'default_sets' => 3, 'default_reps' => 8,  'video_url' => 'https://www.youtube.com/embed/eGo4IYlbE5g'],
            ['title' => 'Plank with Shoulder Taps',                      'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/jgQ49dXfznk'],
            ['title' => 'Bicep Curls with Tubing',                        'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/8PfGDH8rEEs'],
            ['title' => 'Bicep Curl with Dumbbells',                      'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/ykJmrZ5v0Oo'],
            ['title' => 'Finger Tip Push-Ups',                            'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'advanced',      'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/pNF2U6Koo0M'],
            ['title' => 'Wrist Deviation',                                'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 12, 'video_url' => 'https://www.youtube.com/embed/3J8MdcNxNyc'],
            ['title' => 'Wrist Curls',                                    'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 15, 'video_url' => 'https://www.youtube.com/embed/NoO4ol8Zw2I'],
            ['title' => 'Wrist Extension with Dumbbells',                 'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 15, 'video_url' => 'https://www.youtube.com/embed/la-0c4ubkvs'],
            ['title' => 'Wrist Extension Hold',                           'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/UqTPz7YYaBI'],
            ['title' => 'Ball Squeeze',                                   'area' => 'elbow_forearm_wrist', 'category' => 'strengthening', 'difficulty' => 'beginner',      'default_sets' => 3, 'default_reps' => 15, 'video_url' => 'https://www.youtube.com/embed/8h0tSMxLNG4'],

            // ─────────────────────────────────────────────────────────────
            // ELBOW, FOREARM & WRIST — Stretching
            // ─────────────────────────────────────────────────────────────
            ['title' => 'Forearm Extensor Stretch',                       'area' => 'elbow_forearm_wrist', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/Ayhu7TzNGSQ'],
            ['title' => 'Wall Stretch (Forearm)',                         'area' => 'elbow_forearm_wrist', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/cW5WuyfICjA'],
            ['title' => 'Crossover Stretch',                              'area' => 'elbow_forearm_wrist', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/4OL9RvuqLd4'],
            ['title' => 'Physio Ball Stretch',                            'area' => 'elbow_forearm_wrist', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/geSPIV82yc0'],
            ['title' => 'Tricep Stretch',                                 'area' => 'elbow_forearm_wrist', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/_IOHtPSYGbk'],
            ['title' => 'Finger Stretch',                                 'area' => 'elbow_forearm_wrist', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/05H0tjWx8UA'],
            ['title' => 'Wrist Extension Stretch',                        'area' => 'elbow_forearm_wrist', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/cOYA0cTIwzM'],
            ['title' => 'Wrist Flexion Stretch',                          'area' => 'elbow_forearm_wrist', 'category' => 'stretching', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 30, 'video_url' => 'https://www.youtube.com/embed/gMl9dFSOehs'],

            // ─────────────────────────────────────────────────────────────
            // ELBOW, FOREARM & WRIST — Range of Motion
            // ─────────────────────────────────────────────────────────────
            ['title' => 'Farmer Walk',                                    'area' => 'elbow_forearm_wrist', 'category' => 'rom', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/rt17lmnaLSM'],
            ['title' => 'Crab Walk',                                      'area' => 'elbow_forearm_wrist', 'category' => 'rom', 'difficulty' => 'intermediate', 'default_sets' => 3, 'default_reps' => 10, 'video_url' => 'https://www.youtube.com/embed/XAHZRIoNsHE'],

            // ─────────────────────────────────────────────────────────────
            // ELBOW, FOREARM & WRIST — Functional
            // ─────────────────────────────────────────────────────────────
            ['title' => 'Scooping and Pouring Water',                     'area' => 'elbow_forearm_wrist', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Practice scooping water and pouring it into a cup to train forearm and wrist control.'],
            ['title' => 'High Fives Palm Up',                             'area' => 'elbow_forearm_wrist', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Doing high fives with your palm up to train supination.'],
            ['title' => 'Filling Plastic Eggs',                           'area' => 'elbow_forearm_wrist', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Filling plastic eggs with things that make sounds; one hand holds the egg.'],
            ['title' => 'Carrying Books Palms Up',                        'area' => 'elbow_forearm_wrist', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Carrying books or trays with your palms facing up to train forearm supination.'],
            ['title' => 'Bouncing Tennis Ball on Racket',                 'area' => 'elbow_forearm_wrist', 'category' => 'functional', 'difficulty' => 'intermediate', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Bouncing a tennis ball on a tennis racket to improve wrist control and coordination.'],
            ['title' => 'Wringing Out a Towel',                           'area' => 'elbow_forearm_wrist', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Wringing out a towel by twisting it to train forearm pronation and supination.'],
            ['title' => 'Turning Pages of a Book',                        'area' => 'elbow_forearm_wrist', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Turning the pages of a book to train fine motor control of the wrist and fingers.'],
            ['title' => 'Guess Which Hand Game',                          'area' => 'elbow_forearm_wrist', 'category' => 'functional', 'difficulty' => 'beginner', 'default_sets' => 2, 'default_reps' => 10, 'description' => 'Turn over your hand palm-up to reveal a hidden object, training supination and proprioception.'],
        ];

        foreach ($exercises as $ex) {
            Exercise::create(array_merge($ex, ['is_active' => true]));
        }

        // ── Apply standard ROM correct_angles by area + title pattern ────────
        // Rather than duplicating the entire array, we resolve and update
        // correct_angles in a second pass. Admin can override any of these in
        // the Filament panel. The `side` field defaults to 'bilateral'; PTs can
        // change a specific exercise to 'left' or 'right' for post-surgical clients.
        Exercise::all()->each(function (Exercise $exercise): void {
            $angles = $this->resolveAngles($exercise->area, $exercise->title, $exercise->category);
            if ($angles !== null) {
                $exercise->update(['correct_angles' => $angles]);
            }
        });

        $exerciseCount = Exercise::count();
        $withRom = Exercise::whereNotNull('correct_angles')->count();
        $this->command->info("Seeded {$exerciseCount} exercises. {$withRom} have standard ROM tracking rules.");
    }

    /**
     * Resolve the correct_angles JSON for an exercise based on body area and title keywords.
     *
     * Landmark triplets always use LEFT-side indices; `side: 'bilateral'` instructs
     * the frontend to also compute the mirrored RIGHT-side angle automatically.
     *
     * All clinical ROM bounds (min/max) are per the standard goniometric norms:
     *   Shoulder Flexion 0–180°, Extension 0–60°, Abduction 0–180°
     *   Elbow Flexion 0–160°, Extension 0–10°
     *   Hip Flexion 0–125°, Extension 0–15°, Abduction 0–45°
     *   Knee Flexion 0–140°, Extension 140–0°
     *   Neck Lateral 0–45°, Flexion/Extension 0–45°, Rotation 0–60°
     */
    private function resolveAngles(string $area, string $title, string $category): ?array
    {
        $title = strtolower($title);

        return match ($area) {
            'neck' => $this->neckAngles($title),
            'shoulder' => $this->shoulderAngles($title),
            'elbow_forearm_wrist' => $this->elbowAngles($title),
            'lower_limb' => $this->lowerLimbAngles($title),
            'back' => $this->backAngles($title),
            default => null,
        };
    }

    // ── Neck ─────────────────────────────────────────────────────────────────

    private function neckAngles(string $title): ?array
    {
        // Rotation exercises — monitor neck rotation via ear-nose-shoulder triangle
        if (str_contains($title, 'rotation')) {
            return [$this->jointRule(
                joint: 'neck_rotation',
                movement: 'neck_rotation',
                landmarks: [7, 0, 11],         // L.ear – nose – L.shoulder
                min: 120, max: 180,             // upright ~180°, full rotate ~120°
                feedbackLow: 'Relax — you are rotating past a comfortable range',
                feedbackHigh: 'Rotate your head further in the direction of movement',
                repJoint: true,
                upThreshold: 165, downThreshold: 130,
            )];
        }

        // Lateral flexion / side bend — require explicit flexion/tilt/bend context
        // to avoid matching 'lateral raise' (a shoulder exercise in neck area).
        if (str_contains($title, 'lateral flexion') || str_contains($title, 'lateral tilt') || str_contains($title, 'side bend') || str_contains($title, 'sideways')) {
            return [$this->jointRule(
                joint: 'neck_lateral',
                movement: 'neck_lateral',
                landmarks: [7, 11, 23],         // L.ear – L.shoulder – L.hip
                min: 130, max: 180,             // upright ~175°, full tilt ~130°
                feedbackLow: 'Ease off — excessive lateral tilt',
                feedbackHigh: 'Tilt your head further toward your shoulder',
                repJoint: true,
                upThreshold: 165, downThreshold: 135,
            )];
        }

        // Extension exercises (chin up / backward)
        if (str_contains($title, 'extension') && ! str_contains($title, 'flexion')) {
            return [$this->jointRule(
                joint: 'neck_extension',
                movement: 'neck_extension',
                landmarks: [7, 11, 23],
                min: 130, max: 180,
                feedbackLow: 'Do not overextend your neck',
                feedbackHigh: 'Extend your head back further',
                repJoint: true,
                upThreshold: 165, downThreshold: 135,
            )];
        }

        // Flexion (chin to chest) — and combined flex/extension
        if (str_contains($title, 'flexion') || str_contains($title, 'chin tuck') || str_contains($title, 'retraction') || str_contains($title, 'protraction')) {
            return [$this->jointRule(
                joint: 'neck_flexion',
                movement: 'neck_flexion',
                landmarks: [7, 11, 23],
                min: 130, max: 180,
                feedbackLow: 'Ease off — do not overstretch',
                feedbackHigh: 'Bring your chin further toward your chest',
                repJoint: true,
                upThreshold: 165, downThreshold: 135,
            )];
        }

        // Exercises like Dumbbell Shrug, Shoulder Shrug, One-Arm Row, Upright Row,
        // Reverse Fly, Lateral Raise, Push-Up, Theraband Rows, Bridge, etc. are
        // categorised under 'neck' area but do not involve measurable neck joint
        // angles at MediaPipe landmarks. Return null → visibility-based scoring.
        return null;
    }

    // ── Shoulder ──────────────────────────────────────────────────────────────

    private function shoulderAngles(string $title): ?array
    {
        // Abduction (arm out to side)
        if (str_contains($title, 'abduction') || str_contains($title, 'lateral raise')) {
            return [$this->jointRule(
                joint: 'shoulder_abduction',
                movement: 'shoulder_abduction',
                landmarks: [23, 11, 13],        // hip – shoulder – elbow
                min: 0, max: 180,
                feedbackLow: 'Lower your arm fully between reps',
                feedbackHigh: 'Raise your arm higher to the side',
                repJoint: true,
                upThreshold: 150, downThreshold: 30,
            )];
        }

        // Extension (arm behind body)
        if (str_contains($title, 'extension')) {
            return [$this->jointRule(
                joint: 'shoulder_extension',
                movement: 'shoulder_extension',
                landmarks: [23, 11, 13],
                min: 0, max: 60,
                feedbackLow: 'Do not hyperextend the shoulder',
                feedbackHigh: 'Extend your arm further behind you',
                repJoint: true,
                upThreshold: 50, downThreshold: 10,
            )];
        }

        // External rotation
        if (str_contains($title, 'external rotation') || str_contains($title, 'ext. rotation')) {
            return [$this->jointRule(
                joint: 'shoulder_er',
                movement: 'shoulder_er',
                landmarks: [13, 11, 23],        // elbow – shoulder – hip
                min: 0, max: 90,
                feedbackLow: 'Do not over-rotate the shoulder outward',
                feedbackHigh: 'Rotate your arm outward further',
                repJoint: true,
                upThreshold: 75, downThreshold: 20,
            )];
        }

        // Internal rotation
        if (str_contains($title, 'internal rotation') || str_contains($title, 'int. rotation')) {
            return [$this->jointRule(
                joint: 'shoulder_ir',
                movement: 'shoulder_ir',
                landmarks: [13, 11, 23],
                min: 0, max: 70,
                feedbackLow: 'Do not over-rotate inward',
                feedbackHigh: 'Rotate your arm inward further',
                repJoint: true,
                upThreshold: 60, downThreshold: 15,
            )];
        }

        // Flexion (arm forward/overhead)
        if (str_contains($title, 'flexion') || str_contains($title, 'forward raise') || str_contains($title, 'overhead') || str_contains($title, 'press')) {
            return [$this->jointRule(
                joint: 'shoulder_flexion',
                movement: 'shoulder_flexion',
                landmarks: [23, 11, 13],            // hip – shoulder – elbow
                min: 0, max: 180,
                feedbackLow: 'Lower your arm fully at the starting position',
                feedbackHigh: 'Raise your arm higher overhead',
                repJoint: true,
                upThreshold: 150, downThreshold: 30,
            )];
        }

        // Adduction
        if (str_contains($title, 'adduction') || str_contains($title, 'cross body')) {
            return [$this->jointRule(
                joint: 'shoulder_adduction',
                movement: 'shoulder_adduction',
                landmarks: [23, 11, 13],
                min: 0, max: 30,
                feedbackLow: 'Ease off — do not over-cross the midline',
                feedbackHigh: 'Bring your arm further across your body',
                repJoint: true,
                upThreshold: 25, downThreshold: 5,
            )];
        }

        // Exercises like Shoulder Shrug, Scapular Retraction, Pendulum, Posture
        // exercises, etc. — not directly trackable via shoulder angle landmarks.
        // Return null → visibility-based scoring only.
        return null;
    }

    // ── Elbow, Forearm & Wrist ────────────────────────────────────────────────

    private function elbowAngles(string $title): ?array
    {
        // Extension exercises
        if (str_contains($title, 'extension') || str_contains($title, 'tricep') || str_contains($title, 'push')) {
            return [$this->jointRule(
                joint: 'elbow_extension',
                movement: 'elbow_extension',
                landmarks: [11, 13, 15],        // shoulder – elbow – wrist
                min: 150, max: 180,             // 0–10° extension measured as near-straight
                feedbackLow: 'Extend your arm fully at the top',
                feedbackHigh: 'Keep bending — do not lock out too early',
                repJoint: true,
                upThreshold: 160, downThreshold: 90,
            )];
        }

        // Wrist exercises — track elbow as secondary, wrist angle unavailable in pose
        if (str_contains($title, 'wrist') || str_contains($title, 'finger')) {
            // Wrist angles are not directly trackable with MediaPipe Pose (no metacarpal landmarks).
            // Track elbow flexion as a proxy for forearm positioning.
            return [$this->jointRule(
                joint: 'elbow_flexion',
                movement: 'elbow_flexion',
                landmarks: [11, 13, 15],
                min: 70, max: 130,             // stable mid-range for wrist work
                feedbackLow: 'Keep your elbow slightly bent during this exercise',
                feedbackHigh: 'Relax your arm — do not over-straighten',
                repJoint: false,
            )];
        }

        // Flexion (bicep curl, elbow bend)
        if (str_contains($title, 'flexion') || str_contains($title, 'curl') || str_contains($title, 'bicep') || str_contains($title, 'bend')) {
            return [$this->jointRule(
                joint: 'elbow_flexion',
                movement: 'elbow_flexion',
                landmarks: [11, 13, 15],            // shoulder – elbow – wrist
                min: 0, max: 160,
                feedbackLow: 'Extend your arm fully at the bottom',
                feedbackHigh: 'Curl your arm up further',
                repJoint: true,
                upThreshold: 140, downThreshold: 60,
            )];
        }

        // No trackable elbow angle for this exercise → visibility-based scoring.
        return null;
    }

    // ── Lower Limb ────────────────────────────────────────────────────────────

    private function lowerLimbAngles(string $title): ?array
    {
        // Knee-specific exercises
        if (
            str_contains($title, 'knee') ||
            str_contains($title, 'squat') ||
            str_contains($title, 'lunge') ||
            str_contains($title, 'step')
        ) {
            $rules = [$this->jointRule(
                joint: 'knee_flexion',
                movement: 'knee_flexion',
                landmarks: [23, 25, 27],        // hip – knee – ankle
                min: 0, max: 140,
                feedbackLow: 'Straighten your leg fully between reps',
                feedbackHigh: 'Bend your knee further',
                repJoint: true,
                upThreshold: 110, downThreshold: 30,
            )];

            // Add hip flexion as secondary for squats/lunges
            if (str_contains($title, 'squat') || str_contains($title, 'lunge')) {
                $rules[] = $this->jointRule(
                    joint: 'hip_flexion',
                    movement: 'hip_flexion',
                    landmarks: [11, 23, 25],    // shoulder – hip – knee
                    min: 70, max: 125,
                    feedbackLow: 'Stand taller at the top of the movement',
                    feedbackHigh: 'Hinge at the hips more as you lower down',
                    repJoint: false,
                    weight: 0.3,
                );
            }

            return $rules;
        }

        // Hip-specific exercises
        if (
            str_contains($title, 'hip') ||
            str_contains($title, 'bridge') ||
            str_contains($title, 'abduction') ||
            str_contains($title, 'adduction')
        ) {
            if (str_contains($title, 'abduction')) {
                return [$this->jointRule(
                    joint: 'hip_abduction',
                    movement: 'hip_abduction',
                    landmarks: [23, 25, 27],    // hip – knee – ankle used as proxy
                    min: 0, max: 45,
                    feedbackLow: 'Bring your leg back to the starting position',
                    feedbackHigh: 'Lift your leg further out to the side',
                    repJoint: true,
                    upThreshold: 35, downThreshold: 10,
                )];
            }

            return [$this->jointRule(
                joint: 'hip_flexion',
                movement: 'hip_flexion',
                landmarks: [11, 23, 25],        // shoulder – hip – knee
                min: 0, max: 125,
                feedbackLow: 'Lower your leg to the starting position',
                feedbackHigh: 'Raise your knee higher toward your chest',
                repJoint: true,
                upThreshold: 90, downThreshold: 20,
            )];
        }

        // No clearly trackable joint angle for this lower-limb exercise
        // (e.g. ankle, calf, balance, or postural exercises without a named joint).
        // Return null → visibility-based scoring only.
        return null;
    }

    // ── Back ──────────────────────────────────────────────────────────────────

    private function backAngles(string $title): ?array
    {
        // Row variations — elbow flexion at the joint
        if (str_contains($title, 'row') || str_contains($title, 'pull')) {
            return [$this->jointRule(
                joint: 'elbow_flexion',
                movement: 'elbow_flexion',
                landmarks: [11, 13, 15],
                min: 30, max: 150,
                feedbackLow: 'Extend your arms fully at the front',
                feedbackHigh: 'Pull your elbows further back',
                repJoint: true,
                upThreshold: 130, downThreshold: 50,
            )];
        }

        // Hip hinge / deadlift / bridge — hip flexion
        if (str_contains($title, 'bridge') || str_contains($title, 'deadlift') || str_contains($title, 'hinge')) {
            return [$this->jointRule(
                joint: 'hip_flexion',
                movement: 'hip_flexion',
                landmarks: [11, 23, 25],
                min: 0, max: 125,
                feedbackLow: 'Extend your hips fully at the top',
                feedbackHigh: 'Hinge forward more at the hips',
                repJoint: true,
                upThreshold: 160, downThreshold: 70,
            )];
        }

        // Overhead / extension back exercises
        if (str_contains($title, 'overhead') || str_contains($title, 'extension') || str_contains($title, 'press')) {
            return [$this->jointRule(
                joint: 'shoulder_flexion',
                movement: 'shoulder_flexion',
                landmarks: [23, 11, 13],
                min: 0, max: 180,
                feedbackLow: 'Lower your arms fully',
                feedbackHigh: 'Reach further overhead',
                repJoint: true,
                upThreshold: 150, downThreshold: 30,
            )];
        }

        // General back exercises (e.g. core, postural, spine) without a specific
        // trackable joint angle. Return null → visibility-based scoring only.
        return null;
    }

    // ── Joint rule builder ────────────────────────────────────────────────────

    private function jointRule(
        string $joint,
        string $movement,
        array $landmarks,
        int $min,
        int $max,
        string $feedbackLow,
        string $feedbackHigh,
        bool $repJoint = false,
        ?int $upThreshold = null,
        ?int $downThreshold = null,
        float $weight = 1.0,
        string $side = 'bilateral',
    ): array {
        $rule = [
            'joint' => $joint,
            'movement' => $movement,
            'side' => $side,
            'landmarks' => $landmarks,
            'min' => $min,
            'max' => $max,
            'feedback_low' => $feedbackLow,
            'feedback_high' => $feedbackHigh,
            'weight' => $weight,
            'rep_joint' => $repJoint,
        ];

        if ($repJoint && $upThreshold !== null) {
            $rule['up_threshold'] = $upThreshold;
            $rule['down_threshold'] = $downThreshold;
        }

        return $rule;
    }
}
