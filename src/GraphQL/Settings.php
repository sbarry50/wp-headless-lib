<?php
/**
 * Class for registering custom settings with WPGraphQL
 *
 * @package    SB2Media\Headless\GraphQL
 * @since      1.0.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\GraphQL;

use Illuminate\Support\Str;
use SB2Media\Headless\GraphQL\GraphQLManager;
use SB2Media\Headless\Contracts\WordPressAPIContract;
use SB2Media\Headless\Contracts\GraphQLResolverContract;
use function SB2Media\Headless\app;

class Settings extends GraphQLManager implements WordPressAPIContract
{
    /**
     * Register the custom GraphQL settings with WPGraphQL
     *
     * @since 1.0.0
     * @return void
     */
    public function register()
    {
        $filtered_settings = [];
        $graphql_config = $this->graphqlConfig($this->config);

        $this->filterSettings($graphql_config);
    }

    /**
     * Add custom GraphQL settings to WPGraphQL with hook API
     *
     * @since 1.0.0
     * @return void
     */
    public function add()
    {
        app('events')->addAction('graphql_init', [$this, 'register']);
    }

    /**
     * Filter the settings through GraphQL
     *
     * @since 1.0.0
     * @param array $settings
     * @return void
     */
    private function filterSettings($settings)
    {
        $camel_case_option_group = Str::camel(app('settings')->option_group);

        app('events')->addFilter('graphql_settings_fields', function ($fields) use ($settings) {
            return $this->resolveFilteredSettings($fields, $settings);
        });
    
        app('events')->addFilter("graphql_{$camel_case_option_group}Settings_fields", function ($fields) use ($settings) {
            return $this->resolveFilteredSettings($fields, $settings, true);
        });
    }

    /**
     * Resolve the parameters for the filtered settings
     *
     * @since 1.0.0
     * @param array $fields
     * @param array $settings
     * @param boolean $group
     * @return void
     */
    private function resolveFilteredSettings($fields, $settings, $group = false)
    {
        foreach ($settings as $setting) {
            if ($group) {
                $key = str_replace('_', '', $setting['id']);
                $name = Str::camel($setting['id']);
            } else {
                $key = str_replace('_', '', app('settings')->option_group) . 'settings' . str_replace('_', '', $setting['id']);
                $name = Str::camel(str_replace('_', '', ucwords(app('settings')->option_group, '_')) . 'Settings' . ucwords($setting['id'], '_'));
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
