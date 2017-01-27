<?php
namespace Bitsoflove\TranslationsModule;

use Illuminate\Foundation\Application;
use Anomaly\Streams\Platform\Addon\Addon;
use Anomaly\Streams\Platform\Addon\AddonServiceProvider;

class TranslationsModuleServiceProvider extends AddonServiceProvider
{

    protected $plugins = [];

    protected $commands = [];

    protected $routes = [];

    protected $middleware = []; // overwritten in constructor

    protected $listeners = [];

    protected $aliases = [];

    protected $bindings = [];

    protected $providers = [];

    protected $singletons = [];

    protected $overrides = [];

    protected $mobile = [];

    /**
     * TranslationsModuleServiceProvider constructor.
     */
    public function __construct(Application $app, Addon $addon)
    {
        $this->middleware = (array) config('bitsoflove.module.translations::translations.middleware');
        parent::__construct($app, $addon);
    }

    public function register()
    {

    }

    public function map()
    {

    }

}
