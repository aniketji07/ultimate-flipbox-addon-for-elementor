<?php

namespace UFAE\Widget\Simple\Ufae_Editor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use UFAE\Widget\Simple\Ufae_Editor\Ufae_Editor_Item;
if ( ! class_exists( 'Ufae_Editor_Output' ) ) {
	/**
	 * Class Ufae_Editor_Output
	 *
	 * Handles the output rendering for the Ultimate Flipbox Addon for Elementor.
	 */
	class Ufae_Editor_Output {

		/**
		 * Instance of the Ufae_Editor_Item class.
		 *
		 * This property holds the loop object responsible for rendering
		 * the flipbox items in the editor output.
		 *
		 * @var Ufae_Editor_Item
		 */
		private $loop_obj;

		/**
		 * Ufae_Editor_Output constructor.
		 * Initializes the loop object.
		 */
		public function __construct() {
			$this->loop_obj = new Ufae_Editor_Item();
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
				var widgetId=view.getIDInt();
				var layout='vertical';
				var animation=settings.ufae_simple_animation_option ? settings.ufae_simple_animation_option : 'flip' ;
				let animation_dir=settings.ufae_simple_flip_direction && '' !== settings.ufae_simple_flip_direction ? '-'+settings.ufae_simple_flip_direction : '-left' ;
				var transition_time=settings.ufae_simple_transition_duration ? settings.ufae_simple_transition_duration : '1000' ;
				
				animation_dir              =['flip', 'flip-classic', 'slide'].includes(animation) ? animation_dir : '';
				
				view.addRenderAttribute( 'ufae_container' , {'id': 'ufae_simple_'+widgetId, 'class': ['ufae-container','ufae-layout-'+layout],'data-ufae-animation':animation+animation_dir,'data-ufae-transition':transition_time});

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
