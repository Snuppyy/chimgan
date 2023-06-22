<?php

use MoonShine\Exceptions\MoonShineNotFoundException;
use MoonShine\Models\MoonshineUser;

return [
  'dir' => 'app/MoonShine',
  'namespace' => 'App\MoonShine',

  'title' => env('MOONSHINE_TITLE', 'Chimgan CRM'),
  'logo' => env('MOONSHINE_LOGO', '/images/logo.png'),

  'route' => [
    'prefix' => env('MOONSHINE_ROUTE_PREFIX', '/'),
    'middleware' => ['moonshine'],
    'custom_page_slug' => 'custom_page',
    'notFoundHandler' => MoonShineNotFoundException::class
  ],
  'use_migrations' => true,
  'use_notifications' => true,
  'auth' => [
    'enable' => true,
    'fields' => [
      'username' => 'email',
      'password' => 'password',
      'name' => 'name',
      'avatar' => 'avatar'
    ],
    'guard' => 'moonshine',
    'guards' => [
      'moonshine' => [
        'driver' => 'session',
        'provider' => 'moonshine',
      ],
    ],
    'providers' => [
      'moonshine' => [
        'driver' => 'eloquent',
        'model' => MoonshineUser::class,
      ],
    ],
    'footer' => ''
  ],
  'locales' => [
    'ru', 'uz'
  ],
  'middlewares' => [],
  'tinymce' => [
    'file_manager' => false, // or 'laravel-filemanager' prefix for lfm
    'token' => env('MOONSHINE_TINYMCE_TOKEN', ''),
    'version' => env('MOONSHINE_TINYMCE_VERSION', '6')
  ],
  'socialite' => [
    // 'driver' => 'path_to_image_for_button'
  ],
  'header' => null, // blade path
  'footer' => []
];
