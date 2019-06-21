<?php
/**
 * Class for registering custom image sizes and processing images
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
use function SB2Media\Headless\url;

class Media extends WordPress implements WordPressAPIContract
{
    /**
     * Register the menus with WordPress
     *
     * @since 1.0.0
     * @return this
     */
    public function register()
    {
        foreach ($this->config as $config) {
            add_image_size(
                $config['id'],
                $config['width'],
                $config['height'],
                $config['crop']
            );
        }
    }

    /**
     * Add menus to WordPress through hook API
     *
     * @since 1.0.0
     * @return void
     */
    public function add()
    {
        app('events')->addAction('after_setup_theme', array($this, 'register'));
    }
    
    /**
     * Filter the config for an image upload
     *
     * @since 1.0.0
     * @param array $config
     * @return array
     */
    public function imageUploadFilter(array $config)
    {
        $config['args']['label'] = isset($config['args']['label']) ? $config['args']['label'] : '';
        $config['args']['admin_size'] = isset($config['args']['admin_size']) ? $config['args']['admin_size'] : 'medium';
        $config['args']['admin_width'] = isset($config['args']['admin_width']) ? $config['args']['admin_width'] : 150;
        $config['args']['admin_height'] = isset($config['args']['admin_height']) ? $config['args']['admin_height'] : 150;
        $config['args']['graphql_size'] = isset($config['args']['graphql_size']) ? $config['args']['graphql_size'] : 'full';
        $config['default_image'] = url('resources/img', 'no-image.png');
        $wp_img_id = array_key_exists('page', $config) ? get_option($config['id']) : get_post_meta(get_the_ID(), $config['id'], true);

        if (!empty($wp_img_id)) {
            $image_attributes = wp_get_attachment_image_src($wp_img_id, $config['args']['admin_size']);
            $config['src'] = $image_attributes[0];
            $config['value'] = (int) $wp_img_id;
        } else {
            $config['src'] = $config['default_image'];
            $config['value'] = null;
        }

        return $config;
    }
}
