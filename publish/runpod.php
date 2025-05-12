<?php

declare(strict_types=1);

use function Hyperf\Support\env;

return [
    'default' => [
        'base_uri' => env('RUN_POD_BASE_URI', 'https://api.runpod.io/v2'),
        'token' => env('RUN_POD_TOKEN', ''),
        'timeout' => env('RUN_POD_TIMEOUT', 10),
    ],
];
