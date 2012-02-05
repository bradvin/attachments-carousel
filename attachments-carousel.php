<?php
  /*
   Plugin Name: Attachments Carousel
   Description: Shortcode to show an image carousel containing attachments of the current post
   Version: 0.1
   Author: bradvin
   Author URI: http://themergency.com
  */

  /*  Copyright 2012 Brad Vincent (email : bradvin@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  */

class AttachmentsCarousel {
	
	/*--------------------------------------------*
	 * Constants
	 *--------------------------------------------*/
	const name = 'Attachments Carousel';
	const slug = 'attachments-carousel';
	 
	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/
	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {
    	// Load JavaScript and stylesheets
      add_action( 'init', array( &$this, 'register_scripts_and_styles') );
		
      add_shortcode( 'attachments-carousel', array( &$this, 'render_carousel' ) );
	} // end constructor
  
  function render_carousel($atts) {
    global $post;
    global $attachments_carousel_count;
    
    if (empty($attachments_carousel_count)) {
      $attachments_carousel_count = 0;
    }
    
    $attachments_carousel_count++;
    
    $post_id = $post->ID;
    
    // Extract the attributes
    extract(shortcode_atts(array(
      'orderby' => 'rand',
      'size' => 'medium',
      'number' => -1,  //all
      'target' => '_blank'
    ), $atts));
    
		$html = '<!-- no attachment images found -->';
    
    $args = array(
      'post_type' => 'attachment',
      'numberposts' => $number,
      'post_status' => null,
      'post_parent' => $post->ID
    );

    //get all attachments for the current post
    $attachments = get_posts( $args );
    $i = 0;
    
    if ( $attachments ) {
    
      $html = '<div id="ca-container-' . $attachments_carousel_count . '" class="ca-container"><div class="ca-wrapper">';
    
      foreach ( $attachments as $attachment ) {
        $full_img_info = wp_get_attachment_image_src( $attachment->ID, 'full' );
        $thumb_img_info = wp_get_attachment_image_src( $attachment->ID, $size );
        
        $html .= '  <div class="ca-item ca-item-'.$i.'">
    <div class="ca-item-main">
      <a href="' . $full_img_info[0] . '" target="'.$target.'"><img src="' . $thumb_img_info[0] . '" width="' . $thumb_img_info[1] . '" height="' . $thumb_img_info[2] . '" border="0" /></a>
    </div>
  </div>';
        
        $i++;
      }
    }    
        
    $html .= '</div></div>';
    
    return $this->render_script($attachments_carousel_count) . $html;
  }
  
  function render_script($count) {
    global $attachments_carousel_count;
    return '<script type="text/javascript">jQuery(document).ready(function() {jQuery(\'#ca-container-'.$count.'\').contentcarousel();});</script>';
  }
	
	/**
	 * Registers and enqueues stylesheets for the administration panel and the
	 * public facing site.
	 */
	function register_scripts_and_styles() {
    $this->load_file( self::slug . '-js', '/js/' . self::slug . '.js', true );
    $this->load_file( self::slug . '-css', '/css/' . self::slug . '.css' );
	} // end register_scripts_and_styles
	
	/**
	 * Helper function for registering and enqueueing scripts and styles.
	 *
	 * @name	The 	ID to register with WordPress
	 * @file_path		The path to the actual file
	 * @is_script		Optional argument for if the incoming file_path is a JavaScript source file.
	 */
	private function load_file( $name, $file_path, $is_script = false ) {
		
		$url = plugins_url($file_path, __FILE__);
		$file = plugin_dir_path(__FILE__) . $file_path;

		if( file_exists( $file ) ) {
			if( $is_script ) {
				wp_register_script( $name, $url, array('jquery') );
				wp_enqueue_script( $name );
			} else {
				wp_register_style( $name, $url );
				wp_enqueue_style( $name );
			} // end if
		} // end if
    
	} // end load_file
  
} // end class

new AttachmentsCarousel();
?>