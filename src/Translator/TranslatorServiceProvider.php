<?php namespace Bitsoflove\TranslationsModule\Translator;

use Illuminate\Translation\TranslationServiceProvider;
use Bitsoflove\TranslationsModule\Translator\Translator as BolTranslator;
use Illuminate\Translation\Translator as LaravelTranslator;
use Symfony\Component\Translation\TranslatorInterface;

class TranslatorServiceProvider extends TranslationServiceProvider
{
    public function boot()
    {
        $this->app->offsetUnset('translation.loader');
        $this->app->offsetUnset('translator');

        $this->registerLoader();

        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];

            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            $locale = $app['config']['app.locale'];

            $trans = new BolTranslator($loader, $locale);
            $trans->setFallback($app['config']['app.fallback_locale']);
            return $trans;
        });

    }
}