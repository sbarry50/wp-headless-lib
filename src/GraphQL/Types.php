<?php
/**
 * Class for registering custom types with WPGraphQL
 *
 * @package    SB2Media\Headless\GraphQL
 * @since      1.0.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\GraphQL;

use SB2Media\Headless\GraphQL\GraphQLManager;
use SB2Media\Headless\Contracts\WordPressAPIContract;
use function SB2Media\Headless\app;

class Types extends GraphQLManager implements WordPressAPIContract
{
    /**
     * Register custom GraphQL types with WPGraphQL
     *
     * @since 1.0.0
     * @return void
     */
    public function register()
    {
        foreach ($this->config as $config) {
            $config['type'] = lcfirst($config['type']);

            if (!$this->isValidType($config['type'])) {
                throw new Exception(printf(__("GraphQL {$config['type']} type invalid. Allowed values are 'enum', 'object', 'union', 'scalar' and 'interface'", app()->text_domain)));
            }

            $register_function = 'register_graphql_' . lcfirst($config['type']) . '_type';

            $register_function($config['id'], $config['args']);
        }
    }

    /**
     * Add custom GraphQL types through hook API
     *
     * @since 1.0.0
     * @return void
     */
    public function add()
    {
        app('events')->addAction('graphql_register_types', [$this, 'register']);
    }

    /**
     * Check if type to be registered is a valid GraphQL type
     *
     * @since 1.0.0
     * @param string $type
     * @return boolean
     */
    private function isValidType(string $type)
    {
        $allowed_types = ['enum', 'object', 'union', 'scalar', 'interface'];

        return in_array($type, $allowed_types) ? true : false;
    }
}
