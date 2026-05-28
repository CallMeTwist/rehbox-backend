<?php

namespace App\Support;

class ConditionAreaMap
{
    private const MAP = [
        'lower_back_pain' => 'back',
        'upper_back_pain' => 'back',
        'sciatica' => 'back',
        'neck_strain' => 'head_neck',
        'cervical_pain' => 'head_neck',
        'shoulder_impingement' => 'upper_limbs',
        'rotator_cuff' => 'upper_limbs',
        'frozen_shoulder' => 'upper_limbs',
        'tennis_elbow' => 'elbow_forearm_wrist',
        'carpal_tunnel' => 'elbow_forearm_wrist',
        'wrist_sprain' => 'elbow_forearm_wrist',
        'knee_pain' => 'lower_limbs',
        'acl_recovery' => 'lower_limbs',
        'meniscus_tear' => 'lower_limbs',
        'ankle_sprain' => 'lower_limbs',
        'plantar_fasciitis' => 'lower_limbs',
        'hip_pain' => 'lower_limbs',
        'pelvic_dysfunction' => 'pelvic',
        'postnatal_recovery' => 'pelvic',
        'chest_tightness' => 'chest',
        'post_thoracic_surgery' => 'chest',
    ];

    public static function areaFor(?string $condition): ?string
    {
        if ($condition === null || $condition === '') {
            return null;
        }

        return self::MAP[$condition] ?? null;
    }
}
