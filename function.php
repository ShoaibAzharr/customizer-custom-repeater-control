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

	
	// footer
	$wp_customize->add_section( 'wpb_footer' , array(
		'title'             => __('Footer', 'wpb_edu'),
		'panel'             => 'wpb_footer_panel',
	) );

	$wp_customize->add_setting( 'wpb_footer_partners', array(
		'sanitize_callback' => 'wpb_repeater_sanitize'
	) );

	$wp_customize->add_control( 
		new WPB_Repeater_Control( 
			$wp_customize, 'wpb_footer_partners', array(
				'label'             => esc_html__( 'Partners','wpb_edu' ),
				'singular'          => esc_html__( 'Partner','wpb_edu' ),
				'section'           => 'wpb_footer',
				'priority'          => 6,
				'wpb_link_control'  => true,
				'link_label'		=> 'Site Link',
				'wpb_image_control' => true,
				'image_label'		=> 'Logo',
				'wpb_text_control'  => true,
				'text_label'		=> 'Alt Text',
				'text_domain'       => 'text_domain_here',
				'id'                => 0
			)
		)
	);

}
add_action( 'customize_register', 'wp_customizer_controls', 19 );