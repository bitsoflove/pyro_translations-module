<?php return [

    'middleware' => [
        // list any middlewares you want to use here
        // you can use middleware to manipulate this config on the fly

        //TranslationsModuleMiddleware::class,
    ],

    'streams' => [

        /**
         * Possible values:
         *
         * 'all' or an array of EntryModel classes
         */
        'models' => 'all',

        /*
        'models' => [
                'model' => YourEntryModel::class,
                'fields' => ['title'],                     // only allow 'title' field
                'default' => true,                         // auto select
            ],
            [
                'model' => YourSecondEntryModel::class,
                'fields' => [],                           // all translatable fields
                'default' => false,                       // do not auto select
            ]
        ],
        */

        'locales' => [
            'default' => config('streams::locales.default'),

            'supported' => array_keys(
                config('streams::locales.supported')
            ),
        ],
    ]
];