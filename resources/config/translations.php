<?php return [

    'streams' => [

        /**
         * Possible values:
         *
         * 'none', 'all' or an array of EntryModel classes
         */
        'models' => [
            /*
            [
                'model' => \Puratos\SolutionsModule\Solution\SolutionModel::class,
                //'fields' => null,
                'default' => true,
            ],
            */
            [
                'model' => \Puratos\FiltersModule\IngredientType\IngredientTypeModel::class,
                'fields' => ['title'],
                'default' => true,
            ],
            [
                'model' => \Puratos\FiltersModule\Ingredient\IngredientModel::class,
                'fields' => [],
                'default' => false,
            ],
            [
                'model' => \Puratos\FiltersModule\IngredientFilter\IngredientFilterModel::class,
                'fields' => null,
                'default' => false,
            ],
            [
                'model' => \Puratos\FiltersModule\IngredientFilterOption\IngredientFilterOptionModel::class,
                'fields' => [],
                'default' => false,
            ],
        ],

        'locales' => [
            'default' => config('streams::locales.default'),

            'supported' => array_keys(
                config('streams::locales.supported')
            ),
        ],
    ]
];