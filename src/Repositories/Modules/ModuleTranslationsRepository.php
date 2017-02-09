<?php
namespace Bitsoflove\TranslationsModule\Repositories\Modules;

use Bitsoflove\TranslationsModule\Repositories\Modules\Interfaces\TranslationRepositoryInterface;

class ModuleTranslationsRepository implements TranslationRepositoryInterface
{
    protected $fileTranslationsRepo;
    protected $dbTranslationsRepo;

    public function __construct()
    {
        $this->fileTranslationsRepo = app(FileTranslationRepository::class);
        $this->dbTranslationsRepo = app(DatabaseTranslationRepository::class);
    }

    public function getAddonTranslations($addonNamespace, $locale, array $parameters=[]) {
        $fileTranslations = $this->fileTranslationsRepo->getAddonTranslations($addonNamespace, $locale, $parameters);
        $databaseTranslations = $this->dbTranslationsRepo->getAddonTranslations($addonNamespace, $locale, $parameters);

        $merged = $this->mergeFileTranslationsWithDatabaseTranslations($fileTranslations, $databaseTranslations);
        return $merged;
    }

    private function mergeFileTranslationsWithDatabaseTranslations($fileTranslations, $databaseTranslations) {
        $merged = array_merge($fileTranslations, $databaseTranslations);
        return $merged;
    }

    public function save($data) {
        // always save to the database
        // just doing this one by one. Yes, we should probably batch this

        foreach($data as $entry)  {
            $entry = (array) $entry;
            $key = $entry['identifier'];
            $locale = $entry['locale'];
            $value = $entry['value'];

            $this->dbTranslationsRepo->updateOrCreateTranslation($key, $locale, $value);
        }
    }
}
