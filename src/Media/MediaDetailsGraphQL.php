<?php
/**
 * Class for resolving the MediaDetails object type
 *
 * @package    SB2Media\Headless\Media
 * @since      0.1.0
 * @author     sbarry
 * @link       http://example.com
 * @license    GNU General Public License 2.0+
 */

namespace SB2Media\Headless\Media;

use SB2Media\Headless\Contracts\GraphQLResolverContract;

class MediaDetailsGraphQL implements GraphQLResolverContract
{
    /**
     * Resolve the media details for GraphQL
     *
     * @since 0.1.0
     * @param array $config    The GraphQL configuration
     * @param mixed $value     The value of the setting/meta-field in the WP database
     * @param int   $post_id   The WP post unique identifier
     * @return void
     */
    public function resolve(array $config, $value, int $post_id = null)
    {
        $media_details = wp_get_attachment_metadata($value);
        $media_details['file'] = wp_get_attachment_url($value, 'full');
        $media_details['ID'] = $value;

        return $media_details;
    }
}
