<?php
/**
 * Class for registering WordPress administration submenus
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

class SubMenus extends WordPress implements WordPressAPIContract
{
    /**
     * Register the submenus with WordPress
     *
     * @since 0.1.0
     * @return this
     */
    public function register()
    {
        foreach ($this->config as $config) {
            $callback = !empty($config['callback']) ? $config['callback'] : function () use ($config) {
                $this->callback('menu-page', $config);
            };
            
            add_submenu_page(
                $config['parent_slug'],
                $config['page_title'],
                $config['menu_title'],
                $config['capability'],
                $config['menu_slug'],
                $callback
            );
        }

        return $this;
    }

    /**
     * Add submenus to WordPress through hook API
     *
     * @since 0.1.0
     * @return void
     */
    public function add()
    {
        app('events')->addAction('admin_menu', [$this, 'register']);
    }
}
