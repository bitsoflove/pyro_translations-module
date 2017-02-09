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
}
