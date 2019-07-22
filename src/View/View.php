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

use SB2Media\Headless\Application;
use SB2Media\Headless\File\Loader;

class View
{
    /**
     * Application instance
     *
     * @since 0.3.0
     * @var Application
     */
    public $app;
 
    /**
     * Constructor
     *
     * @since 0.3.0
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Find the view file, pass it the data and render the output
     *
     * @since 0.1.0
     * @param string $file The relative view file path
     * @param mixed $data
     * @return void
     */
    public function render(string $file, $data = [])
    {
        $file_path = $this->app->view($file);

        if (file_exists($file_path) && is_readable($file_path)) {
            echo Loader::loadOutputFile($file_path, $data);
        }
    }
}
