<?php
/**
 * Class for registering WordPress administration menus
 *
 * @package    SB2Media\Headless\WordPress
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\WordPress;

use SB2Media\Headless\Events\EventManager;
use SB2Media\Headless\Contracts\WordPressAPIContract;

class Menus extends WordPress implements WordPressAPIContract
{
    /**
     * Register the menus with WordPress
     *
     * @since 0.1.0
     * @return this
     */
    public function register()
    {
        foreach ($this->config as $config) {
            $callback = function ($args) use ($config) {
                $this->callback($config);
            };

            add_menu_page(
                $config['page_title'],
                $config['menu_title'],
                $config['capability'],
                $config['menu_slug'],
                $callback,
                $config['icon_url'],
                $config['position']
            );
        }
    }

    /**
     * Add menus to WordPress through hook API
     *
     * @since 0.1.0
     * @return void
     */
    public function add()
    {
        EventManager::addAction('admin_menu', array($this, 'register'));
    }
}
