<?php
namespace Bitsoflove\TranslationsModule\Repositories\Modules;

use Anomaly\Streams\Platform\Asset\Asset;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Bitsoflove\TranslationsModule\Repositories\Modules\Interfaces\TranslationRepositoryInterface;
use Bitsoflove\TranslationsModule\Translation\Form\TranslationFormBuilder;
use Bitsoflove\TranslationsModule\Translation\Table\TranslationTableBuilder;

class ModuleTranslationsRepository implements TranslationRepositoryInterface
{
    protected $fileTranslationsRepo;
    protected $dbTranslationsRepo;

    public function __construct()
    {
        $this->fileTranslationsRepo = app(FileTranslationRepository::class);
        $this->dbTranslationsRepo = app(DatabaseTranslationRepository::class);
    }

    public function getAddonTranslations($addonNamespace, $locale, array $parameters=[]) {
        $fileTranslations = $this->fileTranslationsRepo->getAddonTranslations($addonNamespace, $locale, $parameters);
        $databaseTranslations = $this->dbTranslationsRepo->getAddonTranslations($addonNamespace, $locale, $parameters);

        $merged = $this->mergeFileTranslationsWithDatabaseTranslations($fileTranslations, $databaseTranslations);
        return $merged;
    }

    private function mergeFileTranslationsWithDatabaseTranslations($fileTranslations, $databaseTranslations) {
        $merged = array_merge($fileTranslations, $databaseTranslations);
        return $merged;
    }
}
