<?php namespace OFFLINE\Clockwork;

use App;
use Config;
use Illuminate\Foundation\AliasLoader;
use System\Classes\PluginBase;

/**
 * Clockwork Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Clockwork',
            'description' => 'Debugging and profiling plugin for October CMS',
            'author'      => 'OFFLINE',
            'icon'        => 'icon-tachometer',
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        $this->app->register(\OFFLINE\Clockwork\Classes\OctoberClockworkServiceProvider::class);

        AliasLoader::getInstance()->alias('Clockwork', \Clockwork\Support\Laravel\Facade::class);
    }
}
