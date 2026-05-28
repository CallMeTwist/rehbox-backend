<?php

use App\Support\ConditionAreaMap;

it('maps known conditions to areas', function () {
    expect(ConditionAreaMap::areaFor('lower_back_pain'))->toBe('back')
        ->and(ConditionAreaMap::areaFor('knee_pain'))->toBe('lower_limbs')
        ->and(ConditionAreaMap::areaFor('shoulder_impingement'))->toBe('upper_limbs')
        ->and(ConditionAreaMap::areaFor('neck_strain'))->toBe('head_neck');
});

it('returns null for unknown or empty conditions', function () {
    expect(ConditionAreaMap::areaFor('unknown_condition'))->toBeNull()
        ->and(ConditionAreaMap::areaFor(null))->toBeNull()
        ->and(ConditionAreaMap::areaFor(''))->toBeNull();
});
