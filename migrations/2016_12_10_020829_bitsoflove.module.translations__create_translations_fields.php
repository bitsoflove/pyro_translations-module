<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class BitsofloveModuleTranslationsCreateTranslationsFields extends Migration
{

    /**
     * The addon fields.
     *
     * @var array
     */
    protected $fields = [
        'key' => [
            'type' => 'anomaly.field_type.text',
        ],
        'value' => [
            'type' => 'anomaly.field_type.text',
        ],

    ];

}
