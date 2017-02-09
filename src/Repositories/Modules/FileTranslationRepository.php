<?php
namespace Bitsoflove\TranslationsModule\Repositories\Modules;

use Anomaly\Streams\Platform\Addon\Addon;
use Anomaly\Streams\Platform\Addon\AddonCollection;
use Bitsoflove\TranslationsModule\Repositories\Modules\Interfaces\TranslationRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\Translator;

class FileTranslationRepository implements TranslationRepositoryInterface
{
    public function getAddonTranslations($addonNamespace, $locale, array $parameters=[]) {

        //can this be done more efficiently ?
        $addonCollection = app(AddonCollection::class);
        $addon = $addonCollection->where('namespace', $addonNamespace)->first();

        $langPath = $addon->getPath('resources/lang');
        $namespace = $addon->getNamespace();
        $dir = $langPath . "/$locale";

        // ensure all keys known
        // can this be done more efficiently ?
        $fileTranslationKeys = $this->getFileTranslationKeys($addon);
        $dotNotations = [];
        foreach($fileTranslationKeys as $key) {
            $dotNotations[$key] = null;
        }

        // fill translations
        $this->appendFileTranslationsInDotNotationFromFolder($namespace, $dir, [], $dotNotations);
        return $dotNotations;
    }
























    private function getFileTranslationKeys(Addon $addon) {
        $langPath = $addon->getPath('resources/lang');
        $namespace = $addon->getNamespace();

        $dotNotations = $this->getAllFileTranslationsInDotNotation($namespace, $langPath);
        $allKeys = $this->getAllTranslationKeys($dotNotations);

        return $allKeys;
    }


    private function getAllTranslationKeys(array $dotNotationsByLocale) {
        $merged = [];
        foreach($dotNotationsByLocale as $locale => $dotNotations) {
            $merged = array_merge($merged, $dotNotations);
        }
        return array_keys($merged);
    }


    private function getAllFileTranslationsInDotNotation($namespace, $langPath) {
        // first find all directories, these are our default supported languages
        $dirs = array_filter(glob("$langPath/*"), 'is_dir');

        $dotNotationsByLang = [];
        foreach($dirs as $dir) {
            $dirSegments = explode('/', $dir);

            $lang = array_pop($dirSegments);
            $segments = [];

            $dotNotations = [];
            $this->appendFileTranslationsInDotNotationFromFolder($namespace, $dir, $segments, $dotNotations);
            $dotNotationsByLang[$lang] = $dotNotations;
        }

        return $dotNotationsByLang;
    }


    private function appendFileTranslationsInDotNotationFromFolder($namespace, $langPath, $segments, array &$existingNotations = [])
    {
        $files = array_filter(glob("$langPath/*"), 'is_file');


        // first find all .php files in this folder, add keys to the $existingNotations (throw exception on duplicates)
        foreach($files as $file) {
            // we're just interested in the actual filename...
            $fileExpl = explode('/', $file);
            $filename = array_pop($fileExpl);

            $this->appendFileTranslationsInDotNotation($namespace, $langPath, $segments, $filename, $existingNotations);
        }

        // then find all folders in this folder. Call getAllNotations again
        $folders = array_filter(glob("$langPath/*"), 'is_dir');
        foreach($folders as $folder) {
            $segmentsCopy = $segments; // create copy

            // we're just interested in the actual foldername...
            $folderExpl = explode('/', $folder);
            $foldername = array_pop($folderExpl);

            $folderPath = "$langPath/$foldername";
            $segmentsCopy[] = $foldername;

            $this->appendFileTranslationsInDotNotationFromFolder($namespace, $folderPath, $segmentsCopy, $existingNotations);
        }
    }

    private function appendFileTranslationsInDotNotation($namespace, $directory, $segments, $filename, &$existingNotations)
    {
        try {
            $fullFilename = "$directory/$filename";
            $translations = include($fullFilename);

            $segmentsImpl = implode('.', $segments);
            $prefix = empty($segments) ? '' : '.';
            $filenameWithoutExtension = substr($filename, 0, -4);

            $partialKey = '' . $namespace . '::' . $segmentsImpl . $prefix . $filenameWithoutExtension;
            $this->addDotNotationsFromFile($partialKey, $translations, $existingNotations);
        } catch(\Exception $e) {
            Log::error($e);
        }
    }

    private function addDotNotationsFromFile($partialKey, array $translationsArray, array &$existingNotations) {
        foreach($translationsArray as $key => $translation) {
            try {
                $newKey =  "$partialKey.$key";

                if(is_array($translation)) {
                    return $this->addDotNotationsFromFile($newKey, $translation, $existingNotations);
                }

                if(isset($existingNotations[$newKey])) {
                    throw new \Exception("Key was already added: $newKey");
                }

                $existingNotations[$newKey] = $translation;
            } catch(\Exception $e) {
                Log::warning($e);
            }
        }
    }
}
