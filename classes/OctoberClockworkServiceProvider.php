<?php namespace OFFLINE\Clockwork\Classes;


use Clockwork\Authentication\AuthenticatorInterface;
use Clockwork\Clockwork;
use Clockwork\DataSource\EloquentDataSource;
use Clockwork\DataSource\LaravelCacheDataSource;
use Clockwork\DataSource\LaravelDataSource;
use Clockwork\DataSource\PhpDataSource;
use Clockwork\DataSource\XdebugDataSource;
use Clockwork\Request\Log;
use Clockwork\Support\Laravel\ClockworkServiceProvider;
use Clockwork\Support\Laravel\ClockworkSupport;
use OFFLINE\Clockwork\Classes\DataSource\OctoberDataSource;
use OFFLINE\Clockwork\Classes\DataSource\OctoberEventsDataSource;
use OFFLINE\Clockwork\Classes\DataSource\OctoberMailerDataSource;

class OctoberClockworkServiceProvider extends ClockworkServiceProvider
{
    /**
     * Register all needed services. This method has been modified
     * to work with October.
     */
    public function register()
    {
        $this->app['config']->set('clockwork', $this->app['config']->get('offline.clockwork::config'));

        $this->app->singleton('clockwork.support', function ($app) {
            return new ClockworkSupport($app);
        });

        $this->app->singleton('clockwork.log', function ($app) {
            return (new Log)
                ->collectStackTraces($app['clockwork.support']->getConfig('collect_stack_traces'));
        });

        $this->app->singleton('clockwork.authenticator', function ($app) {
            return $app['clockwork.support']->getAuthenticator();
        });

        $this->app->singleton('clockwork.laravel', function ($app) {
            return (new OctoberDataSource($app))
                ->collectViews($app['clockwork.support']->isCollectingViews())
                ->setLog($app['clockwork.log']);
        });

        $this->app->singleton('clockwork.mailer', function ($app) {
            return new OctoberMailerDataSource($app['events']);
        });

        $this->app->singleton('clockwork.eloquent', function ($app) {
            return (new EloquentDataSource($app['db'], $app['events']))
                ->collectStackTraces($app['clockwork.support']->getConfig('collect_stack_traces'));
        });

        $this->app->singleton('clockwork.cache', function ($app) {
            return (new LaravelCacheDataSource($app['events']))
                ->collectStackTraces($app['clockwork.support']->getConfig('collect_stack_traces'));
        });

        $this->app->singleton('clockwork.events', function ($app) {
            $support = $app['clockwork.support'];

            $app['cache.store']->setEventDispatcher($app['events']);

            return (new OctoberEventsDataSource($app['events'], $support->getConfig('ignored_events', [])))
                ->collectStackTraces($support->getConfig('collect_stack_traces'));
        });

        $this->app->singleton('clockwork.xdebug', function ($app) {
            return new XdebugDataSource;
        });

        $this->app->singleton('clockwork', function ($app) {
            $clockwork = new Clockwork();
            $support   = $app['clockwork.support'];

            $clockwork
                ->addDataSource(new PhpDataSource())
                ->addDataSource($app['clockwork.laravel'])
                ->addDataSource($app['clockwork.mailer']);

            if ($support->isCollectingDatabaseQueries()) {
                $clockwork->addDataSource($app['clockwork.eloquent']);
            }

            if ($support->isCollectingCacheStats()) {
                $clockwork->addDataSource($app['clockwork.cache']);
            }

            if ($support->isCollectingEvents()) {
                $clockwork->addDataSource($app['clockwork.events']);
            }

            if (in_array('xdebug', get_loaded_extensions())) {
                $clockwork->addDataSource($app['clockwork.xdebug']);
            }

            $clockwork->setAuthenticator($app['clockwork.authenticator']);
            $clockwork->setLog($app['clockwork.log']);
            $clockwork->setStorage($support->getStorage());

            $support->configureSerializer();

            return $clockwork;
        });

        $this->app['clockwork.laravel']->listenToEarlyEvents();

        // Listen to October's Mailer send events to capture any outgoing mail.
        $this->registerMailerEvents();

        // set up aliases for all Clockwork parts so they can be resolved by the IoC container
        $this->app->alias('clockwork.support', ClockworkSupport::class);
        $this->app->alias('clockwork.log', Log::class);
        $this->app->alias('clockwork.authenticator', AuthenticatorInterface::class);
        $this->app->alias('clockwork.laravel', LaravelDataSource::class);
        $this->app->alias('clockwork.mailer', OctoberMailerDataSource::class);
        $this->app->alias('clockwork.eloquent', EloquentDataSource::class);
        $this->app->alias('clockwork.cache', LaravelCacheDataSource::class);
        $this->app->alias('clockwork.events', OctoberEventsDataSource::class);
        $this->app->alias('clockwork.xdebug', XdebugDataSource::class);
        $this->app->alias('clockwork', Clockwork::class);

        $this->registerCommands();

        if ($this->app['clockwork.support']->getConfig('register_helpers', true)) {
            require $this->vendorDir() . '/helpers.php';
        }
    }

    /**
     * Listen to October's Mailer events and log any outgoing emails.
     */
    protected function registerMailerEvents()
    {
        $this->app['events']->listen('mailer.prepareSend', function ($mailer, $view, $message) {
            app('clockwork.mailer')->beforeSendPerformed($message);
        });
        $this->app['events']->listen('mailer.send', function ($mailer, $view, $message) {
            app('clockwork.mailer')->sendPerformed($message);
        });
    }

    /**
     * Returns the path to the local or shared vendors directory.
     *
     * @return string
     */
    protected function vendorDir()
    {
        $local = __DIR__ . '/../vendor/itsgoingd/clockwork/Clockwork/Support/Laravel/';
        if (is_dir($local)) {
            return $local;
        }

        $shared = __DIR__ . '/../../../../vendor/itsgoingd/clockwork/Clockwork/Support/Laravel/';
        if (is_dir($shared)) {
            return $shared;
        }

        throw new \RuntimeException('Could not locate Clockwork composer package. Did you run composer install?');
    }
}
