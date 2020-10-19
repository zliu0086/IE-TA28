<?php
/**
 * Plugin Name: Hot Random Image
 * Plugin URI: https://hot-themes.com/wordpress/plugins/random-image
 * Description: Hot Random Image is a basic widget that shows a randomly picked image from a selected folder where images are stored.
 * Version: 1.3
 * Author: HotThemes
 * Author URI: https://hot-themes.com
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'hot_random_image_load_widgets' );
add_action('admin_init', 'hot_random_image_textdomain');
/**
 * Register our widget.
 * 'HotEffectsRotator' is the widget class used below.
 *
 * @since 0.1
 */
function hot_random_image_load_widgets() {
	register_widget( 'Hotrandom_image' );
}

function hot_random_image_textdomain() {
	load_plugin_textdomain('hot_random_image', false, dirname(plugin_basename(__FILE__) ) . '/languages');
}
	
/**
 * Hotrandom_image Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
 
class Hotrandom_image extends WP_Widget {
     
	/**
	 * Widget setup.
	 */
	 
	function __construct() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'Hot_random_image', 'description' => __('Hot Random Image', 'hot_random_image') );

		/* Widget control settings. */
		$control_ops = array(  'id_base' => 'hot-random_image' );

		/* Create the widget. */
		parent::__construct( 'hot-random_image', __('Hot Random Image', 'hot_random_image'), $widget_ops, $control_ops );
		
		add_action('wp_head', array( $this, 'Hotrandom_image_inline_scripts_and_styles'),13);

		add_filter( 'the_content', array( &$this, 'Hotrandom_image_Render' ));
		add_filter( 'the_excerpt', array( &$this, 'Hotrandom_image_Render' ));
		add_filter( 'widget_text', array( &$this, 'Hotrandom_image_Render' ));
    }
	
	function Hotrandom_image_inline_scripts_and_styles(){
	   // MULTIPLE WIDGETS ON PAGE ARE SUPPORTED !!!
	   $all_options = parent::get_settings();
	   $echo_noconflict = false;
	   
		echo '<style type="text/css">';
		echo '/* Hot Random Image START */';
		foreach ($all_options as $key => $value){
		    $options = $all_options[$key];
			if(!isset($options['folder'])) continue;
			if(!$options['folder'])continue;
			
			echo ' 
			#random-image-'.$key.'{
				width:'.$options['width'].';
				height:'.$options['height'].';
			}
			';
	    }
	 
		echo '
		/* Hot Random Image END */
		</style>';
	   
	}
	
	function GetDefaults()
	{
		return array(
			'title' => ''
			,'width' => '100%'
			,'height' => 'auto'
			,'folder' => ''
			,'alt' => 'Random image'
			,'link' => ''
			,'userinput' => ''
		);
	}

	/**
	 * Shortcode
	 */
	function Hotrandom_image_Render($srchtml) {

		if ( !preg_match("#{randomimage}(.*?){/randomimage}#s",$srchtml) ) {
			return $srchtml;
		}

		if (preg_match_all("#{randomimage}(.*?){/randomimage}#s", $srchtml, $matches, PREG_PATTERN_ORDER) > 0) {

			$RANDOM_IMAGEScount = -1;

			foreach ($matches[0] as $match) {
				$RANDOM_IMAGEScount++;

				$hotrandomimage_input = preg_replace("/{.+?}/", "", $match);
				$hotrandomimage_params = explode(",", $hotrandomimage_input);

				$keywords = explode(" ", $hotrandomimage_params[0]);
				$keywords_number = count($keywords);
				$keywords_number_1 = $keywords_number - 1;

				/*

				The parameters entered in the shortcode should be separated by colon.
				{randomimage}images/random,100%,auto,Random image,https://hot-themes.com/{/randomimage}

				They can be called from the array:

				$hotrandomimage_params[0] --> Path to images
				$hotrandomimage_params[1] --> Width
				$hotrandomimage_params[2] --> Height
				$hotrandomimage_params[3] --> Alt text
				$hotrandomimage_params[4] --> Image link
				...

				*/

				$image = '';
				if ( ! isset($hotrandomimage_params[1] ) ) {
					$hotrandomimage_params[1] = '100%';
				}
				if ( ! isset($hotrandomimage_params[2] ) ) {
					$hotrandomimage_params[2] = 'auto';
				}
				if ( ! isset($hotrandomimage_params[3] ) ) {
					$hotrandomimage_params[3] = '';
				}
				if ( ! isset($hotrandomimage_params[4] ) ) {
					$hotrandomimage_params[4] = '';
				}

	    		$images1 = glob($hotrandomimage_params[0].'/*.jpg');
				$images2 = glob($hotrandomimage_params[0].'/*.png');
				$images3 = glob($hotrandomimage_params[0].'/*.gif');
				$images4 = glob($hotrandomimage_params[0].'/*.jpeg');
				$images5 = glob($hotrandomimage_params[0].'/*.svg');
				
				$images = array();
				
				if(!empty($images1))
					$images = array_merge($images,$images1);
				if(!empty($images2))
					$images = array_merge($images,$images2);
				if(!empty($images3))
					$images = array_merge($images,$images3);
				if(!empty($images4))
					$images = array_merge($images,$images4);
				if(!empty($images5))
					$images = array_merge($images,$images5);
					
				$ind = rand(1,count($images)) ;
	 		    $image = $images[$ind - 1];

	 		    $html = '';

	 		    if($image){
					if($hotrandomimage_params[4]){
						$html .= '<a href="'.$hotrandomimage_params[4].'">';
					}
						$html .= '<img width="'.$hotrandomimage_params[1].'" height="'.$hotrandomimage_params[2].'" class="hot-random-image" src="'.get_site_url().'/'.$image.'" alt="'.$hotrandomimage_params[3].'" />';

					if($hotrandomimage_params[4]){
						$html .= '</a>';
					}
				}

				$srchtml = preg_replace( "#{randomimage}".$hotrandomimage_input."{/randomimage}#s", $html, $srchtml );


			}
		}
		return $srchtml;

	}
	
	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Before widget (defined by themes). */
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;

		if (!empty($title)) {
			echo $before_title . $title . $after_title;
		}

        $defaults = $this->GetDefaults();
		$instance = wp_parse_args( (array) $instance, $defaults );  
		
		$image = '';
	    $link  = '';
	    
		$input = str_replace("\n", ";",$instance["userinput"]);
		$input = str_replace("\r", ";",$instance["userinput"]);
		$input = str_replace(" ", "",$input); 
		$use_input =false;
		
		if($input == ''){
			$images1 = glob($instance["folder"].'/*.jpg');
			$images2 = glob($instance["folder"].'/*.png');
			$images3 = glob($instance["folder"].'/*.gif');
			$images4 = glob($instance["folder"].'/*.jpeg');
			$images5 = glob($instance["folder"].'/*.svg');
			
			$images = array();
			
			if(!empty($images1))
				$images = array_merge($images,$images1);
			if(!empty($images2))
				$images = array_merge($images,$images2);
			if(!empty($images3))
				$images = array_merge($images,$images3);
			if(!empty($images4))
				$images = array_merge($images,$images4);
			if(!empty($images5))
				$images = array_merge($images,$images5);
				
			$ind = rand(1,count($images)) ;
 		    $image = $images[$ind - 1];
 		    $link = $instance["link"];
		
		}else{
			$loop = 0;
			$images = array();
			$links  = array();

			$input = str_replace(";;", ";",$input);
			$input = str_replace(";;", ";",$input);
			$input = str_replace(";;", ";",$input);
			$input = str_replace(";;", ";",$input);
			$arr = explode(';',$input);

			for($loop = 0; $loop < count($arr);$loop++){
				$il_val = explode('|',$arr[$loop]);

				$images[$loop] = $instance["folder"].'/'.$il_val[0];  
				$links[$loop]  = $il_val[1]; 
		   	}
		   
			$ind = rand(1,count($images));
			$image = $images[$ind - 1]; 
			$link  = $links[$ind - 1]; 
		}
		
		if($image){
			if($link){ ?>
				<a href="<?php echo $link; ?>">
		<?php } ?>
				<img id="random-image-<?php echo $this->number; ?>" class="hot-random-image" src="<?php echo get_site_url().'/'.$image; ?>" alt="<?php echo $instance["alt"]; ?>" />
	    <?php
			if($link){ ?>
				</a>
			<?php }
		}
		
		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
    	
		foreach($new_instance as $key => $option)
		{
			$instance[$key]     = $new_instance[$key];
		} 
		
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
	    $defaults = $this->GetDefaults();
		$instance = wp_parse_args( (array) $instance, $defaults );  ?>

		<!-- Hot Random Image: Text Input -->

		<p><?php _e( 'Title:','hot_random_image' ); ?><br/>
		<input  type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" id="<?php echo $this->get_field_id( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'folder' ); ?>"><?php _e('Path to images:','hot_random_image'); ?></label>
			<input class="widefat" type="text" name="<?php echo $this->get_field_name( 'folder' ); ?>" id="<?php echo $this->get_field_id( 'folder' ); ?>" value="<?php echo $instance['folder']; ?>" class="text" />
			<span style="font-size:0.9em; display: block;"><?php _e('Enter path relative to your WordPress installation.<br/>In example "wp-content/uploads/2014/12"','hot_random_image'); ?></span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e('Width:','hot_random_image'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'width' ); ?>" id="<?php echo $this->get_field_id( 'width' ); ?>" value="<?php echo $instance['width']; ?>" size="5" />
			<span style="font-size:0.9em; display: block;"><?php _e('Enter dimension and unit (in example "200px" or "100%" or "auto")','hot_random_image'); ?></span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e('Height:','hot_random_image'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'height' ); ?>" id="<?php echo $this->get_field_id( 'height' ); ?>" value="<?php echo $instance['height']; ?>" size="5" />
			<span style="font-size:0.9em; display: block;"><?php _e('Enter dimension and unit (in example "200px" or "100%" or "auto")','hot_random_image'); ?></span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'alt' ); ?>"><?php _e('Alt text:','hot_random_image'); ?></label>
			<input class="widefat" type="text" name="<?php echo $this->get_field_name( 'alt' ); ?>" id="<?php echo $this->get_field_id( 'alt' ); ?>" value="<?php echo $instance['alt']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link' ); ?>"><?php _e('Image link:','hot_random_image'); ?></label>
			<input class="widefat" type="text" name="<?php echo $this->get_field_name( 'link' ); ?>" id="<?php echo $this->get_field_id( 'link' ); ?>" value="<?php echo $instance['link']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'userinput' ); ?>"><?php _e('Select specific images (optional)','hot_random_image'); ?></label>
			<textarea class="widefat" rows="5" name="<?php echo $this->get_field_name( 'userinput' ); ?>" id="<?php echo $this->get_field_id( 'userinput' ); ?>" ><?php echo $instance['userinput']; ?></textarea>
			<span style="font-size:0.9em; display: block;"><?php _e('Leave this blank if want to rotate all images from the specified folder. If you want to rotate only selected images, specify them here. You can use this format: <code>image name|image link</code> for each image (one per line). On some servers, you must use <code>;</code> to separate images.','hot_random_image'); ?></span>
		</p>

	<?php  
	}
}

?>