<?php
namespace Bitsoflove\TranslationsModule\Http\Controller\Admin;

use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Bitsoflove\TranslationsModule\Repositories\Modules\ModuleTranslationsRepository;
use Bitsoflove\TranslationsModule\Repositories\Streams\StreamTranslationsRepository;
use Bitsoflove\TranslationsModule\Services\FilterService;
use Bitsoflove\TranslationsModule\Services\ModuleSheetService;
use Bitsoflove\TranslationsModule\Services\StreamSheetService;
use Symfony\Component\Console\Input\Input;

class ApiController extends AdminController
{
    protected $filterService;
    protected $streamSheetService;
    protected $moduleSheetService;

    protected $streamTranslationsRepository;
    protected $moduleTranslationsRepository;

    public function __construct()
    {
        parent::__construct();

        // use services for fetching filters and sheet data.
        // wouldn't make sense to use a repository for this
        $this->filterService      = app(FilterService::class);
        $this->streamSheetService = app(StreamSheetService::class);
        $this->moduleSheetService = app(ModuleSheetService::class);

        // use repositories to save data
        $this->streamTranslationsRepository = app(StreamTranslationsRepository::class);
        $this->moduleTranslationsRepository = app(ModuleTranslationsRepository::class);
    }

    public function filters()
    {
        $data = $this->filterService->getFilters();
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }

    public function sheet()
    {
        $type         = \Input::get('type'); // modules | streams
        $streams      = explode(',', \Input::get('streams'));
        $baseLanguage = \Input::get('base-language');
        $locales      = explode(',', \Input::get('locales'));

        $data = $this->streamSheetService->getData($streams, $baseLanguage, $locales);
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }

    public function save()
    {
        $data   = $_POST;
        $result = $this->streamTranslationsRepository->save($data);
        return response()->json($result, 200, [], JSON_PRETTY_PRINT);
    }

    public function saveDev()
    {
        $data = json_decode('{
          "filters.ingredient_types.1.title.nl-be": {
            "module": "filters",
            "stream": "ingredient_types",
            "entityId": "1",
            "field": "title",
            "locale": "nl-be",
            "value": "12"
          },
          "filters.ingredient_types.2.title.nl-be": {
            "module": "filters",
            "stream": "ingredient_types",
            "entityId": "2",
            "field": "title",
            "locale": "nl-be",
            "value": "14"
          },
          "filters.ingredient_types.3.title.nl-be": {
            "module": "filters",
            "stream": "ingredient_types",
            "entityId": "3",
            "field": "title",
            "locale": "nl-be",
            "value": ""
          }
        }', true);

        $result = $this->translationService->save($data);
        return response()->json($result, 200, [], JSON_PRETTY_PRINT);
    }
}
