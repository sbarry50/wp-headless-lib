<?php
/**
 * Class for registering WordPress sections
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

class Sections extends WordPress implements WordPressAPIContract
{
    /**
     * Register the sections with WordPress
     *
     * @since 0.1.0
     * @return void
     */
    public function register()
    {
        foreach ($this->config as $config) {
            $callback = !empty($config['callback']) ? $config['callback'] : function () use ($config) {
                $this->callback('section', $config);
            };

            add_settings_section(
                $config['id'],
                $config['title'],
                $callback,
                $config['page']
            );
        }

        return $this;
    }

    /**
     * Add sections to WordPress through hook API
     *
     * @since 0.1.0
     * @return void
     */
    public function add()
    {
        app('events')->addAction('admin_init', [$this, 'register']);
    }
}
