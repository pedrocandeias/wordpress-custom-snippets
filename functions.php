// Remove Dashboard Widgets

add_action( 'admin_init', 'remove_dashboard_meta' );
function remove_dashboard_meta() {
remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');
}

//Disable WordPress admin bar for all logged in users
add_filter('show_admin_bar', '__return_false');

//This will prepend your WordPress RSS feed content with the featured image
add_filter('the_content', 'smartwp_featured_image_in_rss_feed');
function smartwp_featured_image_in_rss_feed( $content ) {
  global $post;
  if( is_feed() ) {
    if ( has_post_thumbnail( $post->ID ) ){
      $prepend = '<div>' . get_the_post_thumbnail( $post->ID, 'medium', array( 'style' => 'margin-bottom: 10px;' ) ) . '</div>';
      $content = $prepend . $content;
    }
  }
  return $content;
}

//Change the default excerpt length in WordPress (default is 55 words)
function smartwp_change_excerpt_length( $length ) {
  return 24;
}
add_filter( 'excerpt_length', 'smartwp_change_excerpt_length', 9999);

//Create an admin user
function smartwp_create_admin_user(){
  $username = 'yourusername';
  $password = '2JyAEQJ9B9Jf5T8a';
  $email = 'change@me.com';
  //This will ensure it only tries to create the user once (based on email/username)
  if ( !username_exists( $username ) && !email_exists( $email ) ) {
    $userid = wp_create_user( $username, $password, $email );
    $user = new WP_User( $userid );
    $user->set_role( 'administrator' );
  }
}
add_action('init', 'smartwp_create_admin_user');

//Enable shortcodes in text widgets
add_filter('widget_text', 'do_shortcode');

//Adds a custom logo to the top left of the WordPress admin
function smartwp_custom_logo_wp_dashboard() {
  echo "<style type='text/css'>
    #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
      background-image: url('" . get_bloginfo('stylesheet_directory') . "https://cdn.smartwp.com/admin-icon.png');
      background-size: contain;
      background-position: 0 0;
      color:rgba(0, 0, 0, 0);
    }
    #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon {
      background-position: 0 0;
    }
    </style>";
}
add_action('wp_before_admin_bar_render', 'smartwp_custom_logo_wp_dashboard');

//Enable SVG upload

function svgs_upload_mimes( $mimes = array() ) {

	global $svgs_options;

	if ( empty( $svgs_options['restrict'] ) || current_user_can( 'administrator' ) ) {

		// allow SVG file upload
		$mimes['svg'] = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';

		return $mimes;

	} else {

		return $mimes;

	}

}
add_filter( 'upload_mimes', 'svgs_upload_mimes', 99 );

/**
 * Check Mime Types
 */
function svgs_upload_check( $checked, $file, $filename, $mimes ) {

	if ( ! $checked['type'] ) {

		$check_filetype		= wp_check_filetype( $filename, $mimes );
		$ext				= $check_filetype['ext'];
		$type				= $check_filetype['type'];
		$proper_filename	= $filename;

		if ( $type && 0 === strpos( $type, 'image/' ) && $ext !== 'svg' ) {
			$ext = $type = false;
		}

		$checked = compact( 'ext','type','proper_filename' );
	}

	return $checked;

}
add_filter( 'wp_check_filetype_and_ext', 'svgs_upload_check', 10, 4 );

/**
 * Mime Check fix for WP 4.7.1 / 4.7.2
 *
 * Fixes uploads for these 2 version of WordPress.
 * Issue was fixed in 4.7.3 core.
 */
function svgs_allow_svg_upload( $data, $file, $filename, $mimes ) {

	global $wp_version;
	if ( $wp_version !== '4.7.1' || $wp_version !== '4.7.2' ) {
		return $data;
	}

	$filetype = wp_check_filetype( $filename, $mimes );

	return [
		'ext'				=> $filetype['ext'],
		'type'				=> $filetype['type'],
		'proper_filename'	=> $data['proper_filename']
	];

}
add_filter( 'wp_check_filetype_and_ext', 'svgs_allow_svg_upload', 10, 4 );


