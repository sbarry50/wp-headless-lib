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
            $callback = function ($args) use ($config) {
                $this->callback($config);
            };

            switch ($config['parent_slug']) {
                case 'Dashboard':
                    $func = 'add_dashboard_page';
                    break;
                case 'Posts':
                    $func = 'add_posts_page';
                    break;
                case 'Media':
                    $func = 'add_media_page';
                    break;
                case 'Pages':
                    $func = 'add_pages_page';
                    break;
                case 'Comments':
                    $func = 'add_comments_page';
                    break;
                case 'Appearance':
                    $func = 'add_theme_page';
                    break;
                case 'Plugins':
                    $func = 'add_plugins_page';
                    break;
                case 'Users':
                    $func = 'add_users_page';
                    break;
                case 'Tools':
                    $func = 'add_management_page';
                    break;
                case 'Settings':
                    $func = 'add_options_page';
                    break;
                default:
                    $func = 'add_submenu_page';
                    break;
            }
            if ('add_submenu_page' == $func) {
                $func(
                    $config['parent_slug'],
                    $config['page_title'],
                    $config['menu_title'],
                    $config['capability'],
                    $config['menu_slug'],
                    $callback
                );
            } else {
                $func(
                    $config['page_title'],
                    $config['menu_title'],
                    $config['capability'],
                    $config['menu_slug'],
                    $callback
                );
            }
            
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
