<?php
/**
 * Dependency injection container class which extends Pimple
 *
 * @package    SB2Media\Headless\Container
 * @since      1.0.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\Container;

use Pimple\Container as Pimple;
use SB2Media\Headless\File\Loader;
use SB2Media\Headless\Config\Config;
use SB2Media\Headless\Support\Paths;
use SB2Media\Headless\Contracts\ContainerContract;
use function SB2Media\Headless\app;

class Container extends Pimple implements ContainerContract
{
    /**
     * Instance of Container
     *
     * @since 1.0.0
     * @var   Container
     */
    public static $instance;

    /**
     * Collection of config keys
     *
     * @since 1.0.0
     * @var   array
     */
    public $collection = [];

    /**
     * Contructor
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        self::$instance = $this;
    }

    /**
     * Get instance of Container
     *
     * @since    1.0.0
     * @param    string    $id    The unique identifier for the parameter or object
     * @return   Container
     */
    public static function getInstance(string $id = null)
    {
        return self::$instance->get(self::$instance->id($id));
    }

    /**
     * Set instance of Container
     *
     * @since    1.0.0
     * @param    string    $id          The unique identifier for the parameter or object
     * @param    mixed     $value
     */
    public static function setInstance(string $id, $value)
    {
        return self::$instance->set(self::$instance->id($id), $value);
    }

    /**
     * Get item from container
     *
     * @since    1.0.0
     * @param    string    $id          The unique identifier for the parameter or object
     * @return   mixed
     */
    public function get(string $id)
    {
        return $this->offsetGet($this->id($id));
    }

    /**
     * Set item in container
     *
     * @since    1.0.0
     * @param    string    $id          The unique identifier for the parameter or object
     * @param    mixed     $value
     * @return   void
     */
    public function set(string $id, $value)
    {
        $this->offsetSet($this->id($id), $value);
    }

    /**
     * Checks if a parameter or an object is set.
     *
     * @since    1.0.0
     * @param    string    $plugin_id   The unique identifier for the plugin
     * @param    string    $id          The unique identifier for the parameter or object
     * @return   bool
     */
    public function has(string $id)
    {
        return $this->offsetExists($this->id($id));
    }

    /**
     * Set the collection
     *
     * @since   1.0.0
     * @param   string  $collection_key
     * @param   array   $collection_id
     * @return  void
     */
    public function setCollection(string $collection_key, array $collection_id)
    {
        $this->collection[$collection_key] = $collection_id;
    }

    /**
     * Get the config keys
     *
     * @since   1.0.0
     * @param   string  $collection_key
     * @return  void
     */
    public function getCollection(string $collection_key)
    {
        return $this->collection[$collection_key];
    }

    /**
     * Build and return the full unique identifier for the parameter or object
     *
     * @since   1.0.0
     * @param   string    $id    The unique identifier of the object or parameter
     * @return  string
     */
    public function id(string $id)
    {
        return app()->id . '.' . $id;
    }
}
