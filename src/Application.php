<?php
/**
 * The core plugin class.
 *
 * @package    SB2Media\Headless
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use SB2Media\Headless\File\Loader;
use SB2Media\Headless\Config\Config;
use SB2Media\Headless\Events\EventManager;
use SB2Media\Headless\Container\Container;

class Application extends Container
{

    /**
     * Container instance
     *
     * @since 0.1.0
     * @var Container
     */
    public $app;

    /**
     * The plugin basename
     *
     * @since 0.1.0
     * @var string
     */
    public $basename;

    /**
     * The plugin id
     *
     * @since 0.1.0
     * @var string
     */
    public $id;

    /**
     * The plugin root directory path
     *
     * @since 0.1.0
     * @var string
     */
    public $path;

    /**
     * The plugin name
     *
     * @since 0.1.0
     * @var string
     */
    public $name;

    /**
     * The plugin root
     *
     * @since 0.1.0
     * @var string
     */
    public $root;

    /**
     * The plugin text domain
     *
     * @since 0.1.0
     * @var string
     */
    public $text_domain;

    /**
     * The plugin root directory url
     *
     * @since 0.1.0
     * @var string
     */
    public $url;

    /**
     * The plugin version
     *
     * @since 0.1.0
     * @var string
     */
    public $version;

    /**
     * Flag for whether the plugin has been booted or not
     *
     * @since 0.3.0
     * @var boolean
     */
    private $booted = false;

    /**
     * Constructor
     *
     * @since 0.1.0
     * @param string $root  The plugin root file
     */
    public function __construct(string $root)
    {
        parent::__construct();
        $this->root = $root;
        $this->basename = plugin_basename($root);
        $this->id = strtolower(str_replace(' ', '-', $this->headerData('Name')));
        $this->name = $this->headerData('Name');
        $this->path = plugin_dir_path($root);
        $this->text_domain = $this->headerData('TextDomain');
        $this->url = plugin_dir_url($root);
        $this->version = $this->headerData('Version');
    }

    /**
     * Boot the plugin. Executes all initial tasks necessary to prepare the plugin to perform its objective(s).
     *
     * @since  0.1.0
     * @return this    Instance of this object.
     */
    public function boot()
    {
        if ($this->booted) {
            return;
        }
        
        $this->registerConfigs();
        $this->registerProviders();
        $this->enqueueScripts();
        
        $this->booted = true;

        return $this;
    }

    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @since  0.1.0
     * @param  array|string $key
     * @param  mixed        $default
     * @return Config
     *
     * @copyright Taylor Otwell
     * @license   https://github.com/laravel/framework/blob/5.8/LICENSE.md MIT
     * @link      https://github.com/laravel/framework/blob/5.8/src/Illuminate/Foundation/helpers.php#L263-L285
     */
    public function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->get('config');
        }
        if (is_array($key)) {
            return $this->get('config')->set($key);
        }
        return $this->get('config')->get($key, $default);
    }

    /**
     * Get the (sub)directory path of the plugin or full file path.
     *
     * @since 0.1.0
     * @param string $subdir    Optionally, the name of a subdirectory
     * @param string $file      Optionally, the name of a file
     * @return string
     */
    public function path(string $subdir = '', string $file = '')
    {
        if (empty($subdir) && empty($file)) {
            return trailingslashit($this->path);
        }
    
        if (!empty($subdir) && empty($file)) {
            return trailingslashit($this->path . $subdir);
        }
        
        return trailingslashit($this->path . $subdir) . $file;
    }

    /**
     * Get the (sub)directory url of the plugin or full file path url.
     *
     * @since 0.1.0
     * @param string $subdir    Optionally, the name of a subdirectory
     * @param string $file      Optionally, the name of a file
     * @return string
     */
    public function url(string $subdir = '', string $file = '')
    {
        if (empty($subdir) && empty($file)) {
            return trailingslashit($this->url);
        }
    
        if (!empty($subdir) && empty($file)) {
            return trailingslashit($this->url . $subdir);
        }
        
        return trailingslashit($this->url . $subdir) . $file;
    }

    /**
     * Get the (sub)directory path of the view.
     *
     * @since 0.1.1
     * @param string $relative_path      Optionally, the relative path to the file from the views directory
     * @return string
     */
    public function view(string $relative_path = '')
    {
        $views_dir = $this->path('resources/views');

        if (empty($relative_path)) {
            return $views_dir;
        }

        if (!Str::endsWith($relative_path, '.php')) {
            $relative_path = $relative_path . '.php';
        }

        return $views_dir . $relative_path;
    }

    /**
     * Get plugin data from the plugin's bootstrap file header comment using WP core's get_file_data function
     *
     * @since  0.1.0
     * @param  string    $id    Plugin header data unique id
     * @return array            Array of plugin data from the bootstrap file header comment
     */
    public function headerData(string $id)
    {
        $default_headers = [
            'Name'        => 'Plugin Name',
            'PluginURI'   => 'Plugin URI',
            'Version'     => 'Version',
            'Description' => 'Description',
            'Author'      => 'Author',
            'AuthorURI'   => 'Author URI',
            'TextDomain'  => 'Text Domain',
            'DomainPath'  => 'Domain Path',
            'Network'     => 'Network',
            // Site Wide Only is deprecated in favor of Network.
            '_sitewide'   => 'Site Wide Only',
        ];

        return get_file_data($this->root, $default_headers)[$id];
    }

    /**
     * Register all the configurations
     *
     * @since 0.1.0
     * @return void
     */
    protected function registerConfigs()
    {
        $config_dir = scandir($this->path('config'));
        $config_files = $this->filterConfigDir($config_dir);
        $config_ids = [];

        $this->set('config', new Config());

        foreach ($config_files as $config_id => $config_file) {
            $config = [];
            $config_values = Loader::loadFile($this->path('config', $config_file));
            $config[$config_id] = $config_values;
            $this->config($config);
        }
    }

    /**
     * Register the providers with the Container
     *
     * @since 0.1.0
     * @return void
     */
    protected function registerProviders()
    {
        $providers = $this->config('providers');
        $keys = [];

        foreach ($providers as $collection_key => $collection) {
            $this->setCollection("{$this->id}.providers.{$collection_key}", $collection);

            foreach ($collection as $id => $config) {
                $this->setInstance($id, $config);
            }
        }

        return $this;
    }

    /**
     * Enqueue the application scripts and stylesheets with Wordpress
     *
     * @since 0.1.0
     * @return void
     */
    protected function enqueueScripts()
    {
        EventManager::addAction('admin_enqueue_scripts', [$this->get('admin-enqueue'), 'enqueue']);
        EventManager::addAction('wp_enqueue_scripts', [$this->get('enqueue'), 'enqueue']);
    }

    /**
     * Register the class in the container
     *
     * @since 0.1.0
     * @param string $id
     * @param array $config
     * @return void
     */
    protected function setInstance(string $id, array $config)
    {
        $args = [];

        if (array_key_exists('app', $config) && $config['app']) {
            $args[] = $this;
        }

        if (array_key_exists('dependencies', $config)) {
            foreach ($config['dependencies'] as $dependency) {
                $args[] = $this->get($dependency);
            }
        }

        if (array_key_exists('config', $config)) {
            foreach ($config['config'] as $cfg) {
                $args[] = $this->config($cfg);
            }
        }

        if (array_key_exists('params', $config)) {
            foreach ($config['params'] as $param) {
                $args[] = $param;
            }
        }
        
        if (!empty($args)) {
            $this->set($id, new $config['class'](...$args));
        } else {
            $this->set($id, new $config['class']());
        }
    }

    /**
     * Filter out unwanted files from the config directory
     *
     * @since 0.1.0
     * @param array $config_dir
     * @return array
     */
    protected function filterConfigDir(array $config_dir)
    {
        foreach ($config_dir as $key => $value) {
            if (in_array($value, array('.','..','index.php')) || strpos($value, '.php') == false) {
                unset($config_dir[$key]);
            }
        }
        foreach ($config_dir as $config_file) {
            $config_id = str_replace('.php', '', $config_file);
            $config[$config_id] = $config_file;
        }
        return $config;
    }
}
