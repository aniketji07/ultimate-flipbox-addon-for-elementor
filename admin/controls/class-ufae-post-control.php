<?php

namespace UFAE\Admin\Controls;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}


use Elementor\Group_Control_Base;

if (! class_exists('Ufae_Post_Control')) {
	/**
	 * Class Ufae_Post_Control
	 *
	 * This class handles the custom control for Elementor presets.
	 *
	 * @since 1.0.0
	 */
	class Ufae_Post_Control extends \Elementor\Group_Control_Base
	{

		/**
		 * Fields.
		 *
		 * Holds all the border control fields.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @static
		 *
		 * @var array Border control fields.
		 */
		protected static $fields;

		/**
		 * Get border control type.
		 *
		 * Retrieve the control type, in this case `border`.
		 *
		 * @since 1.0.0
		 * @access public
		 * @static
		 *
		 * @return string Control type.
		 */
		public static function get_type()
		{
			return 'ufae_post_control';
		}

		/**
		 * Init fields.
		 *
		 * Initialize border control fields.
		 *
		 * @since 1.2.2
		 * @access protected
		 *
		 * @return array Control fields.
		 */
		protected function init_fields()
		{
			$fields = [];

			$post_types = $this->ufae_get_post_type();
			$post_data = $this->ufae_post_data($post_types);

			$fields['type'] = [
				'label' => esc_html__('Post Type', 'ufae'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $post_types,
				'default' => 'post',
			];

			foreach ($post_data as $post_type => $taxonomies) {
				foreach($taxonomies as $taxonomy => $taxonomy_data){
					$fields['taxonomy_'.$taxonomy] = [
						'label' => esc_html__($taxonomy_data['name'], 'ufae'),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => array_merge(['' => esc_html__('Select '.$taxonomy_data['name'], 'ufae')], $taxonomy_data['terms']),
						'default' => '',
						'condition' => [
							'type' => $post_type,
						],
					];
				}
			}

			return $fields;
		}

		public function ufae_post_data($post_types)
		{ ?>
				<?php
				$ufae_post_data = [];
				foreach ($post_types as $post_type => $label) {
					$taxanomies = get_object_taxonomies($post_type);

					foreach ($taxanomies as $taxonomy) {
						$terms = get_terms([
							'taxonomy' => $taxonomy,
							'hide_empty' => false,
						]);

						$taxonomy_data = get_taxonomy($taxonomy);

						if (!isset($ufae_post_data[$post_type])) {
							$ufae_post_data[$post_type] = [];
						}

						if (!isset($ufae_post_data[$post_type][$taxonomy])) {
							$ufae_post_data[$post_type][$taxonomy] = ['name' => $taxonomy_data->label, 'terms' => []];
						}

						foreach ($terms as $term) {
							$ufae_post_data[$post_type][$taxonomy]['terms'][$term->slug] = $term->name;
						}
					}
				}

				return $ufae_post_data;
			}

			public function ufae_get_post_type()
			{
				$post_types = get_post_types(
					array(
						'public'            => true,
						'show_in_nav_menus' => true,
					),
					'objects'
				);
				$post_types = wp_list_pluck($post_types, 'label', 'name');

				return array_diff_key($post_types, array('elementor_library', 'attachment'));
			}

			/**
			 * Get default options.
			 *
			 * Retrieve the default options of the border control. Used to return the
			 * default options while initializing the border control.
			 *
			 * @since 1.9.0
			 * @access protected
			 *
			 * @return array Default border control options.
			 */
			protected function get_default_options()
			{
				return [
					'popover' => false,
				];
			}
		}
	}
