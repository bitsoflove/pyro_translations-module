<?php

return [
    // api
    'admin/translations/api/filters'            => 'Bitsoflove\TranslationsModule\Http\Controller\Admin\ApiController@filters',
    'admin/translations/api/sheet/modules'      => 'Bitsoflove\TranslationsModule\Http\Controller\Admin\ApiController@moduleSheet',
    'admin/translations/api/sheet/streams'      => 'Bitsoflove\TranslationsModule\Http\Controller\Admin\ApiController@streamSheet',

    'admin/translations/api/save-dev'           => 'Bitsoflove\TranslationsModule\Http\Controller\Admin\ApiController@saveDev',
    'admin/translations/api/save/streams'       => [
        'as'   => 'bitsoflove.translations.save',
        'verb' => 'POST',
        'uses' => 'Bitsoflove\TranslationsModule\Http\Controller\Admin\ApiController@saveStreamTranslations',
    ],
    'admin/translations/api/save/modules'       => [
        'as'   => 'bitsoflove.translations.save',
        'verb' => 'POST',
        'uses' => 'Bitsoflove\TranslationsModule\Http\Controller\Admin\ApiController@saveModuleTranslations',
    ],
];
