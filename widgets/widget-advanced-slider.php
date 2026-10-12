<?php
if ( ! defined( 'ABSPATH' ) ) {
exit; // Exit if accessed directly.
}

/**
 * Class Elementor_Advanced_Slider_Widget
 *
 * Commercial-grade kinetic slider for Elementor.
 *style
 * Structural notes:
 *  - `.adv-slide-inner` is a single-cell CSS Grid; content block and overlay
 *    image are independently positioned via justify-self/align-self so any
 *    combination of alignments is possible.
 *  - Background is a separate absolutely-positioned layer (`.adv-slide-bg`)
 *    so it can be animated (Ken Burns) independently of slide/content layout,
 *    and so a video background can be swapped in without touching layout CSS.
 *  - v2.1: the background layer is now a real <img> so the LCP request can
 *    carry fetchpriority="high" (CSS background images never can).
 */
class Elementor_Advanced_Slider_Widget extends \Elementor\Widget_Base {

public function get_name() {
return 'advanced_slider';
}

public function get_title() {
return esc_html__( 'Advanced Kinetic Slider', 'adv-kinetic-slider' );
}

public function get_icon() {
return 'eicon-slideshow';
}

public function get_categories() {
return [ 'general' ];
}

public function get_keywords() {
return [ 'slider', 'carousel', 'hero', 'swiper', 'kinetic', 'banner' ];
}

public function get_script_depends() {
return [ 'swiper', 'advanced-slider-js' ];
}

public function get_style_depends() {
return [ 'swiper', 'advanced-slider-css' ];
}

/**
 * Shared animation option list used by every per-element animation
 * dropdown (subheading, heading, description, buttons, overlay image).
 */
private function get_animation_options() {
return [
'anim-none'         => esc_html__( 'None', 'adv-kinetic-slider' ),
'anim-fade-up'      => esc_html__( 'Fade Up', 'adv-kinetic-slider' ),
'anim-fade-down'    => esc_html__( 'Fade Down', 'adv-kinetic-slider' ),
'anim-fade-left'    => esc_html__( 'Fade Left', 'adv-kinetic-slider' ),
'anim-fade-right'   => esc_html__( 'Fade Right', 'adv-kinetic-slider' ),
'anim-zoom-in'      => esc_html__( 'Zoom In', 'adv-kinetic-slider' ),
'anim-zoom-out'     => esc_html__( 'Zoom Out', 'adv-kinetic-slider' ),
'anim-blur-in'      => esc_html__( 'Blur In', 'adv-kinetic-slider' ),
'anim-rotate-in'    => esc_html__( 'Rotate In', 'adv-kinetic-slider' ),
'anim-flip-up'      => esc_html__( 'Flip Up (3D)', 'adv-kinetic-slider' ),
'anim-flip-down'    => esc_html__( 'Flip Down (3D)', 'adv-kinetic-slider' ),
'anim-elastic-up'   => esc_html__( 'Elastic Up', 'adv-kinetic-slider' ),
'anim-slide-reveal' => esc_html__( 'Slide Reveal (clip)', 'adv-kinetic-slider' ),
];
}

protected function register_controls() {

/* =========================================================
 * CONTENT TAB
 * ========================================================= */

// ---------------------------------------------------------
// SECTION: Slides Repeater
// ---------------------------------------------------------
$this->start_controls_section(
'section_slides',
[ 'label' => esc_html__( 'Slides', 'adv-kinetic-slider' ) ]
);

$repeater = new \Elementor\Repeater();

$repeater->start_controls_tabs( 'slide_tabs' );

// --- TAB: Background ---
$repeater->start_controls_tab( 'slide_tab_bg', [ 'label' => esc_html__( 'Background', 'adv-kinetic-slider' ) ] );

$repeater->add_control(
'bg_type',
[
'label'   => esc_html__( 'Background Type', 'adv-kinetic-slider' ),
'type'    => \Elementor\Controls_Manager::CHOOSE,
'options' => [
'image' => [ 'title' => 'Image', 'icon' => 'eicon-image' ],
'video' => [ 'title' => 'Video', 'icon' => 'eicon-video-camera' ],
],
'default' => 'image',
'toggle'  => false,
]
);

$repeater->add_control(
'slide_bg',
[
'label'     => esc_html__( 'Background Image', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::MEDIA,
'condition' => [ 'bg_type' => 'image' ],
]
);

$repeater->add_control(
'bg_size',
[
'label'     => esc_html__( 'Background Fit', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::SELECT,
'default'   => '',
'options'   => [
''          => esc_html__( 'Default (Cover)', 'adv-kinetic-slider' ),
'cover'     => esc_html__( 'Cover (fill & crop)', 'adv-kinetic-slider' ),
'contain'   => esc_html__( 'Contain (fit inside, no crop)', 'adv-kinetic-slider' ),
'auto'      => esc_html__( 'Auto (original size)', 'adv-kinetic-slider' ),
'100% 100%' => esc_html__( 'Fill (stretch to fit)', 'adv-kinetic-slider' ),
],
'condition' => [ 'bg_type' => 'image' ],
]
);

$repeater->add_control(
'bg_position',
[
'label'     => esc_html__( 'Background Position', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::SELECT,
'default'   => '',
'options'   => [
''              => esc_html__( 'Default (Center)', 'adv-kinetic-slider' ),
'left top'      => esc_html__( 'Left Top', 'adv-kinetic-slider' ),
'center top'    => esc_html__( 'Center Top', 'adv-kinetic-slider' ),
'right top'     => esc_html__( 'Right Top', 'adv-kinetic-slider' ),
'left center'   => esc_html__( 'Left Center', 'adv-kinetic-slider' ),
'center center' => esc_html__( 'Center Center', 'adv-kinetic-slider' ),
'right center'  => esc_html__( 'Right Center', 'adv-kinetic-slider' ),
'left bottom'   => esc_html__( 'Left Bottom', 'adv-kinetic-slider' ),
'center bottom' => esc_html__( 'Center Bottom', 'adv-kinetic-slider' ),
'right bottom'  => esc_html__( 'Right Bottom', 'adv-kinetic-slider' ),
],
'condition' => [ 'bg_type' => 'image' ],
]
);

$repeater->add_control(
'bg_repeat',
[
'label'     => esc_html__( 'Background Repeat', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::SELECT,
'default'   => '',
'options'   => [
''          => esc_html__( 'Default (No Repeat)', 'adv-kinetic-slider' ),
'no-repeat' => esc_html__( 'No Repeat', 'adv-kinetic-slider' ),
'repeat'    => esc_html__( 'Repeat (tile)', 'adv-kinetic-slider' ),
'repeat-x'  => esc_html__( 'Repeat Horizontally', 'adv-kinetic-slider' ),
'repeat-y'  => esc_html__( 'Repeat Vertically', 'adv-kinetic-slider' ),
],
'condition' => [ 'bg_type' => 'image' ],
]
);

$repeater->add_control(
'bg_note',
[
'type'            => \Elementor\Controls_Manager::RAW_HTML,
'raw'             => esc_html__( 'Tip: use "Contain" if your image is getting cropped and you want the full artwork visible (may show empty space on the sides). Global fallback defaults are set in Layout & Alignment → Background Image Fit.', 'adv-kinetic-slider' ),
'content_classes' => 'elementor-descriptor',
'condition'       => [ 'bg_type' => 'image' ],
]
);

$repeater->add_control(
'kenburns_effect',
[
'label'     => esc_html__( 'Ken Burns Motion', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::SELECT,
'default'   => 'none',
'options'   => [
'none'      => esc_html__( 'None (static)', 'adv-kinetic-slider' ),
'zoom-in'   => esc_html__( 'Zoom In', 'adv-kinetic-slider' ),
'zoom-out'  => esc_html__( 'Zoom Out', 'adv-kinetic-slider' ),
'pan-left'  => esc_html__( 'Pan Left → Right', 'adv-kinetic-slider' ),
'pan-right' => esc_html__( 'Pan Right → Left', 'adv-kinetic-slider' ),
],
'condition' => [ 'bg_type' => 'image' ],
]
);

$repeater->add_control(
'bg_video_mp4',
[
'label'       => esc_html__( 'Video File (MP4)', 'adv-kinetic-slider' ),
'type'        => \Elementor\Controls_Manager::MEDIA,
'media_types' => [ 'video' ],
'condition'   => [ 'bg_type' => 'video' ],
]
);

$repeater->add_control(
'bg_video_poster',
[
'label'     => esc_html__( 'Video Poster (fallback image)', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::MEDIA,
'condition' => [ 'bg_type' => 'video' ],
]
);

$repeater->add_control(
'overlay_tint_color',
[
'label'     => esc_html__( 'Overlay Tint', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::COLOR,
'default'   => 'rgba(0,0,0,0.4)',
'selectors' => [
'{{WRAPPER}} {{CURRENT_ITEM}} .adv-slide-overlay-tint' => 'background: {{VALUE}};',
],
]
);

$repeater->end_controls_tab();

// --- TAB: Content ---
$repeater->start_controls_tab( 'slide_tab_content', [ 'label' => esc_html__( 'Content', 'adv-kinetic-slider' ) ] );

$repeater->add_control(
'sub_heading',
[ 'label' => esc_html__( 'Sub Heading', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::TEXT ]
);

$repeater->add_control(
'anim_sub_heading',
[
'label'   => esc_html__( 'Sub Heading Animation', 'adv-kinetic-slider' ),
'type'    => \Elementor\Controls_Manager::SELECT,
'default' => 'anim-fade-up',
'options' => $this->get_animation_options(),
]
);

$repeater->add_control(
'kinetic_heading',
[
'label'       => esc_html__( 'Heading', 'adv-kinetic-slider' ),
'type'        => \Elementor\Controls_Manager::TEXT,
'label_block' => true,
'separator'   => 'before',
]
);

$repeater->add_control(
'heading_tag',
[
'label'   => esc_html__( 'Heading HTML Tag', 'adv-kinetic-slider' ),
'type'    => \Elementor\Controls_Manager::SELECT,
'default' => 'h2',
'options' => [ 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'div' => 'div' ],
]
);

$repeater->add_control(
'anim_heading',
[
'label'   => esc_html__( 'Heading Animation', 'adv-kinetic-slider' ),
'type'    => \Elementor\Controls_Manager::SELECT,
'default' => 'anim-fade-up',
'options' => $this->get_animation_options(),
]
);

$repeater->add_control(
'description',
[ 'label' => esc_html__( 'Description', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'separator' => 'before' ]
);

$repeater->add_control(
'anim_description',
[
'label'   => esc_html__( 'Description Animation', 'adv-kinetic-slider' ),
'type'    => \Elementor\Controls_Manager::SELECT,
'default' => 'anim-fade-up',
'options' => $this->get_animation_options(),
]
);

$repeater->add_control( 'button_1_text', [ 'label' => esc_html__( 'Button 1 Text', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::TEXT, 'separator' => 'before' ] );
$repeater->add_control( 'button_1_link', [ 'label' => esc_html__( 'Button 1 Link', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::URL ] );
$repeater->add_control( 'button_1_icon', [ 'label' => esc_html__( 'Button 1 Icon', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::ICONS, 'skin' => 'inline', 'default' => [ 'value' => '', 'library' => '' ] ] );
$repeater->add_control( 'button_2_text', [ 'label' => esc_html__( 'Button 2 Text', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::TEXT ] );
$repeater->add_control( 'button_2_link', [ 'label' => esc_html__( 'Button 2 Link', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::URL ] );
$repeater->add_control( 'button_2_icon', [ 'label' => esc_html__( 'Button 2 Icon', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::ICONS, 'skin' => 'inline', 'default' => [ 'value' => '', 'library' => '' ] ] );

$repeater->add_control(
'anim_buttons',
[
'label'   => esc_html__( 'Buttons Animation', 'adv-kinetic-slider' ),
'type'    => \Elementor\Controls_Manager::SELECT,
'default' => 'anim-fade-up',
'options' => $this->get_animation_options(),
]
);

$repeater->end_controls_tab();

// --- TAB: Overlay Image ---
$repeater->start_controls_tab( 'slide_tab_overlay', [ 'label' => esc_html__( 'Overlay Image', 'adv-kinetic-slider' ) ] );

$repeater->add_control(
'overlay_image',
[ 'label' => esc_html__( 'Overlay Image', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::MEDIA ]
);

$repeater->add_control(
'overlay_alt',
[
'label'       => esc_html__( 'Overlay Image Alt Text', 'adv-kinetic-slider' ),
'type'        => \Elementor\Controls_Manager::TEXT,
'description' => esc_html__( 'Leave blank to reuse the slide heading as alt text.', 'adv-kinetic-slider' ),
]
);

$repeater->add_control(
'anim_overlay',
[
'label'   => esc_html__( 'Image Animation', 'adv-kinetic-slider' ),
'type'    => \Elementor\Controls_Manager::SELECT,
'default' => 'anim-zoom-in',
'options' => $this->get_animation_options(),
]
);

$repeater->end_controls_tab();

// --- TAB: Layout (per-slide positioning overrides) ---
$repeater->start_controls_tab( 'slide_tab_layout', [ 'label' => esc_html__( 'Layout', 'adv-kinetic-slider' ) ] );

$repeater->add_control(
'content_position',
[
'label'     => esc_html__( 'Content Block Position', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::CHOOSE,
'options'   => [
'start'  => [ 'title' => 'Left', 'icon' => 'eicon-h-align-left' ],
'center' => [ 'title' => 'Center', 'icon' => 'eicon-h-align-center' ],
'end'    => [ 'title' => 'Right', 'icon' => 'eicon-h-align-right' ],
],
'default'   => '',
'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .adv-slide-content' => 'justify-self: {{VALUE}};' ],
]
);

$repeater->add_control(
'text_align',
[
'label'     => esc_html__( 'Text Alignment', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::CHOOSE,
'options'   => [
'left'   => [ 'title' => 'Left', 'icon' => 'eicon-text-align-left' ],
'center' => [ 'title' => 'Center', 'icon' => 'eicon-text-align-center' ],
'right'  => [ 'title' => 'Right', 'icon' => 'eicon-text-align-right' ],
],
'default'   => '',
'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .adv-slide-content' => 'text-align: {{VALUE}};' ],
]
);

$repeater->add_control(
'buttons_align',
[
'label'     => esc_html__( 'Buttons Alignment', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::CHOOSE,
'options'   => [
'flex-start' => [ 'title' => 'Left', 'icon' => 'eicon-text-align-left' ],
'center'     => [ 'title' => 'Center', 'icon' => 'eicon-text-align-center' ],
'flex-end'   => [ 'title' => 'Right', 'icon' => 'eicon-text-align-right' ],
],
'default'   => '',
'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .adv-slide-buttons' => 'justify-content: {{VALUE}};' ],
]
);

$repeater->add_control(
'overlay_position',
[
'label'     => esc_html__( 'Overlay Image Position', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::CHOOSE,
'options'   => [
'start'  => [ 'title' => 'Left', 'icon' => 'eicon-h-align-left' ],
'center' => [ 'title' => 'Center', 'icon' => 'eicon-h-align-center' ],
'end'    => [ 'title' => 'Right', 'icon' => 'eicon-h-align-right' ],
],
'default'   => '',
'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .adv-slide-overlay-img' => 'justify-self: {{VALUE}};' ],
]
);

$repeater->add_control(
'overlay_vertical_position',
[
'label'     => esc_html__( 'Overlay Image Vertical Position', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::CHOOSE,
'options'   => [
'start'  => [ 'title' => 'Top', 'icon' => 'eicon-v-align-top' ],
'center' => [ 'title' => 'Middle', 'icon' => 'eicon-v-align-middle' ],
'end'    => [ 'title' => 'Bottom', 'icon' => 'eicon-v-align-bottom' ],
],
'default'   => '',
'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .adv-slide-overlay-img' => 'align-self: {{VALUE}};' ],
]
);

$repeater->add_control( 'custom_class', [ 'label' => esc_html__( 'Custom CSS Class', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::TEXT, 'separator' => 'before' ] );

$repeater->end_controls_tab();

$repeater->end_controls_tabs();

$this->add_control(
'slides_list',
[
'label'       => esc_html__( 'Slides', 'adv-kinetic-slider' ),
'type'        => \Elementor\Controls_Manager::REPEATER,
'fields'      => $repeater->get_controls(),
'title_field' => '{{{ kinetic_heading }}}',
'default'     => [
[
'kinetic_heading' => esc_html__( 'Your Powerful Headline Here', 'adv-kinetic-slider' ),
'sub_heading'     => esc_html__( 'WELCOME TO OUR SITE', 'adv-kinetic-slider' ),
'description'     => esc_html__( 'Add a short, compelling description for this slide.', 'adv-kinetic-slider' ),
'button_1_text'   => esc_html__( 'Get Started', 'adv-kinetic-slider' ),
],
],
]
);

$this->end_controls_section();

// ---------------------------------------------------------
// SECTION: Slider Layout & Alignment Defaults
// ---------------------------------------------------------
$this->start_controls_section( 'section_slider_settings', [ 'label' => esc_html__( 'Layout & Alignment', 'adv-kinetic-slider' ) ] );

$this->add_control(
'align_note',
[
'type'            => \Elementor\Controls_Manager::RAW_HTML,
'raw'             => esc_html__( 'Defaults for all slides. Override per-slide in the Slides section → Layout tab.', 'adv-kinetic-slider' ),
'content_classes' => 'elementor-descriptor',
]
);

$this->add_responsive_control(
'default_content_position',
[
'label'     => esc_html__( 'Content Block Position', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::CHOOSE,
'options'   => [
'start'  => [ 'title' => 'Left', 'icon' => 'eicon-h-align-left' ],
'center' => [ 'title' => 'Center', 'icon' => 'eicon-h-align-center' ],
'end'    => [ 'title' => 'Right', 'icon' => 'eicon-h-align-right' ],
],
'default'   => 'start',
'selectors' => [ '{{WRAPPER}} .adv-slide-content' => 'justify-self: {{VALUE}};' ],
]
);

$this->add_responsive_control(
'default_text_align',
[
'label'     => esc_html__( 'Text Alignment', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::CHOOSE,
'options'   => [
'left'   => [ 'title' => 'Left', 'icon' => 'eicon-text-align-left' ],
'center' => [ 'title' => 'Center', 'icon' => 'eicon-text-align-center' ],
'right'  => [ 'title' => 'Right', 'icon' => 'eicon-text-align-right' ],
],
'default'   => 'left',
'selectors' => [ '{{WRAPPER}} .adv-slide-content' => 'text-align: {{VALUE}};' ],
]
);

$this->add_responsive_control(
'default_buttons_align',
[
'label'     => esc_html__( 'Buttons Alignment', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::CHOOSE,
'options'   => [
'flex-start' => [ 'title' => 'Left', 'icon' => 'eicon-text-align-left' ],
'center'     => [ 'title' => 'Center', 'icon' => 'eicon-text-align-center' ],
'flex-end'   => [ 'title' => 'Right', 'icon' => 'eicon-text-align-right' ],
],
'default'   => 'flex-start',
'selectors' => [ '{{WRAPPER}} .adv-slide-buttons' => 'justify-content: {{VALUE}};' ],
]
);

$this->add_responsive_control(
'default_overlay_position',
[
'label'     => esc_html__( 'Overlay Image Position', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::CHOOSE,
'options'   => [
'start'  => [ 'title' => 'Left', 'icon' => 'eicon-h-align-left' ],
'center' => [ 'title' => 'Center', 'icon' => 'eicon-h-align-center' ],
'end'    => [ 'title' => 'Right', 'icon' => 'eicon-h-align-right' ],
],
'default'   => 'end',
'selectors' => [ '{{WRAPPER}} .adv-slide-overlay-img' => 'justify-self: {{VALUE}};' ],
]
);

$this->add_responsive_control(
'default_overlay_vertical_position',
[
'label'     => esc_html__( 'Overlay Image Vertical Position', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::CHOOSE,
'options'   => [
'start'  => [ 'title' => 'Top', 'icon' => 'eicon-v-align-top' ],
'center' => [ 'title' => 'Middle', 'icon' => 'eicon-v-align-middle' ],
'end'    => [ 'title' => 'Bottom', 'icon' => 'eicon-v-align-bottom' ],
],
'default'   => 'center',
'selectors' => [ '{{WRAPPER}} .adv-slide-overlay-img' => 'align-self: {{VALUE}};' ],
]
);

$this->add_responsive_control(
'slider_height',
[
'label'      => esc_html__( 'Slider Height', 'adv-kinetic-slider' ),
'type'       => \Elementor\Controls_Manager::SLIDER,
'range'      => [
'px' => [ 'min' => 300, 'max' => 1200 ],
'vh' => [ 'min' => 10, 'max' => 100 ],
],
'size_units' => [ 'px', 'vh' ],
'selectors'  => [ '{{WRAPPER}} .adv-swiper .swiper-slide' => 'min-height: {{SIZE}}{{UNIT}};' ],
]
);

$this->add_responsive_control(
'content_padding',
[
'label'      => esc_html__( 'Content Padding', 'adv-kinetic-slider' ),
'type'       => \Elementor\Controls_Manager::DIMENSIONS,
'size_units' => [ 'px', '%', 'em' ],
'selectors'  => [ '{{WRAPPER}} .adv-slide-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
]
);

$this->add_control(
'heading_bg_fit_defaults',
[
'label'     => esc_html__( 'Background Image Fit (Global Default)', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::HEADING,
'separator' => 'before',
]
);

$this->add_control(
'bg_fit_note',
[
'type'            => \Elementor\Controls_Manager::RAW_HTML,
'raw'             => esc_html__( 'Applies to all slides unless overridden per-slide in the Slides section → Background tab.', 'adv-kinetic-slider' ),
'content_classes' => 'elementor-descriptor',
]
);

$this->add_control(
'default_bg_size',
[
'label'     => esc_html__( 'Background Fit', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::SELECT,
'default'   => 'cover',
'options'   => [
'cover'     => esc_html__( 'Cover (fill & crop)', 'adv-kinetic-slider' ),
'contain'   => esc_html__( 'Contain (fit inside, no crop)', 'adv-kinetic-slider' ),
'auto'      => esc_html__( 'Auto (original size)', 'adv-kinetic-slider' ),
'100% 100%' => esc_html__( 'Fill (stretch to fit)', 'adv-kinetic-slider' ),
],
'selectors' => [
'{{WRAPPER}} .adv-slide-bg' => 'background-size: {{VALUE}};',
],
]
);

$this->add_control(
'default_bg_position',
[
'label'     => esc_html__( 'Background Position', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::SELECT,
'default'   => 'center center',
'options'   => [
'left top'      => esc_html__( 'Left Top', 'adv-kinetic-slider' ),
'center top'    => esc_html__( 'Center Top', 'adv-kinetic-slider' ),
'right top'     => esc_html__( 'Right Top', 'adv-kinetic-slider' ),
'left center'   => esc_html__( 'Left Center', 'adv-kinetic-slider' ),
'center center' => esc_html__( 'Center Center', 'adv-kinetic-slider' ),
'right center'  => esc_html__( 'Right Center', 'adv-kinetic-slider' ),
'left bottom'   => esc_html__( 'Left Bottom', 'adv-kinetic-slider' ),
'center bottom' => esc_html__( 'Center Bottom', 'adv-kinetic-slider' ),
'right bottom'  => esc_html__( 'Right Bottom', 'adv-kinetic-slider' ),
],
'selectors' => [
'{{WRAPPER}} .adv-slide-bg' => 'background-position: {{VALUE}};',
],
]
);

$this->add_control(
'default_bg_repeat',
[
'label'     => esc_html__( 'Background Repeat', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::SELECT,
'default'   => 'no-repeat',
'options'   => [
'no-repeat' => esc_html__( 'No Repeat', 'adv-kinetic-slider' ),
'repeat'    => esc_html__( 'Repeat (tile)', 'adv-kinetic-slider' ),
'repeat-x'  => esc_html__( 'Repeat Horizontally', 'adv-kinetic-slider' ),
'repeat-y'  => esc_html__( 'Repeat Vertically', 'adv-kinetic-slider' ),
],
'selectors' => [
'{{WRAPPER}} .adv-slide-bg' => 'background-repeat: {{VALUE}};',
],
]
);

$this->end_controls_section();

// ---------------------------------------------------------
// SECTION: Transition & Autoplay
// ---------------------------------------------------------
$this->start_controls_section( 'section_transition', [ 'label' => esc_html__( 'Transition & Autoplay', 'adv-kinetic-slider' ) ] );

$this->add_control(
'effect',
[
'label'   => esc_html__( 'Slide Transition Effect', 'adv-kinetic-slider' ),
'type'    => \Elementor\Controls_Manager::SELECT,
'default' => 'slide',
'options' => [
'slide'     => 'Slide',
'fade'      => 'Cross-Fade',
'coverflow' => 'Coverflow',
],
]
);

$this->add_control( 'speed', [ 'label' => esc_html__( 'Transition Speed (ms)', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 800, 'min' => 100, 'max' => 3000 ] );
$this->add_control( 'loop', [ 'label' => esc_html__( 'Infinite Loop', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
$this->add_control( 'autoplay', [ 'label' => esc_html__( 'Autoplay', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
$this->add_control( 'autoplay_speed', [ 'label' => esc_html__( 'Autoplay Speed (ms)', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 5000, 'min' => 1000, 'condition' => [ 'autoplay' => 'yes' ] ] );
$this->add_control( 'pause_on_hover', [ 'label' => esc_html__( 'Pause on Hover', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => [ 'autoplay' => 'yes' ] ] );

$this->add_control(
'heading_kinetic_timing',
[ 'label' => esc_html__( 'Content Animation Timing', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::HEADING, 'separator' => 'before' ]
);

$this->add_control(
'kinetic_duration',
[
'label'     => esc_html__( 'Element Animation Duration (ms)', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::NUMBER,
'default'   => 900,
'min'       => 100,
'max'       => 3000,
'selectors' => [ '{{WRAPPER}} .adv-swiper' => '--adv-elem-duration: {{VALUE}}ms;' ],
]
);

$this->add_control(
'kinetic_stagger',
[
'label'       => esc_html__( 'Stagger Between Elements (ms)', 'adv-kinetic-slider' ),
'type'        => \Elementor\Controls_Manager::NUMBER,
'default'     => 150,
'min'         => 0,
'max'         => 800,
'description' => esc_html__( 'Delay added between subheading → heading → description → buttons → image.', 'adv-kinetic-slider' ),
]
);

$this->add_control(
'kenburns_duration',
[
'label'     => esc_html__( 'Ken Burns Duration (ms)', 'adv-kinetic-slider' ),
'type'      => \Elementor\Controls_Manager::NUMBER,
'default'   => 6500,
'min'       => 1000,
'max'       => 20000,
'selectors' => [ '{{WRAPPER}} .adv-swiper' => '--adv-kb-duration: {{VALUE}}ms;' ],
]
);

$this->end_controls_section();

// ---------------------------------------------------------
// SECTION: Navigation / UI Elements
// ---------------------------------------------------------
$this->start_controls_section( 'section_ui', [ 'label' => esc_html__( 'Navigation & UI', 'adv-kinetic-slider' ) ] );

$this->add_control( 'show_arrows', [ 'label' => esc_html__( 'Show Arrows', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
$this->add_control( 'show_pagination', [ 'label' => esc_html__( 'Show Pagination Dots', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes' ] );
$this->add_control( 'show_progress', [ 'label' => esc_html__( 'Show Autoplay Progress Bar', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => [ 'autoplay' => 'yes' ] ] );
$this->add_control( 'show_counter', [ 'label' => esc_html__( 'Show Slide Counter', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'no' ] );
$this->add_control( 'show_toggle', [ 'label' => esc_html__( 'Show Play/Pause Button', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'no', 'condition' => [ 'autoplay' => 'yes' ] ] );

$this->end_controls_section();

/* =========================================================
 * STYLE TAB
 * ========================================================= */

$this->start_controls_section( 'section_style_typography', [ 'label' => esc_html__( 'Typography & Colors', 'adv-kinetic-slider' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );

$this->add_control( 'sub_heading_color', [ 'label' => esc_html__( 'Subheading Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .adv-slide-subheading' => 'color: {{VALUE}};' ] ] );
$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'sub_heading_typography', 'label' => 'Subheading Typography', 'selector' => '{{WRAPPER}} .adv-slide-subheading' ] );

$this->add_control( 'hr1', [ 'type' => \Elementor\Controls_Manager::DIVIDER ] );

$this->add_control( 'heading_color', [ 'label' => esc_html__( 'Heading Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .adv-slide-heading' => 'color: {{VALUE}};' ] ] );
$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'heading_typography', 'label' => 'Heading Typography', 'selector' => '{{WRAPPER}} .adv-slide-heading' ] );

$this->add_control( 'hr2', [ 'type' => \Elementor\Controls_Manager::DIVIDER ] );

$this->add_control( 'desc_color', [ 'label' => esc_html__( 'Description Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .adv-slide-desc' => 'color: {{VALUE}};' ] ] );
$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'desc_typography', 'label' => 'Description Typography', 'selector' => '{{WRAPPER}} .adv-slide-desc' ] );

$this->end_controls_section();

// --- Buttons styling (unchanged pattern from v1, kept intact) ---
$this->start_controls_section( 'section_style_buttons', [ 'label' => esc_html__( 'Buttons Styling', 'adv-kinetic-slider' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );

$this->add_control( 'heading_btn_1_style', [ 'label' => esc_html__( 'Button 1', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::HEADING ] );
$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'btn_1_typography', 'selector' => '{{WRAPPER}} .adv-btn.btn-1' ] );

$this->start_controls_tabs( 'tabs_btn_1_colors' );

$this->start_controls_tab( 'tab_btn_1_normal', [ 'label' => esc_html__( 'Normal', 'adv-kinetic-slider' ) ] );
$this->add_control( 'btn_1_color', [ 'label' => esc_html__( 'Text Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .adv-btn.btn-1' => 'color: {{VALUE}};' ] ] );
$this->add_control( 'btn_1_bg', [ 'label' => esc_html__( 'Background Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .adv-btn.btn-1' => 'background-color: {{VALUE}};' ] ] );
$this->add_control( 'btn_1_border_color', [ 'label' => esc_html__( 'Border Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .adv-btn.btn-1' => 'border-color: {{VALUE}}; border-style: solid; border-width: 2px;' ] ] );
$this->end_controls_tab();

$this->start_controls_tab( 'tab_btn_1_hover', [ 'label' => esc_html__( 'Hover', 'adv-kinetic-slider' ) ] );
$this->add_control( 'btn_1_color_hover', [ 'label' => esc_html__( 'Text Hover Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .adv-btn.btn-1:hover' => 'color: {{VALUE}};' ] ] );
$this->add_control( 'btn_1_bg_hover', [ 'label' => esc_html__( 'Background Hover Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .adv-btn.btn-1:hover' => 'background-color: {{VALUE}};' ] ] );
$this->add_control( 'btn_1_border_hover', [ 'label' => esc_html__( 'Border Hover Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .adv-btn.btn-1:hover' => 'border-color: {{VALUE}};' ] ] );
$this->end_controls_tab();

$this->end_controls_tabs();

$this->add_control( 'heading_btn_2_style', [ 'label' => esc_html__( 'Button 2', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::HEADING, 'separator' => 'before' ] );
$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'btn_2_typography', 'selector' => '{{WRAPPER}} .adv-btn.btn-2' ] );

$this->start_controls_tabs( 'tabs_btn_2_colors' );

$this->start_controls_tab( 'tab_btn_2_normal', [ 'label' => esc_html__( 'Normal', 'adv-kinetic-slider' ) ] );
$this->add_control( 'btn_2_color', [ 'label' => esc_html__( 'Text Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .adv-btn.btn-2' => 'color: {{VALUE}};' ] ] );
$this->add_control( 'btn_2_bg', [ 'label' => esc_html__( 'Background Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .adv-btn.btn-2' => 'background-color: {{VALUE}};' ] ] );
$this->add_control( 'btn_2_border_color', [ 'label' => esc_html__( 'Border Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .adv-btn.btn-2' => 'border-color: {{VALUE}}; border-style: solid; border-width: 2px;' ] ] );
$this->end_controls_tab();

$this->start_controls_tab( 'tab_btn_2_hover', [ 'label' => esc_html__( 'Hover', 'adv-kinetic-slider' ) ] );
$this->add_control( 'btn_2_color_hover', [ 'label' => esc_html__( 'Text Hover Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .adv-btn.btn-2:hover' => 'color: {{VALUE}};' ] ] );
$this->add_control( 'btn_2_bg_hover', [ 'label' => esc_html__( 'Background Hover Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .adv-btn.btn-2:hover' => 'background-color: {{VALUE}};' ] ] );
$this->add_control( 'btn_2_border_hover', [ 'label' => esc_html__( 'Border Hover Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .adv-btn.btn-2:hover' => 'border-color: {{VALUE}};' ] ] );
$this->end_controls_tab();

$this->end_controls_tabs();

$this->add_responsive_control( 'btn_border_radius', [ 'label' => esc_html__( 'Border Radius', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%', 'em' ], 'selectors' => [ '{{WRAPPER}} .adv-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ], 'separator' => 'before' ] );
$this->add_responsive_control( 'btn_padding', [ 'label' => esc_html__( 'Padding', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', '%', 'em' ], 'selectors' => [ '{{WRAPPER}} .adv-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );

$this->end_controls_section();

// --- UI element styling (arrows, dots, progress bar, counter) ---
$this->start_controls_section( 'section_style_ui', [ 'label' => esc_html__( 'Navigation & UI Styling', 'adv-kinetic-slider' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );

$this->add_control( 'arrow_color', [ 'label' => esc_html__( 'Arrow Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'color: {{VALUE}};' ], 'condition' => [ 'show_arrows' => 'yes' ] ] );
$this->add_control( 'dot_color', [ 'label' => esc_html__( 'Pagination Dot Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .swiper-pagination-bullet' => 'background: {{VALUE}};' ], 'condition' => [ 'show_pagination' => 'yes' ] ] );
$this->add_control( 'dot_active_color', [ 'label' => esc_html__( 'Active Dot Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .swiper-pagination-bullet-active' => 'background: {{VALUE}};' ], 'condition' => [ 'show_pagination' => 'yes' ] ] );
$this->add_control( 'progress_color', [ 'label' => esc_html__( 'Progress Bar Color', 'adv-kinetic-slider' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .adv-slider-progress-bar' => 'background: {{VALUE}};' ], 'condition' => [ 'show_progress' => 'yes' ] ] );

$this->end_controls_section();
}

/**
 * Resolves the alt text for the overlay image, stripping any markup
 * that may have been entered in the (wp_kses_post-allowed) heading field.
 */
private function resolve_alt_text( $slide ) {
if ( ! empty( $slide['overlay_alt'] ) ) {
return $slide['overlay_alt'];
}
if ( ! empty( $slide['kinetic_heading'] ) ) {
return wp_strip_all_tags( $slide['kinetic_heading'] );
}
return '';
}

protected function render() {
$settings = $this->get_settings_for_display();

if ( empty( $settings['slides_list'] ) ) {
return;
}

$stagger_ms  = absint( $settings['kinetic_stagger'] );
$swiper_opts = [
'effect'        => $settings['effect'],
'speed'         => absint( $settings['speed'] ),
'loop'          => ( 'yes' === $settings['loop'] ),
'navigation'    => ( 'yes' === $settings['show_arrows'] ),
'pagination'    => ( 'yes' === $settings['show_pagination'] ),
'autoplay'      => ( 'yes' === $settings['autoplay'] ),
'autoplaySpeed' => absint( $settings['autoplay_speed'] ),
'pauseOnHover'  => ( 'yes' === $settings['pause_on_hover'] ),
];

$wrapper_style = sprintf(
'--adv-stagger-1:%1$dms;--adv-stagger-2:%2$dms;--adv-stagger-3:%3$dms;--adv-stagger-4:%4$dms;--adv-stagger-5:%5$dms;--adv-stagger-6:%6$dms;',
$stagger_ms * 1,
$stagger_ms * 2,
$stagger_ms * 3,
$stagger_ms * 4,
$stagger_ms * 5,
$stagger_ms * 6
);
?>
<div class="adv-slider-wrapper" style="<?php echo esc_attr( $wrapper_style ); ?>" data-swiper-settings="<?php echo esc_attr( wp_json_encode( $swiper_opts ) ); ?>" role="region" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Slider', 'adv-kinetic-slider' ); ?>">
<div class="swiper adv-swiper">
<div class="swiper-wrapper">

<?php foreach ( $settings['slides_list'] as $index => $slide ) :

$is_video   = ( 'video' === ( $slide['bg_type'] ?? 'image' ) ) && ! empty( $slide['bg_video_mp4']['url'] );
$bg_img     = ! empty( $slide['slide_bg']['url'] ) ? esc_url( $slide['slide_bg']['url'] ) : '';
$poster_img = ! empty( $slide['bg_video_poster']['url'] ) ? esc_url( $slide['bg_video_poster']['url'] ) : $bg_img;
$overlay_img = ! empty( $slide['overlay_image']['url'] ) ? esc_url( $slide['overlay_image']['url'] ) : '';

$btn1_key    = 'btn1_' . $slide['_id'];
$btn2_key    = 'btn2_' . $slide['_id'];

if ( ! empty( $slide['button_1_link']['url'] ) ) {
$this->add_link_attributes( $btn1_key, $slide['button_1_link'] );
}
if ( ! empty( $slide['button_2_link']['url'] ) ) {
$this->add_link_attributes( $btn2_key, $slide['button_2_link'] );
}

$slide_class = 'swiper-slide adv-slide';
if ( ! $is_video && ! empty( $slide['kenburns_effect'] ) && 'none' !== $slide['kenburns_effect'] ) {
$slide_class .= ' adv-kenburns adv-kenburns-' . sanitize_html_class( $slide['kenburns_effect'] );
}
if ( ! empty( $slide['custom_class'] ) ) {
$slide_class .= ' ' . esc_attr( $slide['custom_class'] );
}

$heading_tag = ! empty( $slide['heading_tag'] ) ? sanitize_html_class( $slide['heading_tag'] ) : 'h2';
$alt_text    = $this->resolve_alt_text( $slide );

// v2.1 LCP fix: the background is now a real <img>, so the LCP request
// can carry fetchpriority="high" (a CSS background image never can).
// Per-slide fit/position overrides are applied inline (custom-rendered
// repeater fields can't reliably target {{CURRENT_ITEM}} in Elementor's
// selector system) and fall back to the widget's global default controls.
$object_fit_map = [
'cover'     => 'cover',
'contain'   => 'contain',
'auto'      => 'none',
'100% 100%' => 'fill',
];
$bg_size_raw  = ! empty( $slide['bg_size'] ) ? $slide['bg_size'] : $settings['default_bg_size'];
$bg_pos_raw   = ! empty( $slide['bg_position'] ) ? $slide['bg_position'] : $settings['default_bg_position'];
$object_fit   = isset( $object_fit_map[ $bg_size_raw ] ) ? $object_fit_map[ $bg_size_raw ] : 'cover';
$object_pos   = ! empty( $bg_pos_raw ) ? $bg_pos_raw : 'center center';
$bg_fit_style = 'object-fit: ' . $object_fit . '; object-position: ' . $object_pos . ';';
?>

<div class="<?php echo esc_attr( $slide_class ); ?>" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( sprintf( '%1$d / %2$d', $index + 1, count( $settings['slides_list'] ) ) ); ?>">

<?php if ( $is_video ) : ?>
<video
class="adv-slide-bg-video"
muted
loop
playsinline
preload="metadata"
<?php echo $poster_img ? 'poster="' . esc_url( $poster_img ) . '"' : ''; ?>
>
<source src="<?php echo esc_url( $slide['bg_video_mp4']['url'] ); ?>" type="video/mp4">
</video>
<?php elseif ( $bg_img ) : ?>
<img
class="adv-slide-bg"
src="<?php echo esc_url( $bg_img ); ?>"
alt="<?php echo esc_attr( $alt_text ); ?>"
<?php if ( 0 === $index ) : ?>
fetchpriority="high"
loading="eager"
data-no-lazy="1"
<?php else : ?>
fetchpriority="low"
loading="lazy"
<?php endif; ?>
decoding="async"
style="<?php echo esc_attr( $bg_fit_style ); ?>"
>
<?php endif; ?>

<div class="adv-slide-overlay-tint"></div>

<div class="adv-slide-inner">
<div class="adv-slide-content">

<?php if ( ! empty( $slide['sub_heading'] ) ) : ?>
<span class="adv-slide-subheading kinetic-elem delay-1 <?php echo esc_attr( $slide['anim_sub_heading'] ); ?>">
<?php echo esc_html( $slide['sub_heading'] ); ?>
</span>
<?php endif; ?>

<?php if ( ! empty( $slide['kinetic_heading'] ) ) : ?>
<<?php echo esc_attr( $heading_tag ); ?> class="adv-slide-heading kinetic-elem delay-2 <?php echo esc_attr( $slide['anim_heading'] ); ?>">
<?php echo wp_kses_post( $slide['kinetic_heading'] ); ?>
</<?php echo esc_attr( $heading_tag ); ?>>
<?php endif; ?>

<?php if ( ! empty( $slide['description'] ) ) : ?>
<div class="adv-slide-desc kinetic-elem delay-3 <?php echo esc_attr( $slide['anim_description'] ); ?>">
<?php echo wp_kses_post( $slide['description'] ); ?>
</div>
<?php endif; ?>

<?php if ( ! empty( $slide['button_1_text'] ) || ! empty( $slide['button_2_text'] ) ) : ?>
<div class="adv-slide-buttons kinetic-elem delay-4 <?php echo esc_attr( $slide['anim_buttons'] ); ?>">

<?php if ( ! empty( $slide['button_1_text'] ) ) : ?>
<a class="adv-btn btn-1" <?php echo ! empty( $slide['button_1_link']['url'] ) ? $this->get_render_attribute_string( $btn1_key ) : 'href="#"'; ?>>
<?php if ( ! empty( $slide['button_1_icon'] ) && is_array( $slide['button_1_icon'] ) && ! empty( $slide['button_1_icon']['value'] ) ) : ?>
<?php \Elementor\Icons_Manager::render_icon( $slide['button_1_icon'], [ 'aria-hidden' => 'true' ] ); ?>
<?php endif; ?>
<?php echo esc_html( $slide['button_1_text'] ); ?>
</a>
<?php endif; ?>

<?php if ( ! empty( $slide['button_2_text'] ) ) : ?>
<a class="adv-btn btn-2" <?php echo ! empty( $slide['button_2_link']['url'] ) ? $this->get_render_attribute_string( $btn2_key ) : 'href="#"'; ?>>
<?php if ( ! empty( $slide['button_2_icon'] ) && is_array( $slide['button_2_icon'] ) && ! empty( $slide['button_2_icon']['value'] ) ) : ?>
<?php \Elementor\Icons_Manager::render_icon( $slide['button_2_icon'], [ 'aria-hidden' => 'true' ] ); ?>
<?php endif; ?>
<?php echo esc_html( $slide['button_2_text'] ); ?>
</a>
<?php endif; ?>

</div>
<?php endif; ?>

</div>

<?php if ( $overlay_img ) : ?>
<div class="adv-slide-overlay-img kinetic-elem delay-5 <?php echo esc_attr( $slide['anim_overlay'] ); ?>">
<img src="<?php echo esc_url( $overlay_img ); ?>" alt="<?php echo esc_attr( $alt_text ); ?>" loading="<?php echo 0 === $index ? 'eager' : 'lazy'; ?>" />
</div>
<?php endif; ?>

</div>
</div>

<?php endforeach; ?>

</div>

<?php if ( 'yes' === $settings['show_pagination'] ) : ?>
<div class="swiper-pagination"></div>
<?php endif; ?>

<?php if ( 'yes' === $settings['show_arrows'] ) : ?>
<div class="swiper-button-prev" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Previous slide', 'adv-kinetic-slider' ); ?>"></div>
<div class="swiper-button-next" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Next slide', 'adv-kinetic-slider' ); ?>"></div>
<?php endif; ?>

<?php if ( 'yes' === $settings['autoplay'] && 'yes' === $settings['show_progress'] ) : ?>
<div class="adv-slider-progress"><div class="adv-slider-progress-bar"></div></div>
<?php endif; ?>

<?php if ( 'yes' === $settings['show_counter'] ) : ?>
<div class="adv-slider-counter">
<span class="current">01</span><span class="sep">/</span><span class="total">0<?php echo count( $settings['slides_list'] ); ?></span>
</div>
<?php endif; ?>

<?php if ( 'yes' === $settings['autoplay'] && 'yes' === $settings['show_toggle'] ) : ?>
<button type="button" class="adv-slider-toggle" data-state="playing" aria-label="<?php esc_attr_e( 'Toggle autoplay', 'adv-kinetic-slider' ); ?>">
<svg viewBox="0 0 24 24"><path d="M6 5h4v14H6zM14 5h4v14h-4z"/></svg>
</button>
<?php endif; ?>

</div>
</div>
<?php
}
}