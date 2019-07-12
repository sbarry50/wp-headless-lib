<?php
/**
 * View finder class
 *
 * @package    SB2Media\Headless\View
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\View;

use SB2Media\Headless\File\Loader;
use function SB2Media\Headless\app;
use function SB2Media\Headless\view;

class View
{
    /**
     * Find the view file, pass it the data and render the output
     *
     * @since 0.1.0
     * @param string $file The relative view file path
     * @param array $data
     * @return void
     */
    public function render(string $file, array $data = [])
    {
        $file_path = view($file);

        if (file_exists($file_path) && is_readable($file_path)) {
            echo Loader::loadOutputFile($file_path, $data);
        }
    }
}
