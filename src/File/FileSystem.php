<?php
/**
 * Class for managing files.
 *
 * @package    SB2Media\Headless\File
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\File;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem as FileSystemBase;

class FileSystem extends FileSystemBase
{
    /**
     * Check if a file is present in a directory or its subdirectories
     *
     * @since 1.0.0
     * @param string $filename
     * @param string $directory
     * @return void
     */
    public function fileExistsInDirectory(string $filename, string $directory)
    {
        $files = $this->allFiles($directory);

        foreach ($files as $file) {
            $filenames[] = $file->getBasename();
        }
        
        while (Str::contains($filename, '/')) {
            $filename = Str::after($filename, '/');
        }

        return in_array($filename, $filenames);
    }

    public function getRelativeFilePath(string $filename, string $directory)
    {
        $files = $this->allFiles($directory);
        $path = '';

        foreach ($files as $file) {
            if ($filename === $file->getBasename()) {
                $path = $file->getPathname();
                break;
            }
        }
        
        return str_replace($directory, '', $path);
    }
}
