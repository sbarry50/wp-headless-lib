<?php
/**
 * Abstract class for interacting with WordPress' various API's to extend its base functionality
 *
 * @package    SB2Media\Headless\WordPress
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\WordPress;

use SB2Media\Headless\Application;
use SB2Media\Headless\Contracts\WordPressAPIContract;

abstract class WordPress implements WordPressAPIContract
{
    /**
     * Application instance
     *
     * @since 0.3.0
     * @var Application
     */
    public $app;
    
    /**
     * Functionality Configuration
     *
     * @since 0.1.0
     * @var array $config
     */
    public $config;

    /**
     * Constructor
     *
     * @since 0.1.0
     * @param array $config
     */
    public function __construct(Application $app, array $config)
    {
        $this->app = $app;
        $this->config = $config;
        $this->add();
    }

    /**
     * Register the functionality through one of WordPress' API's
     *
     * @since 0.1.0
     * @return this
     */
    abstract public function register();

    /**
     * Add extended functionality to WordPress through hook API
     *
     * @since 0.1.0
     * @return void
     */
    abstract public function add();

    /**
     * Callback function to route data to appropriate template for display
     *
     * @since 0.1.0
     * @param array  $config  Functionality configuration
     * @return void
     */
    public function callback(array $config)
    {
        // Return if there is no callback set
        if (!isset($config['callback'])) {
            return;
        }

        if (is_array($config['callback']) && $this->callbackHasContainerId($config['callback'])) {
            $config['callback'][0] = $this->app->get($config['callback'][0]);
        }
        
        if (is_callable($config['callback'])) {
            call_user_func($config['callback'], $config);
        }

        if (is_string($config['callback']) && $this->app->get('file')->fileExistsInDirectory($config['callback'], $this->app->view())) {
            $relative_path = $this->app->get('file')->getRelativeFilePath($config['callback'], $this->app->view());
            $this->app->get('views')->render($relative_path, $config);
        }

        if (isset($config['args']['description']) && !empty($config['args']['description'])) {
            $this->app->get('views')->render('fields/description', $config);
        }
    }

    /**
     * Check if the callback array includes a container id that needs resolving
     *
     * @since 0.1.0
     * @param array $callback
     * @return void
     */
    protected function callbackHasContainerId(array $callback)
    {
        return !class_exists($callback[0])  && !is_object($callback[0]) && $this->app->has($callback[0]);
    }
}
