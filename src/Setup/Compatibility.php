<?php
/**
 * Class that checks if all system requirements are met to run this plugin.
 *
 * @package    SB2Media\Headless\Setup
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\Setup;

use SB2Media\Headless\Application;
use SB2Media\Headless\Events\EventManager;

class Compatibility
{
    /**
     * Application instance
     *
     * @since 0.3.0
     * @var Application
     */
    public $app;

    /**
     * The name of the plugin
     *
     * @since 0.3.6
     * @var String
     */
    public $plugin_name;

    /**
     * Current version of WordPress
     *
     * @var string
     */
    public $wp_version;

    /**
     * Minimum version of WordPress required to run plugin
     *
     * @var string
     */
    public $min_wp_version = '5.0';

    /**
     * Current version of PHP
     *
     * @var string
     */
    public $php_version;

    /**
     * Minimum version of PHP required to run plugin
     *
     * @var string
     */
    public $min_php_version = '7.0';

    /**
     * Any plugins that are dependencies for this plugin
     *
     * @var string|array
     */
    public $dependencies;

    /**
     * Constructor
     *
     * @since 0.1.0
     */
    public function __construct(Application $app, $dependencies = null)
    {
        $this->app = $app;
        $this->plugin_name = $app->name;
        $this->wp_version = get_bloginfo('version');
        $this->php_version = phpversion();
        $this->dependencies = $dependencies;
        $this->checkCompatibility();
        $this->checkDependencies();
    }

    /**
     * Render the "Requirements not met" error notice
     *
     * @since  0.1.0
     * @return void
     */
    public function renderCompatibilityNotice()
    {
        $this->app->get('views')->render('errors/compatibility-notice', $this);
    }

    /**
     * Render the dependent plugins not active error notice
     *
     * @since  0.3.8
     * @param  $dependency    The plugin dependency configuration
     * @return void
     */
    public function renderDependencyNotice(array $dependency)
    {
        $args = [
            'plugin_name' => $this->app->name,
            'dependency_name' => $dependency['name'],
            'dependency_src' => $dependency['src']
        ];

        $this->app->get('views')->render('errors/dependency-notice', $args);
    }

    /**
     * Render the dashicon in the "Requirements not met" error notice
     *
     * @since  0.1.0
     * @param  string    $current Current version
     * @param  string    $minimum Minimum required version
     * @return null
     */
    public function renderDashicon($current, $minimum)
    {
        $this->isCompatible($current, $minimum) ? ($dashicon = 'yes' and $color = '#46b450') : ($dashicon = 'no' and $color = '#dc3232');

        printf('<span class="dashicons dashicons-%s" style="color:%s"></span>', $dashicon, $color);
    }

    /**
     * Disable the plugin and hide the default "Plugin activated" notice
     *
     * @since  0.1.0
     * @return null
     */
    public function disablePlugin()
    {
        if (current_user_can('activate_plugins') && is_plugin_active($this->app->basename)) {
            deactivate_plugins($this->app->basename);

            // Hide the default "Plugin activated" notice
            if (isset($_GET[ 'activate' ])) {
                unset($_GET[ 'activate' ]);
            }
        }
    }

    /**
     * Check if requirements are met to activate and run plugin
     *
     * @since  0.1.0
     * @return null
     */
    private function checkCompatibility()
    {
        if ($this->allCompatible()) {
            return;
        }

        if (!$this->allCompatible()) {
            EventManager::addAction('admin_init', [$this, 'disablePlugin']);
            EventManager::addAction('admin_notices', [$this, 'renderCompatibilityNotice']);
        }
    }

    /**
     * Check to see if all plugin dependencies are active
     *
     * @since 0.3.8
     * @return void
     */
    private function checkDependencies()
    {
        if (is_null($this->dependencies)) {
            return;
        }

        foreach ($this->dependencies as $dependency) {
            if (!$this->dependencyActive($dependency['path'])) {
                EventManager::addAction('admin_init', [$this, 'disablePlugin']);
                EventManager::addAction('admin_notices', function () use ($dependency) {
                    $this->renderDependencyNotice($dependency);
                });
            }
        }
    }

    /**
     * Check if all requirements are met
     *
     * @since  0.1.0
     * @return bool
     */
    private function allCompatible()
    {
        return $this->isCompatible($this->wp_version, $this->min_wp_version) &&
               $this->isCompatible($this->php_version, $this->min_php_version);
    }

    /**
     * Check if specific requirement is met
     *
     * @since  0.1.0
     * @param  string    $current Current version
     * @param  string    $minimum Minimum required version
     * @return bool
     */
    private function isCompatible($current, $minimum)
    {
        return version_compare($current, $minimum, '>=');
    }

    /**
     * Check if any dependent plugins are active
     *
     * @since 0.3.8
     * @param  string   $dependency_path    The path to the dependency
     * @return boolean
     */
    private function dependencyActive(string $dependency_path)
    {
        return in_array($dependency_path, apply_filters('active_plugins', get_option('active_plugins')));
    }
}
