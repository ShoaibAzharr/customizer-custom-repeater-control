<?php
/**
 * Repeater sanitization callback.
 *
 * @param string  $input   String to sanitize.
 * @return string Sanitized
 */
function ace_repeater_sanitize( $input ) {

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

	require 'ace/classes/class-repeater-control.php' ;

	$wp_customize->add_panel( 'ace_footer_panel', array(
		'priority'          => 23,
		'capability'        => 'edit_theme_options',
		'theme_supports'    => '',
		'title'             => esc_html__( 'Section: Footer', 'ace_edu' ),
		'description'       => esc_html__( 'This panel allows you to customize Footer areas of the Theme.', 'ace_edu' ),
	) );


	$wp_customize->add_section( 'ace_footer' , array(
		'title'     => esc_html__( 'ACE Footer Logos', 'ace_edu' ),
		'priority'  => 10,
		'panel'     => 'ace_footer_panel',
	) );
	$wp_customize->add_setting( 'ace_footer_logos', array(
		'sanitize_callback' => 'ace_repeater_sanitize'
	) );

	$wp_customize->add_control( new ACE_Repeater_Control( $wp_customize, 'ace_footer_logos', array(
		'label'             => esc_html__( 'Logos','ace_edu' ),
		'section'           => 'ace_footer',
		'priority'          => 15,
	  	'ace_link_control'  => true,
		'ace_image_control' => true,
	) ) );

}
add_action( 'customize_register', 'wp_customizer_controls', 19 );