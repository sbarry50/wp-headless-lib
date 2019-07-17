<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @package    SB2Media\Headless\Setup
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\Setup;

use SB2Media\Headless\Application;

class I18n
{
    /**
     * Application instance
     *
     * @since 0.3.0
     * @var Application
     */
    public $app;
 
    /**
     * Constructor
     *
     * @since 0.1.0
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->loadPluginTextDomain();
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    0.1.0
     */
    public function loadPluginTextDomain()
    {
        \load_plugin_textdomain(
            $this->app->text_domain,
            false,
            $this->app->path('lang')
        );
    }
}
