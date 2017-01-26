<?php
namespace Bitsoflove\TranslationsModule\Services;

use Anomaly\Streams\Platform\Stream\StreamModel;

class TranslationService
{
    public function save(array $data)
    {

        $success = [];
        $fail    = [];
        foreach ($data as $key => $item) {
            $res = $this->saveItem($item);
            if ($res) {
                $success[$key] = true;
            } else {
                $fail[$key] = true;
            }
        }
        return compact('success', 'fail');
    }

    private function saveItem($item)
    {
        $module   = $item['module'];
        $stream   = $item['stream'];
        $entityId = $item['entityId'];
        $field    = $item['field'];
        $locale   = $item['locale'];
        $value    = $item['value'];

        //1. get stream entity by using module, stream, entityId
        $streamEntity = $this->getStreamEntity($module, $stream, $entityId);

        //2. get entity translation by using $entity->translateOrNew($locale);
        $translation = $streamEntity->translateorNew($locale);

        //3. set value by using $translation->$field = $value
        $translation->$field = $value;

        //4. $translation->save
        return $translation->save();
    }

    private function getStreamEntity($module, $stream, $entityId)
    {
        $stream      = StreamModel::where('namespace', $module)->where('slug', $stream)->first();
        $streamModel = $stream->getEntryModel();
        $entity      = $streamModel->where('id', $entityId)->first();
        return $entity;
    }
}
