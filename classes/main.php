<?php namespace BEA\Model_Factory;

/**
 * Class Main
 *
 * @package BEA\Model_Factory
 * @author  Maxime CULEA
 */
class Main {
	/**
	 * Use the trait
	 */
	use Singleton;

	protected function init() {
		add_action( 'init', array( $this, 'init_translations' ) );
	}

	/**
	 * Retrieve registered post type which has a model_class arg
	 * model_class is the post type's model class name to be used unstead WP_Post ones
	 * For using this, you must add "model_class" arg to the register_post_type
	 *
	 * @return array : post type => model name
	 * @author Maxime CULEA
	 */
	private static function get_mapping_models() {
		$all_pt = get_post_types( [ ], 'objects' );
		if ( empty( $all_pt ) ) {
			return array();
		}

		$mapping_model = array();
		foreach ( $all_pt as $pt ) {
			if ( ! isset( $pt->model_class ) || empty( $pt->model_class ) ) {
				continue;
			}
			$mapping_model[ $pt->name ] = $pt->model_class;
		}

		return $mapping_model;
	}

	/**
	 * Get the given post_type's model class name
	 *
	 * @param $post_type
	 *
	 * @return bool
	 * @author Maxime CULEA
	 */
	public static function get_mapping_model( $post_type ) {
		$mapping_model = self::get_mapping_models();

		return isset( $mapping_model[ $post_type ] ) ? $mapping_model[ $post_type ] : false;
	}

	/**
	 * Transform a WP_Post into the post type's model (model_class)
	 *
	 * @param \WP_Post $post
	 *
	 * @return \WP_Post
	 * @author Maxime CULEA
	 */
	public static function get_model( \WP_Post $post ) {
		if ( ! isset( $post->post_type ) || ! self::get_mapping_model( $post->post_type ) ) {
			return $post;
		}

		$klass = self::get_mapping_model( $post->post_type );

		/**
		 * Instantiate with the mapped model
		 */
		return new $klass( $post );
	}

	/**
	 * Make a wp query which not returns only WP_Post array but also mapped model depending on the post type
	 *
	 * @param array $args
	 *
	 * @return \WP_Query
	 * @author Maxime CULEA
	 */
	public static function wp_query_with_models( $args = array() ) {
		$query = new \WP_Query( $args );
		if ( ! $query->have_posts() ) {
			return $query;
		}
		return self::add_models_to_wp_query( $query );
	}

	/**
	 * Transform query's posts WP_Post into post type's model, if needed
	 *
	 * @param \WP_Query $query
	 *
	 * @return \WP_Query
	 * @author Maxime CULEA
	 */
	public static function add_models_to_wp_query( \WP_Query $query ) {
		if ( ! $query->have_posts() ) {
			return $query;
		}
		$posts_with_model = array_map( array( __CLASS__, 'get_model' ), $query->posts );
		/**
		 * Afterward, set $query->posts with modelized posts
		 */
		$query->posts = $posts_with_model;
		return $query;
	}

	/**
	 * Load the plugin translation
	 */
	public function init_translations() {
		// Load translations
		load_plugin_textdomain( 'bea-model-factory', false, BEA_MODEL_FACTORY_DIR . 'languages' );
	}
}

