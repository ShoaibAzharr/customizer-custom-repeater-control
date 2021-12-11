<?php
if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return null;
}

/**
 * Custom control class for customizer to repeat fields.
 */
class ACE_Repeater_Control extends WP_Customize_Control {

/**
 * Declaring data members for class.
 */
	public $id;
	private $boxtitle = array();
	private $ace_image_control = false;
	private $ace_text_control = false;
	private $ace_link_control = false;

	/**
	 * Constructor for class.
	 * @param $manager
	 * @param int $id
	 * @param array  $args
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		/*Get options from customizer.php*/
		$this->boxtitle   = esc_html__( 'Cusomizer Repeater', 'ace_edu' );
		if ( ! empty( $this->label ) ) {
			$this->boxtitle = $this->label;
		}

		if ( ! empty( $args['ace_image_control'] ) ) {
			$this->ace_image_control = $args['ace_image_control'];
		}

		if ( ! empty( $args['ace_text_control'] ) ) {
			$this->ace_text_control = $args['ace_text_control'];
		}

		if ( ! empty( $args['ace_link_control'] ) ) {
			$this->ace_link_control = $args['ace_link_control'];
		}
		if ( ! empty( $args['id'] ) ) {
			$this->id = $args['id'];
		}

	}

/**
 * Enqueuing scripts and stylesheets.
 */
	public function enqueue() {


		wp_enqueue_style( 'ace-repeater-admin-stylesheet',  'http://dev.pro/wp-content/themes/twentytwentyone/ace/inc/admin-style.css', array() );

		wp_enqueue_script( 'ace-repeater-script',  'http://dev.pro/wp-content/themes/twentytwentyone/ace/inc/customizer_repeater.js', array( 'jquery', 'jquery-ui-draggable' ), true );

	}

/**
 * Rendering the content.
 */
	public function render_content() {

		// Get default options.
		$this_default = json_decode( $this->setting->default );

		// Get values in json format.
		$values = $this->value();
		// Decode values.
		$json = json_decode( $values );

		if ( ! is_array( $json ) ) {
			$json = array( $values );
		} ?>

		<span class="ace-control-title"><?php echo esc_html( $this->label ); ?></span>
		<div class="ace-repeater-general-control-repeater customizer-repeater-general-control-droppable">
			<?php
			if ( ( count( $json ) == 1 && '' === $json[0] ) || empty( $json ) ) {
				if ( ! empty( $this_default ) ) {
					$this->iterate_array( $this_default );
					?>
					<input type="hidden"
						   id="customizer-repeater-<?php echo esc_html( $this->id ); ?>-collector" <?php $this->link(); ?>
						   class="ace-repeater-collector"
						   value="<?php echo esc_textarea( json_encode( $this_default ) ); ?>"/>
					<?php
				} else {
					$this->iterate_array();
					?>
					<input type="hidden" id="customizer-repeater-<?php echo esc_html( $this->id ); ?>-collector" <?php $this->link(); ?> class="ace-repeater-collector"/>
					<?php
				}
			} else {
				$this->iterate_array( $json );
				?>
				<input type="hidden" id="customizer-repeater-<?php echo esc_html( $this->id ); ?>-collector" <?php $this->link(); ?> class="ace-repeater-collector" value="<?php echo esc_textarea( $this->value() ); ?>"/>
				<?php
			}
			?>
			</div>
		<button type="button" class="button add_field customizer-repeater-new-field" value="1">
			<?php echo esc_html( 'Add Logo' ); ?>
		</button>
		<?php
		}

