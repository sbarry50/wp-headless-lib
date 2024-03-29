<?php
/**
 * Class for registering WordPress settings
 *
 * @package    SB2Media\Headless\WordPress
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\WordPress;

use Illuminate\Support\Str;
use SB2Media\Headless\Events\EventManager;
use SB2Media\Headless\Contracts\WordPressAPIContract;

class Settings extends WordPress implements WordPressAPIContract
{
    /**
     * Register the settings with WordPress
     *
     * @since 0.1.0
     * @return void
     */
    public function register()
    {
        foreach ($this->config as $config) {
            $args = $this->resolveArgs($config);
            register_setting(
                str_replace('-', '_', $config['page']),
                $config['id'],
                $args
            );
        }
    }

    /**
     * Add settings to WordPress through hook API
     *
     * @since 0.1.0
     * @return void
     */
    public function add()
    {
        // WP-GraphQL only works with settings if we hook into 'init' when we register_setting
        EventManager::addAction('init', [$this, 'register']);
        EventManager::addAction('admin_init', [$this, 'addSettingsFields']);
    }

    /**
     * Add the settings fields with WordPress
     *
     * @since 0.1.0
     * @return void
     */
    public function addSettingsFields()
    {
        foreach ($this->config as $config) {
            $config['value'] = get_option($config['id']);
            $callback = function ($args) use ($config) {
                $this->callback($config);
            };
            
            add_settings_field(
                $config['id'],
                $config['title'],
                $callback,
                $config['page'],
                $config['section'],
                $config['args']
            );
        }
    }

    /**
     * Resolve the setting args to be passed in register_setting
     *
     * @since 0.1.0
     * @param Array $config
     * @return void
     */
    private function resolveArgs($config)
    {
        $defaults = [
            'type'              => 'string',
            'group'             => str_replace('-', '_', $config['page']),
            'description'       => '',
            'sanitize_callback' => null,
            'show_in_rest'      => true,
            'show_in_graphql'   => true,
        ];
    
        $register_setting_args = array_key_exists('register_setting_args', $config) ? $config['register_setting_args'] : [];
        
        return array_merge($defaults, $register_setting_args);
    }
}
