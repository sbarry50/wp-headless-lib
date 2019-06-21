<?php
/**
 * Class that enqueues stylesheets and scripts with WordPress.
 *
 * @package    SB2Media\Headless\Enqueue
 * @since      1.0.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\Enqueue;

use function SB2Media\Headless\app;

class EnqueueManager
{
    /**
     * Collection of stylesheets
     *
     * @since 1.0.0
     * @var array
     */
    public $stylesheets = [];

    /**
     * Collection of scripts
     *
     * @since 1.0.0
     * @var array
     */
    public $scripts = [];

    /**
     * Constructor
     *
     * @since 1.0.0
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->stylesheets = $config['stylesheets'];
        $this->scripts = $config['scripts'];
    }

    /**
     * Enqueue the collection of stylesheets and scripts into WordPress. Callback function to hook into 'wp_enqueue_scripts' and 'admin_enqueue_scripts'.
     *
     * @since  1.0.0
     * @return null
     */
    public function enqueue()
    {
        if (! empty($this->stylesheets)) {
            foreach ($this->stylesheets as $stylesheet) {
                wp_enqueue_style(
                    $stylesheet['id'],
                    $stylesheet['src'],
                    $stylesheet['dependencies'],
                    $stylesheet['version'],
                    $stylesheet['media']
                );
            }
        }

        if (! empty($this->scripts)) {
            $media_enqueued = false;
            
            foreach ($this->scripts as $script) {
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

                if (array_key_exists('localization', $script) && !empty($script['localization'])) {
                    $l10n = is_object($script['localization']['values'][0]) ? $script['localization']['values']() : $script['localization']['values'];

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
