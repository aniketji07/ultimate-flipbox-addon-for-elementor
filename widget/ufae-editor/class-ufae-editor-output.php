<?php

namespace Ultimate_Flipbox_Addon_For_Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Ultimate_Flipbox_Addon_For_Elementor\Ufae_Editor_Loop;
if ( ! class_exists( 'Ufae_Editor_Output' ) ) {
	/**
	 * Class Ufae_Editor_Output
	 *
	 * Handles the output rendering for the Ultimate Flipbox Addon for Elementor.
	 */
	class Ufae_Editor_Output {

		/**
		 * Instance of the Ufae_Editor_Loop class.
		 *
		 * This property holds the loop object responsible for rendering
		 * the flipbox items in the editor output.
		 *
		 * @var Ufae_Editor_Loop
		 */
		private $loop_obj;

		/**
		 * Ufae_Editor_Output constructor.
		 * Initializes the loop object.
		 */
		public function __construct() {
			$this->loop_obj = Ufae_Editor_Loop::init();
		}

		/**
		 * Renders the output for the flipbox items.
		 *
		 * @return void
		 */
		public function render() {
			?>
		<div class="ufae-wrapper">
			<#
				var widgetId=settings.widget_id;
				var layout=settings.ufae_layout_option;
				var animation=settings.ufae_animation_option ? settings.ufae_animation_option : 'flip' ;

				view.addRenderAttribute( 'ufae_container' , {'class': ['ufae-container','ufae-layout-'+layout],'data-ufae-animation':animation});
				#>
				<div {{{ view.getRenderAttributeString( "ufae_container" ) }}}>
					<?php
					$this->loop_obj->flipbox_items();
					?>
				</div>
		</div>

			<?php
		}
	}

}
