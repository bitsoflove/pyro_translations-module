# PyroCMS stream-based translation module (alpha)
Generic streams translations module for PyroCMS 3.1 and up
(using [react-handsontable](https://github.com/handsontable/react-handsontable))


> **Heads up!**: Currently still in Alpha state. It works, but there are no automated tests yet, and the package is still subject to code review & refactor.

## Installation
- `composer require bitsoflove/translations-module`
- `php artisan module:install translations`
- navigate to the `config/app.php` providers array. Replace Laravel's default `TranslationServiceProvider` with the `TranslatorServiceProvider` from this package.


    ~~`Illuminate\Translation\TranslationServiceProvider::class,`~~
    `\Bitsoflove\TranslationsModule\Translator\TranslatorServiceProvider::class,`

You might still have to run the build script

- `cd core/bitsoflove/translations-module && npm install && npm run dist`

## Configuration

First, you'll have to publish the config file:
- `php artisan addon:publish bitsoflove.module.translations`

By default, after installing this module, every admin will be able to translate all streams.
To allow only a subset of that list, update the published config file accordingly:

```php
<?php return [

    'middleware' => [
        // list any middlewares you want to use here
        // you can use middleware to manipulate this config before the page gets rendered

        //TranslationsModuleMiddleware::class,
    ],

    'streams' => [

        /**
         * Possible values:
         *
         * 'all' or an array of EntryModel classes
         */
        'models' => [
            [
                'model' => YourModel::class,
                'fields' => ['title'], // just allow translation of the title field
                'default' => true,
            ],
            [
                'model' => YourSecondModel::class,
                'fields' => [], // all fields
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
```
## Developing
1. Instead of using `composer require`, manually add the following entry to the composer 'require' list: `"bitsoflove/translations-module": "dev-master"`
2. Add this repository to the composer repositories list (to ensure you'll fetch the repo)
```
    "repositories": [
        {
            "url": "https://github.com/bitsoflove/pyro_translations-module.git",
            "type": "git"
        }
    ],
```

Then:
- `composer install`
- `php artisan module:install translations`
- `cd core/bitsoflove/translations-module`
- `cp .env.example.js .env.js`
- Change the [proxy](https://github.com/bitsoflove/pyro_translations-module/blob/master/.env.example.js#L4) property in `.env.js` to the host of your local pyro application
- `npm install`
- `npm run dev`

## Roadmap


~~**0.0.1**~~

- ~~Reusable proof of concept~~
- ~~Supports middleware~~
- ~~Supports file translations (overwrites `Lang::get()` and `trans()` using extended `Translator`)~~

**0.0.2**
- code review & refactor
- performance updates
- add autosave option
- ui updates (loader, general styling)

**0.0.3 till 0.1.0**

- support laravel `fallback` and `replace` options
- ensure all dist assets are both in the repo and up to date
- google auto translate
- view entry (streams)
- tests

**Under consideration**

- revisions
- add default seeder
