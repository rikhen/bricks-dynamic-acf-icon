<?php
namespace Bricks;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Element_Dynamic_ACF_Icon extends Element {
	public $category = 'custom';
	public $name     = 'dynamic-acf-icon';
	public $icon     = 'ti-star'; // You might want to change this icon

	public function get_label() {
		return esc_html__( 'Dynamic ACF Icon', 'bricks' );
	}

	public function set_controls() {
		$this->controls['icon'] = [
			'tab'     => 'content',
			'label'   => esc_html__( 'ACF Field Name', 'bricks' ),
			'type'    => 'text',
			'default' => '',
		];
		
		$this->controls['iconColor'] = [
			'tab'      => 'content',
			'label'    => esc_html__( 'Color', 'bricks' ),
			'type'     => 'color',
			'inline' => true,
			'css'      => [
				[
					'property' => 'stroke',
				],
				[
					'property' => 'color',
				],
			],
		];

		$this->controls['iconFillColor'] = [
			'tab'      => 'content',
			'label'    => esc_html__( 'Fill', 'bricks' ),
			'type'     => 'color',
			'inline' => true,
			'css'      => [
				[
					'property' => 'fill',
				],
			],
		];

		$this->controls['height'] = [
			'tab'      => 'content',
			'label'    => esc_html__( 'Height', 'bricks' ),
			'type'     => 'number',
			'units'    => true,
			'inline' => true,
			'css'      => [
				[
					'property' => 'height',
				],
			],
		];

		$this->controls['width'] = [
			'tab'      => 'content',
			'label'    => esc_html__( 'Width', 'bricks' ),
			'type'     => 'number',
			'units'    => true,
			'inline' => true,
			'css'      => [
				[
					'property' => 'width',
				],
			],
		];

		$this->controls['strokeWidth'] = [
			'tab'      => 'content',
			'label'    => esc_html__( 'Stroke Width', 'bricks' ),
			'type'     => 'number',
			'units'    => true,
			'inline' => true,
			'css'      => [
				[
					'property' => 'stroke-width',
				],
			],
		];

		$this->controls['iconSize'] = [
			'tab'      => 'content',
			'label'    => esc_html__( 'Size', 'bricks' ),
			'type'     => 'number',
			'units'    => true,
			'inline' => true,
			'css'      => [
				[
					'property' => 'font-size',
				],
			],
		];

		$this->controls['link'] = [
			'tab'   => 'content',
			'label' => esc_html__( 'Link', 'bricks' ),
			'type'  => 'link',
		];
	}

	public function render() {
		$settings = $this->settings;
		$icon     = ! empty( $settings['icon'] ) ? $settings['icon'] : false;
		$link     = ! empty( $settings['link'] ) ? $settings['link'] : false;

		vis($settings, 'settings', 'blue', 'Custom Element Settings Arrays');
		if ( ! $icon ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'No ACF field name provided.', 'bricks' ),
				]
			);
		}

		if ( isset( $settings['icon'] ) && Query::is_looping() ) {
			if ( strpos( $settings['icon'], '{' ) !== false ) {
				$this->attributes['_root']['class'][] = 'brxe-icon';
				$this->attributes['_root']['data-query-loop-index'] = Query::get_loop_index();
				// Get current query object
				$loop_object_id = Query::get_loop_object_id();
				$icon = $this->get_acf_icon_array( 'featured_icon', $loop_object_id);
			}
		}

		// Linked icon: Remove custom attributes from root to add to the link (@since 1.7)
		if ( $link ) {
			$custom_attributes = $this->get_custom_attributes( $settings );

			if ( is_array( $custom_attributes ) ) {
				foreach ( $custom_attributes as $key => $value ) {
					if ( isset( $this->attributes['_root'][ $key ] ) ) {
						unset( $this->attributes['_root'][ $key ] );
					}
				}
			}
		}
		
		// Support dynamic data color in loop (@since 1.8)
		if ( isset( $settings['iconColor']['raw'] ) && Query::is_looping() ) {
			if ( strpos( $settings['iconColor']['raw'], '{' ) !== false ) {
				$this->attributes['_root']['data-query-loop-index'] = Query::get_loop_index();
			}
		}

		if ( isset( $settings['iconFillColor']['raw'] ) && Query::is_looping() ) {
			if ( strpos( $settings['iconFillColor']['raw'], '{' ) !== false ) {
				$this->attributes['_root']['data-query-loop-index'] = Query::get_loop_index();
			}
		}

		$icon = self::render_icon( $icon, $this->attributes['_root'] );

		echo $icon;

	}

	public function get_acf_icon_array($acf_field_name, $loop_object_id) {

		// Fetch the ACF field data
		$acf_icon = get_field($acf_field_name, $loop_object_id);

		// Check if ACF field is not empty and is an array
		if (!empty($acf_icon) && is_array($acf_icon)) {

			// Check if file extension is SVG
			$extension = pathinfo($acf_icon['url'], PATHINFO_EXTENSION);
			if (strtolower($extension) !== 'svg') {
				return $this->render_element_placeholder(
					[
						'title' => esc_html__( 'The ACF field does not contain a valid SVG URL.', 'bricks' ),
					]
				);
			}

			// Construct and return the desired array structure
			return array(
				'library' => 'svg',
				'svg' => array(
					'id' => $acf_icon['ID'], // or $acf_icon['id'] depending on your ACF return format
					'filename' => $acf_icon['filename'],
					'url' => $acf_icon['url']
				)
			);
		}
	
		// Return null or some default structure if ACF field is empty
		return null;
	}
}