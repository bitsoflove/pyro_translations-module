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
                $loader = app()->make('translation.loader');//$app['translation.loader'];
                $locale = $app['config']['app.locale'];
                $trans  = new BolTranslator($loader, $locale);
                $trans->setFallback($app['config']['app.fallback_locale']);

                return $trans;
            });


            $this->configureStreamBasedTranslations();

        } catch (\Exception $e) {
            Log::critical($e);
        }
    }

    /**
     * Stream-based translations must be configured afterwards
     * See https://github.com/anomalylabs/streams-platform/blob/1.2/src/Application/Command/ConfigureTranslator.php
     */
    private function configureStreamBasedTranslations()
    {
        $name = str_slug(config('app.name'));
        $publishedPath = resource_path("$name/streams/lang");
        $originalPath = base_path('vendor/anomaly/streams-platform/resources/lang');

        $path = file_exists($publishedPath) ? $publishedPath : $originalPath;
        $this->app->make('translator')->addNamespace('streams', $path);
    }

}
