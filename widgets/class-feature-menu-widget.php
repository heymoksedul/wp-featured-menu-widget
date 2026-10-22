<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

class Feature_Menu_Widget extends Widget_Base {

	public function get_name() {
		return 'feature_menu';
	}

	public function get_title() {
		return __( 'Feature Menu', 'feature-menu-widget' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_categories() {
		return [ 'feature-menu' ];
	}

	public function get_keywords() {
		return [ 'menu', 'feature', 'hover', 'submenu', 'apple', 'video' ];
	}

	public function get_style_depends() {
		return [ 'fmw-style' ];
	}

	public function get_script_depends() {
		return [ 'fmw-gsap', 'fmw-script' ];
	}

	protected function register_controls() {

		/* ===== GENERAL ===== */
		$this->start_controls_section( 'section_general', [
			'label' => __( 'General', 'feature-menu-widget' ),
		] );

		$this->add_control( 'default_video', [
			'label'       => __( 'Default Background Video', 'feature-menu-widget' ),
			'type'        => Controls_Manager::MEDIA,
			'media_type'  => 'video',
			'default'     => [
				'url' => 'https://pub-5a76eae4010144fe8d4cae1ca37b0351.r2.dev/CreatoCore%20Background%20.mp4',
			],
			'description' => __( 'Shown before any menu item is hovered or active.', 'feature-menu-widget' ),
		] );

		$this->end_controls_section();

		/* ===== MENU ITEMS ===== */
		$this->start_controls_section( 'section_menu', [
			'label' => __( 'Menu Items', 'feature-menu-widget' ),
		] );

		$sub_repeater = new Repeater();

		$sub_repeater->add_control( 'sub_title', [
			'label'       => __( 'Title', 'feature-menu-widget' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => __( 'Submenu Item', 'feature-menu-widget' ),
			'label_block' => true,
		] );

		$sub_repeater->add_control( 'sub_link', [
			'label'       => __( 'Link', 'feature-menu-widget' ),
			'type'        => Controls_Manager::URL,
			'placeholder' => __( 'https://your-link.com', 'feature-menu-widget' ),
			'default'     => [ 'url' => '#' ],
		] );

		$sub_repeater->add_control( 'sub_image', [
			'label'   => __( 'Background Image (on hover)', 'feature-menu-widget' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
		] );

		$repeater = new Repeater();

		$repeater->add_control( 'item_title', [
			'label'       => __( 'Title', 'feature-menu-widget' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => __( 'Menu Item', 'feature-menu-widget' ),
			'label_block' => true,
		] );

		$repeater->add_control( 'item_type', [
			'label'   => __( 'Type', 'feature-menu-widget' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'submenu' => __( 'Has Submenu', 'feature-menu-widget' ),
				'single'  => __( 'Single Link (no submenu)', 'feature-menu-widget' ),
			],
			'default' => 'submenu',
		] );

		$repeater->add_control( 'item_image', [
			'label'   => __( 'Background Image (on hover/active)', 'feature-menu-widget' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
		] );

		$repeater->add_control( 'item_link', [
			'label'       => __( 'Link', 'feature-menu-widget' ),
			'type'        => Controls_Manager::URL,
			'placeholder' => __( 'https://your-link.com', 'feature-menu-widget' ),
			'default'     => [ 'url' => '#' ],
			'condition'   => [ 'item_type' => 'single' ],
		] );

		$repeater->add_control( 'submenu_items', [
			'label'       => __( 'Submenu Items', 'feature-menu-widget' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $sub_repeater->get_controls(),
			'title_field' => '{{{ sub_title }}}',
			'condition'   => [ 'item_type' => 'submenu' ],
		] );

		$this->add_control( 'menu_items', [
			'label'       => __( 'Items', 'feature-menu-widget' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'title_field' => '{{{ item_title }}}',
			'default'     => $this->get_default_menu_items(),
		] );

		$this->end_controls_section();

		/* ===== SUBMENU STYLE ===== */
		$this->start_controls_section( 'section_submenu_style', [
			'label' => __( 'Submenu', 'feature-menu-widget' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( 'submenu_max_height', [
			'label'       => __( 'Max Height (Scroll Y)', 'feature-menu-widget' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => [ 'px', 'vh' ],
			'range'       => [
				'px' => [ 'min' => 150, 'max' => 900, 'step' => 10 ],
				'vh' => [ 'min' => 20, 'max' => 95, 'step' => 1 ],
			],
			'default'     => [
				'unit' => 'px',
				'size' => 440,
			],
			'selectors'   => [
				'{{WRAPPER}} .submenu-panel' => 'max-height: {{SIZE}}{{UNIT}} !important;',
			],
			'description' => __( 'When submenu items exceed this height, a vertical scrollbar appears.', 'feature-menu-widget' ),
		] );

		$this->add_responsive_control( 'submenu_width', [
			'label'      => __( 'Width', 'feature-menu-widget' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [ 'min' => 220, 'max' => 600, 'step' => 5 ],
			],
			'default'    => [
				'unit' => 'px',
				'size' => 320,
			],
			'selectors'  => [
				'{{WRAPPER}} .submenu-panel' => 'width: {{SIZE}}{{UNIT}} !important;',
			],
		] );

		$this->add_responsive_control( 'submenu_items_gap', [
			'label'      => __( 'Items Gap', 'feature-menu-widget' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [ 'min' => 4, 'max' => 30, 'step' => 1 ],
			],
			'default'    => [
				'unit' => 'px',
				'size' => 12,
			],
			'selectors'  => [
				'{{WRAPPER}} .submenu-list' => 'gap: {{SIZE}}{{UNIT}} !important;',
			],
		] );

		$this->add_responsive_control( 'submenu_padding', [
			'label'      => __( 'Padding', 'feature-menu-widget' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'default'    => [
				'top'      => 20,
				'right'    => 22,
				'bottom'   => 20,
				'left'     => 22,
				'unit'     => 'px',
				'isLinked' => false,
			],
			'selectors'  => [
				'{{WRAPPER}} .submenu-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
			],
		] );

		$this->add_control( 'hide_scrollbar', [
			'label'        => __( 'Hide Scrollbar', 'feature-menu-widget' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Yes', 'feature-menu-widget' ),
			'label_off'    => __( 'No', 'feature-menu-widget' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'selectors'    => [
				'{{WRAPPER}} .submenu-list' => 'scrollbar-width: none !important; -ms-overflow-style: none !important;',
				'{{WRAPPER}} .submenu-list::-webkit-scrollbar' => 'display: none !important; width: 0 !important; height: 0 !important;',
			],
		] );

		$this->end_controls_section();
	}

	private function get_default_menu_items() {
		return [
			[
				'item_title'    => 'Home Services',
				'item_type'     => 'submenu',
				'submenu_items' => [
					[ 'sub_title' => 'HVAC' ],
					[ 'sub_title' => 'Landscaping' ],
					[ 'sub_title' => 'Pest Control' ],
					[ 'sub_title' => 'Plumbing' ],
					[ 'sub_title' => 'Roofing' ],
					[ 'sub_title' => 'Electrical' ],
				],
			],
			[
				'item_title'    => 'Small Businesses',
				'item_type'     => 'submenu',
				'submenu_items' => [
					[ 'sub_title' => 'Accounting & Finance' ],
					[ 'sub_title' => 'Marketing & SEO' ],
					[ 'sub_title' => 'Legal Services' ],
					[ 'sub_title' => 'IT & Tech Support' ],
					[ 'sub_title' => 'HR & Staffing' ],
				],
			],
			[ 'item_title' => 'Technology', 'item_type' => 'single' ],
			[ 'item_title' => 'E-Commerce', 'item_type' => 'single' ],
			[ 'item_title' => 'Not-for-Profit', 'item_type' => 'single' ],
			[ 'item_title' => 'Start Up', 'item_type' => 'single' ],
			[ 'item_title' => 'Car Rental', 'item_type' => 'single' ],
		];
	}

	protected function render() {
		$settings    = $this->get_settings_for_display();
		$widget_id   = 'fmw-' . $this->get_id();
		$video_url   = ! empty( $settings['default_video']['url'] ) ? $settings['default_video']['url'] : '';
		$menu_items  = ! empty( $settings['menu_items'] ) ? $settings['menu_items'] : [];
		$top_count   = count( $menu_items );
		$placeholder = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
		?>
		<section class="mac-section fmw-section" id="<?php echo esc_attr( $widget_id ); ?>">
			<div class="bg-images">
				<?php if ( $video_url ) : ?>
					<video class="bg-image bg-video" autoplay muted loop playsinline>
						<source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4" />
					</video>
				<?php endif; ?>

				<?php
				foreach ( $menu_items as $item ) :
					$img_url = ! empty( $item['item_image']['url'] ) ? $item['item_image']['url'] : $placeholder;
					?>
					<img class="bg-image" src="<?php echo esc_url( $img_url ); ?>" alt="" />
				<?php endforeach; ?>

				<?php
				foreach ( $menu_items as $item ) :
					if ( ( $item['item_type'] ?? 'submenu' ) === 'submenu' && ! empty( $item['submenu_items'] ) ) :
						foreach ( $item['submenu_items'] as $sub ) :
							$simg = ! empty( $sub['sub_image']['url'] ) ? $sub['sub_image']['url'] : $placeholder;
							?>
							<img class="bg-image" src="<?php echo esc_url( $simg ); ?>" alt="" />
						<?php endforeach;
					endif;
				endforeach;
				?>
			</div>

			<div class="overlay"></div>

			<button class="close-btn" type="button" aria-label="<?php esc_attr_e( 'Close', 'feature-menu-widget' ); ?>">
				<svg width="14" height="14" viewBox="0 0 24 24" fill="none">
					<path d="M18 6L6 18" stroke="white" stroke-width="2" stroke-linecap="round" />
					<path d="M6 6L18 18" stroke="white" stroke-width="2" stroke-linecap="round" />
				</svg>
			</button>

			<div class="content">
				<div class="left-panel">
					<div class="side-nav">
						<button class="nav-btn nav-prev" type="button" aria-label="<?php esc_attr_e( 'Previous', 'feature-menu-widget' ); ?>">
							<svg viewBox="0 0 24 24" fill="none"><path d="M18 15L12 9L6 15" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
						</button>
						<button class="nav-btn nav-next" type="button" aria-label="<?php esc_attr_e( 'Next', 'feature-menu-widget' ); ?>">
							<svg viewBox="0 0 24 24" fill="none"><path d="M6 9L12 15L18 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
						</button>
					</div>

					<div class="feature-list">
						<?php
						$sub_cursor = $top_count;
						foreach ( $menu_items as $item ) :
							$type  = $item['item_type'] ?? 'submenu';
							$title = $item['item_title'] ?? '';

							if ( $type === 'submenu' && ! empty( $item['submenu_items'] ) ) :
								?>
								<div class="feature-item">
									<button class="feature-button" type="button">
										<div class="feature-button-left">
											<span class="feature-title"><?php echo esc_html( $title ); ?></span>
										</div>
										<div class="plus"><span></span><span></span></div>
									</button>
									<div class="submenu-panel">
										<div class="submenu-list">
											<?php
											foreach ( $item['submenu_items'] as $sub ) :
												$sub_link = ! empty( $sub['sub_link']['url'] ) ? $sub['sub_link']['url'] : '#';
												$target   = ! empty( $sub['sub_link']['is_external'] ) ? ' target="_blank"' : '';
												$nofollow = ! empty( $sub['sub_link']['nofollow'] ) ? ' rel="nofollow"' : '';
												?>
												<a href="<?php echo esc_url( $sub_link ); ?>"<?php echo $target . $nofollow; ?> class="submenu-item" data-img-index="<?php echo esc_attr( $sub_cursor ); ?>">
													<div class="submenu-item-left">
														<span class="submenu-item-title"><?php echo esc_html( $sub['sub_title'] ?? '' ); ?></span>
													</div>
													<div class="submenu-right-arrow">
														<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
													</div>
												</a>
												<?php
												$sub_cursor++;
											endforeach;
											?>
										</div>
									</div>
								</div>
								<?php
							else :
								$link     = ! empty( $item['item_link']['url'] ) ? $item['item_link']['url'] : '#';
								$target   = ! empty( $item['item_link']['is_external'] ) ? ' target="_blank"' : '';
								$nofollow = ! empty( $item['item_link']['nofollow'] ) ? ' rel="nofollow"' : '';
								?>
								<div class="feature-item link-item">
									<a href="<?php echo esc_url( $link ); ?>"<?php echo $target . $nofollow; ?> class="feature-button">
										<div class="feature-button-left">
											<span class="feature-title"><?php echo esc_html( $title ); ?></span>
										</div>
										<div class="right-arrow">
											<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
										</div>
									</a>
								</div>
								<?php
							endif;
						endforeach;
						?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
