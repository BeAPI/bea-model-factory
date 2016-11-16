<?php
/*
 Plugin Name: BEA - Model factory
 Version: 1.1.0
 Plugin URI: http://www.beapi.fr
 Description: Add a model factory feature
 Author: BE API Technical team
 Author URI: http://www.beapi.fr
 Domain Path: languages
 Text Domain: bea-model-factory
 Network: true

 ----

 Copyright 2016 BE API Technical team (human@beapi.fr)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class BEA_Model_Factory {
	/**
	 * @var BEA_Model_Factory
	 * @author Maxime Culea
	 */
	public static $instance;

	/**
	 * Private
	 */
	private function __construct() {
	}

	/**
	 * @return BEA_Model_Factory
	 * @author Alexandre Sadowski
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new BEA_Model_Factory();
		}

		return self::$instance;
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
	 * @param WP_Post $post
	 *
	 * @return WP_Post
	 * @author Maxime CULEA
	 */
	public static function get_model( WP_Post $post ) {
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
	 * @return WP_Query
	 * @author Maxime CULEA
	 */
	public static function wp_query_with_models( $args = array() ) {
		$query = new WP_Query( $args );
		if ( ! $query->have_posts() ) {
			return $query;
		}

		return self::add_models_to_wp_query( $query );
	}

	/**
	 * Transform query's posts WP_Post into post type's model, if needed
	 *
	 * @param WP_Query $query
	 *
	 * @return WP_Query
	 * @author Maxime CULEA
	 */
	public static function add_models_to_wp_query( WP_Query $query ) {
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
}
