<?php
/**
 * Class for managing interaction with WPGraphQL and GraphQL-PHP
 *
 * @package    SB2Media\Headless\GraphQL
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\GraphQL;

use Exception;
use WPGraphQL\TypeRegistry;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use SB2Media\Headless\Application;
use SB2Media\Headless\Contracts\WordPressAPIContract;

abstract class GraphQLManager implements WordPressAPIContract
{
    /**
     * Application instance
     *
     * @since 0.3.0
     * @var Application
     */
    public $app;
    
    /**
     * GraphQL core scalars
     *
     * @since 0.1.0
     * @var array
     */
    public $core_scalars = [
        'Bool'    => '\WPGraphQL\Types::boolean',
        'Boolean' => '\WPGraphQL\Types::boolean',
        'Float'   => '\WPGraphQL\Types::float',
        'Number'  => '\WPGraphQL\Types::float',
        'Id'      => '\WPGraphQL\Types::id',
        'Int'     => '\WPGraphQL\Types::int',
        'Integer' => '\WPGraphQL\Types::int',
        'String'  => '\WPGraphQL\Types::string'
    ];

    /**
     * Functionality Configuration
     *
     * @since 0.1.0
     * @var array $config
     */
    public $config;

    /**
     * Constructor
     *
     * @since 0.1.0
     * @param array $config
     */
    public function __construct(Application $app, array $config)
    {
        $this->app = $app;
        $this->config = $config;
        $this->add();
    }

    /**
     * Register the extended functionality with WPGraphQL
     *
     * @since 0.1.0
     * @return void
     */
    abstract public function register();

    /**
     * Add extended functionality to WPGraphQL through hook API
     *
     * @since 0.1.0
     * @return void
     */
    abstract public function add();

    /**
     * Process the configuration for GraphQL
     *
     * @since 0.1.0
     * @param array $config
     * @return void
     */
    protected function graphqlConfig($config)
    {
        $graphql_config = [];

        if ('multi-select.php' === $config['callback'] || 'multi-checkbox.php' === $config['callback']) {
            $config['graphql']['type'] = ['list_of' => 'String'];
        }

        $graphql_config = [
            'id'            => isset($config['id']) ? $config['id'] : '',
            'description'   => isset($config['description']) ? $config['description'] : '',
            'page'          => isset($config['page']) ? $config['page'] : '',
            'type'          => isset($config['graphql']['type']) ? $config['graphql']['type'] : 'String',
            'resolver'      => isset($config['graphql']['resolver']) ? $config['graphql']['resolver'] : '',
            'default_value' => isset($config['graphql']['default_value']) ? $config['graphql']['default_value'] : null,
            'args'          => isset($config['args']) ? $config['args'] : '',
        ];

        return $graphql_config;
    }

    /**
     * Resolve the GraphQL type from the setting/meta-field configuration
     *
     * @since 0.1.0
     * @param string|array $type
     * @return mixed
     */
    protected function resolveType($type)
    {
        if (is_string($type)) {
            $resolved_type = TypeRegistry::get_type($type);

            if (! empty($resolved_type)) {
                return $resolved_type;
            } else {
                return null;
            }
        }

        return TypeRegistry::setup_type_modifiers($type);
    }

    /**
     * Resolve the value of the field for GraphQL
     *
     * @since 0.1.0
     * @param array $config
     * @param mixed $value
     * @return void
     */
    protected function resolveField(array $config, $value, int $post_id = null)
    {
        $type = $config['type'];

        if (is_array($type) && isset($type['non_null'])) {
            $type = $type['non_null'];
        }

        if (is_string($type)) {
            if ($this->isCoreScalar($type)) {
                if (is_null($config['default_value'])) {
                    return $this->resolveCoreScalars($type, $value);
                }
                
                return !empty($value) ? $this->resolveCoreScalars($type, $value) : $config['default_value'];
            }

            if (isset($config['resolver'])) {
                if (is_null($config['default_value'])) {
                    return $this->app->get($config['resolver'])->resolve($config, $value, $post_id);
                }
                
                return !empty($value) ? $this->app->get($config['resolver'])->resolve($config, $value, $post_id) : $config['default_value'];
            }
        }

        if (is_array($type) && isset($type['list_of'])) {
            if (is_string($type['list_of']) && $this->isCoreScalar($type['list_of'])) {
                if (is_null($config['default_value'])) {
                    return !empty($value) ? (array) $value : (array) [];
                }
                
                return !empty($value) ? (array) $value : (array) $config['default_value'];
            }

            if (is_string($type['list_of']) && isset($config['resolver'])) {
                if (is_null($config['default_value'])) {
                    return (array) $this->app->get($config['resolver'])->resolve($config, $value, $post_id);
                }
                
                return !empty($value) ? (array) $this->app->get($config['resolver'])->resolve($config, $value, $post_id) : (array) $config['default_value'];
            }
        }
    }

    /**
     * Check if type is a core scalar
     *
     * @since 0.1.0
     * @param string $type
     * @return boolean
     */
    protected function isCoreScalar(string $type)
    {
        return in_array($type, array_keys($this->core_scalars));
    }

    /**
     * Resolve core scalar types
     *
     * @since 0.1.0
     * @param string $type
     * @param mixed $value
     * @return void
     */
    protected function resolveCoreScalars(string $type, $value)
    {
        $class = Arr::get($this->core_scalars, $type);
        
        if (is_callable($class)) {
            return call_user_func($class)->serialize($value);
        }
    }
}
