<?php

namespace UFAE\Widget\Post\Frontend;

use UFAE\Widget\Post\Frontend\Ufae_Frontend_Loop;
use WP_Query;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (! class_exists('Ufae_Frontend_Output')) {
	/**
	 * Class Ufae_Frontend_Output
	 *
	 * Handles the output for the Ultimate Flipbox Addon for Elementor.
	 */
	class Ufae_Frontend_Output
	{

		/**
		 * Settings for the frontend output.
		 *
		 * @var array
		 */
		private $settings;

		/**
		 * Parent_obj widget instance.
		 *
		 * @var object
		 */
		private $parent_obj;

		/**
		 * Loop object for rendering flipbox items.
		 *
		 * @var Ufae_Frontend_Loop
		 */
		private $loop_obj;

		/**
		 * Ufae_Frontend_Output constructor.
		 *
		 * @param array  $settings Settings for the frontend output.
		 * @param object $parent_obj parent_obj widget instance.
		 */
		public function __construct($settings, $parent_obj)
		{
			$this->settings   = $settings;
			$this->parent_obj = $parent_obj;
			$this->loop_obj   = new Ufae_Frontend_Loop($settings);
		}

		/**
		 * Renders the frontend output.
		 */
		public function render()
		{
			$widget_id                  = $this->parent_obj->get_id();
			$layout                     = $this->settings['ufae_post_layout_option'];
			$animation                  = isset($this->settings['ufae_post_animation_option']) && ! empty($this->settings['ufae_post_animation_option']) ? $this->settings['ufae_post_animation_option'] : 'flip';
			$animation_dir              = isset($this->settings['ufae_post_flip_direction']) && ! empty($this->settings['ufae_post_flip_direction']) ? '-' . $this->settings['ufae_post_flip_direction'] : '-left';
			$animation_dir              = in_array($animation, array('flip', 'flip-classic', 'slide')) ? $animation_dir : '';
			$transition_time            = isset($this->settings['ufae_post_transition_duration']) && ! empty($this->settings['ufae_post_transition_duration']) ? $this->settings['ufae_post_transition_duration'] : '1000';
			$horizontal_layout          = 'horizontal' === $layout;
			$horizontal_container_class = $horizontal_layout ? 'ufae_horizontal_container' : '';

			$this->parent_obj->add_render_attribute(
				'ufae_post_container',
				array(
					'id'                   => 'ufae_post_' . esc_attr($widget_id),
					'class'                => array(
						'ufae-container',
						'ufae-layout-' . esc_attr($layout),
					),
					'data-ufae-animation'  => esc_attr($animation . $animation_dir),
					'data-ufae-transition' => esc_attr($transition_time),
				)
			);

			if ($horizontal_layout) {
				$this->parent_obj->add_render_attribute(
					'ufae_post_container',
					array(
						'class'               => array(
							esc_html($horizontal_container_class),
						),
						'data-ufae-slideview' => isset($this->settings['ufae_post_hr_slider_perview_control']) ? esc_attr($this->settings['ufae_post_hr_slider_perview_control']) : 2,
					)
				);
			}

			echo '<div class="ufae-wrapper">';
			echo '<div ' . $this->parent_obj->get_render_attribute_string('ufae_post_container') . '>';
			if ($horizontal_layout) {
				echo '<div class="ufae-swiper-container"><div class="swiper-wrapper">';
			}

			$this->ufae_post_post($this->settings);
	
			if ($horizontal_layout) {
				echo '</div></div>';
			}
			echo '</div>';

			if ($horizontal_layout) {
				echo '<div class="swiper-button-next"></div>';
				echo '<div class="swiper-button-prev"></div>';
				echo '<div class="ufae-swiper-pagination"></div>';
			}

			echo '</div>';
		}

		public function ufae_post_post($settings)
		{
			$posts = $this->ufae_post_query($settings);
			
			if ($posts->have_posts()) {
				while ($posts->have_posts()) {
					$posts->the_post();
					$this->loop_obj->flipbox_items(get_the_ID());
				}
				wp_reset_postdata(); // Close the post loop
			}
		}

		private function ufae_post_query($settings)
		{
			$post_type = !empty($settings['ufae_post_type']) ? sanitize_text_field($settings['ufae_post_type']) : 'post';
			$taxanomies = get_object_taxonomies($post_type);

			$taxanomy = [];
			$args = [
				'post_type' => $post_type,
				'post_status' => 'publish'
			];

			if (!empty($settings['ufae_post_order'])) {
				$args['order'] = sanitize_text_field($settings['ufae_post_order']);
			}

			if (!empty($settings['ufae_post_show_post'])) {
				$args['posts_per_page'] = intval($settings['ufae_post_show_post']);
			}

			foreach ($taxanomies as $taxanomie) {
				$taxonomie_value = !empty($settings['ufae_post_taxonomy_' . $taxanomie]) ? $settings['ufae_post_taxonomy_' . $taxanomie] : '';

				if (!empty($taxonomie_value)) {
					$taxanomy[] = [
						'taxonomy' => sanitize_key($taxanomie),
						'field' => 'slug',
						'terms' => sanitize_text_field($taxonomie_value),
					];
				}
			}

			if (!empty($taxanomy)) {
				$args['tax_query'] = count($taxanomy) > 1 ? array_merge(['relation' => 'AND'], $taxanomy) : $taxanomy;
			}

			$posts = new WP_Query($args);
			return $posts;
		}
	}
}
