<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @package    SB2Media\Headless\Setup
 * @since      1.0.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\Setup;

use function SB2Media\Headless\app;

class I18n
{
    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->loadPluginTextDomain();
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function loadPluginTextDomain()
    {
        \load_plugin_textdomain(
            app()->text_domain,
            false,
            app()->path('lang')
        );
    }
}
