<?php
namespace Bitsoflove\TranslationsModule\Services;

use Anomaly\Streams\Platform\Assignment\AssignmentModel;
use Anomaly\Streams\Platform\Entry\EntryModel;
use Anomaly\Streams\Platform\Stream\StreamModel;
use Illuminate\Database\Eloquent\Collection;

class ModuleSheetService
{
    public function getData($moduleNamespaces, $baseLocale, $locales)
    {
        $allLocales = array_merge([$baseLocale], $locales);
        throw new \Exception("Not implemented: ModuleSheetService@getData");

        // use repositories for this m8
    }
}
