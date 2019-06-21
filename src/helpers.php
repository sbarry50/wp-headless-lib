<?php
/**
 * Helper functions.
 *
 * @package    SB2Media\Headless
 * @since      1.0.0
 * @author     sbarry50
 * @link       https://github.com/sbarry50/simple-wordpress-settings-pages
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless;

use Exception;
use SB2Media\Headless\Application;
use SB2Media\Headless\File\Loader;

/**
 * Launch the application
 *
 * @since 1.0.0
 * @param string $plugin_root_file
 * @return void
 */
function launch(string $plugin_root_file)
{
    $app = app($plugin_root_file);
    $app->boot();
}

/**
 * Set the Application instance / Get the available container instance.
 *
 * @since  1.0.0
 * @param  string $abstract
 * @return Application
 *
 * Based on Laravel's app helper
 * @copyright Taylor Otwell
 * @license   https://github.com/laravel/framework/blob/5.8/LICENSE.md MIT
 * @link      https://github.com/laravel/framework/blob/5.8/src/Illuminate/Foundation/helpers.php#L107-L123
 */
function app($abstract = null)
{
    static $app;

    if ((!$app && is_null($abstract)) || (!$app && !is_file($abstract))) {
        throw new Exception('Path to plugin root file must be passed to app() to instantiate Application');
    }

    if ($app && is_file($abstract)) {
        throw new Exception('Application has already been instantiated');
    }

    if (!$app) {
        $app = new Application($abstract);
    }

    if (is_null($abstract) || is_file($abstract)) {
        return $app;
    }

    return $app->get($abstract);
}

/**
 * Get / set the specified configuration value.
 *
 * If an array is passed as the key, we will assume you want to set an array of values.
 *
 * @since  1.0.0
 * @param  array|string $key
 * @param  mixed        $default
 * @return Config
 *
 * @copyright Taylor Otwell
 * @license   https://github.com/laravel/framework/blob/5.8/LICENSE.md MIT
 * @link      https://github.com/laravel/framework/blob/5.8/src/Illuminate/Foundation/helpers.php#L263-L285
 */
function config($key = null, $default = null)
{
    if (is_null($key)) {
        return app('config');
    }
    if (is_array($key)) {
        return app('config')->set($key);
    }
    return app('config')->get($key, $default);
}

/**
 * Get the path of a config file
 *
 * @since 1.0.0
 * @param string $filename
 * @return void
 */
function configPath(string $filename)
{
    return path('config') . $filename . '.php';
}

/**
 * Get the (sub)directory path of the plugin.
 *
 * @since 1.0.0
 * @param string $subdir    Optionally, the name of a subdirectory
 * @return string
 */
function path($subdir = '', $file = '')
{
    if (empty($subdir) && empty($file)) {
        return app()->path();
    }

    if (!empty($subdir) && empty($file)) {
        return app()->path($subdir);
    }
    
    return app()->path($subdir, $file);
}

/**
 * Get the (sub)directory url of the plugin.
 *
 * @since 1.0.0
 * @param string $subdir    Optionally, the name of a subdirectory
 * @return string
 */
function url($subdir = '', $file = '')
{
    if (empty($subdir) && empty($file)) {
        return app()->url();
    }

    if (!empty($subdir) && empty($file)) {
        return app()->url($subdir);
    }
    
    return app()->url($subdir, $file);
}

/**
 * Get plugin data from the plugin's bootstrap file header comment using WP core's get_file_data function
 *
 * @since  1.0.0
 * @param  string    $id    Plugin header data unique id
 * @return array            Array of plugin data from the bootstrap file header comment
 */
function headerData(string $id, string $plugin_root_file)
{
    $default_headers = array(
        'Name'        => 'Plugin Name',
        'PluginURI'   => 'Plugin URI',
        'Version'     => 'Version',
        'Description' => 'Description',
        'Author'      => 'Author',
        'AuthorURI'   => 'Author URI',
        'TextDomain'  => 'Text Domain',
        'DomainPath'  => 'Domain Path',
        'Network'     => 'Network',
        // Site Wide Only is deprecated in favor of Network.
        '_sitewide'   => 'Site Wide Only',
    );
    return get_file_data($plugin_root_file, $default_headers)[$id];
}

/**
 * Checks if an array is multi-dimensional
 *
 * @since  1.0.0
 * @param  string    $arr    The array to be checked
 * @return bool
 *
 * @link https://pageconfig.com/post/checking-multidimensional-arrays-in-php
 */
function isMultiArray($arr)
{
    rsort($arr);
    return isset($arr[0]) && is_array($arr[0]);
}
