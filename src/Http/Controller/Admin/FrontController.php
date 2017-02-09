<?php
namespace Bitsoflove\TranslationsModule\Http\Controller\Admin;

use Anomaly\Streams\Platform\Addon\Addon;
use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Addon\AddonManager;
use Anomaly\Streams\Platform\Addon\Module\ModuleModel;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Bitsoflove\TranslationsModule\Repositories\Modules\ModuleTranslationsRepository;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Debug\Exception\FatalErrorException;

class FrontController extends AdminController
{
    protected $translationRepo;

    public function __construct()
    {
        parent::__construct();
        $this->translationRepo = app(ModuleTranslationsRepository::class);
    }

    public function streams() {
        return view('module::admin/streams');
    }

    public function modules($namespace) {

        return view('module::admin/modules');

        // deze code hoort eigenlijk thuis in de api service
        $language = 'en';
        $translations = $this->translationRepo->getAddonTranslations($namespace, $language);
        return $translations;


        //$addonCollection = app(AddonCollection::class);
        //foreach($addonCollection as $addon) {

            //$namespace = $addon->getNamespace();
            $translations = $this->translationRepo->getAddonTranslations($namespace, $language);

            echo("FrontController@modules:");
            dd($translations);


        //}
        return view('module::admin/modules');
    }
}
