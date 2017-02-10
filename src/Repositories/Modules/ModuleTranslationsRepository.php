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


    /**
     * todo later: fallback and replace
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true, $fileMatches=null) {

        // 1. we already got our file matches so just query the database
        $dbMatches = $this->dbTranslationsRepo->get($key, $replace, $locale, $fallback);

        // 2. can we find an exact match ? return exact match
        if(isset($dbMatches[$key])) {
            return $dbMatches[$key];
        }

        // 3. no exact match found on the database level ?
        //    3a. if $fileMatches is not empty and not an array, we do have an exact file match on the file driver
        if(!empty($fileMatches) && !is_array($fileMatches)) {
            return $fileMatches;
        }

        //    3b. else return assoc array (merged)
        if(!is_array($fileMatches)) {
            $fileMatches = [];
        }
        if(!is_array($dbMatches)) {
            $dbMatches = [];
        }

        $merged = $this->mergeFileTranslationsWithDatabaseTranslations($fileMatches, $dbMatches);
        return empty($merged) ? null : $merged;
    }


    /**
     * This method is mainly here to support the admin view
     */
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
