<?php
/**
 * Class for registering WordPress custom post types
 *
 * @package    SB2Media\Headless\WordPress
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\WordPress;

use SB2Media\Headless\Contracts\WordPressAPIContract;
use function SB2Media\Headless\app;

class CustomPostTypes extends WordPress implements WordPressAPIContract
{
    /**
     * Register the custom post types with WordPress
     *
     * @since 0.1.0
     * @return this
     */
    public function register()
    {
        foreach ($this->config as $config) {
            register_post_type($config['id'], $config);
        }

        return $this;
    }

    /**
     * Add custom post types to WordPress through hook API
     *
     * @since 0.1.0
     * @return void
     */
    public function add()
    {
        app('events')->addAction('init', array($this, 'register'));
    }
}
