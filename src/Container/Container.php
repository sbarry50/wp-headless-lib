<?php
/**
 * Dependency injection container class which extends Pimple
 *
 * @package    SB2Media\Headless\Container
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\Container;

use Pimple\Container as Pimple;
use SB2Media\Headless\Contracts\ContainerContract;

class Container extends Pimple implements ContainerContract
{
    /**
     * Collection of config keys
     *
     * @since 0.1.0
     * @var   array
     */
    public $collection = [];

    /**
     * Get item from container
     *
     * @since    0.1.0
     * @param    string    $id          The unique identifier for the parameter or object
     * @return   mixed
     */
    public function get(string $id)
    {
        return $this->offsetGet($id);
    }

    /**
     * Set item in container
     *
     * @since    0.1.0
     * @param    string    $id          The unique identifier for the parameter or object
     * @param    mixed     $value
     * @return   void
     */
    public function set(string $id, $value)
    {
        $this->offsetSet($id, $value);
    }

    /**
     * Checks if a parameter or an object is set.
     *
     * @since    0.1.0
     * @param    string    $plugin_id   The unique identifier for the plugin
     * @param    string    $id          The unique identifier for the parameter or object
     * @return   bool
     */
    public function has(string $id)
    {
        return $this->offsetExists($id);
    }

    /**
     * Set the collection
     *
     * @since   0.1.0
     * @param   string  $key
     * @param   array   $items
     * @return  void
     */
    public function setCollection(string $key, array $items)
    {
        $this->collection[$key] = $items;
    }

    /**
     * Add to the collection
     *
     * @since   0.1.0
     * @param   string  $key
     * @param   mixed   $items
     * @return  void
     */
    public function addToCollection(string $key, $items)
    {
        if (is_string($items)) {
            array_push($this->collection[$key], $items);
            
            return;
        }

        if (is_array($items)) {
            foreach ($items as $item) {
                array_push($this->collection[$key], $item);
            }
        }
    }

    /**
     * Get the config keys
     *
     * @since   0.1.0
     * @param   string  $key
     * @return  void
     */
    public function getCollection(string $key)
    {
        return $this->collection[$key];
    }
}
