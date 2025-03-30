<?php

namespace UFAE\Widget\Post\Frontend;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

use Elementor\Icons_Manager;
use EmptyIterator;

if (! class_exists('Ufae_Frontend_Loop')) {
	/**
	 * Class Ufae_Frontend_Loop
	 *
	 * This class handles the frontend loop for the Ultimate Flipbox Addon for Elementor.
	 * It manages the rendering of flipbox items and their settings.
	 */
	class Ufae_Frontend_Loop
	{

		/**
		 * This array contains the configuration settings for the flipbox frontend rendering.
		 *
		 * @var array $settings The settings for the frontend loop.
		 */
		private $settings = array();

		/**
		 * This integer holds the post id that will be rendered in the frontend.
		 *
		 * @var int $post_id The post id.
		 */
		private $post_id = 0;

		/**
		 * This array defines the sequence in which the front elements (icon, title, description, button) are rendered.
		 *
		 * @var array $front_element_order The order of elements displayed on the front side of the flipbox.
		 */
		private $front_element_order = array();

		/**
		 * This array defines the sequence in which the back elements (icon, title, description, button) are rendered.
		 *
		 * @var array $back_element_order The order of elements displayed on the back side of the flipbox.
		 */
		private $back_element_order = array();

		/**
		 * Ufae_Frontend_Loop constructor.
		 *
		 * This constructor accepts settings as a parameter and assigns them to the instance variable.
		 *
		 * @param array $settings The settings to be used for the frontend loop.
		 */
		public function __construct($settings)
		{
			$this->settings = $settings;

			$this->front_element_order = array(
				'icon',
				'title',
				'desc',
				'button',
			);

			$this->back_element_order = array(
				'icon',
				'title',
				'desc',
				'button',
			);

			$this->update_element_order();
		}

		/**
		 * Renders the flipbox items.
		 *
		 * This method outputs the HTML structure for a flipbox item, including the front and back sides.
		 *
		 * @param array $item The item data to render.
		 */
		public function flipbox_items($post_id)
		{
			$this->post_id               = absint($post_id);
			$layout                = $this->settings['ufae_post_layout_option'];
			$horizontal_layout     = 'horizontal' === $layout;
			$horizontal_item_class = $horizontal_layout ? ' swiper-slide' : '';
			$animation             = isset($this->settings['ufae_post_animation_option']) && ! empty($this->settings['ufae_post_animation_option']) ? $this->settings['ufae_post_animation_option'] : 'flip';

			echo '<div class="ufae-flipbox-item ufae-post-' . esc_attr($this->post_id) . esc_attr($horizontal_item_class) . '">';
			echo '<div class="ufae-flipbox-inner">';
			echo '<div class="ufae-flipbox-inner-overlay">';
			$this->render_sides_content('front', '');
			if ('curtain' === $animation) {
				$this->render_sides_content('front', ' ufae-front_duplicate');
				$this->render_sides_content('front', ' ufae-front-duplicate_overlay');
			}
			$this->render_sides_content('back', '');
			echo '</div>';
			echo '</div>';
			echo '</div>';
		}

		/**
		 * Renders the content for the specified side of the flipbox.
		 *
		 * This method outputs the HTML structure for the specified side (front or back) of the flipbox,
		 * including the elements in the defined order.
		 *
		 * @param string $side The side of the flipbox to render ('front' or 'back').
		 */
		private function render_sides_content($side, $wrp_cls = '')
		{
			$side = esc_html($side);

			$element_order = $this->{$side . '_element_order'};
			$bg_type = isset($this->settings['ufae_post_container_' . $side . '_bg_type']) && ! empty($this->settings['ufae_post_container_' . $side . '_bg_type']) ? $this->settings['ufae_post_container_' . $side . '_bg_type'] : '';
			$feature_image_enable = isset($this->settings['ufae_post_container_' . $side . '_bg_featured_image']) && 'yes' !== $this->settings['ufae_post_container_' . $side . '_bg_featured_image'] ? 'no' : 'yes';

			$wrapper_attr = [
				"class" => ['ufae-flipbox-' . esc_attr($side), 'ufae-bg-' . esc_attr($bg_type)]
			];

			if (!empty($wrp_cls)) {
				$wrapper_attr['class'][] = esc_attr($wrp_cls);
			}

			if ('image' === $bg_type) {
				$image_exists = false;
				if ('yes' === $feature_image_enable) {

					$thumbnail_id = get_post_thumbnail_id($this->post_id);

					if ($thumbnail_id) {
						$thumbnail_url = wp_get_attachment_image_url($thumbnail_id, 'full');

						if (!empty($thumbnail_url)) {
							$image_exists = true;
							$wrapper_attr['style'] = ["--ufae-box-" . esc_attr($side) . "-bg-image: url('" . esc_url($thumbnail_url) . "');"];
						}
					}
				} else {
					$bg_image = isset($this->settings['ufae_post_container_' . $side . '_bg_image']) ? $this->settings['ufae_post_container_' . $side . '_bg_image'] : false;

					if ($bg_image && isset($bg_image['url']) && !empty($bg_image['url'])) {
						$image_exists = true;
					}
				}

				if (!$image_exists) {
					unset($wrapper_attr['class'][1]);
				}
			}

			if (empty($bg_type) || 'simple' === $bg_type) {
				unset($wrapper_attr['class'][1]);
			}

			echo '<div class="' . esc_attr(implode(' ', $wrapper_attr['class'])) . '"' . (isset($wrapper_attr['style']) ? ' style="' . implode(' ', $wrapper_attr['style']) . '"' : '') . '>';
			// echo '<div class="ufae-flipbox-' . esc_attr($side) . esc_attr($wrp_cls) . ' ufae-bg-' . esc_attr($bg_type) . '">';
			echo '<div class="ufae-flipbox-content-overlay">';
			echo '<div class="ufae-flipbox-content">';

			foreach ($element_order as $element) {

				$element_enable = isset($this->settings['ufae_post_' . $side . '_' . $element . '_enable']) && 'yes' !== $this->settings['ufae_post_' . $side . '_' . $element . '_enable'] ? false : true;

				if (! $element_enable) {
					continue;
				}

				switch ($element) {
					case 'icon':
						$this->render_icon($side);
						break;
					case 'title':
						$this->render_title($side);
						break;
					case 'desc':
						$this->render_desc($side);
						break;
					case 'button':
						$this->render_button($side);
						break;
				}
			}

			echo '</div>';
			echo '</div>';
			echo '</div>';
		}

		/**
		 * Renders the icon for the specified side of the flipbox.
		 *
		 * This method outputs the HTML for the icon based on the icon type (icon, image, or text).
		 *
		 * @param string $side The side of the flipbox to render the icon for ('front' or 'back').
		 */
		private function render_icon($side)
		{
			$side      = esc_html($side);
			$icon_enable = isset($this->settings['ufae_post_' . $side . '_icon_enable']) && ! empty($this->settings['ufae_post_' . $side . '_icon_enable']) ? $this->settings['ufae_post_' . $side . '_icon_enable'] : false;
			$icon_option = isset($this->settings['ufae_post_' . $side . '_icon_option']) && ! empty($this->settings['ufae_post_' . $side . '_icon_option']) ? $this->settings['ufae_post_' . $side . '_icon_option'] : false;

			if ($icon_enable === "yes" && $icon_option) {
				if ('custom_icon' === $icon_option) {
					echo '<div class="ufae-icon-wrapper">';
					Icons_Manager::render_icon(
						$this->settings['ufae_post_' . $side . '_custom_icon'],
						array(
							'aria-hidden' => 'true',
							'class'       => 'ufae-icon',
						)
					);
					echo '</div>';
				} else if ('feature_image' === $icon_option) {
					$thumbnail_id = get_post_thumbnail_id($this->post_id);
					if ($thumbnail_id) {
						$thumbnail_url = wp_get_attachment_image_url($thumbnail_id, 'full');
						if (!empty($thumbnail_url)) {
							echo '<div class="ufae-icon-wrapper">';
							echo '<img src="' . esc_url($thumbnail_url) . '" alt="' . esc_attr(get_the_title($this->post_id)) . '">';
							echo '</div>';
						}
					}
				}
			}
		}

		/**
		 * Renders the title for the specified side of the flipbox.
		 *
		 * This method outputs the HTML for the title element, using the specified title tag.
		 *
		 * @param string $side The side of the flipbox to render the title for ('front' or 'back').
		 */
		private function render_title($side)
		{
			$title = get_the_title($this->post_id);

			$allowed_tags 	= array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span', 'div');
			if ($title && !empty($title)) {
				$title_tag = isset($this->settings['ufae_post_title_tag']) && !empty($this->settings['ufae_post_title_tag']) ? $this->settings['ufae_post_title_tag'] : 'h3';
				$title_tag = in_array($title_tag, $allowed_tags) ? $title_tag : 'h2';
				echo '<' . esc_html(esc_attr($title_tag)) . ' class="ufae-title">' . esc_html($title) . '</' . esc_html(esc_attr($title_tag)) . '>';
			}
		}

		/**
		 * Renders the description for the specified side of the flipbox.
		 *
		 * This method outputs the HTML for the description element.
		 *
		 * @param string $side The side of the flipbox to render the description for ('front' or 'back').
		 */
		private function render_desc($side)
		{
			$description = isset($this->settings['ufae_post_' . $side . '_description']) && ! empty($this->settings['ufae_post_' . $side . '_description']) ? $this->settings['ufae_post_' . $side . '_description'] : false;

			$description = get_the_excerpt($this->post_id);

			if (empty($description)) {
				$description = get_the_content($this->post_id);
			}

			$length = isset($this->settings['ufae_post_' . $side . '_desc_length']) && !empty($this->settings['ufae_post_' . $side . '_desc_length']) ? $this->settings['ufae_post_' . $side . '_desc_length'] : 20;
			$length = (int) $length;

			$description = wp_trim_words($description, $length, '...');

			if ($description && !empty($description)) {
				echo '<p class="ufae-desc">' . wp_kses_post($description) . '</p>';
			}
		}

		/**
		 * Renders the button for the specified side of the flipbox.
		 *
		 * This method outputs the HTML for the button element, including the URL and attributes.
		 *
		 * @param string $side The side of the flipbox to render the button for ('front' or 'back').
		 */
		private function render_button($side)
		{
			$button_enable = isset($this->settings['ufae_post_' . $side . '_button_enable']) ? esc_html($this->settings['ufae_post_' . $side . '_button_enable']) : 'no';
			$btn_text      = isset($this->settings['ufae_post_' . $side . '_button_text']) && ! empty($this->settings['ufae_post_' . $side . '_button_text']) ? $this->settings['ufae_post_' . $side . '_button_text'] : 'Read More';

			if ('yes' === $button_enable) {
				$btn_url = get_the_permalink($this->post_id);

				echo '<div class="ufae-btn-wrapper">';
				if ($btn_url) {
					$blank_attr    = '_blank';
					$nofollow_attr = 'nofollow';

					echo '<a href="' . esc_url($btn_url) . '" class="ufae-button" target="' . esc_attr($blank_attr) . '" rel="' . esc_attr($nofollow_attr) . '" ' .  '>' . esc_html($btn_text) . '</a>';
				} else {
					echo '<button class="ufae-button">' . esc_html($btn_text) . '</button>';
				}
				echo '</div>';
			}
		}

		/**
		 * Updates the order of elements for the front and back sides of the flipbox.
		 *
		 * This method processes the settings to determine the order in which elements are displayed.
		 */
		private function update_element_order()
		{
			$sides              = array('front', 'back');
			$predefined_element = array('icon', 'title', 'desc', 'button');

			foreach ($sides as $side) {
				$order_settings = isset($this->settings['ufae_post_' . $side . '_element_position']) ? $this->settings['ufae_post_' . $side . '_element_position'] : '';
				$order_settings = esc_html(trim($order_settings));
				$new_order      = array();

				if (! empty($order_settings) && preg_match('/\b(icon|title|desc|button)\b/', $order_settings)) {
					$element_names = array_unique(array_filter(explode(',', $order_settings)));

					foreach ($element_names as $element_name) {
						if (in_array($element_name, $predefined_element, true)) {
							$new_order[] = $element_name;
						}
					}

					$new_order = array_merge($new_order, array_diff($predefined_element, $new_order));

					$this->{$side . '_element_order'} = $new_order;
				}
			}
		}
	}
}
