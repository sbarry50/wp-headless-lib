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
use SB2Media\Headless\File\Loader;
use SB2Media\Headless\Config\Config;
use SB2Media\Headless\Container\Container;
use function SB2Media\Headless\headerData;

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
     * Constructor
     *
     * @since 0.1.0
     * @param string $root  The plugin root file
     */
    public function __construct(string $root)
    {
        parent::__construct();
        $this->basename = plugin_basename($root);
        $this->id = strtolower(str_replace(' ', '-', headerData('Name', $root)));
        $this->name = headerData('Name', $root);
        $this->path = plugin_dir_path($root);
        $this->root = $root;
        $this->text_domain = headerData('TextDomain', $root);
        $this->url = plugin_dir_url($root);
        $this->version = headerData('Version', $root);
    }

    /**
     * Boot the plugin. Executes all initial tasks necessary to prepare the plugin to perform its objective(s).
     *
     * @since  0.1.0
     * @return this    Instance of this object.
     */
    public function boot()
    {
        static $booted;
        if ($booted) {
            return;
        }

        $booted = true;

        $this->registerConfigs();
        $this->registerProviders();
        app('events')->addAction('admin_enqueue_scripts', [app('admin-enqueue'), 'enqueue']);
        // app('events')->addAction('wp_enqueue_scripts', [app('enqueue'), 'enqueue']);

        return $this;
    }

    /**
     * Register all the configurations
     *
     * @since 0.1.0
     * @return void
     */
    public function registerConfigs()
    {
        $config_dir = scandir(path('config'));
        $config_files = $this->filterConfigDir($config_dir);
        $config_ids = [];

        app()->set('config', new Config());

        foreach ($config_files as $config_id => $config_file) {
            $config = [];
            $config_values = Loader::loadFile(path('config') . $config_file);
            $config[$config_id] = $config_values;
            config($config);
        }
    }

    /**
     * Register the providers with the Container
     *
     * @since 0.1.0
     * @return void
     */
    public function registerProviders()
    {
        $providers = config('providers');
        $keys = [];

        foreach ($providers as $collection_key => $collection) {
            $this->setCollection("{$this->id}.providers.{$collection_key}", $collection);

            foreach ($collection as $id => $config) {
                $this->instantiate($id, $config);
            }
        }

        return $this;
    }

    /**
     * Register the class in the container
     *
     * @since 0.1.0
     * @param string $id
     * @param array $config
     * @return void
     */
    public function instantiate(string $id, array $config)
    {
        $args = [];

        if (array_key_exists('config', $config)) {
            foreach ($config['config'] as $cfg) {
                $args[] = config($cfg);
            }
        }

        if (array_key_exists('dependencies', $config)) {
            foreach ($config['dependencies'] as $dependency) {
                $args[] = app($dependency);
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
     * Resolve the dependency from the configuration file
     *
     * @since 0.1.0
     * @param array    $dependency
     * @return array
     */
    protected function resolve(string $type, string $dependency)
    {
        if ('config' === $type) {
            return config($dependency);
        }
        
        if ('dependencies' === $type) {
            return app($dependency);
        }
    }

    /**
     * Get the (sub)directory path of the plugin or full file path.
     *
     * @since 0.1.0
     * @param string $subdir    Optionally, the name of a subdirectory
     * @param string $file      Optionally, the name of a file
     * @return string
     */
    public function path($subdir = '', $file = '')
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
    public function url($subdir = '', $file = '')
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
