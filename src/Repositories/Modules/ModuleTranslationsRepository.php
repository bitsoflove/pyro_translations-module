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

        // let's not be silly
        if(!is_string($key)) {
            return null;
        }

        // 1. we already got our file matches so just query the database
        $dbMatches = $this->dbTranslationsRepo->get($key, $replace, $locale, $fallback);

        // 2. can we find an exact match on the database level? return exact match
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

        // before we can return a merged array, we need to remove the prefix keys from the database result,
        $sanitizedDbMatches = $this->sanitizeDbMatches($key, $dbMatches);

        // and merge the two recursively, distinct
        $merged = $this->mergeFileTranslationsWithDatabaseTranslations($fileMatches, $sanitizedDbMatches);

        // that's it
        return empty($merged) ? null : $merged;
    }


    /**
     * This method is mainly here to support the admin view
     */
    public function getAddonTranslations($addonNamespace, $locale, array $parameters=[]) {
        $fileTranslations = $this->fileTranslationsRepo->getAddonTranslations($addonNamespace, $locale, $parameters);
        $databaseTranslations = $this->dbTranslationsRepo->getAddonTranslations($addonNamespace, $locale, $parameters);

        //    3b. else return assoc array (merged)
        if(!is_array($fileTranslations)) {
            $fileTranslations = [];
        }
        if(!is_array($databaseTranslations)) {
            $databaseTranslations = [];
        }

        $merged = $this->mergeFileTranslationsWithDatabaseTranslations($fileTranslations, $databaseTranslations);
        return $merged;
    }

    private function mergeFileTranslationsWithDatabaseTranslations(array $fileTranslations, array $databaseTranslations) {
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

    /**
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):
     *
     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('org value', 'new value'));
     *
     * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('new value'));
     *
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function.
     *
     * @param array $array1
     * @param array $array2
     * @return array
     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
     */
    private function arrayMergeRecursiveDistinct ( array &$array1, array &$array2 )
    {
        $merged = $array1;

        foreach ( $array2 as $key => &$value )
        {
            if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
            {
                $merged [$key] = $this->arrayMergeRecursiveDistinct ( $merged [$key], $value );
            }
            else
            {
                $merged [$key] = $value;
            }
        }

        return $merged;
    }

    private function sanitizeDbMatches($key, array $dbMatches)
    {
        $sanitized = [];
        foreach($dbMatches as $dbKey => $value) {
            // handle case: ErrorException: strpos(): Empty needle
            if(empty($key) || empty($dbKey)) {
                continue;
            }

            $value = (string) $value; // handle NULL
            $sanitizedKey = str_replace_first($key, '' , $dbKey);
            $sanitizedKey = ltrim($sanitizedKey, '.'); // remove leading dots
            array_set($sanitized, $sanitizedKey, $value);
        }

        return $sanitized;
    }
}
