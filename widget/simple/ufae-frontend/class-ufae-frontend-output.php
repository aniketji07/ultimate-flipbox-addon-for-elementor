<?php

namespace UFAE\Widget\Simple\Ufae_Frontend;

use UFAE\Widget\Simple\Ufae_Frontend\Ufae_Frontend_Item;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Ufae_Frontend_Output' ) ) {
	/**
	 * Class Ufae_Frontend_Output
	 *
	 * Handles the output for the Ultimate Flipbox Addon for Elementor.
	 */
	class Ufae_Frontend_Output {

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
		 * @var Ufae_Frontend_Item
		 */
		private $loop_obj;

		/**
		 * Ufae_Frontend_Output constructor.
		 *
		 * @param array  $settings Settings for the frontend output.
		 * @param object $parent_obj parent_obj widget instance.
		 */
		public function __construct( $settings, $parent_obj ) {
			$this->settings   = $settings;
			$this->parent_obj = $parent_obj;
			$this->loop_obj   = new Ufae_Frontend_Item( $settings );
		}

		/**
		 * Renders the frontend output.
		 */
		public function render() {
			$widget_id                  = $this->parent_obj->get_id();
			$layout                     = 'vertical';
			$animation                  = isset( $this->settings['ufae_simple_animation_option'] ) && ! empty( $this->settings['ufae_simple_animation_option'] ) ? $this->settings['ufae_simple_animation_option'] : 'flip';
			$animation_dir              = isset( $this->settings['ufae_simple_flip_direction'] ) && ! empty( $this->settings['ufae_simple_flip_direction'] ) ? '-' . $this->settings['ufae_simple_flip_direction'] : '-left';
			$animation_dir              = in_array($animation, array('flip', 'flip-classic', 'slide')) ? $animation_dir : '';
			$transition_time            = isset( $this->settings['ufae_simple_transition_duration'] ) && ! empty( $this->settings['ufae_simple_transition_duration'] ) ? $this->settings['ufae_simple_transition_duration'] : '1000';

			$this->parent_obj->add_render_attribute(
				'ufae_simple_container',
				array(
					'id'                   => 'ufae_simple_' . esc_attr( $widget_id ),
					'class'                => array(
						'ufae-container',
						'ufae-layout-' . esc_attr( $layout ),
					),
					'data-ufae-animation'  => esc_attr( $animation . $animation_dir ),
					'data-ufae-transition' => esc_attr( $transition_time ),
				)
			);

			echo '<div class="ufae-wrapper">';
			echo '<div ' . $this->parent_obj->get_render_attribute_string( 'ufae_simple_container' ) . '>';

			$this->loop_obj->flipbox_items();

			echo '</div>'; 
			echo '</div>';
		}
	}

}
