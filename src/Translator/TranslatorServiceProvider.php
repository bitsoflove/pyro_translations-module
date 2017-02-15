<?php
namespace Bitsoflove\TranslationsModule\Translator;

use Anomaly\Streams\Platform\Model\Translations\TranslationsTranslationsEntryModel;
use Bitsoflove\TranslationsModule\TranslationsModule;
use Bitsoflove\TranslationsModule\Translator\Translator as BolTranslator;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\TranslationServiceProvider;

class TranslatorServiceProvider extends TranslationServiceProvider
{
    public function boot()
    {
        try {
            if(!$this->shouldInitializeCustomTranslator()) {
                parent::register();
                return;
            }

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
        } catch (\Exception $e) {
            Log::critical($e);
        }
    }

    public function register() {
        if($this->shouldInitializeCustomTranslator()) {
            return;
        }

        parent::register();
    }


    protected function shouldInitializeCustomTranslator() {
        $module = app(TranslationsModule::class);

        if (!env('INSTALLED')) {
            return false;
        }

        // for some reason, module is never returning true here...
        //if (!$module->isInstalled()) {
            //return false;
        //}

        // translations streams compiled ?
        $class = TranslationsTranslationsEntryModel::class;

        if(!class_exists($class)) {
            return false;
        }

        die('yep');

    }
}
