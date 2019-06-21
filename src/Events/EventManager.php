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

class EventManager extends Events
{

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
    public function addAction(string $event, $callback, int $priority = 10, int $acceptedArgs = 1)
    {
        return $this->on($event, $callback, $priority, $acceptedArgs);
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
    public function addFilter(string $name, $callback, int $priority = 10, int $acceptedArgs = 1)
    {
        return $this->filter($name, $callback, $priority, $acceptedArgs);
    }

    /**
     * Remove an action from an event hook already registered through the WordPress Plugin API
     *
     * @since  0.1.0
     * @param string $event           The event hook
     * @param        $callback        The function to add to the event hook
     * @param int    $priority        Used to specify the order in which the functions associated with a particular action are executed.
     * @param int    $acceptedArgs    The number of arguments the function accepts.
     * @return EventEmitter
     */
    public function removeAction(string $event, $callback, $priority = 10)
    {
        return $this->off($event, $callback, $priority);
    }

    /**
     * Get an instance of the Event Emitter class
     *
     * @since  0.1.0
     * @param  string    $id   Name of the instance to retrieve
     * @return EventEmitter
     */
    public static function getEventManager()
    {
        return \SB2Media\Headless\app('event-manager');
    }
}
