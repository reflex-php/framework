<?php
/**
 * Reflex
 *
 * @package   Reflex
 * @version   1.0.0
 * @author    Reflex Community
 * @license   MIT License
 * @copyright 2013 Reflex Community
 * @link      http://reflex.aziri.us/
 */

namespace Reflex;

use Illuminate\Support\ServiceProvider;
use Reflex\Bastion\Bastion;
use Reflex\Di\Container;
use Reflex\Di\Forge;
use Reflex\Di\ProviderFactory;
use View;

/**
 * ReflexServiceProvider
 *
 * @package    Reflex
 * @subpackage Core
 */
class ReflexServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('reflex/framework');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerReflexContainer();
    }

    protected function registerReflexContainer()
    {
        $this->registerReflexContainerProviderFactory();
        $this->registerReflexContainerForge();
        $this->app['reflex.container']  =   $this->app->share(
            function ($app) {
                return new Container(
                    $app['reflex.container.forge'],
                    $app['reflex.container.factory']
                );
            }
        );
    }

    protected function registerReflexContainerProviderFactory()
    {
        $this->app['reflex.container.factory']  =   $this->app->share(
            function () {
                return new ProviderFactory;
            }
        );
    }

    protected function registerReflexContainerForge()
    {
        $this->app['reflex.container.forge']    =   $this->app->share(
            function () {
                return new Forge;
            }
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('reflex.container', 'reflex.container.forge', 'reflex.container.factory');
    }
}
