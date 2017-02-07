<?php namespace Bitsoflove\TranslationsModule\Models;

use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Addon\FieldType\FieldTypePresenter;
use Anomaly\Streams\Platform\Addon\FieldType\FieldTypeQuery;
use Anomaly\Streams\Platform\Assignment\AssignmentCollection;
use Anomaly\Streams\Platform\Assignment\Contract\AssignmentInterface;
use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Field\Contract\FieldInterface;
use Anomaly\Streams\Platform\Model\EloquentModel;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\ModelObserver;
use Laravel\Scout\Searchable;
use Robbo\Presenter\PresentableInterface;

class Translation extends Model
{
    public function __construct(array $attributes)
    {
        $this->table = env('APPLICATION_REFERENCE') . "_translations";

        parent::__construct($attributes);
    }

    public $fillable = [
        'key',
    ];

    /**
     * The foreign key for translations.
     *
     * @var string
     */
    protected $translationForeignKey = 'translation_id';




}
