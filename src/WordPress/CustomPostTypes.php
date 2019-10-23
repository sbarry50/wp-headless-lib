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

use SB2Media\Headless\Events\EventManager;
use SB2Media\Headless\Contracts\WordPressAPIContract;

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
        EventManager::addAction('init', [$this, 'register']);
    }

    /**
     * Retrieve all the post ID's for a custom post type
     *
     * @since 0.1.0
     * @param string $post_type
     * @return array
     */
    public static function getPostIds(string $post_type)
    {
        return get_posts([
            'fields'         => 'ids',
            'posts_per_page' => -1,
            'post_type'      => $post_type,
            'post_status' => ['publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash']
        ]);
    }
}
