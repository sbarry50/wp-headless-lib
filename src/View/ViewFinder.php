<?php
/**
 * View composer class
 *
 * @package    SB2Media\Headless\View
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\View;

use InvalidArgumentException;
use SB2Media\Headless\File\Loader;
use function SB2Media\Headless\app;
use function SB2Media\Headless\view;

class ViewFinder
{
    /**
     * Find the view file, pass it the configuration and render the output
     *
     * @since 0.1.0
     * @param string $filename
     * @param array $config
     * @param boolean $field
     * @return void
     */
    public function render(string $filename, array $config = [], bool $field = false)
    {
        if ('image-upload' == $filename) {
            $config = app('media')->imageUploadFilter($config);
        }

        $file = $this->viewFilePath($filename, $field);

        if (file_exists($file) && is_readable($file)) {
            echo Loader::loadOutputFile($file, $config);
        }
    }

    /**
     * Get the fully qualified file path of the view in either the plugin or the framework.
     *
     * @since 0.1.0
     * @param string    $filename
     * @param boolean   $field
     * @return string
     */
    private function viewFilePath(string $filename, bool $field)
    {
        return $field ? view("fields/{$filename}") : view($filename);
    }
}