/**
 * Iterating array for repeating input fields.
 * @param  array  $array
 */
	private function iterate_array( $array = array() ) {
		/*Counter that helps checking if the box is first and should have the delete button disabled*/
		$it = 0;
		if ( ! empty( $array ) ) {
			foreach ( $array as $box ) {
				?>
				<div class="ace-repeater-general-control-repeater-container customizer-repeater-draggable">
					<div class="customizer-repeater-customize-control-title">
						<?php echo esc_html( $this->boxtitle ); ?>
					</div>
					<div class="ace-repeater-box-content-hidden">
						<?php
						$choice = $image_url = $text = $link = '';
						if ( ! empty( $box->choice ) ) {
							$choice = $box->choice;
						}
						if ( ! empty( $box->image_url ) ) {
							$image_url = $box->image_url;
						}
						if ( ! empty( $box->text ) ) {
							$text = $box->text;
						}
						if ( ! empty( $box->link ) ) {
							$link = $box->link;
						}
						if ( $this->ace_image_control == true ) {
							$this->image_control( $image_url, $choice );
						}
						if ( $this->ace_text_control == true ) {
							$this->input_control(
								array(
									'label' => 'text',
									'class' => 'ace-repeater-text-control',
									'type'  => 'textarea',
									'sanitize_callback' => 'wp_kses_post'
								), $text
							);
						}
						if ( $this->ace_link_control ) {
							$this->input_control(
								array(
									'label' => 'Link',
									'class' => 'ace-repeater-link-control',
									'sanitize_callback' => 'esc_url_raw',
								), $link
							);
						}
						?>

						<input type="hidden" class="logo-repeater-box-id" value=" <?php if ( ! empty( $this->id ) ) { echo esc_attr( $this->id ); } ?> ">
						<button type="button" class="logo-repeater-general-control-remove-field button"
						<?php
						if ( $it == 0 ) {
							echo 'style="display:none;"';
						}
						?>
						>
							<?php esc_html_e( 'Delete field', 'ace_edu' ); ?>
						</button>

					</div>
				</div>

				<?php
				$it++;
			}
		} else {
		?>
			<div class="ace-repeater-general-control-repeater-container">
				<div class="customizer-repeater-customize-control-title">
					<?php echo esc_html( $this->boxtitle ); ?>
				</div>
				<div class="ace-repeater-box-content-hidden">
					<?php
					if ( $this->ace_image_control == true ) {
						$this->image_control();
					}
					if ( $this->ace_text_control == true ) {
						$this->input_control(
							array(
								'label' => 'Text',
								'class' => 'ace-repeater-text-control',
								'type'  => 'textarea',
								'sanitize_callback' => 'wp_kses_post'
							)
						);
					}
					if ( $this->ace_link_control == true ) {
						$this->input_control(
							array(
								'label' => 'Image Link',
								'class' => 'ace-repeater-link-control',
								'sanitize_callback' => 'esc_url_raw',
							)
						);
					}
					?>
					<input type="hidden" class="logo-repeater-box-id">
					<button type="button" class="logo-repeater-general-control-remove-field button" style="display:none;">
						<?php esc_html_e( 'Delete field', 'ace_edu' ); ?>
					</button>
				</div>
			</div>
			<?php
		}
	}

/**
 * Input control for repeater field.
 * @param  array $options
 * @param  string $value
 */
	private function input_control( $options, $value = '' ) {
	?>
	<div id="<?php echo str_replace( " ", "-", strtolower($options['label'] ) ) ?>">
		<span class="ace-control-title"><?php echo esc_html( $options['label'] ); ?></span>
		<?php
		if ( ! empty( $options['type'] ) && $options['type'] === 'textarea' ) :
		?>
			<textarea class="<?php echo esc_attr( $options['class'] ); ?>" placeholder="<?php echo esc_attr( $options['label'] ); ?>"><?php echo ( ! empty( $options['sanitize_callback'] ) ? call_user_func_array( $options['sanitize_callback'], array( $value ) ) : esc_attr( $value ) ); ?></textarea>
			<?php
		else :
		?>
			<input type="text" value="<?php echo ( ! empty( $options['sanitize_callback'] ) ? call_user_func_array( $options['sanitize_callback'], array( $value ) ) : esc_attr( $value ) ); ?>" class="<?php echo esc_attr( $options['class'] ); ?>" placeholder="<?php echo $options['label']; ?>"/>
			<?php
		endif; ?>

	</div> <?php
	}

/**
 * Image control for repeater field.
 * @param  string $value
 * @param  string $show
 */
	private function image_control( $value = '', $show = '' ) {
	?>
		<div class="ace-repeater-image-control" <?php if ( $show === 'customizer_repeater_icon' || $show === 'customizer_repeater_none' ) { echo 'style="display:none;"'; } ?> >
			<span class="ace-control-title">
				<?php esc_html_e( 'Image', 'ace_edu' ); ?>
			</span>
			<input type="text" class="widefat custom-media-url" value="<?php echo esc_attr( $value ); ?>">
			<img src="<?php echo esc_attr( $value ); ?>" class="custom-media-box">
			<input type="button" class="button button-primary customizer-repeater-custom-media-button" value="<?php esc_html_e( 'Upload Image', 'ace_edu' ); ?>" />
		</div>
		<?php
	}


}
