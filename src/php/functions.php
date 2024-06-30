<?php
/**
 * write your functions here.
 */
 wp_register_style( 'Swiper_css', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css' );
 wp_enqueue_style('Swiper_css');

 wp_register_script( 'Swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', null, null, true );
 wp_enqueue_script('Swiper');
 
if( ! defined('ABSPATH') ){
	exit;
}

add_action( 'wp_enqueue_scripts', 'streamtube_child_enqueue_styles' );
add_action( 'login_enqueue_scripts', 'streamtube_child_enqueue_styles' );



function streamtube_child_enqueue_styles() {
	wp_enqueue_style(
		'streamtube-child-style',
		get_stylesheet_uri(),
	    array( 'streamtube-style' ),
	    filemtime( get_stylesheet_directory() . '/style.css' )
	);
}

function load_dev_scripts() {
    $developer = $_COOKIE['developer']; // Get the developer cookie

    if (isset($developer)) {
        // Load the developer-specific CSS file
        wp_enqueue_style('developer_css', get_stylesheet_directory_uri() . '/dev/' . $developer . '/css/main.css', array(), filemtime(get_stylesheet_directory() . '/dev/' . $developer . '/css/main.css'));

        // Load the developer-specific JS file
        wp_enqueue_script('developer_js', get_stylesheet_directory_uri() . '/dev/' . $developer . '/js/script.js', array(), filemtime(get_stylesheet_directory() . '/dev/' . $developer . '/js/script.js'));
    } else {
        // Load the main CSS file
        wp_enqueue_style('main_css', get_stylesheet_directory_uri() . '/dist/css/main.css', array(), filemtime(get_stylesheet_directory() . '/dist/css/main.css'));

        // Load the main JS file
        wp_enqueue_script('main_js', get_stylesheet_directory_uri() . '/dist/js/script.js', array(), filemtime(get_stylesheet_directory() . '/dist/js/script.js'));
    }
}
add_action('wp_enqueue_scripts', 'load_dev_scripts');


// send custom ecomail WebHook
add_action( 'elementor_pro/forms/new_record', function( $record, $handler ) {
    $form_name = $record->get_form_settings( 'form_name' );

    if ( 'Countdown_form' !== $form_name) {
        return;
    }
	$url="";
	if ( 'Countdown_form' == $form_name ) {
	$url='https://api2.ecomailapp.cz/lists/1/subscribe';
    }

    $raw_fields = $record->get( 'fields' );
    $fields = [];
    foreach ( $raw_fields as $id => $field ) {
        $fields[ $id ] = $field['value'];
    }
	
	$data= array(
        'name' => $fields['name'],
        'email' => $fields['email']
    );

$data = json_encode(array(
'subscriber_data' => array( 'name' => null, 'surname' => null, 'email' => $data["email"],  "gender" => null,
	"bounce_soft" => 0,
  "bounced_hard" => 0,
  "bounce_message" => null,
  "already_subscribed" => false ),
    'trigger_autoresponders' => false,
    'update_existing' => false,
    'resubscribe' => false,
));
	$response = wp_remote_post( $url, array(
           'method' => 'POST',
           'headers' => array('key'=> 'a328f15bd34608051cf1868265e7d7675fcb88a56f8059b5e4b69f2e1405ea09', 'Content-Type' => 'application/json'),
           'body' => $data
        )
    );
	if ( is_wp_error( $response ) ) {
		$output['result'] = $response->get_error_message();
	    $handler->add_response_data( true, $output );
    } else {
		$output['result'] = "success";
	    $handler->add_response_data( true, $output );
    }
}, 10, 2 );