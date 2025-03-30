<?php

namespace UFAE\Widget\Simple;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

use Elementor\Widget_Base;
use UFAE\Widget\Simple\Ufae_Frontend\Ufae_Frontend_Output;
use UFAE\Widget\Simple\Ufae_Editor\Ufae_Editor_Output;

if (! class_exists('Ufae_simple_Widget')) {
	/**
	 * Class Ufae_Register_Widget
	 *
	 * This class is responsible for registering the UFAE widget with Elementor.
	 * It extends the Elementor\Widget_Base class and defines the widget's properties,
	 * controls, and rendering methods.
	 */
	class Ufae_Simple_Widget extends \Elementor\Widget_Base
	{


		private $common_selector = '.ufae-wrapper';
		/**
		 * Ufae_simple_Widget constructor.
		 *
		 * This method initializes the widget, registers styles and scripts
		 * based on the current environment (minified or not) and user login status.
		 *
		 * @param array $data Widget data.
		 * @param null  $args Widget arguments.
		 */
		public function __construct($data = array(), $args = null)
		{
			parent::__construct($data, $args);

			$ext = (false) ? '.min' : ''; // Use minified CSS if $min_v is true

			// Register styles
			wp_register_style('ufae-common-style', UFAE_URL . 'assets/css/ufae-common' . $ext . '.css', array(), UFAE_VERSION, 'all');
			wp_register_style('ufae-vertical-style', UFAE_URL . 'assets/css/ufae-vertical' . $ext . '.css', array(), UFAE_VERSION, 'all');
			// Widget editor styles.
			wp_register_style('ufae-widget-editor', UFAE_URL . 'assets/css/ufae-widget-editor' . $ext . '.css', array(), UFAE_VERSION, 'all');

			add_action('elementor/frontend/after_enqueue_scripts', array($this, 'ufae_register_frontend_scripts'));
		}

		/**
		 * Register frontend scripts for the widget.
		 *
		 * This method is used to register the frontend scripts for the widget
		 * based on the current environment (minified or not) and user login status.
		 */
		public function ufae_register_frontend_scripts()
		{

			$ext = (true) ? '.min' : ''; // Use minified CSS if $min_v is true

			// Register scripts
			wp_register_script('ufae-common-script', UFAE_URL . 'assets/js/ufae-common' . $ext . '.js', array(), UFAE_VERSION, true);
		}

		/**
		 * Get the script dependencies for the widget.
		 *
		 * This method checks if the Elementor editor is in edit or preview mode
		 * and returns the appropriate scripts based on the layout setting.
		 *
		 * @return array List of script handles to be enqueued.
		 */
		public function get_script_depends()
		{
			$scripts = array('ufae-common-script');

			return $scripts;
		}

		/**
		 * Get the style dependencies for the widget.
		 *
		 * This method checks if the Elementor editor is in edit or preview mode
		 * and returns the appropriate styles based on the layout setting.
		 *
		 * @return array List of style handles to be enqueued.
		 */
		public function get_style_depends()
		{
			$styles = array('ufae-common-style', 'ufae-vertical-style');

			if (\Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
				return array_merge($styles, array('ufae-widget-editor'));
			}

			return $styles;
		}

		/**
		 * Get widget name.
		 *
		 * @return string Widget name.
		 */
		public function get_name()
		{
			return 'ufae_simple_flipbox_widget';
		}

		/**
		 * Get widget title.
		 *
		 * @return string Widget title.
		 */
		public function get_title()
		{
			return esc_html__('Ultimate Flipbox', 'ultimate-flipbox-addon-for-elementor');
		}

		/**
		 * Get widget icon.
		 *
		 * @return string Widget icon.
		 */
		public function get_icon()
		{
			return 'eicon-flip-box'; // Elementor icon
		}

		/**
		 * Get categories for the widget.
		 *
		 * @return array Widget categories.
		 */
		public function get_categories()
		{
			return array('ufae'); // Change to your desired category
		}

		/**	
		 * Get keywords for the widget.
		 *
		 * This method returns an array of keywords associated with the widget.
		 *
		 * @return array Keywords associated with the widget.   
		 */
		public function get_keywords()
		{
			return ['flipbox', 'ultimate', 'flip box', 'flipbox widget'];
		}

		/**
		 * Register widget controls.
		 *
		 * This method is used to define the controls for the widget in the Elementor editor.
		 */
		protected function _register_controls()
		{
			$this->ufae_content_controls();
			$this->ufae_style_controls();
		}

		/**
		 * Register content controls for the widget.
		 *
		 * This method is used to define the content-related controls for the widget in the Elementor editor.
		 */
		protected function ufae_content_controls()
		{

			$this->start_controls_section(
				'ufae_simple_content_section',
				array(
					'label' => esc_html__('Flipbox Content', 'ultimate-flipbox-addon-for-elementor'),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			// Flipbox Tabs - START
			$this->start_controls_tabs(
				'ufae_simple_flipbox_items'
			);

			// Fipbox Front Tab - START
			$this->start_controls_tab(
				'ufae_simple_front_content_tab',
				array(
					'label' => esc_html__('Front', 'ultimate-flipbox-addon-for-elementor'),
				)
			);

			$this->add_control(
				'ufae_simple_front_title_enable',
				array(
					'label'        => esc_html__('Enable Title', 'ultimate-flipbox-addon-for-elementor'),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__('Yes', 'ultimate-flipbox-addon-for-elementor'),
					'label_off'    => esc_html__('No', 'ultimate-flipbox-addon-for-elementor'),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'ufae_simple_front_title',
				array(
					'label'       => esc_html__('Title', 'ultimate-flipbox-addon-for-elementor'),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => esc_html__('Default Title', 'ultimate-flipbox-addon-for-elementor'),
					'label_block' => true,
					'default'     => 'WordPress Basics',
					'condition'   => array(
						'ufae_simple_front_title_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_front_title_divider',
				array(
					'label' => esc_html__('Divider', 'ultimate-flipbox-addon-for-elementor'),
					'type'  => \Elementor\Controls_Manager::DIVIDER,
				)
			);

			$this->add_control(
				'ufae_simple_front_desc_enable',
				array(
					'label'        => esc_html__('Enable Description', 'ultimate-flipbox-addon-for-elementor'),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__('Yes', 'ultimate-flipbox-addon-for-elementor'),
					'label_off'    => esc_html__('No', 'ultimate-flipbox-addon-for-elementor'),
					'return_value' => 'yes',
					'default'      => 'no',
				)
			);

			$this->add_control(
				'ufae_simple_front_description',
				array(
					'label'       => esc_html__('Description', 'ultimate-flipbox-addon-for-elementor'),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'default'     => esc_html__('Default description text.', 'ultimate-flipbox-addon-for-elementor'),
					'label_block' => true,
					'default' => 'Learn the fundamentals of WordPress, from installation to customization.',
					'condition'   => array(
						'ufae_simple_front_desc_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_front_desc_divider',
				array(
					'label' => esc_html__('Divider', 'ultimate-flipbox-addon-for-elementor'),
					'type'  => \Elementor\Controls_Manager::DIVIDER,
				)
			);

			$this->add_control(
				'ufae_simple_front_icon_enable',
				array(
					'label'        => esc_html__('Enable Icon', 'ultimate-flipbox-addon-for-elementor'),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__('Yes', 'ultimate-flipbox-addon-for-elementor'),
					'label_off'    => esc_html__('No', 'ultimate-flipbox-addon-for-elementor'),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'ufae_simple_front_icon_type',
				array(
					'label'   => esc_html__('Icon Type', 'ultimate-flipbox-addon-for-elementor'),
					'type'    => \Elementor\Controls_Manager::CHOOSE,
					'options' => array(
						'icon'  => array(
							'title' => esc_html__('Icon', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fab fa-font-awesome',
						),
						'image' => array(
							'title' => esc_html__('Icon Image', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fa fa-image',
						),
						'text'  => array(
							'title' => esc_html__('Icon Text', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fa fa-list-ol',
						),
					),
					'default' => 'icon',
					'condition' => array(
						'ufae_simple_front_icon_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_front_icon',
				array(
					'label'     => esc_html__('Icon', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::ICONS,
					'default'   => array(
						'value'   => 'fab fa-wordpress-simple',
						'library' => 'fa-brands',
					),
					'condition' => array(
						'ufae_simple_front_icon_enable' => 'yes',
						'ufae_simple_front_icon_type' => 'icon',
					),
				)
			);

			$this->add_control(
				'ufae_simple_front_icon_image',
				array(
					'label'     => esc_html__('Icon Image', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::MEDIA,
					'default'   => array(
						'url' => '',
					),
					'condition' => array(
						'ufae_simple_front_icon_enable' => 'yes',
						'ufae_simple_front_icon_type' => 'image',
					),
				)
			);

			$this->add_control(
				'ufae_simple_front_icon_text',
				array(
					'label'       => esc_html__('Icon Text', 'ultimate-flipbox-addon-for-elementor'),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => esc_html__('Default Text', 'ultimate-flipbox-addon-for-elementor'),
					'label_block' => true,
					'condition'   => array(
						'ufae_simple_front_icon_enable' => 'yes',
						'ufae_simple_front_icon_type' => 'text',
					),
				)
			);

			$this->end_controls_tab();
			// Fipbox Front Tab - START

			// Fipbox Back Tab - START
			$this->start_controls_tab(
				'ufae_simple_back_content_tab',
				array(
					'label' => esc_html__('Back', 'ultimate-flipbox-addon-for-elementor'),
				)
			);

			$this->add_control(
				'ufae_simple_back_title_enable',
				array(
					'label'        => esc_html__('Enable Title', 'ultimate-flipbox-addon-for-elementor'),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__('Yes', 'ultimate-flipbox-addon-for-elementor'),
					'label_off'    => esc_html__('No', 'ultimate-flipbox-addon-for-elementor'),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'ufae_simple_back_title',
				array(
					'label'       => esc_html__('Title', 'ultimate-flipbox-addon-for-elementor'),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => esc_html__('Default Title', 'ultimate-flipbox-addon-for-elementor'),
					'label_block' => true,
					'default'     => 'Documentation',
					'condition'   => array(
						'ufae_simple_back_title_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_back_title_divider',
				array(
					'label' => esc_html__('Divider', 'ultimate-flipbox-addon-for-elementor'),
					'type'  => \Elementor\Controls_Manager::DIVIDER,
				)
			);


			$this->add_control(
				'ufae_simple_back_desc_enable',
				array(
					'label'        => esc_html__('Enable Description', 'ultimate-flipbox-addon-for-elementor'),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__('Yes', 'ultimate-flipbox-addon-for-elementor'),
					'label_off'    => esc_html__('No', 'ultimate-flipbox-addon-for-elementor'),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'ufae_simple_back_description',
				array(
					'label'       => esc_html__('Description', 'ultimate-flipbox-addon-for-elementor'),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'default'     => esc_html__('Default description text.', 'ultimate-flipbox-addon-for-elementor'),
					'label_block' => true,
					'default'    => 'Access the official WordPress documentation for in-depth guides and tutorials.',
					'condition'   => array(
						'ufae_simple_back_desc_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_back_desc_divider',
				array(
					'label' => esc_html__('Divider', 'ultimate-flipbox-addon-for-elementor'),
					'type'  => \Elementor\Controls_Manager::DIVIDER,
				)
			);

			$this->add_control(
				'ufae_simple_back_icon_enable',
				array(
					'label'        => esc_html__('Enable Icon', 'ultimate-flipbox-addon-for-elementor'),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__('Yes', 'ultimate-flipbox-addon-for-elementor'),
					'label_off'    => esc_html__('No', 'ultimate-flipbox-addon-for-elementor'),
					'return_value' => 'yes',
					'default'      => 'no',
				)
			);

			$this->add_control(
				'ufae_simple_back_icon_type',
				array(
					'label'     => esc_html__('Icon Type', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::CHOOSE,
					'options'   => array(
						'icon'  => array(
							'title' => esc_html__('Icon', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fab fa-font-awesome',
						),
						'image' => array(
							'title' => esc_html__('Icon Image', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fa fa-image',
						),
						'text'  => array(
							'title' => esc_html__('Icon Text', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fa fa-list-ol',
						),
					),
					'default'   => 'icon',
					'condition' => array(
						'ufae_simple_back_icon_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_back_icon',
				array(
					'label'     => esc_html__('Icon', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::ICONS,
					'default'   => array(
						'value'   => 'fab fa-wordpress-simple',
						'library' => 'fa-brands',
					),
					'condition' => array(
						'ufae_simple_back_icon_enable' => 'yes',
						'ufae_simple_back_icon_type' => 'icon',
					),
				)
			);

			$this->add_control(
				'ufae_simple_back_icon_image',
				array(
					'label'     => esc_html__('Icon Image', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::MEDIA,
					'default'   => array(
						'url' => '',
					),
					'condition' => array(
						'ufae_simple_back_icon_enable' => 'yes',
						'ufae_simple_back_icon_type' => 'image',
					),
				)
			);

			$this->add_control(
				'ufae_simple_back_icon_text',
				array(
					'label'       => esc_html__('Icon Text', 'ultimate-flipbox-addon-for-elementor'),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => esc_html__('Default Text', 'ultimate-flipbox-addon-for-elementor'),
					'label_block' => true,
					'condition'   => array(
						'ufae_simple_back_icon_enable' => 'yes',
						'ufae_simple_back_icon_type' => 'text',
					),
				)
			);

			$this->add_control(
				'ufae_simple_back_icon_divider',
				array(
					'label' => esc_html__('Divider', 'ultimate-flipbox-addon-for-elementor'),
					'type'  => \Elementor\Controls_Manager::DIVIDER,
				)
			);

			$this->add_control(
				'ufae_simple_back_button_enable',
				array(
					'label'        => esc_html__('Enable Button', 'ultimate-flipbox-addon-for-elementor'),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__('Yes', 'ultimate-flipbox-addon-for-elementor'),
					'label_off'    => esc_html__('No', 'ultimate-flipbox-addon-for-elementor'),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'ufae_simple_back_button_text',
				array(
					'label'       => esc_html__('Button Text', 'ultimate-flipbox-addon-for-elementor'),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => esc_html__('Click Here', 'ultimate-flipbox-addon-for-elementor'),
					'label_block' => true,
					'condition'   => array(
						'ufae_simple_back_button_enable' => 'yes',
					),
					'default'    => 'Read Docs',
				)
			);

			$this->add_control(
				'ufae_simple_back_button_url',
				array(
					'label'       => esc_html__('Button URL', 'ultimate-flipbox-addon-for-elementor'),
					'type'        => \Elementor\Controls_Manager::URL,
					'default'     => array(
						'url'         => '#',
						'is_external' => false,
						'nofollow'    => false,
					),
					'label_block' => true,
					'condition'   => array(
						'ufae_simple_back_button_enable' => 'yes',
					),
				)
			);

			$this->end_controls_tab();
			// Fipbox Back Tab - END

			$this->end_controls_tabs();

			$this->end_controls_section();

			$this->ufae_content_layout_controls();
		}

		/**
		 * Register style controls for the widget.
		 *
		 * This method is responsible for initializing and registering all style-related controls
		 * for the widget in the Elementor editor. It calls individual methods to set up styles
		 * for various components of the widget, ensuring a modular and organized approach.
		 */
		protected function ufae_style_controls()
		{
			// Register styles for the container box of the widget
			$this->ufae_container_styles();

			// Register styles for the title of the widget
			$this->ufae_title_styles();

			// Register styles for the description of the widget
			$this->ufae_desc_styles();

			// Register styles for the icon used in the widget
			$this->ufae_icon_styles();

			// Register styles for the button within the widget
			$this->ufae_btn_styles();
		}

		/**
		 * Register container styles for the widget.
		 *
		 * This method is used to define the style-related controls for the container in the Elementor editor.
		 */
		protected function ufae_container_styles()
		{
			// container box style settings - START
			$this->start_controls_section(
				'ufae_simple_container_style_section',
				array(
					'label' => esc_html__('Container Box Settings', 'ultimate-flipbox-addon-for-elementor'),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);

			$this->start_controls_tabs(
				'ufae_simple_flipbox_container_style_tabs'
			);

			// container box front style tab - START
			$this->start_controls_tab(
				'ufae_simple_front_container_tab',
				array(
					'label' => esc_html__('Front', 'ultimate-flipbox-addon-for-elementor'),
				)
			);

			$this->add_control(
				'ufae_simple_container_front_bg_type',
				array(
					'label'   => esc_html__('Background Type', 'twae'),
					'type'    => \Elementor\Controls_Manager::CHOOSE,
					'default' => 'simple',
					'options' => array(
						'simple'     => array(
							'title' => esc_html__('Simple', 'twae'),
							'icon'  => 'eicon-paint-brush',
						),
						'gradient'   => array(
							'title' => esc_html__('Gradient', 'twae'),
							'icon'  => 'eicon-barcode',
						),
						'image' => array(
							'title' => esc_html__('Image', 'twae'),
							'icon'  => 'eicon-image',
						),
					),
					'toggle'  => false,
				)
			);

			$this->add_control(
				'ufae_simple_container_front_bg_color',
				array(
					'label'     => esc_html__('Background Color', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-front-bg-color: {{VALUE}}',
					),
					'default'   => '#2F4F4F',
					'condition' => array(
						'ufae_simple_container_front_bg_type' => 'simple',
					),
				)
			);

			$this->add_control(
				'ufae_simple_container_front_bg_gradient_color_1',
				array(
					'label'     => esc_html__('Gradient Color 1', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-front-bg-gradient-color-1: {{VALUE}}',
					),
					'default' => '#9b51e0',
					'condition' => array(
						'ufae_simple_container_front_bg_type' => 'gradient',
					),
				)
			);

			$this->add_control(
				'ufae_simple_container_front_bg_gradient_color_2',
				array(
					'label'     => esc_html__('Gradient Color 2', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-front-bg-gradient-color-2: {{VALUE}}',
					),
					'default' => '#b984ff',
					'condition' => array(
						'ufae_simple_container_front_bg_type' => 'gradient',
					),
				)
			);

			$this->add_control(
				'ufae_simple_container_front_bg_gradient_direction',
				array(
					'label'     => esc_html__('Gradient Direction', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'default'   => 'to right',
					'options'   => array(
						'to right' => esc_html__('To Right', 'ultimate-flipbox-addon-for-elementor'),
						'to left'  => esc_html__('To Left', 'ultimate-flipbox-addon-for-elementor'),
						'to bottom' => esc_html__('To Bottom', 'ultimate-flipbox-addon-for-elementor'),
						'to top'   => esc_html__('To Top', 'ultimate-flipbox-addon-for-elementor'),
					),
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-front-bg-gradient-direction: {{VALUE}}',
					),
					'condition' => array(
						'ufae_simple_container_front_bg_type' => 'gradient',
					),
				)
			);

			$this->add_control(
				'ufae_simple_container_front_bg_image',
				[
					'label'     => esc_html__('Background Image', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::MEDIA,
					'selectors' => [
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-front-bg-image: url("{{URL}}");',
					],
					'condition' => [
						'ufae_simple_container_front_bg_type' => 'image',
					],
					'render_type' => 'template'
				]
			);

			$this->add_control(
				'ufae_simple_container_front_bg_overlay', // Front overlay control
				array(
					'label'   => esc_html__('Enable Overlay', 'twae'),
					'type'    => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__('Yes', 'twae'),
					'label_off' => esc_html__('No', 'twae'),
					'default' => 'no',
					'condition' => [
						'ufae_simple_container_front_bg_type' => 'image', // Only show if 'image' background type is selected
					],
				)
			);

			$this->add_control(
				'ufae_simple_container_front_bg_overlay_color', // Front overlay color control
				array(
					'label'     => esc_html__('Overlay Color', 'twae'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'default'   => 'rgba(0, 0, 0, 0.5)', // Default black semi-transparent overlay
					'selectors' => [
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-front-bg-image-overlay: {{VALUE}};',
					],
					'condition' => [
						'ufae_simple_container_front_bg_overlay' => 'yes', // Only show if overlay is enabled
						'ufae_simple_container_front_bg_type' => 'image', // Only show if 'image' background type is selected
					],
				)
			);

			$this->add_control(
				'ufae_simple_container_front_border_popover',
				array(
					'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
					'label'        => esc_html__('Front Border', 'ultimate-flipbox-addon-for-elementor'),
					'label_off'    => esc_html__('Default', 'ultimate-flipbox-addon-for-elementor'),
					'label_on'     => esc_html__('Custom', 'ultimate-flipbox-addon-for-elementor'),
					'return_value' => 'yes',
				)
			);

			$this->start_popover();

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				array(
					'name'      => 'ufae_simple_container_front_border',
					'selector'  => '{{WRAPPER}} ' . esc_html($this->common_selector) . ' .ufae-flipbox-front',
					'condition' => array(
						'ufae_simple_container_front_border_popover' => 'yes',
					),
				)
			);

			$this->end_popover();

			$this->add_responsive_control(
				'ufae_simple_front_items_justify',
				array(
					'label'     => esc_html__('Content Justify', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::CHOOSE,
					'options'   => array(
						'start'  => array(
							'title' => esc_html__('Align Left', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fas fa-align-left',
						),
						'center' => array(
							'title' => esc_html__('Align Center', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fas fa-align-center',
						),
						'end'    => array(
							'title' => esc_html__('Align Right', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fas fa-align-right',
						),
					),
					'default'   => 'center',
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-front-items-justify: {{VALUE}}',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_front_items_alignment',
				array(
					'label'     => esc_html__('Item Alignment', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::CHOOSE,
					'options'   => array(
						'start'  => array(
							'title' => esc_html__('Align Top', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fas fa-align-left',
						),
						'center' => array(
							'title' => esc_html__('Align Middle', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fas fa-align-center',
						),
						'end'    => array(
							'title' => esc_html__('Align Bottom', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fas fa-align-right',
						),
					),
					'default'   => 'center',
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-front-items-alignment: {{VALUE}}',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_container_front_padding',
				array(
					'label'      => esc_html__('Front Padding', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '2',
						'right'  => '2',
						'bottom' => '2',
						'left'   => '2',
						'unit'   => 'em',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-front-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->end_controls_tab();
			// container box front style tab - END

			// container box back style tab - START
			$this->start_controls_tab(
				'ufae_simple_back_container_tab',
				array(
					'label' => esc_html__('Back', 'ultimate-flipbox-addon-for-elementor'),
				)
			);

			$this->add_control(
				'ufae_simple_container_back_bg_type',
				array(
					'label'   => esc_html__('Background Type', 'twae'),
					'type'    => \Elementor\Controls_Manager::CHOOSE,
					'default' => 'simple',
					'options' => array(
						'simple'     => array(
							'title' => esc_html__('Simple', 'twae'),
							'icon'  => 'eicon-paint-brush',
						),
						'gradient'   => array(
							'title' => esc_html__('Gradient', 'twae'),
							'icon'  => 'eicon-barcode',
						),
						'image' => array(
							'title' => esc_html__('Image', 'twae'),
							'icon'  => 'eicon-image',
						),
					),
					'toggle'  => false,
				)
			);

			$this->add_control(
				'ufae_simple_container_back_bg_color',
				array(
					'label'     => esc_html__('Background Color', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-back-bg-color: {{VALUE}}',
					),
					'default'   => '#4E5338',
					'condition' => array(
						'ufae_simple_container_back_bg_type' => 'simple',
					),
				)
			);

			$this->add_control(
				'ufae_simple_container_back_bg_gradient_color_1',
				array(
					'label'     => esc_html__('Gradient Color 1', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-back-bg-gradient-color-1: {{VALUE}}',
					),
					'default' => '#f78da7',
					'condition' => array(
						'ufae_simple_container_back_bg_type' => 'gradient',
					),
				)
			);

			$this->add_control(
				'ufae_simple_container_back_bg_gradient_color_2',
				array(
					'label'     => esc_html__('Gradient Color 2', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-back-bg-gradient-color-2: {{VALUE}}',
					),
					'default' => '#ffd3b6',
					'condition' => array(
						'ufae_simple_container_back_bg_type' => 'gradient',
					),
				)
			);

			$this->add_control(
				'ufae_simple_container_back_bg_gradient_direction',
				array(
					'label'     => esc_html__('Gradient Direction', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'default'   => 'to right',
					'options'   => array(
						'to right' => esc_html__('To Right', 'ultimate-flipbox-addon-for-elementor'),
						'to left'  => esc_html__('To Left', 'ultimate-flipbox-addon-for-elementor'),
						'to bottom' => esc_html__('To Bottom', 'ultimate-flipbox-addon-for-elementor'),
						'to top'   => esc_html__('To Top', 'ultimate-flipbox-addon-for-elementor'),
					),
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-back-bg-gradient-direction: {{VALUE}}',
					),
					'condition' => array(
						'ufae_simple_container_back_bg_type' => 'gradient',
					),
				)
			);

			$this->add_control(
				'ufae_simple_container_back_bg_image',
				[
					'label'     => esc_html__('Background Image', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::MEDIA,
					'selectors' => [
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-back-bg-image: url("{{URL}}");',
					],
					'condition' => [
						'ufae_simple_container_back_bg_type' => 'image',
					],
					'render_type' => 'template'
				]
			);

			$this->add_control(
				'ufae_simple_container_back_bg_overlay', // Back overlay control
				array(
					'label'   => esc_html__('Enable Overlay', 'twae'),
					'type'    => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => esc_html__('Yes', 'twae'),
					'label_off' => esc_html__('No', 'twae'),
					'default' => 'no',
					'condition' => [
						'ufae_simple_container_back_bg_type' => 'image', // Only show if 'image' background type is selected
					],
				)
			);

			$this->add_control(
				'ufae_simple_container_back_bg_overlay_color', // Back overlay color control
				array(
					'label'     => esc_html__('Overlay Color', 'twae'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'default'   => 'rgba(0, 0, 0, 0.5)', // Default black semi-transparent overlay
					'selectors' => [
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-back-bg-image-overlay: {{VALUE}};',
					],
					'condition' => [
						'ufae_simple_container_back_bg_overlay' => 'yes', // Only show if overlay is enabled
						'ufae_simple_container_back_bg_type' => 'image', // Only show if 'image' background type is selected
					],
				)
			);

			$this->add_control(
				'ufae_simple_container_back_border_popover',
				array(
					'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
					'label'        => esc_html__('Back Border', 'ultimate-flipbox-addon-for-elementor'),
					'label_off'    => esc_html__('Default', 'ultimate-flipbox-addon-for-elementor'),
					'label_on'     => esc_html__('Custom', 'ultimate-flipbox-addon-for-elementor'),
					'return_value' => 'yes',
				)
			);

			$this->start_popover();

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				array(
					'name'      => 'ufae_simple_container_back_border',
					'selector'  => '{{WRAPPER}} ' . esc_html($this->common_selector) . ' .ufae-flipbox-back',
					'condition' => array(
						'ufae_simple_container_back_border_popover' => 'yes',
					),
				)
			);

			$this->end_popover();

			$this->add_responsive_control(
				'ufae_simple_back_items_justify',
				array(
					'label'     => esc_html__('Content Justify', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::CHOOSE,
					'options'   => array(
						'flex-start' => array(
							'title' => esc_html__('Align Left', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fas fa-align-left',
						),
						'center'     => array(
							'title' => esc_html__('Align Center', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fas fa-align-center',
						),
						'flex-end'   => array(
							'title' => esc_html__('Align Right', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fas fa-align-right',
						),
					),
					'default'   => 'center',
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-back-items-justify: {{VALUE}}',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_back_items_alignment',
				array(
					'label'     => esc_html__('Item Alignment', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::CHOOSE,
					'options'   => array(
						'flex-start' => array(
							'title' => esc_html__('Align Top', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fas fa-align-left',
						),
						'center'     => array(
							'title' => esc_html__('Align Middle', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fas fa-align-center',
						),
						'flex-end'   => array(
							'title' => esc_html__('Align Bottom', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'fas fa-align-right',
						),
					),
					'default'   => 'center',
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-back-items-alignment: {{VALUE}}',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_container_back_padding',
				array(
					'label'      => esc_html__('Back Padding', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '2',
						'right'  => '2',
						'bottom' => '2',
						'left'   => '2',
						'unit'   => 'em',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-back-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->end_controls_tab();
			// container box back style tab - END

			$this->end_controls_tabs();

			$this->add_control(
				'ufae_simple_container_divider',
				array(
					'label' => esc_html__('Divider', 'ultimate-flipbox-addon-for-elementor'),
					'type'  => \Elementor\Controls_Manager::DIVIDER,
				)
			);

			$this->add_responsive_control(
				'ufae_simple_container_margin',
				array(
					'label'      => esc_html__('Margin', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '1',
						'right'  => '1',
						'bottom' => '1',
						'left'   => '1',
						'unit'   => 'em',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-margin-top: {{TOP}}{{UNIT}};--ufae-box-margin-right: {{RIGHT}}{{UNIT}};--ufae-box-margin-left: {{LEFT}}{{UNIT}};--ufae-box-margin-bottom: {{BOTTOM}}{{UNIT}};',
					)
				)
			);

			$this->add_responsive_control(
				'ufae_simple_container_border_radius',
				array(
					'label'      => esc_html__('Border Radius', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '20',
						'right'  => '20',
						'bottom' => '20',
						'left'   => '20',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-bd-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_container_width',
				array(
					'label'      => esc_html__('Width', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => array('px', '%', 'em', 'rem'),
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 1200,
							'step' => 1,
						),
					),
					'default'    => array(
						'size' => 100,
						'unit' => '%',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-width: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_container_height',
				array(
					'label'      => esc_html__('Height', 'ultimate-flipbox-addon-for-elementor'),
					'size_units' => array('px', 'em', 'rem'),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 1200,
							'step' => 1,
						),
					),
					'default'    => array(
						'size' => 350,
						'unit' => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-box-height: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'ufae_simple_container_boxshadow_popover',
				array(
					'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
					'label'        => esc_html__('Box Shadow', 'ultimate-flipbox-addon-for-elementor'),
					'label_off'    => esc_html__('Default', 'ultimate-flipbox-addon-for-elementor'),
					'label_on'     => esc_html__('Custom', 'ultimate-flipbox-addon-for-elementor'),
					'return_value' => 'yes',
				)
			);

			$this->start_popover();

			$this->add_control(
				'ufae_simple_container_box_shadow',
				array(
					'label'     => esc_html__('Box Shadow', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::BOX_SHADOW,
					'default'   => array(
						'color'    => 'rgba(0, 0, 0, 0.1)',
						'blur'     => 10,
						'spread'   => 0,
						'position' => 'outset',
					),
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector)  => '--ufae-box-shadow-hr: {{HORIZONTAL}}px;--ufae-box-shadow-vr: {{VERTICAL}}px;--ufae-box-shadow-blur: {{BLUR}}px;--ufae-box-shadow-spread: {{SPREAD}}px;--ufae-box-shadow-color: {{COLOR}};',
					),
					'condition' => array(
						'ufae_simple_container_boxshadow_popover' => 'yes',
					),
				)
			);

			$this->end_popover();

			$this->end_controls_section();
			// container box style settings - END
		}

		/**
		 * Register title styles for the widget.
		 *
		 * This method is used to define the style-related controls for the title in the Elementor editor.
		 */
		protected function ufae_title_styles()
		{
			// title style settings - START
			$this->start_controls_section(
				'ufae_simple_title_style_section',
				array(
					'label' => esc_html__('Title Style', 'ultimate-flipbox-addon-for-elementor'),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);

			$this->start_controls_tabs(
				'ufae_simple_flipbox_title_style_tabs'
			);

			// title front style tab - START
			$this->start_controls_tab(
				'ufae_simple_front_title_tab',
				array(
					'label' => esc_html__('Front', 'ultimate-flipbox-addon-for-elementor'),
					'condition' => array(
						'ufae_simple_front_title_enable' => 'yes',
					),
				)
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				array(
					'name'      => 'ufae_simple_title_front_typography',
					'selector'  => '{{WRAPPER}} ' . esc_html($this->common_selector) . ' .ufae-flipbox-front .ufae-title',
					'condition' => array(
						'ufae_simple_front_title_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_title_front_bg_color',
				array(
					'label'     => esc_html__('Background Color', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-title-front-bg-color: {{VALUE}};',
					),
					'condition' => array(
						'ufae_simple_front_title_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_title_front_text_color',
				array(
					'label'     => esc_html__('Text Color', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-title-front-color: {{VALUE}};',
					),
					'condition' => array(
						'ufae_simple_front_title_enable' => 'yes',
					),
					'default'   => '#FFFFFF',
				)
			);

			$this->add_responsive_control(
				'ufae_simple_title_front_padding',
				array(
					'label'      => esc_html__('Padding', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '10',
						'right'  => '10',
						'bottom' => '10',
						'left'   => '10',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-title-front-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'ufae_simple_front_title_enable' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_title_front_margin',
				array(
					'label'      => esc_html__('Margin', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '0',
						'right'  => '0',
						'bottom' => '0',
						'left'   => '0',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-title-front-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'ufae_simple_front_title_enable' => 'yes',
					),
				)
			);

			$this->end_controls_tab();
			// title front style tab - END

			// title back style tab - START
			$this->start_controls_tab(
				'ufae_simple_back_title_tab',
				array(
					'label' => esc_html__('Back', 'ultimate-flipbox-addon-for-elementor'),
					'condition' => array(
						'ufae_simple_back_title_enable' => 'yes',
					),
				)
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				array(
					'name'      => 'ufae_simple_title_back_typography',
					'selector'  => '{{WRAPPER}} ' . esc_html($this->common_selector) . ' .ufae-flipbox-back .ufae-title',
					'condition' => array(
						'ufae_simple_back_title_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_title_back_bg_color',
				array(
					'label'     => esc_html__('Background Color', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,

					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-title-back-bg-color: {{VALUE}};',
					),
					'condition' => array(
						'ufae_simple_back_title_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_title_back_text_color',
				array(
					'label'     => esc_html__('Text Color', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-title-back-color: {{VALUE}};',
					),
					'condition' => array(
						'ufae_simple_back_title_enable' => 'yes',
					),
					'default'   => '#FFFFFF',
				)
			);

			$this->add_responsive_control(
				'ufae_simple_title_back_padding',
				array(
					'label'      => esc_html__('Padding', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '10',
						'right'  => '10',
						'bottom' => '10',
						'left'   => '10',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-title-back-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'ufae_simple_back_title_enable' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_title_back_margin',
				array(
					'label'      => esc_html__('Margin', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '0',
						'right'  => '0',
						'bottom' => '0',
						'left'   => '0',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-title-back-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'ufae_simple_back_title_enable' => 'yes',
					),
				)
			);

			$this->end_controls_tab();
			// title back style tab - END

			$this->end_controls_tabs();

			$this->end_controls_section();
			// title style settings - END
		}

		/**
		 * Register description styles for the widget.
		 *
		 * This method is used to define the style-related controls for the description in the Elementor editor.
		 */
		protected function ufae_desc_styles()
		{
			// desc style settings - START
			$this->start_controls_section(
				'ufae_simple_desc_style_section',
				array(
					'label' => esc_html__('Description Style', 'ultimate-flipbox-addon-for-elementor'),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);

			$this->start_controls_tabs(
				'ufae_simple_flipbox_desc_style_tabs'
			);

			// desc front style tab - START
			$this->start_controls_tab(
				'ufae_simple_front_desc_tab',
				array(
					'label' => esc_html__('Front', 'ultimate-flipbox-addon-for-elementor'),
					'condition' => array(
						'ufae_simple_front_desc_enable' => 'yes',
					),
				)
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				array(
					'name'      => 'ufae_simple_desc_front_typography',
					'selector'  => '{{WRAPPER}} ' . esc_html($this->common_selector) . ' .ufae-flipbox-front .ufae-desc',
					'condition' => array(
						'ufae_simple_front_desc_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_desc_front_bg_color',
				array(
					'label'     => esc_html__('Background Color', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,

					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-desc-front-bg-color: {{VALUE}};',
					),
					'condition' => array(
						'ufae_simple_front_desc_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_desc_front_text_color',
				array(
					'label'     => esc_html__('Text Color', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-desc-front-color: {{VALUE}};',
					),
					'condition' => array(
						'ufae_simple_front_desc_enable' => 'yes',
					),
					'default'   => '#FFFFFF',
				)
			);

			$this->add_responsive_control(
				'ufae_simple_desc_front_padding',
				array(
					'label'      => esc_html__('Padding', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '10',
						'right'  => '10',
						'bottom' => '10',
						'left'   => '10',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-desc-front-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'ufae_simple_front_desc_enable' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_desc_front_margin',
				array(
					'label'      => esc_html__('Margin', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '0',
						'right'  => '0',
						'bottom' => '0',
						'left'   => '0',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-desc-front-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'ufae_simple_front_desc_enable' => 'yes',
					),
				)
			);

			$this->end_controls_tab();
			// desc front style tab - END

			// desc back style tab - START
			$this->start_controls_tab(
				'ufae_simple_back_desc_tab',
				array(
					'label' => esc_html__('Back', 'ultimate-flipbox-addon-for-elementor'),
					'condition' => array(
						'ufae_simple_back_desc_enable' => 'yes',
					),
				)
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				array(
					'name'      => 'ufae_simple_desc_back_typography',
					'selector'  => '{{WRAPPER}} ' . esc_html($this->common_selector) . ' .ufae-flipbox-back .ufae-desc',
					'condition' => array(
						'ufae_simple_back_desc_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_desc_back_bg_color',
				array(
					'label'     => esc_html__('Background Color', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-desc-back-bg-color: {{VALUE}};',
					),
					'condition' => array(
						'ufae_simple_back_desc_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_desc_back_text_color',
				array(
					'label'     => esc_html__('Text Color', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-desc-back-color: {{VALUE}};',
					),
					'condition' => array(
						'ufae_simple_back_desc_enable' => 'yes',
					),
					'default'   => '#FFFFFF',
				)
			);

			$this->add_responsive_control(
				'ufae_simple_desc_back_padding',
				array(
					'label'      => esc_html__('Padding', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '10',
						'right'  => '10',
						'bottom' => '10',
						'left'   => '10',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-desc-back-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'ufae_simple_back_desc_enable' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_desc_back_margin',
				array(
					'label'      => esc_html__('Margin', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '0',
						'right'  => '0',
						'bottom' => '0',
						'left'   => '0',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-desc-back-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'ufae_simple_back_desc_enable' => 'yes',
					),
				)
			);

			$this->end_controls_tab();
			// desc back style tab - END

			$this->end_controls_tabs();

			$this->end_controls_section();
			// desc style settings - END
		}

		/**
		 * Register icon styles for the widget.
		 *
		 * This method is used to define the style-related controls for the icon in the Elementor editor.
		 */
		protected function ufae_icon_styles()
		{
			// icon style settings - START
			$this->start_controls_section(
				'ufae_simple_icon_style_section',
				array(
					'label' => esc_html__('Icon Style', 'ultimate-flipbox-addon-for-elementor'),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);

			$this->start_controls_tabs(
				'ufae_simple_flipbox_icon_style_tabs'
			);

			// icon front style tab - START
			$this->start_controls_tab(
				'ufae_simple_front_icon_tab',
				array(
					'label' => esc_html__('Front', 'ultimate-flipbox-addon-for-elementor'),
					'condition' => array(
						'ufae_simple_front_icon_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_icon_front_color',
				array(
					'label'     => esc_html__('Icon Color', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-icon-front-color: {{VALUE}};',
					),
					'condition' => array(
						'ufae_simple_front_icon_enable' => 'yes',
					),
					'default'   => '#FFFFFF',
				)
			);

			$this->add_responsive_control(
				'ufae_simple_icon_front_size',
				array(
					'label'     => esc_html__('Icon Size', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min'  => 0,
							'max'  => 500,
							'step' => 1,
						),
						'em' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 1,
						),
					),
					'default'   => array(
						'unit' => 'px',
						'size' => 64,
					),
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-icon-front-size: {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						'ufae_simple_front_icon_enable' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_icon_front_padding',
				array(
					'label'      => esc_html__('Icon Padding', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '0',
						'right'  => '0',
						'bottom' => '0',
						'left'   => '0',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-icon-front-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'ufae_simple_front_icon_enable' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_icon_front_margin',
				array(
					'label'      => esc_html__('Icon Margin', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '0',
						'right'  => '0',
						'bottom' => '0',
						'left'   => '0',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-icon-front-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'ufae_simple_front_icon_enable' => 'yes',
					),
				)
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				array(
					'name'      => 'ufae_simple_icon_front_border',
					'label'     => esc_html__('Icon Border', 'ultimate-flipbox-addon-for-elementor'),
					'selector'  => '{{WRAPPER}} ' . esc_html($this->common_selector) . ' .ufae-flipbox-front .ufae-icon-wrapper',
					'condition' => array(
						'ufae_simple_front_icon_enable' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_icon_front_border_radius',
				array(
					'label'      => esc_html__('Border Radius', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'unit' => 'px',
						'size' => 10,
					),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
							'step' => 1,
						),
						'%' => array(
							'min' => 0,
							'max' => 100,
							'step' => 1,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-icon-front-bd-radius: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'ufae_simple_front_icon_enable' => 'yes',
					),
				)
			);

			$this->end_controls_tab();
			// icon front style tab - END

			// icon back style tab - START
			$this->start_controls_tab(
				'ufae_simple_back_icon_tab',
				array(
					'label' => esc_html__('Back', 'ultimate-flipbox-addon-for-elementor'),
					'condition' => array(
						'ufae_simple_back_icon_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_icon_back_bg_color',
				array(
					'label'     => esc_html__('Background Color', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-icon-back-bg-color: {{VALUE}};',
					),
					'condition' => array(
						'ufae_simple_back_icon_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_icon_back_color',
				array(
					'label'     => esc_html__('Icon Color', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-icon-back-color: {{VALUE}};',
					),
					'condition' => array(
						'ufae_simple_back_icon_enable' => 'yes',
					),
					'default'   => '#FFFFFF',
				)
			);

			$this->add_responsive_control(
				'ufae_simple_icon_back_size',
				array(
					'label'     => esc_html__('Icon Size', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min'  => 0,
							'max'  => 500,
							'step' => 1,
						),
						'em' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 1,
						),
					),
					'default'   => array(
						'unit' => 'px',
						'size' => 64,
					),
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-icon-back-size: {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						'ufae_simple_back_icon_enable' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_icon_back_padding',
				array(
					'label'      => esc_html__('Icon Padding', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '0',
						'right'  => '0',
						'bottom' => '0',
						'left'   => '0',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-icon-back-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'ufae_simple_back_icon_enable' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_icon_back_margin',
				array(
					'label'      => esc_html__('Icon Margin', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '0',
						'right'  => '0',
						'bottom' => '0',
						'left'   => '0',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-icon-back-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'ufae_simple_back_icon_enable' => 'yes',
					),
				)
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				array(
					'name'      => 'ufae_simple_icon_back_border',
					'label'     => esc_html__('Icon Border', 'ultimate-flipbox-addon-for-elementor'),
					'selector'  => '{{WRAPPER}} ' . esc_html($this->common_selector) . ' .ufae-flipbox-back .ufae-icon-wrapper',
					'condition' => array(
						'ufae_simple_back_icon_enable' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_icon_back_border_radius',
				array(
					'label'      => esc_html__('Border Radius', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'unit' => 'px',
						'size' => 10,
					),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
							'step' => 1,
						),
						'%' => array(
							'min' => 0,
							'max' => 100,
							'step' => 1,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-icon-back-bd-radius: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'ufae_simple_back_icon_enable' => 'yes',
					),
				)
			);

			$this->end_controls_tab();
			// icon back style tab - END

			$this->end_controls_tabs();

			$this->end_controls_section();
			// icon style settings - END
		}

		/**
		 * Register button styles for the widget.
		 *
		 * This method is used to define the style-related controls for the button in the Elementor editor.
		 */
		protected function ufae_btn_styles()
		{
			// btn style settings - START
			$this->start_controls_section(
				'ufae_simple_btn_style_section',
				array(
					'label' => esc_html__('Button Style', 'ultimate-flipbox-addon-for-elementor'),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
					'condition'      => array(
						'ufae_simple_back_button_enable' => 'yes',
					),
				)
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				array(
					'name'           => 'ufae_simple_btn_back_typography',
					'label'          => esc_html__('Button Typography', 'ultimate-flipbox-addon-for-elementor'),
					'selector'       => '{{WRAPPER}} ' . esc_html($this->common_selector) . ' .ufae-flipbox-back .ufae-btn-wrapper .ufae-button',
					'fields_options' => array(
						'font_family'     => array(
							'default' => 'Arial',
						),
						'font_size'       => array(
							'default' => array(
								'unit' => 'px',
								'size' => 16,
							),
						),
						'text_decoration' => array(
							'default' => 'none',
						),
						'line_height'     => array(
							'default' => array(
								'unit' => 'px',
								'size' => 16,
							),
						),
					),
					'condition'      => array(
						'ufae_simple_back_button_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_btn_back_bg_color',
				array(
					'label'     => esc_html__('Background Color', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'default'   => '#000',
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-btn-back-bg-color: {{VALUE}};',
					),
					'condition' => array(
						'ufae_simple_back_button_enable' => 'yes',
					),
					'default'   => '#40D0FF',
				)
			);

			$this->add_control(
				'ufae_simple_btn_back_color',
				array(
					'label'     => esc_html__('Text Color', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'default'   => '#fff',
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-btn-back-color: {{VALUE}};',
					),
					'condition' => array(
						'ufae_simple_back_button_enable' => 'yes',
					),
					'default'   => '#FFFFFF',
				)
			);

			$this->add_responsive_control(
				'ufae_simple_btn_back_width',
				array(
					'label'     => esc_html__('Button Width', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min'  => 0,
							'max'  => 500,
							'step' => 1,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-btn-back-width: {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						'ufae_simple_back_button_enable' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_btn_back_padding',
				array(
					'label'      => esc_html__('Padding', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '10',
						'right'  => '20',
						'bottom' => '10',
						'left'   => '20',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-btn-back-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'ufae_simple_back_button_enable' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'ufae_simple_btn_back_margin',
				array(
					'label'      => esc_html__('Margin', 'ultimate-flipbox-addon-for-elementor'),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array('px', '%', 'em'),
					'default'    => array(
						'top'    => '0',
						'right'  => '0',
						'bottom' => '0',
						'left'   => '0',
						'unit'   => 'px',
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-btn-back-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'ufae_simple_back_button_enable' => 'yes',
					),
				)
			);

			$this->add_control(
				'ufae_simple_btn_back_border_popover',
				array(
					'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
					'label'        => esc_html__('Border', 'ultimate-flipbox-addon-for-elementor'),
					'label_off'    => esc_html__('Default', 'ultimate-flipbox-addon-for-elementor'),
					'label_on'     => esc_html__('Custom', 'ultimate-flipbox-addon-for-elementor'),
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => array(
						'ufae_simple_back_button_enable' => 'yes',
					),
				)
			);

			$this->start_popover();

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				array(
					'name'           => 'ufae_simple_btn_back_border',
					'label'          => esc_html__('Button Border', 'ultimate-flipbox-addon-for-elementor'),
					'selector'       => '{{WRAPPER}} ' . esc_attr($this->common_selector) . ' .ufae-flipbox-back .ufae-btn-wrapper .ufae-button',
					'fields_options' => array(
						'width'  => array(
							'selectors' => array(
								'{{SELECTOR}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; --border-top-width: {{TOP}}{{UNIT}}; --border-right-width: {{RIGHT}}{{UNIT}}; --border-bottom-width: {{BOTTOM}}{{UNIT}}; --border-left-width: {{LEFT}}{{UNIT}};',
							),
							'default'   => array(
								'top'    => '1',
								'right'  => '1',
								'bottom' => '1',
								'left'   => '1',
								'unit'   => 'px',
							),
						),
						'color'  => array(
							'selectors' => array(
								'{{SELECTOR}}:not(:hover)' => 'border-color: {{VALUE}}; --border-color: {{VALUE}};',
							),
							'default'   => '#fff',
						),
						'border' => array(
							'selectors' => array(
								'{{SELECTOR}}' => 'border-style: {{VALUE}}; --border-style: {{VALUE}};',
							),
							'default'   => 'solid',
						),
					),
					'condition'      => array(
						'ufae_simple_btn_back_border_popover' => 'yes',
						'ufae_simple_back_button_enable'      => 'yes',
					),
				)
			);

			$this->end_controls_section();
			// btn style settings - END
		}

		/**
		 * Register layout controls for the widget.
		 *
		 * This method is used to define the layout-related controls for the widget in the Elementor editor.
		 */
		protected function ufae_content_layout_controls()
		{
			$this->start_controls_section(
				'ufae_simple_layout_section',
				array(
					'label' => esc_html__('Layout Settings', 'ultimate-flipbox-addon-for-elementor'),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'ufae_simple_design_option',
				array(
					'label'   => esc_html__('Select Preset', 'ultimate-flipbox-addon-for-elementor'),
					'type'    => 'ufae_design_control',
					'options' => array(
						'ufae-design-0' => __('Default', 'ultimate-flipbox-addon-for-elementor'),
						'ufae-design-1' => __('Simple', 'ultimate-flipbox-addon-for-elementor'),
						'ufae-design-2' => __('Bold', 'ultimate-flipbox-addon-for-elementor'),
						'ufae-design-3' => __('Clean', 'ultimate-flipbox-addon-for-elementor'),
					),
					'message' => __('This setting will overwrite your current settings with the selected design option.', 'ultimate-flipbox-addon-for-elementor'),
					'default' => 'ufae-design-0',
				)
			);

			$this->add_control(
				'ufae_simple_title_tag',
				array(
					'label'   => esc_html__('Title Tag', 'ultimate-flipbox-addon-for-elementor'),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'h1'  => 'H1',
						'h2'  => 'H2',
						'h3'  => 'H3',
						'h4'  => 'H4',
						'h5'  => 'H5',
						'h6'  => 'H6',
						'div' => 'Div',
					),
					'default' => 'h2',
				)
			);

			$this->add_control(
				'ufae_simple_animation_option',
				array(
					'label'   => esc_html__('Flipbox Animation', 'ultimate-flipbox-addon-for-elementor'),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'none'    => __('None', 'ultimate-flipbox-addon-for-elementor'),
						'flip'    => __('Flip', 'ultimate-flipbox-addon-for-elementor'),
						// 'flip-classic'    => __('Flip Classic', 'ultimate-flipbox-addon-for-elementor'),
						'fade'    => __('Fade', 'ultimate-flipbox-addon-for-elementor'),
						'zoom'    => __('Zoom', 'ultimate-flipbox-addon-for-elementor'),
						'slide'   => __('Slide', 'ultimate-flipbox-addon-for-elementor'),
						'curtain' => __('Curtain (Hot)', 'ultimate-flipbox-addon-for-elementor'),
					),
					'default' => 'flip',
				)
			);

			$this->add_control(
				'ufae_simple_container_3d_depth',
				array(
					'label'       => esc_html__('3D Depth', 'ultimate-flipbox-addon-for-elementor'),
					'type'        => \Elementor\Controls_Manager::SLIDER,
					'size_units'  => array('px'),
					'range'       => array(
						'px' => array(
							'min'  => 0,
							'max'  => 150,
							'step' => 10,
						),
					),
					'default'     => array(
						'unit' => 'px',
						'size' => 50,
					),
					'selectors'   => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-container-3d-depth: ({{SIZE}}{{UNIT}});',
					),
					'condition'   => array(
						'ufae_simple_animation_option' => 'flip',
					),
				)
			);

			$this->add_control(
				'ufae_simple_flip_direction',
				array(
					'label'     => esc_html__('Animation Direction', 'ultimate-flipbox-addon-for-elementor'),
					'type'      => \Elementor\Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => esc_html__('Left', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'eicon-h-align-left',
						),
						'right'  => array(
							'title' => esc_html__('Right', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'eicon-h-align-right',
						),
						'top'    => array(
							'title' => esc_html__('Top', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'eicon-v-align-top',
						),
						'bottom' => array(
							'title' => esc_html__('Bottom', 'ultimate-flipbox-addon-for-elementor'),
							'icon'  => 'eicon-v-align-bottom',
						),
					),
					'default'   => 'left',
					'condition' => array(
						'ufae_simple_animation_option' => array('flip', 'flip-classic', 'slide'),
					),
				)
			);

			$this->add_control(
				'ufae_simple_transition_duration',
				array(
					'label'       => esc_html__('Transition Duration (ms)', 'ultimate-flipbox-addon-for-elementor'),
					'type'        => \Elementor\Controls_Manager::NUMBER,
					'default'     => 1000,
					'min'         => 100,
					'max'         => 50000,
					'step'        => 100,
					'description' => esc_html__('Set the duration of the CSS transition in milliseconds.', 'ultimate-flipbox-addon-for-elementor'),
					'selectors'   => array(
						'{{WRAPPER}} ' . esc_html($this->common_selector) => '--ufae-transition-timing: {{VALUE}};',
					),
				)
			);

			// $this->add_control(
			// 	'ufae_simple_front_element_position',
			// 	array(
			// 		'label'       => esc_html__('Front Element Position', 'ultimate-flipbox-addon-for-elementor'),
			// 		'description' => esc_html__('Specify the order of flipbox elements as a comma-separated list (e.g., "icon, title, desc, button"). Ensure each element is separated by a comma.', 'ultimate-flipbox-addon-for-elementor'),
			// 		'type'        => \Elementor\Controls_Manager::TEXT,
			// 		'label_block' => true,
			// 		'default'     => 'icon,title,desc,button',
			// 		'ai'          => false,
			// 	)
			// );

			// $this->add_control(
			// 	'ufae_simple_back_element_position',
			// 	array(
			// 		'label'       => esc_html__('Front Element Position', 'ultimate-flipbox-addon-for-elementor'),
			// 		'description' => esc_html__('Specify the order of flipbox elements as a comma-separated list (e.g., "icon, title, desc, button"). Ensure each element is separated by a comma.', 'ultimate-flipbox-addon-for-elementor'),
			// 		'type'        => \Elementor\Controls_Manager::TEXT,
			// 		'label_block' => true,
			// 		'default'     => 'icon,title,desc,button',
			// 		'ai'          => false,
			// 	)
			// );

			$this->end_controls_section();
		}

		/**
		 * Render the widget output on the frontend.
		 *
		 * @return void
		 */
		protected function render()
		{
			$settings      = $this->get_settings_for_display();

			if (class_exists('UFAE\Widget\Simple\Ufae_Frontend\Ufae_Frontend_Output')) {
				$flipbox = new Ufae_Frontend_Output($settings, $this);
				$flipbox->render();
			}
		}
		/**
		 * Render the widget's content template in the editor.
		 *
		 * This method is responsible for rendering the content template
		 * of the widget in the Elementor editor. It includes the necessary
		 * files for the editor output and loop, and initializes the editor
		 * output rendering.
		 *
		 * @return void
		 */
		protected function content_template()
		{

			if (class_exists('UFAE\Widget\Simple\Ufae_Editor\Ufae_Editor_Output')) {
				$flipbox = new Ufae_Editor_Output();
				$flipbox->render();
			}
		}
	}
}
