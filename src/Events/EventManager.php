<?php
/**
 * The WordPress event manager wrapper for DownShift's Wordpress EventEmitter Interface.
 *
 * @package    SB2Media\Headless\Events
 * @since      0.1.0
 * @author     sbarry
 * @link       http://sb2media.com
 * @license    GNU General Public License 2.0+
 */

 /**
  * This class has been adapted from Josh Pollack's tutorial on Using Pimple as a Service Container in WordPress Development.
  * @link https://torquemag.io/2017/10/using-pimple-service-container-wordpress-development/
  */

namespace SB2Media\Headless\Events;

use DownShift\WordPress\EventEmitter as Events;
use SB2Media\Headless\Application;

class EventManager extends Events
{
    /**
     * Singleton
     *
     * @since 0.3.0
     * @var self
     */
    private static $instance = null;

    /**
     * Constructor
     *
     * @since 0.3.0
     */
    private function __construct()
    {
        // intentionally blank
    }

    /**
     * Clone
     *
     * @since 0.3.0
     * @return void
     */
    private function __clone()
    {
        // intentionally blank
    }

    /**
     * Sleep
     *
     * @since 0.3.0
     * @return void
     */
    private function __sleep()
    {
        // intentionally blank
    }

    /**
     * Wakeup
     *
     * @since 0.3.0
     * @return void
     */
    private function __wakeup()
    {
        // intentionally blank
    }

    /**
     * Singleton
     *
     * @since 0.3.0
     * @return void
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self;
            return self::$instance;
        }
        return self::$instance;
    }

    /**
     * Add an action to an event hook through the WordPress Plugin API
     *
     * @since 0.1.0
     * @param string $event           The event hook
     * @param        $callback        The callback function to add to the event hook
     * @param int    $priority        Used to specify the order in which the functions associated with a particular action are executed.
     * @param int    $acceptedArgs    The number of arguments the function accepts.
     *
     * @return EventEmitter
     */
    public static function addAction(string $event, $callback, int $priority = 10, int $acceptedArgs = 1)
    {
        return self::getInstance()->on($event, $callback, $priority, $acceptedArgs);
    }

    /**
     * Add a filter through the WordPress Plugin API
     *
     * @since 0.1.0
     * @param string $name            The name of the filter
     * @param        $callback        The callback function to be run when the filter is applied.
     * @param int    $priority        Used to specify the order in which the functions associated with a particular action are executed.
     * @param int    $acceptedArgs    The number of arguments the function accepts.
     * @return EventEmitter
     */
    public static function addFilter(string $name, $callback, int $priority = 10, int $acceptedArgs = 1)
    {
        return self::getInstance()->filter($name, $callback, $priority, $acceptedArgs);
    }

    /**
     * Remove an action from an event hook already registered through the WordPress Plugin API
     *
     * @since  0.1.0
     * @param string $event           The event hook
     * @param        $callback        The function to add to the event hook
     * @param int    $priority        Used to specify the order in which the functions associated with a particular action are executed.
     * @return EventEmitter
     */
    public static function removeAction(string $event, $callback, $priority = 10)
    {
        return self::getInstance()->off($event, $callback, $priority);
    }
}
