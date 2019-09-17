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

    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'clock' => function ( $item ) {
                    return \Clockwork\Support\Laravel\Facade::info( $item );
                },
                'emergency' => function ( $item ) {
                    return \Clockwork\Support\Laravel\Facade::emergency( $item );
                },
                'alert' => function ( $item ) {
                    return \Clockwork\Support\Laravel\Facade::alert( $item );
                },
                'critical' => function ( $item ) {
                    return \Clockwork\Support\Laravel\Facade::critical( $item );
                },
                'error' => function ( $item ) {
                    return \Clockwork\Support\Laravel\Facade::error( $item );
                },
                'warning' => function ( $item ) {
                    return \Clockwork\Support\Laravel\Facade::warning( $item );
                },
                'notice' => function ( $item ) {
                    return \Clockwork\Support\Laravel\Facade::notice( $item );
                },
                'info' => function ( $item ) {
                    return \Clockwork\Support\Laravel\Facade::info( $item );
                },
                'startEvent' => function ( $name, $description = null ) {
                    return \Clockwork\Support\Laravel\Facade::startEvent( $name, $description );
                },
                'endEvent' => function ( $name ) {
                    return \Clockwork\Support\Laravel\Facade::endEvent( $name );
                }
            ]
        ];
    }
    
}
