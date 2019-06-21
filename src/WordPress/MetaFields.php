<?php
/**
 * Class for registering WordPress meta fields
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

class MetaFields extends WordPress implements WordPressAPIContract
{
    /**
     * Register the meta fields with WordPress
     *
     * @since 1.0.0
     * @return this
     */
    public function register()
    {
        foreach ($this->config as $cfg) {
            $this->save($cfg['id'], $cfg['meta_box']);
        }

        return $this;
    }

    /**
     * Add meta fields to WordPress through hook API
     *
     * @since 1.0.0
     * @return void
     */
    public function add()
    {
        app('events')->addAction('save_post', [$this, 'register']);
    }

    /**
     * Store custom field meta data in WordPress database
     *
     * @since 1.0.0
     * @param String $meta_key
     * @param String $meta_box_id
     * @return void
     */
    private function save($meta_key, $meta_box_id)
    {
        $post_id = get_the_ID();

        // Check if our nonce is set.
        if (! isset($_POST["${meta_box_id}_nonce"])) {
            return;
        }

        // Verify that the nonce is valid.
        if (! wp_verify_nonce($_POST["${meta_box_id}_nonce"], "${meta_box_id}_nonce")) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check the user's permissions.
        if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
            if (! current_user_can('edit_page', $post_id)) {
                return;
            }
        } else {
            if (! current_user_can('edit_post', $post_id)) {
                return;
            }
        }

        /* OK, it's safe for us to save the data now. */

        // Make sure that it is set.
        if (! isset($_POST[$meta_key])) {
            return;
        }

        // Sanitize user input.
        $my_data = sanitize_text_field($_POST[$meta_key]);

        // Update the meta field in the database.
        update_post_meta($post_id, $meta_key, $_POST[$meta_key]);
    }
}
