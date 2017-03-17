<?php
namespace Bitsoflove\TranslationsModule\Translator;

use Bitsoflove\TranslationsModule\Repositories\Modules\ModuleTranslationsRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\LoaderInterface;
use Illuminate\Translation\Translator as LaravelTranslator;

class Translator extends LaravelTranslator
{
    protected $moduleTranslationsRepo;

    public function __construct(LoaderInterface $loader, $locale)
    {
        parent::__construct($loader, $locale);

        $this->initializeDependencies();
    }

    private function initializeDependencies()
    {
        try {
            if(! class_exists('\Anomaly\Streams\Platform\Model\Translations\TranslationsTranslationsEntryModel')) {
                throw new \Exception("TranslationsTranslationsEntryModel has not been compiled yet. Execute php artisan streams:compile");
            }

            $this->moduleTranslationsRepo = app(ModuleTranslationsRepository::class);
        } catch(\Exception $e) {
            // this might happen when streams:compile hasn't been executed yet
            Log::error($e);
        }
    }

    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        try {

            // fallback to default behaviour if the translations model hasn't been compiled yet
            if(! class_exists('\Anomaly\Streams\Platform\Model\Translations\TranslationsTranslationsEntryModel')) {
                return parent::get($key, $replace, $locale, $fallback);
            }

            if (empty($locale)) {
                $locale = app()->getLocale();
            }
            // if no double colons found, it's not a module translation,
            // so default back to the original behaviour. (currently not supporting those)
            $moduleNamespaceSplitPosition = strpos($key, '::');
            $isModuleTranslation          = $moduleNamespaceSplitPosition >= 0;
            if (!$isModuleTranslation) {
                return parent::get($key, $replace, $locale, $fallback);
            }

            // so it seems we're requesting a module translation.. find out which module
            $moduleNamespace = substr($key, 0, $moduleNamespaceSplitPosition);
            if ($moduleNamespace === 'streams') {
                // this is not a fully qualified module namespace ...
                // always fall back to the original implementation for these
                //
                // might be better to check fully-qualified-module-namespaces by counting 2 dots in the moduleNamespace
                return parent::get($key, $replace, $locale, $fallback);
            }

            // get all the translations for this module in the current locale
            $fileMatches = parent::get($key, $replace, $locale, $fallback);
            return $this->moduleTranslationsRepo->get($key, $replace, $locale, $fallback, $fileMatches);
        } catch(\Exception $e) {
            Log::critical($e);

            // fallback to default behaviour
            return parent::get($key, $replace, $locale, $fallback);
        }
    }

    public function trans($id, array $parameters = [], $domain = 'messages', $locale = null)
    {
        return $this->get($id, $parameters, $locale);
    }
}