//Disable XML-RPC
add_filter('xmlrpc_enabled', '__return_false');

//Remove jQuery migrate
function smartwp_remove_jquery_migrate( $scripts ) {
  if ( !is_admin() && !empty( $scripts->registered['jquery'] ) ) {
    $scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, ['jquery-migrate'] );
  }
}
add_action('wp_default_scripts', 'smartwp_remove_jquery_migrate');


// Add Analytics do head
add_action('wp_head', 'wpb_add_googleanalytics');
function wpb_add_googleanalytics() { ?>
 
// Paste Google Analytics tracking code
 
}

// Change login logo

function pec_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/img/admin/login-logo.png);
			height:165px;
			width:165px;
			background-size: 165px 165px;
			background-repeat: no-repeat;
			padding-bottom: 30px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'pec_login_logo' );

// Enqueue google fonts

function pec_add_google_fonts() {
 
wp_enqueue_style( 'pec-google-fonts', 'https://fonts.googleapis.com/css?name-of-the-font-and-styles', false ); 
}
 
add_action( 'wp_enqueue_scripts', 'pec_add_google_fonts' );

// Remove wlwmanifest

remove_action('wp_head', 'wlwmanifest_link');

/**
 * Disable the emoji's
 */
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );	
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	
	// Remove from TinyMCE
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}
add_action( 'init', 'disable_emojis' );

/**
 * Filter out the tinymce emoji plugin.
 */
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

// Remove customizer default options
// Also remove settings
$wp_customize->remove_control('blogdescription');
$wp_customize->remove_setting('blogdescription');
$wp_customize->remove_control('blogname');
$wp_customize->remove_setting('blogname');


add_action( "customize_register", "pec_theme_customize_register" );
function pec_theme_customize_register( $wp_customize ) {
// Remove blog description and blog name option
// Also remove settings
$wp_customize->remove_control('blogdescription');
$wp_customize->remove_setting('blogdescription');
$wp_customize->remove_control('blogname');
$wp_customize->remove_setting('blogname');

// Remove header image and widgets option
$wp_customize->remove_control("header_image");
$wp_customize->remove_setting('header_image');
$wp_customize->remove_panel("widgets");


 // Remove Colors, Background image, and Static front page 
 $wp_customize->remove_section("colors");
 $wp_customize->remove_section("background_image");
 $wp_customize->remove_section("static_front_page");
}

// Remove widgets from widgets panel

function unregister_default_wp_widgets() {
// WP_Widget_Pages = Pages Widget
// WP_Widget_Calendar = Calendar Widget
// WP_Widget_Archives = Archives Widget
// WP_Widget_Links = Links Widget
// WP_Widget_Media_Audio = Audio Player Media Widget
// WP_Widget_Media_Image = Image Media Widget
// WP_Widget_Media_Video = Video Media Widget
// WP_Widget_Media_Gallery = Gallery Media Widget
// WP_Widget_Meta = Meta Widget
// WP_Widget_Search = Search Widget
// WP_Widget_Text = Text Widget
// WP_Widget_Categories = Categories Widget
// WP_Widget_Recent_Posts = Recent Posts Widget
// WP_Widget_Recent_Comments = Recent Comments Widget
// WP_Widget_RSS = RSS Widget
// WP_Widget_Tag_Cloud = Tag Cloud Widget
// WP_Nav_Menu_Widget = Menus Widget
// WP_Widget_Custom_HTML = Custom HTML Widget
     unregister_widget('WP_Widget_Tag_Cloud');
}
add_action('widgets_init', 'unregister_default_wp_widgets' );

/* Custom dashboard widget */

add_action('wp_dashboard_setup', 'custom_dashboard_widgets');

function custom_dashboard_widgets()
{
    global $wp_meta_boxes;
    global $blog_id;
    if ($blog_id == 1) {
    } else {
        wp_add_dashboard_widget('custom_help_widget', 'The title here', 'custom_dashboard_help');
    }
}

function custom_dashboard_help()
{
    echo 'Just add html';
}
 
