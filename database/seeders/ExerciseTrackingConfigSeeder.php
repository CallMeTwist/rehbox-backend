<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ExerciseTrackingConfigSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            'Knee Extension' => [
                'exercise_type' => 'fundamental_rom',
                'tracking_config' => [
                    'joints' => ['knee'],
                    'mode' => 'fundamental_rom',
                    'movement' => 'knee_extension',
                    'targetROM' => ['knee' => 140],
                ],
            ],
            'Knee Flexion' => [
                'exercise_type' => 'fundamental_rom',
                'tracking_config' => [
                    'joints' => ['knee'],
                    'mode' => 'fundamental_rom',
                    'movement' => 'knee_flexion',
                    'targetROM' => ['knee' => 135],
                ],
            ],
            'Shoulder Flexion' => [
                'exercise_type' => 'fundamental_rom',
                'tracking_config' => [
                    'joints' => ['shoulder'],
                    'mode' => 'fundamental_rom',
                    'movement' => 'shoulder_flexion',
                    'targetROM' => ['shoulder' => 180],
                ],
            ],
            'Hip Flexion' => [
                'exercise_type' => 'fundamental_rom',
                'tracking_config' => [
                    'joints' => ['hip'],
                    'mode' => 'fundamental_rom',
                    'movement' => 'hip_flexion',
                    'targetROM' => ['hip' => 120],
                ],
            ],
            'Elbow Flexion' => [
                'exercise_type' => 'fundamental_rom',
                'tracking_config' => [
                    'joints' => ['elbow'],
                    'mode' => 'fundamental_rom',
                    'movement' => 'elbow_flexion',
                    'targetROM' => ['elbow' => 145],
                ],
            ],
            'Squat' => [
                'exercise_type' => 'composite',
                'tracking_config' => [
                    'joints' => ['knee', 'hip'],
                    'mode' => 'composite',
                    'down' => ['knee' => '<90', 'hip' => '<100'],
                    'up' => ['knee' => '>160', 'hip' => '>160'],
                    'targetROM' => ['knee' => 135, 'hip' => 120],
                ],
            ],
            'Lunge' => [
                'exercise_type' => 'composite',
                'tracking_config' => [
                    'joints' => ['knee', 'hip'],
                    'mode' => 'composite',
                    'down' => ['knee' => '<90'],
                    'up' => ['knee' => '>160'],
                    'targetROM' => ['knee' => 90],
                ],
            ],
            'Bicep Curl' => [
                'exercise_type' => 'composite',
                'tracking_config' => [
                    'joints' => ['elbow'],
                    'mode' => 'composite',
                    'down' => ['elbow' => '<40'],
                    'up' => ['elbow' => '>120'],
                    'targetROM' => ['elbow' => 145],
                ],
            ],
            'Push Up' => [
                'exercise_type' => 'composite',
                'tracking_config' => [
                    'joints' => ['elbow'],
                    'mode' => 'composite',
                    'down' => ['elbow' => '<90'],
                    'up' => ['elbow' => '>150'],
                    'targetROM' => ['elbow' => 160],
                ],
            ],
            'Neck Rotation' => [
                'exercise_type' => 'mobility',
                'tracking_config' => [
                    'joints' => ['neck'],
                    'mode' => 'mobility',
                    'movement' => 'neck_rotation',
                    'targetROM' => ['neck' => 70],
                ],
            ],
            'Cervical Rotation' => [
                'exercise_type' => 'fundamental_rom',
                'tracking_config' => [
                    'joints' => ['neck'],
                    'mode' => 'fundamental_rom',
                    'movement' => 'neck_rotation',
                    'targetROM' => ['neck' => 70],
                ],
            ],
            'Neck Flexion' => [
                'exercise_type' => 'fundamental_rom',
                'tracking_config' => [
                    'joints' => ['neck'],
                    'mode' => 'fundamental_rom',
                    'movement' => 'neck_flexion',
                    'targetROM' => ['neck' => 45],
                ],
            ],
            'Neck Extension' => [
                'exercise_type' => 'fundamental_rom',
                'tracking_config' => [
                    'joints' => ['neck'],
                    'mode' => 'fundamental_rom',
                    'movement' => 'neck_extension',
                    'targetROM' => ['neck' => 45],
                ],
            ],
            'Lateral Flexion' => [
                'exercise_type' => 'fundamental_rom',
                'tracking_config' => [
                    'joints' => ['neck'],
                    'mode' => 'fundamental_rom',
                    'movement' => 'neck_lateral',
                    'targetROM' => ['neck' => 45],
                ],
            ],
            'Lateral Neck Flexion' => [
                'exercise_type' => 'fundamental_rom',
                'tracking_config' => [
                    'joints' => ['neck'],
                    'mode' => 'fundamental_rom',
                    'movement' => 'neck_lateral',
                    'targetROM' => ['neck' => 45],
                ],
            ],
            'Prone Cobra' => [
                'exercise_type' => 'fundamental_rom',
                'tracking_config' => [
                    'joints' => ['spine'],
                    'mode' => 'fundamental_rom',
                    'movement' => 'spine_extension',
                    'targetROM' => ['spine' => 25],
                ],
            ],
            'Stand and Reach' => [
                'exercise_type' => 'fundamental_rom',
                'tracking_config' => [
                    'joints' => ['spine'],
                    'mode' => 'fundamental_rom',
                    'movement' => 'spine_flexion',
                    'targetROM' => ['spine' => 80],
                ],
            ],
            'Sit and Reach' => [
                'exercise_type' => 'fundamental_rom',
                'tracking_config' => [
                    'joints' => ['spine'],
                    'mode' => 'fundamental_rom',
                    'movement' => 'spine_flexion',
                    'targetROM' => ['spine' => 80],
                ],
            ],
            'Shoulder Abduction' => [
                'exercise_type' => 'fundamental_rom',
                'tracking_config' => [
                    'joints' => ['shoulder'],
                    'mode' => 'fundamental_rom',
                    'movement' => 'shoulder_abduction',
                    'targetROM' => ['shoulder' => 180],
                ],
            ],
            'Lateral Raise' => [
                'exercise_type' => 'fundamental_rom',
                'tracking_config' => [
                    'joints' => ['shoulder'],
                    'mode' => 'fundamental_rom',
                    'movement' => 'shoulder_abduction',
                    'targetROM' => ['shoulder' => 90],
                ],
            ],
        ];

        foreach ($configs as $title => $config) {
            Exercise::where('title', 'like', "%{$title}%")->update($config);
        }
    }
}
