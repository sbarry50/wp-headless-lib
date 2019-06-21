<?php
/**
 * Container interface
 *
 * @package    SB2Media\Headless\Contracts
 * @since      0.2.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\Contracts;

interface ContainerContract
{
    /**
     * Get instance of Container
     *
     * @since  0.1.0
     * @param  string  $id  The unique identifier for the parameter or object
     * @return Container
     */
    public static function getInstance(string $id);

    /**
     * Set instance of Container
     *
     * @since    0.1.0
     * @param    string    $id    The unique identifier for the parameter or object
     * @param    mixed     $value
     * @return   void
     */
    public static function setInstance(string $id, $value);

    /**
     * Get item from container
     *
     * @param string $id The unique identifier for the parameter or object
     * @return mixed
     */
    public function get(string $id);

    /**
     * Set item in container
     *
     * @param string $id    The unique identifier for the parameter or object
     * @param mixed $value
     */
    public function set(string $id, $value);

    /**
     * Checks if a parameter or an object is set.
     *
     * @since 0.1.0
     *
     * @param  string $id    The unique identifier for the parameter or object
     * @return bool
     */
    public function has(string $id);

    /**
     * Set the collection
     *
     * @since 0.1.0
     * @param string $collection_key
     * @param array $collection_id
     * @return void
     */
    public function setCollection(string $collection_key, array $collection_id);

    /**
     * Get the config keys
     *
     * @since 0.1.0
     * @param string $key
     * @return void
     */
    public function getCollection(string $collection_key);
}
