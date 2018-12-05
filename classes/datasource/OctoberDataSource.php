<?php namespace OFFLINE\Clockwork\Classes\DataSource;

use Clockwork\DataSource\LaravelDataSource;
use Clockwork\Request\Request;

/**
 * Data source for October CMS
 */
class OctoberDataSource extends LaravelDataSource
{
    protected function resolveAuthenticatedUser(Request $request)
    {
        if ( ! ($user = $this->app['backend.auth']->getUser())) {
            return;
        }

        $request->setAuthenticatedUser($user->email, $user->id, [
            'email' => $user->email,
            'name'  => $user->name,
        ]);
    }
}
