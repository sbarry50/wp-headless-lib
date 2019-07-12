<?php
/**
 * GraphQL Resolver Contract
 *
 * @package    SB2Media\Headless\Contracts
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\Contracts;

interface GraphQLResolverContract
{
    /**
     * Resolve the setting/meta-field for WPGraphQL. Should account for empty values.
     *
     * @since 0.1.0
     * @param array $config    The GraphQL configuration
     * @param mixed $value     The value of the setting/meta-field in the WP database
     * @param int   $post_id   The WP post unique identifier
     * @return void
     */
    public function resolve(array $config, $value, int $post_id = null);
}
