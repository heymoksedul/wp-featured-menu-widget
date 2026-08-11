<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Accordion_Feature_Menu_Widget extends Widget_Base {

	public function get_name() {
		return 'accordion_feature_menu';
	}

	public function get_title() {
		return __( 'Accordion Feature Menu', 'feature-menu-widget' );
	}

	public function get_icon() {
		return 'eicon-accordion';
	}

	public function get_categories() {
		return [ 'feature-menu' ];
	}

	public function get_keywords() {
		return [ 'menu', 'feature', 'accordion', 'apple', 'video' ];
	}

	public function get_style_depends() {
		return [ 'afmw-style' ];
	}

	public function get_script_depends() {
		return [ 'fmw-gsap', 'afmw-script' ];
	}

	protected function register_controls() {

		$this->start_controls_section( 'section_menu', [
			'label' => __( 'Menu Items', 'feature-menu-widget' ),
		] );

		$this->add_control( 'default_image', [
			'label'   => __( 'Default Image', 'feature-menu-widget' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
			'description' => __( 'This image will show when no accordion item is active.', 'feature-menu-widget' ),
		] );

		$repeater = new Repeater();

		$repeater->add_control( 'item_title', [
			'label'       => __( 'Title', 'feature-menu-widget' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => __( 'Menu Item', 'feature-menu-widget' ),
			'label_block' => true,
		] );

		$repeater->add_control( 'item_desc', [
			'label'       => __( 'Description', 'feature-menu-widget' ),
			'type'        => Controls_Manager::TEXTAREA,
			'default'     => __( 'Add your description here.', 'feature-menu-widget' ),
			'label_block' => true,
		] );

		$repeater->add_control( 'item_media_type', [
			'label'   => __( 'Media Type', 'feature-menu-widget' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'image',
			'options' => [
				'image' => __( 'Image', 'feature-menu-widget' ),
				'video' => __( 'Video', 'feature-menu-widget' ),
			],
		] );

		$repeater->add_control( 'item_image', [
			'label'   => __( 'Image', 'feature-menu-widget' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
			'condition' => [ 'item_media_type' => 'image' ],
		] );

		$repeater->add_control( 'item_video', [
			'label'       => __( 'Video Link (MP4/YouTube/Vimeo)', 'feature-menu-widget' ),
			'type'        => Controls_Manager::URL,
			'placeholder' => __( 'https://your-video.mp4', 'feature-menu-widget' ),
			'condition'   => [ 'item_media_type' => 'video' ],
		] );

		$this->add_control( 'menu_items', [
			'label'       => __( 'Items', 'feature-menu-widget' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'title_field' => '{{{ item_title }}}',
			'default'     => [
				[
					'item_title' => 'Make',
					'item_desc'  => 'We manufacture our products using sustainable materials and ethical practices to ensure the highest quality while respecting our planet.',
				],
				[
					'item_title' => 'Package and Ship',
					'item_desc'  => 'Our packaging uses 100% recycled materials and our shipping partners offset their carbon footprint.',
				],
				[
					'item_title' => 'Use',
					'item_desc'  => 'Designed to last. Our products offer years of performance and energy efficiency.',
				],
				[
					'item_title' => 'Recover',
					'item_desc'  => 'We offer a free recycling program. Return your old devices and we will ensure they are processed responsibly.',
				],
			],
		] );

		$this->end_controls_section();

		// Style Section: Container
		$this->start_controls_section( 'section_style_container', [
			'label' => __( 'Container', 'feature-menu-widget' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'container_bg_color', [
			'label'     => __( 'Background Color', 'feature-menu-widget' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#f5f5f7',
			'selectors' => [
				'{{WRAPPER}} .afmw-section' => 'background-color: {{VALUE}} !important;',
			],
		] );

		$this->add_responsive_control( 'container_height', [
			'label' => __( 'Minimum Height', 'feature-menu-widget' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'vh', 'em' ],
			'range' => [
				'px' => [ 'min' => 200, 'max' => 1200 ],
				'vh' => [ 'min' => 20, 'max' => 100 ],
			],
			'default' => [
				'unit' => 'px',
				'size' => 500,
			],
			'selectors' => [
				'{{WRAPPER}} .afmw-section' => 'min-height: {{SIZE}}{{UNIT}} !important;',
			],
		] );

		$this->add_responsive_control( 'container_padding', [
			'label'      => __( 'Padding', 'feature-menu-widget' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .afmw-section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
			],
		] );

		$this->add_responsive_control( 'container_border_radius', [
			'label'      => __( 'Border Radius', 'feature-menu-widget' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .afmw-section' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
			],
		] );

		$this->end_controls_section();

		// Style Section: Accordion Items
		$this->start_controls_section( 'section_style_items', [
			'label' => __( 'Accordion Items', 'feature-menu-widget' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'item_inactive_bg', [
			'label'     => __( 'Inactive Background', 'feature-menu-widget' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#e8e8ed',
			'selectors' => [
				'{{WRAPPER}} .afmw-accordion-item' => 'background-color: {{VALUE}} !important;',
			],
		] );

		$this->add_control( 'item_hover_bg', [
			'label'     => __( 'Hover Background', 'feature-menu-widget' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#d2d2d7',
			'selectors' => [
				'{{WRAPPER}} .afmw-accordion-item:hover' => 'background-color: {{VALUE}} !important;',
			],
		] );

		$this->add_control( 'item_active_bg', [
			'label'     => __( 'Active Background', 'feature-menu-widget' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#e8e8ed',
			'selectors' => [
				'{{WRAPPER}} .afmw-accordion-item.active' => 'background-color: {{VALUE}} !important;',
			],
		] );

		$this->add_responsive_control( 'item_padding', [
			'label'      => __( 'Padding', 'feature-menu-widget' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .afmw-accordion-trigger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'item_border_radius', [
			'label'      => __( 'Inactive Border Radius', 'feature-menu-widget' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .afmw-accordion-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
			],
		] );

		$this->add_responsive_control( 'item_active_border_radius', [
			'label'      => __( 'Active Border Radius', 'feature-menu-widget' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .afmw-accordion-item.active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
			],
		] );

		$this->end_controls_section();

		// Style Section: Typography
		$this->start_controls_section( 'section_style_typography', [
			'label' => __( 'Typography', 'feature-menu-widget' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'title_color', [
			'label'     => __( 'Title Color', 'feature-menu-widget' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#1d1d1f',
			'selectors' => [
				'{{WRAPPER}} .afmw-accordion-title' => 'color: {{VALUE}} !important;',
				'{{WRAPPER}} .afmw-accordion-desc strong' => 'color: {{VALUE}} !important;',
			],
		] );

		$this->add_control( 'title_hover_color', [
			'label'     => __( 'Title Hover Color', 'feature-menu-widget' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#1d1d1f',
			'selectors' => [
				'{{WRAPPER}} .afmw-accordion-item:hover .afmw-accordion-title' => 'color: {{VALUE}} !important;',
			],
		] );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => __( 'Title Typography', 'feature-menu-widget' ),
				'selector' => '{{WRAPPER}} .afmw-accordion-title, {{WRAPPER}} .afmw-accordion-desc strong',
			]
		);

		$this->add_control( 'desc_color', [
			'label'     => __( 'Description Color', 'feature-menu-widget' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#1d1d1f',
			'separator' => 'before',
			'selectors' => [
				'{{WRAPPER}} .afmw-accordion-desc' => 'color: {{VALUE}} !important;',
			],
		] );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'label'    => __( 'Description Typography', 'feature-menu-widget' ),
				'selector' => '{{WRAPPER}} .afmw-accordion-desc',
			]
		);

		$this->end_controls_section();

		// Style Section: Icons & Buttons
		$this->start_controls_section( 'section_style_icons', [
			'label' => __( 'Icons & Navigation', 'feature-menu-widget' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'heading_plus_icon', [
			'label' => __( 'Plus Icon', 'feature-menu-widget' ),
			'type' => Controls_Manager::HEADING,
		] );

		$this->add_control( 'icon_color', [
			'label'     => __( 'Plus Icon Color', 'feature-menu-widget' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#1d1d1f',
			'selectors' => [
				'{{WRAPPER}} .afmw-plus-icon svg path' => 'stroke: {{VALUE}} !important;',
				'{{WRAPPER}} .afmw-plus-icon' => 'border-color: {{VALUE}} !important;',
			],
		] );

		$this->add_control( 'icon_hover_color', [
			'label'     => __( 'Plus Icon Hover Color', 'feature-menu-widget' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#1d1d1f',
			'selectors' => [
				'{{WRAPPER}} .afmw-accordion-item:hover .afmw-plus-icon svg path' => 'stroke: {{VALUE}} !important;',
				'{{WRAPPER}} .afmw-accordion-item:hover .afmw-plus-icon' => 'border-color: {{VALUE}} !important;',
			],
		] );

		$this->add_control( 'heading_nav_buttons', [
			'label' => __( 'Navigation Arrows', 'feature-menu-widget' ),
			'type' => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'nav_bg_color', [
			'label'     => __( 'Arrow Background', 'feature-menu-widget' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#e8e8ed',
			'selectors' => [
				'{{WRAPPER}} .afmw-nav-btn' => 'background-color: {{VALUE}} !important;',
			],
		] );

		$this->add_control( 'nav_icon_color', [
			'label'     => __( 'Arrow Icon Color', 'feature-menu-widget' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#1d1d1f',
			'selectors' => [
				'{{WRAPPER}} .afmw-nav-btn svg path' => 'stroke: {{VALUE}} !important;',
			],
		] );

		$this->add_responsive_control( 'nav_size', [
			'label' => __( 'Arrow Button Size', 'feature-menu-widget' ),
			'type' => Controls_Manager::SLIDER,
			'range' => [
				'px' => [
					'min' => 20,
					'max' => 60,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .afmw-nav-btn' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
			],
		] );

		$this->add_control( 'heading_close_button', [
			'label' => __( 'Close Button', 'feature-menu-widget' ),
			'type' => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'close_bg_color', [
			'label'     => __( 'Close Background', 'feature-menu-widget' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#e8e8ed',
			'selectors' => [
				'{{WRAPPER}} .afmw-close-btn' => 'background-color: {{VALUE}} !important;',
			],
		] );

		$this->add_control( 'close_icon_color', [
			'label'     => __( 'Close Icon Color', 'feature-menu-widget' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#1d1d1f',
			'selectors' => [
				'{{WRAPPER}} .afmw-close-btn svg path' => 'stroke: {{VALUE}} !important;',
			],
		] );

		$this->add_responsive_control( 'close_size', [
			'label' => __( 'Close Button Size', 'feature-menu-widget' ),
			'type' => Controls_Manager::SLIDER,
			'range' => [
				'px' => [
					'min' => 20,
					'max' => 60,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .afmw-close-btn' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
			],
		] );

		$this->end_controls_section();

		// Style Section: Media
		$this->start_controls_section( 'section_style_media', [
			'label' => __( 'Media (Image/Video)', 'feature-menu-widget' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( 'media_width', [
			'label'      => __( 'Width', 'feature-menu-widget' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%', 'vw' ],
			'range' => [
				'%' => [ 'min' => 10, 'max' => 100 ],
				'px' => [ 'min' => 50, 'max' => 1000 ],
			],
			'selectors'  => [
				'{{WRAPPER}} .afmw-media-item' => 'width: {{SIZE}}{{UNIT}} !important;',
				'{{WRAPPER}} .afmw-media-default' => 'width: {{SIZE}}{{UNIT}} !important;',
			],
		] );

		$this->add_responsive_control( 'media_height', [
			'label'      => __( 'Height', 'feature-menu-widget' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'vh', '%' ],
			'range' => [
				'px' => [ 'min' => 100, 'max' => 1000 ],
				'vh' => [ 'min' => 10, 'max' => 100 ],
				'%' => [ 'min' => 10, 'max' => 100 ],
			],
			'selectors'  => [
				'{{WRAPPER}} .afmw-media-item' => 'height: {{SIZE}}{{UNIT}} !important;',
				'{{WRAPPER}} .afmw-media-default' => 'height: {{SIZE}}{{UNIT}} !important;',
			],
		] );

		$this->add_responsive_control( 'media_border_radius', [
			'label'      => __( 'Border Radius', 'feature-menu-widget' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .afmw-media-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
			],
		] );

		$this->add_control( 'media_object_fit', [
			'label' => __( 'Object Fit', 'feature-menu-widget' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'cover',
			'options' => [
				'fill' => __( 'Fill', 'feature-menu-widget' ),
				'cover' => __( 'Cover', 'feature-menu-widget' ),
				'contain' => __( 'Contain', 'feature-menu-widget' ),
			],
			'selectors' => [
				'{{WRAPPER}} .afmw-media-item' => 'object-fit: {{VALUE}} !important;',
				'{{WRAPPER}} .afmw-media-default' => 'object-fit: {{VALUE}} !important;',
			],
		] );

		$this->add_control( 'media_object_position', [
			'label' => __( 'Object Position', 'feature-menu-widget' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'center center',
			'options' => [
				'center center' => __( 'Center Center', 'feature-menu-widget' ),
				'center left'   => __( 'Center Left', 'feature-menu-widget' ),
				'center right'  => __( 'Center Right', 'feature-menu-widget' ),
				'top center'    => __( 'Top Center', 'feature-menu-widget' ),
				'top left'      => __( 'Top Left', 'feature-menu-widget' ),
				'top right'     => __( 'Top Right', 'feature-menu-widget' ),
				'bottom center' => __( 'Bottom Center', 'feature-menu-widget' ),
				'bottom left'   => __( 'Bottom Left', 'feature-menu-widget' ),
				'bottom right'  => __( 'Bottom Right', 'feature-menu-widget' ),
			],
			'selectors' => [
				'{{WRAPPER}} .afmw-media-item' => 'object-position: {{VALUE}} !important;',
				'{{WRAPPER}} .afmw-media-default' => 'object-position: {{VALUE}} !important;',
			],
		] );
	}

	protected function render() {
		$settings    = $this->get_settings_for_display();
		$widget_id   = 'afmw-' . $this->get_id();
		$menu_items  = ! empty( $settings['menu_items'] ) ? $settings['menu_items'] : [];
		$placeholder = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
		?>
		<section class="afmw-section" id="<?php echo esc_attr( $widget_id ); ?>">
			
			<div class="afmw-close-btn" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Close', 'feature-menu-widget' ); ?>">
				<svg width="12" height="12" viewBox="0 0 24 24" fill="none">
					<path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
					<path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
				</svg>
			</div>

			<div class="afmw-content">
				<div class="afmw-left-panel">
					
					<div class="afmw-side-nav">
						<div class="afmw-nav-btn afmw-nav-prev" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Previous', 'feature-menu-widget' ); ?>">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M18 15L12 9L6 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
						</div>
						<div class="afmw-nav-btn afmw-nav-next" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Next', 'feature-menu-widget' ); ?>">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
						</div>
					</div>

					<div class="afmw-accordion-list">
						<?php
						$index = 0;
						foreach ( $menu_items as $item ) :
							$title = $item['item_title'] ?? '';
							$desc  = $item['item_desc'] ?? '';
							?>
							<div class="afmw-accordion-item" data-index="<?php echo esc_attr( $index ); ?>">
								
								<div class="afmw-accordion-trigger" role="button" tabindex="0">
									<div class="afmw-plus-icon">
										<svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
									</div>
									<span class="afmw-accordion-title"><?php echo esc_html( $title ); ?></span>
								</div>
								
								<div class="afmw-accordion-content">
									<p class="afmw-accordion-desc">
										<strong><?php echo esc_html( $title ); ?>.</strong> <?php echo esc_html( $desc ); ?>
									</p>
								</div>
								
							</div>
							<?php
							$index++;
						endforeach;
						?>
					</div>
				</div>

				<div class="afmw-right-panel">
					<div class="afmw-media-container">
						<?php
						// Default Image
						$default_img_url = ! empty( $settings['default_image']['url'] ) ? $settings['default_image']['url'] : $placeholder;
						?>
						<img class="afmw-media-default" src="<?php echo esc_url( $default_img_url ); ?>" alt="" />

						<?php
						$index = 0;
						foreach ( $menu_items as $item ) :
							$media_type = $item['item_media_type'] ?? 'image';
							if ( 'video' === $media_type && ! empty( $item['item_video']['url'] ) ) : ?>
								<video class="afmw-media-item" data-index="<?php echo esc_attr( $index ); ?>" src="<?php echo esc_url( $item['item_video']['url'] ); ?>" muted loop playsinline></video>
							<?php else :
								$img_url = ! empty( $item['item_image']['url'] ) ? $item['item_image']['url'] : $placeholder;
								?>
								<img class="afmw-media-item" data-index="<?php echo esc_attr( $index ); ?>" src="<?php echo esc_url( $img_url ); ?>" alt="" />
							<?php endif; 
							$index++;
						endforeach;
						?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
