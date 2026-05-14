<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Exercise video disk
    |--------------------------------------------------------------------------
    |
    | Which Laravel filesystem disk stores uploaded exercise videos.
    | Start on 'public' (local storage with storage:link). Swap to 's3' later
    | by setting EXERCISE_VIDEO_DISK in .env — no code changes needed.
    |
    */
    'exercise_video_disk' => env('EXERCISE_VIDEO_DISK', 'public'),
];
