<?php
namespace Bitsoflove\TranslationsModule\Services;

use Anomaly\Streams\Platform\Assignment\AssignmentModel;
use Anomaly\Streams\Platform\Entry\EntryModel;
use Anomaly\Streams\Platform\Stream\StreamModel;
use Illuminate\Database\Eloquent\Collection;

class SheetService
{
    public function getData($streamIds, $baseLocale, $locales)
    {
        $allLocales = array_merge([$baseLocale], $locales);
        $streams    = $this->getStreams($streamIds);

        $dataByStream = [];
        foreach ($streams as $stream) {
            $dataByStream[] = [
                'id'         => $stream->id,
                'slug'       => $stream->slug,
                'namespace'  => $stream->namespace,
                'identifier' => $stream->namespace . '.' . $stream->slug,
                'entries'    => $this->getStreamEntries($stream, $allLocales),
            ];
        }

        return $dataByStream;
    }

    private function getStreamEntries(StreamModel $stream, array $locales)
    {
        $translatableAssignments = $stream->assignments()->where('translatable', 1)->with(['field'])->get();
        $fields                  = $translatableAssignments->map(function (AssignmentModel $model) {
            return $model->field;
        });

        $entryModel = $stream->getEntryModel();
        $entries    = $entryModel->all();

        $streamData = [];
        foreach ($entries as $entry) {
            $entryData    = $this->getEntryData($entry, $locales, $fields);
            $streamData[] = [
                'id'    => $entry->id,
                'model' => get_class($entryModel),
                'entry' => $entryData,
            ];
        }
        return $streamData;
    }

    private function getEntryData(EntryModel $entry, array $locales, Collection $fields)
    {
        $entryData = [];
        foreach ($locales as $locale) {
            //$entryDataByLocale[$locale] = [];
            $translation = $entry->translate($locale);

            foreach ($fields as $field) {
                $slug = $field->slug;

                if (!isset($entryData[$slug])) {
                    $entryData[$slug] = [];
                }
                if (!isset($entryData[$slug][$locale])) {
                    $entryData[$slug][$locale] = [];
                }

                $entryData[$slug][$locale] = is_null($translation) ? null : $translation->$slug;
            }
        }
        return $entryData;
    }

    private function getStreams($streamIds)
    {
        return StreamModel::whereIn('id', $streamIds)
            ->get()
            ->keyBy('id');
    }
}
