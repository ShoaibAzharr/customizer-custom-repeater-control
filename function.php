<?php
/**
 * Repeater sanitization callback.
 *
 * @param string  $input   String to sanitize.
 * @return string Sanitized
 */
function wpb_repeater_sanitize( $input ) {

	$input_decoded = json_decode( $input, true );

	if ( ! empty( $input_decoded ) ) {
		foreach ( $input_decoded as $boxk => $box ) {
			foreach ( $box as $key => $value ) {

					$input_decoded[ $boxk ][ $key ] = wp_kses_post( force_balance_tags( $value ) );

			}
		}
		return json_encode( $input_decoded );
	}
	return $input;
}


function wp_customizer_controls( $wp_customize ) {

	require get_template_directory(). '/classes/class-wpb-customizer-repeater-control.php' ;

	$wp_customize->add_panel( 'wpb_footer_panel', array(
		'priority'          => 23,
		'capability'        => 'edit_theme_options',
		'theme_supports'    => '',
		'title'             => esc_html__( 'Section: Footer', 'wpb_text_domain' ),
		'description'       => esc_html__( 'This panel allows you to customize Footer areas of the Theme.', 'wpb_text_domain' ),
	) );

	$wp_customize->add_section( 'wpb_footer' , array(
		'title'     => esc_html__( 'ACE Footer Logos', 'wpb_text_domain' ),
		'priority'  => 10,
		'panel'     => 'wpb_footer_panel',
	) );
	$wp_customize->add_setting( 'wpb_footer_logos', array(
		'sanitize_callback' => 'wpb_repeater_sanitize'
	) );

	$wp_customize->add_control( new WPB_Repeater_Control( $wp_customize, 'wpb_footer_logos', array(
		'label'             => esc_html__( 'Logos','wpb_text_domain' ),
		'singular'          => esc_html__( 'Logo','wpb_text_domain' ),
		'section'           => 'wpb_footer',
		'priority'          => 15,
	  	'wpb_link_control'  => true,
		'wpb_image_control' => true,
		'wpb_text_control'  => true,
	) ) );

}
add_action( 'customize_register', 'wp_customizer_controls', 19 );