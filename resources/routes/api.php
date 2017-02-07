<?php

return [
    // api
    'admin/translations/api/filters'            => 'Bitsoflove\TranslationsModule\Http\Controller\Admin\ApiController@filters',
    'admin/translations/api/sheet'              => 'Bitsoflove\TranslationsModule\Http\Controller\Admin\ApiController@sheet',
    'admin/translations/api/save-dev'           => 'Bitsoflove\TranslationsModule\Http\Controller\Admin\ApiController@saveDev',
    'admin/translations/api/save'               => [
        'as'   => 'bitsoflove.translations.save',
        'verb' => 'POST',
        'uses' => 'Bitsoflove\TranslationsModule\Http\Controller\Admin\ApiController@save',
    ],
];
