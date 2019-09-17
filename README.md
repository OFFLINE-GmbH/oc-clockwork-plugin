# Clockwork Plugin for October CMS

This plugin integrates the awesome [itsgoingd/clockwork](https://github.com/itsgoingd/clockwork) with October 
CMS.

Clockwork is a browser extension, providing tools for debugging and profiling your October CMS applications.
Clockwork captures and visualizes the following data:

* HTTP requests 
* Performance timeline of application execution cycle
* Database queries
* Cache hits and misses
* Log entries
* Session data
* Authenticated user information
* Outgoing emails

## Usage

After the plugin installation is complete add the `laravel.dont-discover` part to the `extra` section of your project's `composer.json` like this:

```
  "extra": {
    "merge-plugin": {
      "include": [
        "plugins/*/*/composer.json"
      ],
      "recurse": true,
      "replace": false,
      "merge-dev": false
    },
    "laravel": {
      "dont-discover": [
        "itsgoingd/clockwork"
      ]
    }
  }
```

Then download the browser extension to get insights into your October CMS 
installation:

- install the [Chrome extension](https://chrome.google.com/webstore/detail/clockwork/dmggabnehkmmfmdffgajcflpdjlnoemp)
- or the [Firefox add-on](https://addons.mozilla.org/en-US/firefox/addon/clockwork-dev-tools/)
- or use the web UI `http://your.app/__clockwork`

All Clockwork features are adapted to work with October CMS. For more information on how to use the `clock` helper 
function refer to [the official Website](https://underground.works/clockwork/).

## Configuration

By default, Clockwork will only be available in debug mode, you can change this and more settings in the 
configuration file.

The plugin provides [file based configuration](./config/config.php) options where you can overwrite 
all the Clockwork settings. Refer to
[the October documentation](https://octobercms.com/docs/plugin/settings#file-configuration)
on how to overwrite these settings.

## Twig helper functions

There are a few twig helper functions available you can use to interact with Clockwork from your views:

See https://github.com/OFFLINE-GmbH/oc-clockwork-plugin/blob/master/Plugin.php#L43 for more details.
