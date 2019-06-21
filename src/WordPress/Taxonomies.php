<?php
/**
 * Class for registering WordPress custom taxonomies
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

class Taxonomies extends WordPress implements WordPressAPIContract
{
    /**
     * Register the taxonomies with WordPress
     *
     * @since 0.1.0
     * @return this
     */
    public function register()
    {
        foreach ($this->config as $config) {
            foreach ($config['supports'] as $object_type) {
                register_taxonomy(
                    $config['id'],
                    $object_type,
                    $config
                );
            }
        }
        return $this;
    }
    /**
     * Add taxonomies to WordPress through hook API
     *
     * @since 0.1.0
     * @return void
     */
    public function add()
    {
        app('events')->addAction('init', [$this, 'register']);
    }
}
