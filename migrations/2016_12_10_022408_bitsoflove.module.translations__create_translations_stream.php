<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class BitsofloveModuleTranslationsCreateTranslationsStream extends Migration
{

    protected $stream = [
        'slug' => 'translations',
        'title_column' => 'key',

        'translatable' => true,
        'searchable'   => true,
        'trashable'    => true,
        'sortable'     => true,
    ];


    protected $assignments = [
        'key' =>[
            'required' => true,
        ],
        'value' =>[
            'required' => false,
            'translatable' => true,
        ],
    ];

}
