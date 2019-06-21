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

use SB2Media\Headless\File\Loader;
use function SB2Media\Headless\app;

class Compatibility
{

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
     * Constructor
     *
     * @since 0.1.0
     */
    public function __construct()
    {
        $this->wp_version = get_bloginfo('version');
        $this->php_version = phpversion();
        $this->check();
    }

    /**
     * Check if requirements are met to activate and run plugin
     *
     * @since  0.1.0
     * @return null
     */
    public function check()
    {
        if ($this->allCompatible()) {
            return;
        } else {
            $this->addAdminEvents();
        }
    }

    /**
     * Check if all requirements are met
     *
     * @since  0.1.0
     * @return bool
     */
    public function allCompatible()
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
    public function isCompatible($current, $minimum)
    {
        return version_compare($current, $minimum, '>=');
    }

    /**
     * Disable the plugin and hide the default "Plugin activated" notice
     *
     * @since  0.1.0
     * @return null
     */
    public function disablePlugin()
    {
        if (current_user_can('activate_plugins') && is_plugin_active(app()->basename)) {
            deactivate_plugins(app()->basename);

            // Hide the default "Plugin activated" notice
            if (isset($_GET[ 'activate' ])) {
                unset($_GET[ 'activate' ]);
            }
        }
    }

    /**
     * Render the "Requirements not met" error notice
     *
     * @since  0.1.0
     * @return null
     */
    public function renderNotice()
    {
        $notice = app()->path('views') . 'errors/compatibility-notice.php';
        printf(Loader::loadOutputFile($notice));
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
     * Add admin event listeners
     *
     * @since 0.1.0
     */
    private function addAdminEvents()
    {
        app('events')->addAction('admin_init', [$this, 'disablePlugin']);
        app('events')->addAction('admin_notices', [$this, 'renderNotice']);
    }
}
