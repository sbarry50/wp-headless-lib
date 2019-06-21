<?php
/**
 * GraphQL Resolver Contract
 *
 * @package    SB2Media\Headless\Contracts
 * @since      1.0.0
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
     * @since 1.0.0
     * @param array $config
     * @param mixed $value
     * @return void
     */
    public function resolve(array $config, $value);
}
