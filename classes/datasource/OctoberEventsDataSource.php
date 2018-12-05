<?php namespace OFFLINE\Clockwork\Classes\DataSource;

use Clockwork\DataSource\LaravelEventsDataSource;

/**
 * Data source for October CMS events component, provides fired events.
 */
class OctoberEventsDataSource extends LaravelEventsDataSource
{
    protected function defaultIgnoredEvents()
    {
        return array_merge(parent::defaultIgnoredEvents(), [
            'cms\..*',
            'db\..*',
            'backend\..*',
            'pages\..*',
            'halcyon\..*',
            'mailer\..*',
            'system\..*',
            'markdown\..*',
        ]);
    }
}
