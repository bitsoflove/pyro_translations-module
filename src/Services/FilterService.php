<?php
namespace Bitsoflove\TranslationsModule\Services;

use Anomaly\Streams\Platform\Assignment\AssignmentModel;
use Anomaly\Streams\Platform\Entry\EntryModel;
use Anomaly\Streams\Platform\Field\FieldModel;
use Anomaly\Streams\Platform\Stream\StreamModel;

class FilterService
{
    public function getFilters()
    {

        $modelsCfg  = config('bitsoflove.module.translations::translations.streams.models');
        $localesCfg = config('bitsoflove.module.translations::translations.streams.locales');

        $data = [
            'streams'   => $this->getStreams($modelsCfg),
            'languages' => $localesCfg,
        ];
        return $data;
    }

    private function getStreams($modelsCfg)
    {

        $translatableStreamsQuery = StreamModel::where('translatable', 1);
        if ($modelsCfg !== 'all') {

            $modelsCfg = collect($modelsCfg);
            if ( ! empty($modelsCfg->count())) {
                $streamIds = $this->getStreamIdsFromModels($modelsCfg);
                $translatableStreamsQuery->whereIn('id', $streamIds);
            }

        }


        $translatableStreams = $translatableStreamsQuery->get();

        $mapped = $translatableStreams->map(function (StreamModel $stream) use ($modelsCfg) {

            $model = $this->getEntryModelFromStream($stream);

            //1. first find the model defined in $modelsCfg that is an instance of $model
            $modelCfg      = $this->getModelCfg($model, $modelsCfg);
            $allowedFields = $this->getAllowedFields($model, $modelCfg);

            $assignments = $stream->assignments()
                ->where('translatable', 1)
                ->whereHas('field', function ($q) use ($allowedFields) {
                    $q->whereIn('id', $allowedFields->keys());
                })
                ->get();

            return [
                'id'         => $stream->id,
                'namespace'  => $stream->namespace,
                'slug'       => $stream->slug,
                'identifier' => $stream->namespace . '.' . $stream->slug,

                'default' => (bool)isset($modelCfg['default']) ? $modelCfg['default'] : false,

                'assignments' => $assignments->map(function (AssignmentModel $assignment) {
                    return [
                        'id'    => $assignment->id,
                        'field' => $assignment->field->toArray(),
                    ];
                }),
            ];
        });

        return $mapped->values()->toArray();
    }

    private function getStreamIdsFromModels($modelsCfg)
    {
        $streamIds = [];
        foreach ($modelsCfg as $cfg) {

            $model    = $cfg['model'];
            $instance = app($model);

            $streamId    = $this->getStreamIdFromEntryModel($instance);
            $streamIds[] = $streamId;
        }

        return $streamIds;
    }

    private function getStreamIdFromEntryModel(EntryModel $instance)
    {
        return $instance->getStream()->id;
    }

    private function getEntryModelFromStream(StreamModel $stream)
    {
        return $stream->getEntryModel();
    }

    private function getModelCfg(EntryModel $model, $modelsCfg)
    {
        try {
            foreach ($modelsCfg as $modelCfg) {
                $modelCandidateClass = $modelCfg['model'];
                $modelCandidate      = app($modelCandidateClass);

                $modelClass = get_class($model);
                if ($modelCandidate instanceof $modelClass) {
                    return $modelCfg;
                }
            }
        } catch (\Exception $e) {
            \Log::error($e);
        }

        return $model; // probably using option 'all'
    }

    private function getAllowedFields(EntryModel $model, $modelCfg)
    {
        //2. Get all fields via assignments of this model
        $fields = $model->getAssignments()->map(function (AssignmentModel $assignment) {
            return $assignment->field;
        });

        $allowedFields = isset($modelCfg['fields']) ? $modelCfg['fields'] : null;

        if ( ! is_null($allowedFields)) {
            $allowedFields = (array)$allowedFields;
            $fields        = $fields->filter(function (FieldModel $field) use ($allowedFields) {
                $slug = $field->getSlug();
                return in_array($slug, $allowedFields);
            });
        }

        return $fields->keyBy('id');
    }
}
