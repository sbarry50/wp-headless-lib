<?php
/**
 * Contract for registering extended functionality with WordPress' various API's
 *
 * @package    SB2Media\Headless\Contracts
 * @since      1.0.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\Contracts;

interface WordPressAPIContract
{
    /**
     * Register the functionality through one of WordPress' API's
     *
     * @since 1.0.0
     * @return void
     */
    public function register();

    /**
     * Add extended functionality to WordPress through the hook API
     *
     * @since 1.0.0
     * @return void
     */
    public function add();
}
