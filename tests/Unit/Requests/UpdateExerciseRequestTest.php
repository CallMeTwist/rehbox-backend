<?php

use App\Http\Requests\UpdateExerciseRequest;

function validateExerciseInput(array $data): \Illuminate\Validation\Validator
{
    $req = UpdateExerciseRequest::create('/exercises', 'POST', $data);
    $req->setContainer(app())->setRedirector(app('redirect'));

    // getValidatorInstance() builds the validator with rules() + messages()
    // AND runs withValidator(), so cross-field rules fire here.
    $reflector = new ReflectionMethod($req, 'getValidatorInstance');
    $reflector->setAccessible(true);

    return $reflector->invoke($req);
}

it('passes for a valid paid+upload exercise', function () {
    $v = validateExerciseInput([
        'title' => 'Glute Bridge',
        'area' => 'back',
        'category' => 'strengthening',
        'access_tier' => 'paid',
        'video_source' => 'upload',
        'video_path' => 'exercises/videos/back/strengthening/glute.mp4',
    ]);
    expect($v->passes())->toBeTrue();
});

it('passes for a valid free+youtube general exercise', function () {
    $v = validateExerciseInput([
        'title' => 'Squat',
        'area' => 'general',
        'category' => 'legs',
        'access_tier' => 'free',
        'video_source' => 'youtube',
        'youtube_url' => 'https://www.youtube.com/watch?v=abcdefghijk',
    ]);
    expect($v->passes())->toBeTrue();
});

it('fails when area=general but access_tier is paid', function () {
    $v = validateExerciseInput([
        'title' => 'X', 'area' => 'general', 'category' => 'legs',
        'access_tier' => 'paid', 'video_source' => 'youtube',
        'youtube_url' => 'https://www.youtube.com/watch?v=abcdefghijk',
    ]);
    expect($v->fails())->toBeTrue();
    expect($v->errors()->first('access_tier'))->toContain('General Exercises must be set to the free tier');
});

it('fails when paid tier has youtube source', function () {
    $v = validateExerciseInput([
        'title' => 'X', 'area' => 'back', 'category' => 'strengthening',
        'access_tier' => 'paid', 'video_source' => 'youtube',
        'youtube_url' => 'https://www.youtube.com/watch?v=abcdefghijk',
    ]);
    expect($v->fails())->toBeTrue();
});

it('fails when paid+upload but video_path is missing', function () {
    $v = validateExerciseInput([
        'title' => 'X', 'area' => 'back', 'category' => 'strengthening',
        'access_tier' => 'paid', 'video_source' => 'upload',
    ]);
    expect($v->fails())->toBeTrue();
    expect($v->errors()->has('video_path'))->toBeTrue();
});

it('fails when youtube_url is not a youtube domain', function () {
    $v = validateExerciseInput([
        'title' => 'X', 'area' => 'general', 'category' => 'legs',
        'access_tier' => 'free', 'video_source' => 'youtube',
        'youtube_url' => 'https://vimeo.com/12345',
    ]);
    expect($v->fails())->toBeTrue();
    expect($v->errors()->has('youtube_url'))->toBeTrue();
});
