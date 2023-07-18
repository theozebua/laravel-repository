<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Repository Locations
    |--------------------------------------------------------------------------
    |
    | This is the location where your repository files at.
    | You can change the value inside the `app_path` to
    | wherever you want.
    |
    | e.g. `'interfaces' => app_path('Repositories/Contracts')`
    */
    'directories' => [

        'interfaces' => app_path('Repositories/Interfaces'),

        'repositories' => app_path('Repositories/Implementations'),

    ],

    /*
    |--------------------------------------------------------------------------
    | String Delimiter
    |--------------------------------------------------------------------------
    |
    | This is used by string helper to determine the
    | fully qualified class name from the given path.
    | For the most of time, you don't need to change this.
    */
    'delimiter' => 'app',

];
