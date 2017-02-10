<?php
namespace Bitsoflove\TranslationsModule\Translator;

use Bitsoflove\TranslationsModule\Repositories\Modules\ModuleTranslationsRepository;
use Illuminate\Translation\LoaderInterface;
use Illuminate\Translation\Translator as LaravelTranslator;

class Translator extends LaravelTranslator
{

    protected $moduleTranslationsRepo;

    public function __construct(LoaderInterface $loader, $locale)
    {
        parent::__construct($loader, $locale);

        $this->moduleTranslationsRepo = app(ModuleTranslationsRepository::class);

    }

    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
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
            // this is not a fully qualified namespace ...
            // always using file based translations for these
            return parent::get($key, $replace, $locale, $fallback);
        }

        // get all the translations for this module in the current locale
        $fileMatches = parent::get($key, $replace, $locale, $fallback);

        return $this->moduleTranslationsRepo->get($key, $replace, $locale, $fallback, $fileMatches);
    }

    public function trans($id, array $parameters = [], $domain = 'messages', $locale = null)
    {
        return parent::trans($id, $parameters, $domain, $locale);
    }

    public static function __callStatic($method, $parameters)
    {
        return parent::__callStatic($method, $parameters);
    }

    public function __call($method, $parameters)
    {
        return parent::__call($method, $parameters);
    }

    public function has($key, $locale = null, $fallback = true)
    {
        return parent::has($key, $locale, $fallback);
    }

    public function transChoice($id, $number, array $parameters = [], $domain = 'messages', $locale = null)
    {
        return parent::transChoice($id, $number, $parameters, $domain, $locale);
    }

    public function load($namespace, $group, $locale)
    {
        return parent::load($namespace, $group, $locale);
    }

}
