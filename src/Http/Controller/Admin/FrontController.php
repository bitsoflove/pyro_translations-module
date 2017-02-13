<?php
namespace Bitsoflove\TranslationsModule\Http\Controller\Admin;

use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Bitsoflove\TranslationsModule\Repositories\Modules\ModuleTranslationsRepository;
use Illuminate\Support\Facades\Log;

class FrontController extends AdminController
{
    protected $translationRepo;

    public function __construct()
    {
        parent::__construct();
        $this->translationRepo = app(ModuleTranslationsRepository::class);
    }

    public function streams() {
        return view('module::admin/translations', [
            'type' => 'streams',
        ]);
    }

    public function modules() {
        return view('module::admin/translations', [
            'type' => 'modules',
        ]);
    }
}
