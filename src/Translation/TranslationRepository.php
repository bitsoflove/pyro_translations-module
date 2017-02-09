<?php namespace Bitsoflove\TranslationsModule\Translation;

use Bitsoflove\TranslationsModule\Translation\Contract\TranslationRepositoryInterface;
use Anomaly\Streams\Platform\Entry\EntryRepository;

class TranslationRepository extends EntryRepository implements TranslationRepositoryInterface
{

    /**
     * The entry model.
     *
     * @var TranslationModel
     */
    protected $model;

    /**
     * Create a new TranslationRepository instance.
     *
     * @param TranslationModel $model
     */
    public function __construct(TranslationModel $model)
    {
        $this->model = $model;
    }


    public function updateOrCreate($key, $locale, $value) {
        try {

            // this should happen via the pyro translations class
            // because pyro will clear cache when fetching

            $translationModel = $this->model->firstOrCreate(['key' => $key]);
            $translationTranslationModel = $translationModel->translateOrNew($locale);

            $translationTranslationModel->value = $value;
            $translationTranslationModel->save();

            return true;
        } catch(\Exception $e) {
            Log::error($e, compact('key', 'locale', 'value'));
        }

        return false;
    }
}
