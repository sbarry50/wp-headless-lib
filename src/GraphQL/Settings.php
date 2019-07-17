<?php
/**
 * Class for registering custom settings with WPGraphQL
 *
 * @package    SB2Media\Headless\GraphQL
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\GraphQL;

use Illuminate\Support\Str;
use SB2Media\Headless\Events\EventManager;
use SB2Media\Headless\GraphQL\GraphQLManager;
use SB2Media\Headless\Contracts\WordPressAPIContract;
use SB2Media\Headless\Contracts\GraphQLResolverContract;
use function SB2Media\Headless\app;

class Settings extends GraphQLManager implements WordPressAPIContract
{
    /**
     * Register the custom GraphQL settings with WPGraphQL
     *
     * @since 0.1.0
     * @return void
     */
    public function register()
    {
        EventManager::addFilter('graphql_settings_fields', function ($fields) {
            return $this->resolveFilteredSettings($fields, $this->config);
        });

        $page_slugs = [];
        foreach ($this->config as $config) {
            if (!in_array($config['page'], $page_slugs)) {
                $page_slugs[] = $config['page'];
            }
        }

        foreach ($page_slugs as $page_slug) {
            $page_config = [];
            foreach ($this->config as $config) {
                if ($config['page'] === $page_slug) {
                    $page_config[] = $config;
                }
            }

            $camel_case_option_group = Str::camel($page_slug);

            EventManager::addFilter("graphql_{$camel_case_option_group}Settings_fields", function ($fields) use ($page_config) {
                return $this->resolveFilteredSettings($fields, $page_config, true);
            });
        }
    }

    /**
     * Add custom GraphQL settings to WPGraphQL with hook API
     *
     * @since 0.1.0
     * @return void
     */
    public function add()
    {
        EventManager::addAction('graphql_init', [$this, 'register']);
    }

    /**
     * Resolve the parameters for the filtered settings
     *
     * @since 0.1.0
     * @param array $fields
     * @param array $settings
     * @param boolean $group
     * @return void
     */
    private function resolveFilteredSettings($fields, $settings, $group = false)
    {
        foreach ($settings as $setting) {
            $setting = $this->graphqlConfig($setting);

            if ($group) {
                $key = str_replace('_', '', $setting['id']);
                $name = Str::camel($setting['id']);
            } else {
                $key = str_replace('-', '', $setting['page']) . 'settings' . str_replace('_', '', $setting['id']);
                $name = Str::camel(str_replace('_', '', ucwords($setting['page'], '_')) . 'Settings' . ucwords($setting['id'], '_'));
            }

            $type = $this->resolveType($setting['type']);

            $fields[$key] = [
                'type' => $type,
                'description' => $setting['description'],
                'resolve' => function ($root, $args, $context, $info) use ($setting, $type) {
                    $value = get_option($setting['id']);
                    return $this->resolveField($setting, $value);
                },
                'name' => $name,
            ];
        }

        return $fields;
    }
}
