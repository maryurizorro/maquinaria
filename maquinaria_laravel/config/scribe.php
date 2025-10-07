<?php

use Knuckles\Scribe\Extracting\Strategies;
use Knuckles\Scribe\Config\Defaults;
use Knuckles\Scribe\Config\AuthIn;
use function Knuckles\Scribe\Config\{removeStrategies, configureStrategy};

return [

    /*
    |--------------------------------------------------------------------------
    | Información general del API
    |--------------------------------------------------------------------------
    */

    'title' => config('app.name') . ' API Documentation',
    'description' => 'Documentación completa de la API generada automáticamente con Scribe.',
    'intro_text' => <<<INTRO
        Bienvenido a la documentación de la API.

        <aside>
        En el panel derecho encontrarás ejemplos de código en diferentes lenguajes.
        Puedes probar los endpoints directamente con el botón <b>Try it out</b>.
        </aside>
    INTRO,

    /*
    |--------------------------------------------------------------------------
    | URL base
    |--------------------------------------------------------------------------
    */
    'base_url' => config('app.url'),

    /*
    |--------------------------------------------------------------------------
    | Rutas incluidas en la documentación
    |--------------------------------------------------------------------------
    */
    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/*'],
                'domains' => ['*'],
            ],
            'include' => [
                // Ejemplo: 'users.index', 'POST /login'
            ],
            'exclude' => [
                // Ejemplo: 'GET /health'
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipo de documentación generada
    |--------------------------------------------------------------------------
    */
    'type' => 'laravel', // 'static', 'external_static', 'external_laravel'

    'theme' => 'default',

    'static' => [
        'output_path' => 'public/docs',
    ],

    'laravel' => [
        'add_routes' => true,
        'docs_url' => '/docs',
        'assets_directory' => null,
        'middleware' => [], // Puedes agregar 'auth' si quieres proteger la vista
    ],

    'external' => [
        'html_attributes' => []
    ],

    /*
    |--------------------------------------------------------------------------
    | "Try It Out" (probar endpoints desde el navegador)
    |--------------------------------------------------------------------------
    */
    'try_it_out' => [
        'enabled' => true,
        'base_url' => null,
        'use_csrf' => false,
        'csrf_url' => '/sanctum/csrf-cookie',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autenticación
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'enabled' => true, // activa si tu API requiere auth
        'default' => true,
        'in' => AuthIn::BEARER->value, // tipo Bearer Token
        'name' => 'Authorization',
        'use_value' => env('SCRIBE_AUTH_KEY', 'Bearer {YOUR_TOKEN}'),
        'placeholder' => '{YOUR_AUTH_KEY}',
        'extra_info' => 'Para acceder, usa un token válido obtenido desde tu panel de usuario o endpoint de login.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Lenguajes de ejemplo en el código
    |--------------------------------------------------------------------------
    */
    'example_languages' => [
        'bash',
        'javascript',
        'php',
        'python',
    ],

    /*
    |--------------------------------------------------------------------------
    | Postman y OpenAPI
    |--------------------------------------------------------------------------
    */
    'postman' => [
        'enabled' => true,
        'overrides' => [
            'info.version' => '1.0.0',
        ],
    ],

    'openapi' => [
        'enabled' => true,
        'overrides' => [
            'info.version' => '1.0.0',
        ],
        'generators' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Agrupación de endpoints
    |--------------------------------------------------------------------------
    */
    'groups' => [
        'default' => 'Endpoints',
        'order' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logo personalizado (opcional)
    |--------------------------------------------------------------------------
    */
    'logo' => false, // o 'img/logo.png' si tienes uno

    /*
    |--------------------------------------------------------------------------
    | Última actualización mostrada
    |--------------------------------------------------------------------------
    */
    'last_updated' => 'Última actualización: {date:F j, Y}',

    /*
    |--------------------------------------------------------------------------
    | Ejemplos generados automáticamente
    |--------------------------------------------------------------------------
    */
    'examples' => [
        'faker_seed' => 1234,
        'models_source' => ['factoryCreate', 'factoryMake', 'databaseFirst'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Estrategias de extracción (Scribe)
    |--------------------------------------------------------------------------
    */
    'strategies' => [
        'metadata' => [
            ...Defaults::METADATA_STRATEGIES,
        ],

        'headers' => [
            ...Defaults::HEADERS_STRATEGIES,
            Strategies\StaticData::withSettings(data: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]),
        ],

        'urlParameters' => [
            ...Defaults::URL_PARAMETERS_STRATEGIES,
        ],

        'queryParameters' => [
            ...Defaults::QUERY_PARAMETERS_STRATEGIES,
        ],

        'bodyParameters' => [
            ...Defaults::BODY_PARAMETERS_STRATEGIES,
        ],

        'responses' => configureStrategy(
            Defaults::RESPONSES_STRATEGIES,
            Strategies\Responses\ResponseCalls::withSettings(
                only: ['GET *'],
                config: [
                    'app.debug' => false,
                ]
            )
        ),

        'responseFields' => [
            ...Defaults::RESPONSE_FIELDS_STRATEGIES,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de base de datos para response calls
    |--------------------------------------------------------------------------
    */
    'database_connections_to_transact' => [config('database.default')],

    /*
    |--------------------------------------------------------------------------
    | Configuración adicional (Fractal)
    |--------------------------------------------------------------------------
    */
    'fractal' => [
        'serializer' => null,
    ],
];
