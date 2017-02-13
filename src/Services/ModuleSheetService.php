<?php
namespace Bitsoflove\TranslationsModule\Services;

use Anomaly\Streams\Platform\Assignment\AssignmentModel;
use Anomaly\Streams\Platform\Entry\EntryModel;
use Anomaly\Streams\Platform\Stream\StreamModel;
use Bitsoflove\TranslationsModule\Repositories\Modules\ModuleTranslationsRepository;
use Illuminate\Database\Eloquent\Collection;

class ModuleSheetService
{

    protected $moduleTranslationRepo;

    public function __construct()
    {
        $this->moduleTranslationRepo = app(ModuleTranslationsRepository::class);
    }

    /**
     * Not the most efficient approach (multiple db lookups, multiple nested foreaches)
     * however, this code is only being run to populate the translations sheet, never globally
     *
     * So feel free to improve, but performance doesn't matter that much (for now)
     */
    public function getData($moduleNamespaces, $baseLocale, $locales)
    {

        $modulesTranslations = [];
        foreach($moduleNamespaces as $moduleNamespace) {
            $baseTranslations = $this->moduleTranslationRepo->getAddonTranslations($moduleNamespace, $baseLocale);

            $localeTranslations = [
                $baseLocale => $baseTranslations
            ];
            foreach($locales as $locale) {
                $localeTranslations[$locale] = $this->moduleTranslationRepo->getAddonTranslations($moduleNamespace, $locale);
            }

            $moduleTranslations = $this->mergeLocaleTranslations($localeTranslations);
            $modulesTranslations[$moduleNamespace] = $moduleTranslations;
        }

        return $modulesTranslations;
    }

    private function mergeLocaleTranslations($localeTranslations)
    {
        $allTranslations = [];

        foreach($localeTranslations as $locale => $translations) {
            foreach($translations as $key => $translation) {
                if(!isset($allTranslations[$key])) {
                    $allTranslations[$key] = [];
                }

                $allTranslations[$key][$locale] = $translation;
            }
        }

        return $allTranslations;
    }
}
