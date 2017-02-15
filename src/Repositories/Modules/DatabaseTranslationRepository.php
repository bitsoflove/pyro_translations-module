<?php
namespace Bitsoflove\TranslationsModule\Repositories\Modules;

use Anomaly\Streams\Platform\Asset\Asset;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Bitsoflove\TranslationsModule\Repositories\Modules\Interfaces\TranslationRepositoryInterface;
use Bitsoflove\TranslationsModule\Translation\Form\TranslationFormBuilder;
use Bitsoflove\TranslationsModule\Translation\Table\TranslationTableBuilder;
use Bitsoflove\TranslationsModule\Translation\TranslationModel;
use Bitsoflove\TranslationsModule\Translation\TranslationRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseTranslationRepository implements TranslationRepositoryInterface
{

    protected $translationEntryRepository;

    public function __construct()
    {
        $this->translationEntryRepository = app(TranslationRepository::class);
    }

    public function get($key, array $replace = [], $locale = null, $fallback = true, $fileMatches=null) {
        $results = TranslationModel::where('key', 'LIKE', "$key%")->get()->keyBy('key');
        $mapped = $this->mapTranslationModelsToKeyValues($results, $locale, $replace);
        return $mapped;
    }


    /**
     * This method is mainly here to support the admin view
     */
    public function getAddonTranslations($addonNamespace, $locale, array $parameters=[]) {
        try {
            $key = "$addonNamespace::%";
            $results = TranslationModel::where('key', 'LIKE', $key)->get()->keyBy('key');
            $mapped = $this->mapTranslationModelsToKeyValues($results, $locale, $parameters);
            return $mapped;
        } catch(\Exception $e) {
            Log::error($e);
        }

        return false;
    }

    public function updateOrCreateTranslation($key, $locale, $value) {
        return $this->translationEntryRepository->updateOrCreate($key, $locale, $value);
    }


    private function mapTranslationModelsToKeyValues(Collection $results, $locale, array $parameters) {
        $mapped = $results->keyBy('key')->map(function(TranslationModel $entry) use ($locale) {
            $translation = $entry->translate($locale);
            return empty($translation) ? null : $translation->value;
        })->toArray();

        // ignore null values (stuff that isn't translated into our language yet on the db level)
        $filtered = array_filter($mapped);
        return $filtered;

    }
}
