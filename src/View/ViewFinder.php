<?php
/**
 * View composer class
 *
 * @package    SB2Media\Headless\View
 * @since      1.0.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\View;

use InvalidArgumentException;
use SB2Media\Headless\File\Loader;
use function SB2Media\Headless\app;
use function SB2Media\Headless\url;
use function SB2Media\Headless\path;

class ViewFinder
{
    /**
     * Find the view file, pass it the configuration and render the output
     *
     * @since 1.0.0
     * @param string $filename
     * @param array $config
     * @param boolean $field
     * @return void
     */
    public function render(string $filename, array $config = [], bool $field = false)
    {
        if ('image-upload' == $filename) {
            $config = app('images')->imageUploadFilter($config);
        }

        $locations = ['plugin', 'framework'];

        foreach ($locations as $location) {
            $file = $this->viewFilePath($filename, $field, $location);

            if (file_exists($file) && is_readable($file)) {
                echo Loader::loadOutputFile($file, $config);
                break;
            }
        }
    }

    /**
     * Get the fully qualified file path of the view in either the plugin or the framework.
     *
     * @since 1.0.0
     * @param string    $filename
     * @param boolean   $field
     * @param string    $location   Whether to check in plugin or framework view folder. Possible values 'plugin' or 'framework'
     * @return string
     */
    private function viewFilePath(string $filename, bool $field, string $location)
    {
        if ('plugin' === $location) {
            $path = path('views');
        } elseif ('framework' === $location) {
            $path = dirname(dirname(dirname(__FILE__))) . '/views/';
        } else {
            throw new InvalidArgumentException(__("'{$location}' is not a valid location argument. Allowed values are 'plugin' or 'framework'", app()->text_domain));
        }

        $path .= $field ? 'fields/' : '';
        
        return $path . $filename . '.php';
    }
}
