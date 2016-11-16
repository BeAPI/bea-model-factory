<?php namespace BEA\Model_Factory;

class Functions {

	/**
	 * Escape by preserving the newlines : <br />
	 * By applying wp_kses on a textarea
	 *
	 * @param $string
	 *
	 * @return mixed
	 * @author Maxime CULEA
	 */
	public static function esc_textarea( $string ) {
		return trim( str_replace( '%newline%', '<br />', wp_kses( str_replace( '<br />', '%newline%', $string ), '' ) ) );
	}

	/**
	 * Get the given post_id excerpt
	 * @param $post_id
	 *
	 * @return string
	 * @author Maxime CULEA
	 */
	public static function get_the_excerpt( $post_id ) {
		$post = get_post( $post_id );
		return ! empty( $post->post_excerpt ) ? $post->post_excerpt : '';
	}

	/**
	 * Generate tiny url
	 *
	 * @author   Maxime Culea
	 *
	 * @param string $post_id
	 *
	 * @return string
	 */
	public static function get_tiny_url( $url = '' ) {
		$url     = ! empty( $url ) ? $url : get_the_permalink();
		$results = wp_remote_get( add_query_arg( [ 'url' => urlencode( $url ) ], 'http://tinyurl.com/api-create.php' ) );

		if ( is_wp_error( $results ) ) {
			return $url;
		}

		if ( 200 !== (int) wp_remote_retrieve_response_code( $results ) ) {
			return $url;
		}

		$tiny_url = wp_remote_retrieve_body( $results );
		return ! empty( $tiny_url ) ? $tiny_url : $url;
	}

	/**
	 * Manage to generate html form the given args
	 *
	 * @param $args
	 *
	 * @return string
	 * @author Maxime CULEA
	 */
	public static function get_generate_html( $args ) {
		if ( empty( $args ) ) {
			return '';
		}

		$html_args = [];
		foreach ( $args as $attribute => $values ) {
			if ( is_array( $values ) ) {
				$value = implode( ' ', $values );
			} else {
				$value = $values;
			}

			$html_args[] = sprintf( '%s="%s"', esc_html( $attribute ), esc_attr( $value ) );
		}

		return ! empty( $html_args ) ? implode( ' ', $html_args ) : '';
	}

	/**
	 * Display the generated html from given args
	 *
	 * @param $args
	 *
	 * @author Maxime CULEA
	 */
	public static function the_generate_html( $args ) {
		$html = self::get_generate_html( $args );
		echo ! empty( $html ) ? sprintf( " %s", $html ) : '';
	}

	/**
	 * Manage to reformat wp_args for a rest api query
	 * 
	 * @param $args
	 *
	 * @return array
	 * @author Maxime CULEA
	 */
	public static function format_rest_api_args( $args ) {
		$formatted_args = [];
		if ( empty ( $args ) ) {
			return $formatted_args;
		}

		foreach ( $args as $arg_name => $arg_value ) {
			if ( is_array( $arg_value ) ) {
				/** Array implementation */
				foreach ( $arg_value as $key => $value ) {
					/** Preserve string key */
					$formatted_args['filter['.$arg_name.']['.$key.']'] = $value;
				}
			} else {
				/** Single value implementation */
				$formatted_args['filter['.$arg_name.']'] = $arg_value;
			}
		}

		return $formatted_args;
	}
}
