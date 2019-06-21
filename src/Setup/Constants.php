<?php
/**
 * Class that defines the plugin's constants.
 *
 * @package    SB2Media\Headless
 * @since      1.0.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */
namespace SB2Media\Headless\Setup;

use SB2Media\Headless\Contracts\ConstantsContract;
use function SB2Media\Headless\app;

class Constants implements ConstantsContract
{
    /**
     * The constants array
     *
     * @var array
     */
    public $constants = [];

    /**
     * Constructor
     *
     * @since  1.0.0
     */
    public function __construct()
    {
        $this->constants = $this->build();
        $this->define();
    }

    /**
     * Defines the plugin's constants
     *
     * @since  1.0.0
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
     * @since 1.0.0
     * @return array    $this->constants    The plugin constants
     */
    public function add(array $constants)
    {
        $this->constants = array_merge($this->constants, $constants);

        return $this;
    }

    /**
     * Get the array of constants
     * @since  1.0.0
     * @return array    $this->constants    Plugin constants
     */
    public function get()
    {
        return $this->constants;
    }

    /**
     * Build the constants configuration array
     *
     * @since   1.0.0
     * @return array
     */
    protected function build()
    {
        $prefix = self::prefix();

        return [
            "{$prefix}_ROOT"        => app()->root,
            "{$prefix}_NAME"        => app()->name,
            "{$prefix}_BASENAME"    => app()->basename,
            "{$prefix}_DIR_PATH"    => app()->path,
            "{$prefix}_DIR_URL"     => app()->url,
            "{$prefix}_TEXT_DOMAIN" => app()->text_domain,
            "{$prefix}_VERSION"     => app()->version,
        ];
    }

    /**
     * Convert the plugin id to the constants prefix
     *
     * @since 1.0.0
     * @param string $plugin_id
     * @return string
     */
    public static function prefix()
    {
        return strtoupper(str_replace('-', '_', app()->id));
    }
}
