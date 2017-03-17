<?php
namespace Bitsoflove\TranslationsModule\Translator;

use Bitsoflove\TranslationsModule\Translator\Translator as BolTranslator;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\TranslationServiceProvider;

class TranslatorServiceProvider extends TranslationServiceProvider
{
    public function boot()
    {
        try {
            // remove any existing translators
            $this->app->offsetUnset('translation.loader');
            $this->app->offsetUnset('translator');

            // register our own translator
            $this->registerLoader();
            $this->app->singleton('translator', function ($app) {
                $loader = $app['translation.loader'];
                $locale = $app['config']['app.locale'];
                $trans  = new BolTranslator($loader, $locale);
                $trans->setFallback($app['config']['app.fallback_locale']);

                // Stream-based translations must be configured afterwards
                // See https://github.com/anomalylabs/streams-platform/blob/1.2/src/Application/Command/ConfigureTranslator.php
                $streamsPath = base_path('vendor/anomaly/streams-platform/resources/lang');
                $trans->addNamespace('streams', $streamsPath);

                return $trans;
            });
        } catch (\Exception $e) {
            Log::critical($e);
        }
    }

}
