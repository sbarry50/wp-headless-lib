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
use SB2Media\Headless\Contracts\WordPressAPIContract;
use function SB2Media\Headless\app;

abstract class GraphQLManager implements WordPressAPIContract
{
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
    public function __construct(array $config)
    {
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

        foreach ($config as $cfg) {
            if ('image-upload' === $cfg['callback']) {
                $cfg = app('media')->imageUploadFilter($cfg);
                $cfg['graphql']['type'] = 'MediaDetails';
                $cfg['graphql']['resolver'] = 'media-details-graphql';
            }

            if ('multi-select' === $cfg['callback'] || 'multi-checkbox' === $cfg['callback']) {
                $cfg['graphql']['type'] = ['list_of' => 'String'];
            }

            $graphql_config[] = [
                'id'            => isset($cfg['id']) ? $cfg['id'] : '',
                'description'   => isset($cfg['description']) ? $cfg['description'] : '',
                'image'         => ('image-upload' === $cfg['callback']) ? true : false,
                'type'          => isset($cfg['graphql']['type']) ? $cfg['graphql']['type'] : 'String',
                'resolver'      => isset($cfg['graphql']['resolver']) ? $cfg['graphql']['resolver'] : '',
                'default_value' => isset($cfg['graphql']['default_value']) ? $cfg['graphql']['default_value'] : null,
                'args'          => isset($cfg['args']) ? $cfg['args'] : '',
            ];
        }

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
    protected function resolveField(array $config, $value)
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
                    return app($config['resolver'])->resolve($config, $value);
                }
                
                return !empty($value) ? app($config['resolver'])->resolve($config, $value) : $config['default_value'];
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
                    return (array) app($config['resolver'])->resolve($config, $value);
                }
                
                return !empty($value) ? (array) app($config['resolver'])->resolve($config, $value) : (array) $config['default_value'];
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
