<?php
/**
 * Configuration class
 *
 * @package    SB2Media\Headless\Config
 * @since      1.0.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\Config;

use Illuminate\Config\Repository;

class Config extends Repository
{
    /**
     * Retrieve the specific configuration item by id if it exists
     *
     * @since 1.0.0
     * @param string $id     The unique identifier of the configuration item
     * @param string $type   The type of configuration
     * @return array
     */
    public function getItem(string $id, string $type)
    {
        if ($this->has($type)) {
            foreach ($this->items[$type] as $item) {
                if (array_key_exists('id', $item) && $item['id'] === $id) {
                    return $item;
                }
            }
        }
    }
}
