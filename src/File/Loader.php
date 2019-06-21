<?php
/**
 * Class that loads views, files and assets.
 *
 * @package    SB2Media\Headless\File
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\File;

use Exception;
use SB2Media\Headless\Setup\Compatibility;
use SB2Media\Headless\Contracts\LoaderContract;
use function SB2Media\Headless\app;

class Loader implements LoaderContract
{
    /**
     * Loads a file
     *
     * @since  0.1.0
     * @param  string    $file     The direct path and filename of the file to be loaded
     * @return string              The contents of the file
     */
    public static function loadFile($file)
    {
        if (self::isValidFile($file)) {
            return include $file;
        }
    }

    /**
     * Load a view file or asset that requires output buffering
     *
     * @since  0.1.0
     * @param  string    $file     The direct path and filename of the file to be loaded
     * @param  mixed     $args     (Opt) Arguments to pass to the file
     * @return string              The contents of the file
     */
    public static function loadOutputFile($file, $args = [])
    {
        if (self::isValidFile($file)) {
            ob_start();

            include $file;

            return ob_get_clean();
        }
    }

    /**
     * Check if the file is valid. Throws error exceptions if not.
     *
     * @since  0.1.0
     * @param  string    $file    The file
     * @return bool
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function isValidFile($file)
    {
        if (! file_exists($file)) {
            throw new Exception(sprintf('%s %s', __('The file does not exist.', app()->text_domain), $file));
        }

        if (! is_readable($file)) {
            throw new Exception(sprintf('%s %s', __('The file is not readable', app()->text_domain), $file));
        }

        return true;
    }
}
