<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class BitsofloveModuleTranslationsCreateTranslationsStream extends Migration
{

    /**
     * The stream definition.
     *
     * @var array
     */
    protected $stream = [
        'slug' => 'translations',
    ];

    /**
     * The stream assignments.
     *
     * @var array
     */
    protected $assignments = [];

}
