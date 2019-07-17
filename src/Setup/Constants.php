<?php
/**
 * Class that defines the plugin's constants.
 *
 * @package    SB2Media\Headless
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */
namespace SB2Media\Headless\Setup;

use SB2Media\Headless\Application;
use SB2Media\Headless\Contracts\ConstantsContract;

class Constants implements ConstantsContract
{
    /**
     * Application instance
     *
     * @since 0.3.0
     * @var Application
     */
    public $app;

    /**
     * The constants array
     *
     * @var array
     */
    public $constants = [];

    /**
     * Constructor
     *
     * @since  0.1.0
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->constants = $this->build();
        $this->define();
    }

    /**
     * Defines the plugin's constants
     *
     * @since  0.1.0
     */
    public function define()
    {
        foreach ($this->constants as $constant => $value) {
            if (! defined($constant)) {
                define($constant, $value);
            }
        }
    }

    /**
     * Add additional constants to the default constants array
     *
     * @since 0.1.0
     * @return array    $this->constants    The plugin constants
     */
    public function add(array $constants)
    {
        $this->constants = array_merge($this->constants, $constants);

        return $this;
    }

    /**
     * Get the array of constants
     * @since  0.1.0
     * @return array    $this->constants    Plugin constants
     */
    public function get()
    {
        return $this->constants;
    }

    /**
     * Build the constants configuration array
     *
     * @since   0.1.0
     * @return array
     */
    protected function build()
    {
        $prefix = $this->prefix();

        return [
            "{$prefix}_ROOT"        => $this->app->root,
            "{$prefix}_NAME"        => $this->app->name,
            "{$prefix}_BASENAME"    => $this->app->basename,
            "{$prefix}_DIR_PATH"    => $this->app->path,
            "{$prefix}_DIR_URL"     => $this->app->url,
            "{$prefix}_TEXT_DOMAIN" => $this->app->text_domain,
            "{$prefix}_VERSION"     => $this->app->version,
        ];
    }

    /**
     * Convert the plugin id to the constants prefix
     *
     * @since 0.1.0
     * @param string $plugin_id
     * @return string
     */
    public function prefix()
    {
        return strtoupper(str_replace('-', '_', $this->app->id));
    }
}
