<?php

/**
 * Adds effects on hover
 */
class Hover_Effects_Widget extends WP_Widget
{
  /** 
   *    Set up the widget name and description.
   */
  public function __construct() {
    
    $widget_options = array( 
      'classname'   => 'Hover_Effects_Widget', 
      'description' => __('A Widget for hover effects', 'hew'),
    );

    parent::__construct( 'Hover_Effects_Widget', 'Hover Effects Widget', $widget_options );
  }

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
  public function widget( $args, $instance ) {
		$title			  = apply_filters( 'wp_editor_widget_title', $instance['title'] );
		$content		  = apply_filters( 'wp_editor_widget_content', $instance['content'] );
		$content_back	= apply_filters( 'wp_editor_widget_content', $instance['content_back'] );
		
		$show = true;
		
		// WPML support?
		if ( function_exists( 'icl_get_languages' ) ) {
			$language = apply_filters( 'wp_editor_widget_language', $instance['language'] );
			$show = ($language == icl_get_current_language());
		}
		
		if ( $show ) {
	
			$default_html = $args['before_widget'];
	
			if ( '1' == $output_title && ! empty( $title ) ) {
				$default_html .= $args['before_title'] . $title . $args['after_title'];
			}
  
      $default_html .= '<div class="hew_container">';
      $default_html .= '<div class="hew_content">';
			$default_html .= $content;
      $default_html .= '</div>';
      $default_html .= '<div class="hew_overlay">';
      $default_html .= '<div class="hew_content_back">';
			$default_html .= $content_back;
      $default_html .= '</div>';
      $default_html .= '</div>';
      $default_html .= '</div>';
    
			$default_html .= $args['after_widget'];
			
			echo apply_filters( 'wp_editor_widget_html', $default_html, $args['id'], $instance, $args['before_widget'], $args['after_widget'], $output_title, $title, $args['before_title'], $args['after_title'], $content );
    }
  } // END widget()
  
	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */

  public function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		}
		else {
			$title = __( 'New title', 'wp-editor-widget' );
		}

		if ( isset( $instance['content'] ) ) {
			$content = $instance['content'];
		}
		else {
			$content = '';
		}

    if ( isset( $instance['content_back'])) {
      $content_back = $instance['content_back'];
    } else {
      $content_back = '';
    }

		$output_title = ( isset( $instance['output_title'] ) && '1' == $instance['output_title'] ? true : false );
		?>
		<input type="hidden" id="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content' ) ); ?>" value="<?php echo esc_attr( $content ); ?>">
		<input type="hidden" id="<?php echo esc_attr( $this->get_field_id( 'content_back' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content_back' ) ); ?>" value="<?php echo esc_attr( $content_back ); ?>">
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'wp-editor-widget' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<a href="javascript:WPEditorWidget.showEditor('<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>');" class="button"><?php _e( 'Edit content', 'wp-editor-widget' ) ?></a>
		</p>
		<p>
			<a href="javascript:WPEditorWidget.showEditor('<?php echo esc_attr( $this->get_field_id( 'content_back' ) ); ?>');" class="button"><?php _e( 'Edit content for background', 'wp-editor-widget' ) ?></a>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'output_title' ) ); ?>">
				<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'output_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'output_title' ) ); ?>" value="1" <?php checked( $output_title, true ) ?>> <?php _e( 'Output title', 'wp-editor-widget' ); ?>
			</label>
		</p>
		<?php if ( function_exists( 'icl_get_languages' ) ) : $languages = icl_get_languages( 'skip_missing=0&orderby=code' ); ?>
			<label for="<?php echo esc_attr( $this->get_field_id( 'language' ) ); ?>">
				<?php _e( 'Language', 'wp-editor-widget' ); ?>:
				<select id="<?php echo esc_attr( $this->get_field_id( 'language' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'language' ) ); ?>">
					<?php foreach ( $languages as $id => $lang ) : ?>
						<option value="<?php echo esc_attr( $lang['language_code'] ) ?>" <?php selected( $instance['language'], $lang['language_code'] ) ?>><?php echo esc_attr( $lang['native_name'] ) ?></option>
					<?php endforeach; ?>
				</select>
			</label>
		<?php endif; ?>
		<?php
			
		do_action( 'wp_editor_widget_form', $this, $instance );
  } // END form()

  	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
  public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title']			= ( ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '' );
		$instance['content']		= ( ! empty( $new_instance['content'] ) ? $new_instance['content'] : '' );
		$instance['content_back']	= ( ! empty( $new_instance['content_back'] ) ? $new_instance['content_back'] : '' );
		$instance['output_title']	= ( isset( $new_instance['output_title'] ) && '1' == $new_instance['output_title'] ? 1 : 0 );
		
		// WPML support
		if ( function_exists( 'icl_get_languages' )  ) {
			$instance['language']   = ( isset( $new_instance['language'] ) ? $new_instance['language'] : '');
		}

		do_action( 'wp_editor_widget_update', $new_instance, $instance );

 	 	return apply_filters( 'wp_editor_widget_update_instance', $instance, $new_instance );
  } // END update()

} // END Hover_Effects_Widget