<?php

return [
    // api
    //'admin/module-translations/api/test'            => 'Bitsoflove\TranslationsModule\Http\Controller\Admin\ApiController@filters',

    'admin/translations/'                  => function() {
        $url = "/admin/translations/modules";
        return redirect($url);
    },
    'admin/translations/streams'           => 'Bitsoflove\TranslationsModule\Http\Controller\Admin\FrontController@streams',
    'admin/translations/modules'           => 'Bitsoflove\TranslationsModule\Http\Controller\Admin\FrontController@modules',

];
