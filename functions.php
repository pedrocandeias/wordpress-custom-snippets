// Remove Dashboard Widgets

add_action( 'admin_init', 'remove_dashboard_meta' );
function remove_dashboard_meta() {
remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
remove_meta_box( 'dashboard_plugins’, 'dashboard', 'normal' );
remove_meta_box( 'dashboard_primary', 'dashboard’, 'side' );
remove_meta_box( 'dashboard_secondary’, 'dashboard', 'normal' );
remove_meta_box( 'dashboard_quick_press’, 'dashboard', 'side' );
remove_meta_box( 'dashboard_recent_drafts’, 'dashboard', 'side' );
remove_meta_box( 'dashboard_recent_comments’, 'dashboard', 'normal' );
remove_meta_box( 'dashboard_right_now’, 'dashboard', 'normal' );
remove_meta_box( 'dashboard_activity’, 'dashboard', 'normal’);//since 3.8
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
function smartwp_enable_svg_upload( $mimes ) {
  //Only allow SVG upload by admins
  if ( !current_user_can( 'administrator' ) ) {
    return $mimes;
  }
  $mimes['svg']  = 'image/svg+xml';
  $mimes['svgz'] = 'image/svg+xml';
  
  return $mimes;
}
add_filter('upload_mimes', 'smartwp_enable_svg_upload');

//Disable XML-RPC
add_filter('xmlrpc_enabled', '__return_false');

//Remove jQuery migrate
function smartwp_remove_jquery_migrate( $scripts ) {
  if ( !is_admin() && !empty( $scripts->registered['jquery'] ) ) {
    $scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, ['jquery-migrate'] );
  }
}
add_action('wp_default_scripts', 'smartwp_remove_jquery_migrate');
