<?php
/**
 * Class for registering WordPress meta boxes
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
use function SB2Media\Headless\config;

class MetaBoxes extends WordPress implements WordPressAPIContract
{
    /**
     * Constructor
     *
     * @since 0.1.0
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->filterClass();
    }

    /**
     * Register the meta boxes with WordPress
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

            add_meta_box(
                $config['id'],
                $config['title'],
                $callback,
                $config['screen'],
                $config['context'],
                $config['priority'],
                $config['args']
            );
        }

        return $this;
    }

    /**
     * Add meta boxes to through WordPress through hook API
     *
     * @since 0.1.0
     * @return void
     */
    public function add()
    {
        app('events')->addAction('add_meta_boxes', [$this, 'register']);
    }

    /**
     * Add custom-meta-box class to WP's postbox classes array
     *
     * @since 0.1.0
     * @param Array $classes
     * @return void
     */
    public function addClass($classes)
    {
        $classes[] = 'custom-meta-box';
        return $classes;
    }

    /**
     * Callback function to route data to appropriate template for display
     *
     * @since 0.1.0
     * @param Array $config
     * @return void
     */
    public function callback(array $config)
    {
        $post_id = get_the_ID();

        $config['meta_fields'] = $this->getMetaFieldsByMetaBox($config['id']);
        
        foreach ($config['meta_fields'] as $key => &$val) {
            $val['value'] = get_post_meta($post_id, $val['id'], true);
        }

        wp_nonce_field("${config['id']}_nonce", "${config['id']}_nonce");

        parent::callback($config);
    }

    /**
     * Emit filter event to add custom meta box class through WordPress hook system
     *
     * @since 0.1.0
     * @param Array $config
     * @return void
     */
    protected function filterClass()
    {
        foreach ($this->config as $config) {
            app('events')->addFilter("postbox_classes_{$config['screen']}_{$config['id']}", [$this, 'addClass']);
        }
    }

    /**
     * Retrieve all the meta fields in a particular section
     *
     * @since 0.1.0
     * @param string $meta_box_id
     * @return array $fields_in_box
     */
    protected function getMetaFieldsByMetaBox(string $meta_box_id)
    {
        $meta_fields = config('meta-fields');
        $fields_in_box = [];

        foreach ($meta_fields as $meta_field) {
            if ($meta_box_id === $meta_field['meta_box']) {
                $fields_in_box[] = $meta_field;
            }
        }

        return $fields_in_box;
    }
}
