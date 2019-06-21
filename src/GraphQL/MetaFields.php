<?php
/**
 * Class for registering custom meta fields with WPGraphQL
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
use function SB2Media\Headless\config;

class MetaFields extends GraphQLManager implements WordPressAPIContract
{
    /**
     * Register the custom GraphQL meta fields with WPGraphQL
     *
     * @since 1.0.0
     * @return void
     */
    public function register()
    {
        $post_types = \WPGraphQL::$allowed_post_types;
        $graphql_config = $this->graphqlConfig($this->config);

        if (! empty($post_types) && is_array($post_types)) {
            foreach ($post_types as $post_type) {
                $post_type_object = get_post_type_object($post_type);

                $meta_fields = $this->metaFieldsInPostType($post_type);
                $meta_fields = $this->graphqlConfig($meta_fields);

                foreach ($meta_fields as $meta_field) {
                    $type = $this->resolveType($meta_field['type']);

                    register_graphql_field($post_type_object->graphql_single_name, $meta_field['id'], [
                        'type' => $type,
                        'description' => $meta_field['description'],
                        'resolve' => function ($post) use ($meta_field, $type) {
                            $value = get_post_meta($post->ID, $meta_field['id'], true);
                            return $this->resolveField($meta_field, $value);
                        }
                    ]);
                }
            }
        }
    }

    /**
     * Add custom meta fields to WPGraphQL through hook API
     *
     * @since 1.0.0
     * @return void
     */
    public function add()
    {
        app('events')->addAction('graphql_register_types', [$this, 'register']);
    }

    /**
     * Retrieve all the meta fields associated with a post type
     *
     * @since 1.0.0
     * @param string $post_type
     * @return void
     */
    protected function metaFieldsInPostType(string $post_type)
    {
        $meta_boxes = config('meta-boxes');
        $fields = [];

        foreach ($this->config as $meta_field) {
            foreach ($meta_boxes as $meta_box) {
                if ($meta_field['meta_box'] === $meta_box['id'] && $post_type === $meta_box['screen']) {
                    $fields[] = $meta_field;
                }
            }
        }

        return $fields;
    }
}
