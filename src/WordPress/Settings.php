<?php
/**
 * Class for registering WordPress settings
 *
 * @package    SB2Media\Headless\WordPress
 * @since      1.0.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\WordPress;

use SB2Media\Headless\Contracts\WordPressAPIContract;
use function SB2Media\Headless\app;

class Settings extends WordPress implements WordPressAPIContract
{
    /**
     * Wordpress Settings API option group name
     *
     * @since 1.0.0
     * @var string
     */
    public $option_group;

    public function __construct(array $config)
    {
        $this->option_group = str_replace('-', '_', app()->id);
        parent::__construct($config);
    }

    /**
     * Register the settings with WordPress
     *
     * @since 1.0.0
     * @return void
     */
    public function register()
    {
        foreach ($this->config as $config) {
            $args = $this->resolveArgs($config);
            register_setting(
                $this->option_group,
                $config['id'],
                $args
            );
        }
    }

    /**
     * Add settings to WordPress through hook API
     *
     * @since 1.0.0
     * @return void
     */
    public function add()
    {
        // WP-GraphQL only works with settings if we hook into 'init' when we register_setting
        app('events')->addAction('init', [$this, 'register']);
        app('events')->addAction('admin_init', [$this, 'addSettingsFields']);
    }

    /**
     * Add the settings fields with WordPress
     *
     * @since 1.0.0
     * @return void
     */
    public function addSettingsFields()
    {
        foreach ($this->config as $config) {
            $config['value'] = get_option($config['id']);
            $callback = (!is_string($config['callback'])) ? $config['callback'] : function () use ($config) {
                $this->callback($config['callback'], $config, true);
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
     * @since 1.0.0
     * @param Array $config
     * @return void
     */
    private function resolveArgs($config)
    {
        $defaults = [
            'type'              => 'string',
            'group'             => $this->option_group,
            'description'       => '',
            'sanitize_callback' => null,
            'show_in_rest'      => true,
            'show_in_graphql'   => true,
        ];
    
        $register_setting_args = array_key_exists('register_setting_args', $config) ? $config['register_setting_args'] : [];
        
        return array_merge($defaults, $register_setting_args);
    }
}
