<?php
/**
 * Class that enqueues stylesheets and scripts with WordPress.
 *
 * @package    SB2Media\Headless\Enqueue
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\Enqueue;

use SB2Media\Headless\Application;

class EnqueueManager
{
    /**
     * Collection of stylesheets
     *
     * @since 0.1.0
     * @var array
     */
    public $stylesheets = [];

    /**
     * Collection of scripts
     *
     * @since 0.1.0
     * @var array
     */
    public $scripts = [];

    /**
     * Constructor
     *
     * @since 0.1.0
     * @param array $config
     */
    public function __construct(Application $app, array $config)
    {
        $this->app = $app;
        $this->stylesheets = $config['stylesheets'];
        $this->scripts = $config['scripts'];
    }

    /**
     * Enqueue the collection of stylesheets and scripts into WordPress. Callback function to hook into 'wp_enqueue_scripts' and 'admin_enqueue_scripts'.
     *
     * @since  0.1.0
     * @return null
     */
    public function enqueue()
    {
        if (! empty($this->stylesheets)) {
            foreach ($this->stylesheets as $stylesheet) {
                if ($this->shouldEnqueue($stylesheet)) {
                    wp_enqueue_style(
                        $stylesheet['id'],
                        $stylesheet['src'],
                        $stylesheet['dependencies'],
                        $stylesheet['version'],
                        $stylesheet['media']
                    );
                }
            }
        }

        if (! empty($this->scripts)) {
            $media_enqueued = false;
            
            foreach ($this->scripts as $script) {
                if ($this->shouldEnqueue($script)) {
                    if (array_key_exists('media', $script) && $script['media'] && !$media_enqueued) {
                        wp_enqueue_media();
                        $media_enqueued = true;
                    }

                    wp_enqueue_script(
                        $script['id'],
                        $script['src'],
                        $script['dependencies'],
                        $script['version'],
                        $script['in_footer']
                    );
    
                    if (isset($script['localization']) && !empty($script['localization'])) {
                        $l10n = [];

                        if (isset($script['localization']['callback']) && !empty($script['localization']['callback'])) {
                            if (is_array($script['localization']['callback']) && is_string($script['localization']['callback'][0])) {
                                if ($this->app->has($script['localization']['callback'][0])) {
                                    $script['localization']['callback'][0] = $this->app->get($script['localization']['callback'][0]);
                                }
                            }

                            if (is_callable($script['localization']['callback'])) {
                                $args = isset($script['localization']['args']) ? $script['localization']['args'] : [];
                                $l10n = call_user_func_array($script['localization']['callback'], $args);
                            }
                        }
    
                        wp_localize_script(
                            $script['id'],
                            $script['localization']['js_object_name'],
                            $l10n
                        );
                    }
                }
            }
        }
    }

    /**
     * Conditionally determine if the file should be enqueued or not.
     *
     * @since 0.1.0
     * @param array $config
     * @return bool
     */
    private function shouldEnqueue(array $config)
    {
        if (!isset($config['only_load'])) {
            return true;
        }

        $only_load = $config['only_load'];

        $page_slug = isset($_GET['page']) ? $_GET['page'] : '';
        if (isset($only_load['page_slug']) && !empty($page_slug)) {
            return $this->checkCurrentPage($page_slug, $only_load['page_slug']);
        }

        $post_type = get_current_screen()->post_type;
        if (isset($only_load['post_type']) && !empty($post_type)) {
            return $this->checkCurrentPage($post_type, $only_load['post_type']);
        }

        return false;
    }

    /**
     * Check the configuration values against the current page
     *
     * @since
     * @param mixed  $config_page    The configuration pages/post types that the asset should be enqueued on
     * @param string $current_page   The current page or post type
     * @return bool
     */
    private function checkCurrentPage($config_page, $current_page)
    {
        if (is_string($config_page) && $config_page === $current_page) {
            return true;
        }

        if (is_array($config_page)) {
            foreach ($config_page as $page) {
                if ($page === $current_page) {
                    return true;
                }
            }
        }

        return false;
    }
}
