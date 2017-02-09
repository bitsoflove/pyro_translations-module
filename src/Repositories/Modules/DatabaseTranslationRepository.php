<?php
namespace Bitsoflove\TranslationsModule\Repositories\Modules;

use Anomaly\Streams\Platform\Asset\Asset;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Bitsoflove\TranslationsModule\Repositories\Modules\Interfaces\TranslationRepositoryInterface;
use Bitsoflove\TranslationsModule\Translation\Form\TranslationFormBuilder;
use Bitsoflove\TranslationsModule\Translation\Table\TranslationTableBuilder;
use Bitsoflove\TranslationsModule\Translation\TranslationModel;
use Illuminate\Support\Facades\DB;

class DatabaseTranslationRepository implements TranslationRepositoryInterface
{
    public function getAddonTranslations($addonNamespace, $locale, array $parameters=[]) {

        $key = "$addonNamespace::%";
        $results = TranslationModel::where('key', 'LIKE', $key)->get()->keyBy('key');
        $mapped = $results->map(function(TranslationModel $entry) use ($locale) {
            $translation = $entry->translate($locale);
            return empty($translation) ? null : $translation->value;
        })->toArray();

        // ignore null values (stuff that isn't translated into our language yet on the db level)
        $filtered = array_filter($mapped);
        return $filtered;
    }
}
