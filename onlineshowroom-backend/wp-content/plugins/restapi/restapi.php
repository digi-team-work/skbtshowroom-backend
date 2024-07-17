<?php
/*
* Plugin Name: Custom Rest API
* Description: Rest API
* Version: 1
* Author: kanlayaphat
*/


function get_seo_data($path) {
  $base_url = "";
  $url = "";
  switch ( wp_get_environment_type() ) {
    case 'local':
      break;
    case 'development':  
      $base_url = WP_BASE_URL_DEV;
      $url = $base_url.'wp-json/yoast/v1/get_head?url=http://skbt-main.local/onlineshowroom-backend/'.$path;
      break;
    case 'staging':
        break;
    case 'production':
      $base_url = WP_BASE_URL_PROD;
      $url = $base_url.'wp-json/yoast/v1/get_head?url='.WP_BASE_URL_PROD.$path;
      break;
    default:
      $base_url = WP_BASE_URL_DEV;
      break;
  }

  try {
    $args = array(
      'timeout' => 15,
    );
    // $url = $base_url.'wp-json/yoast/v1/get_head?url=http://skbt-main.local/onlineshowroom-backend/'.$path;

    $response = wp_remote_get($url, $args);
      
    if (is_wp_error($response)) {
      return $response;
    }

    $body = wp_remote_retrieve_body($response);
    if (empty($body)) {
      return [];
  }

    $data = json_decode($body, true);
    return $data['json'];
    // return $url;

  } catch (Exception $e) {
    return new WP_Error(500, $e->getMessage());
  }
}

// modify data that is queried be seperated in many sections
function custom_section_items($custom_fields, $sort_section) {
  $current_date = date('Y-m-d');
  $section = array();
  $pre_section = array();

  // banner
  $banner = $custom_fields['section-banner'];
  $section[] = array(
    'widget' => 'section-banner',
    'banner_type' => $banner['banner_type'], 
    'image_background_desktop' => $banner['banner_type'] === 'image' ? $banner['banner_image_desktop'] : $banner['banner_link_desktop'],
    'image_background_mobile' => $banner['banner_type'] === 'image' ? $banner['banner_image_mobile'] : "",
  );

  // section 1
  if ( $custom_fields['status_section1']) {
    $section1 = $custom_fields['section-1'];
    $pre_section['Image Grid Section'] = array_merge(
      array('widget' => 'section-1'),
      $section1
    );
    
    // $pre_section['Image Grid Section'] = $section1;

    // $pre_section['Image Grid Section'] = array(
    //   'widget' => 'section-1',
    //   'background_image' => $section1['background_image'],
    //   'background_color' => $section1['background_color'],
    //   'background_size' => $section1['background_size'],
    //   'background_position' => $section1['background_position'],
    //   'title_type' => $section1['title_type'],
    //   'title' => $section1['title_type'] === 'text' ? $section1['title'] : $section1['title_image'],
    //   'mascot' => $section1['mascot'],
    //   'lists' => $section1['lists_section1'],
    // );
  }

  // section 2
  if ( $custom_fields['status_section2'] ) {
    $section2 = $custom_fields['section-2'];
    $pre_section['Image Slide Section'] = array_merge(
      array('widget' => 'section-2'),
      $section2
    );

    // $pre_section['Image Slide Section'] = array(
    //   'widget' => 'section-2',
    //   'background_image' => $section1['background_image'],
    //   'background_color' => $section1['background_color'],
    //   'background_size' => $section1['background_size'],
    //   'background_position' => $section1['background_position'],
    //   'title_type' => $section2['title_type'],
    //   'title' => $section2['title_type'] === 'text' ? $section2['title'] : $section2['title_image'],
    //   'title_color' => $section2['title_color'],
    //   'lists' => $section2['lists_section2'],
    // );
  }

  // section 3
  if ( $custom_fields['status_section3'] ) {
    $section3 = $custom_fields['section-3'];
    $pre_section['Video Section'] = array(
      'widget' => 'section-3',
      'poster' => $section3['poster'],
      'lists' => $section3['video_lists'],
    );
  }

  // section 4
  // check promotion day and (send section with empty string or not send section)
  if ($custom_fields['status_section4'] && isset($custom_fields['section-4'])) {
    $section4 = $custom_fields['section-4'];
    $online = array(
      'widget' => 'section-4',
      'background_color' => $section4['background_color'],
      'image_background_desktop' => $section4['image_background_desktop'],
      'image_background_mobile' => $section4['image_background_mobile'],
      'button' => $section4['button'],
    );

    if (!empty($section4['start_promotion_day']) && !empty($section4['end_promotion_day'])) {
      if ($section4['start_promotion_day'] <= $current_date && $section4['end_promotion_day'] > $current_date) {
        $pre_section['Promotion Section'] = $online;
      } 
    } else if (!empty($section4['start_promotion_day'])) {
      if ($section4['start_promotion_day'] <= $current_date) {
        $pre_section['Promotion Section'] = $online;
      }
    } else if (!empty($section4['end_promotion_day'])) {
      if ($section4['end_promotion_day'] > $current_date) {
        $pre_section['Promotion Section'] = $online;
      }
    }
  }
 
  // section 5
  if ( $custom_fields['status_section5']) {
    $section5 = $custom_fields['section-5'];
    $pre_section['Booking Section'] = array_merge(
      array('widget' => 'section-5'),
      $section5
    );
    // $pre_section['Booking Section'] = array(
    //   'widget' => 'section-5',
    //   'background_color' => $section5['background_color'],
    //   'image_background_desktop' => $section5['image_background_desktop'],
    //   'image_background_mobile' => $section5['image_background_mobile'],
    //   'button' => $section5['button'],
    // );
  }

  // section 6
  if ( $custom_fields['status_section6']) {
    $section6 = $custom_fields['section-6'];
    $pre_section['Related Product Section'] = array(
      'widget' => 'section-6',
      'title' => $section6['title'],
      'lists' => $section6['related_product'],
    );
  }
  
  // sort sections
  $count = 0;
  while ($count < 6) {
    $seq = $sort_section[$count]['section_number'];
    if ( $pre_section[$seq] ) {
      $section[] = $pre_section[$seq];
    }
    $count++;
  }
  return $section;
}

// query fields of product detail page and modify to seven sections
function get_product_detail(WP_REST_Request $request) {
  $param = $request->get_param('id');
  $post_id = intval($param);
  $args = array (
    'post__in' => array($post_id),
    'post_type' => 'products',
    'post_status' => 'publish',
    'posts_per_page' => 1,
  );

  try {
    $query = new WP_Query( $args );

    $items = array();
    $section = array();
  
    if ( !$query->have_posts() ) {
      return new WP_REST_Response( array( 'message' => 'Sorry, no posts matched your criteria.' ), 404 );
    } 

    while ( $query->have_posts() ) {
      $query->the_post();

      global $post;
      $post_slug = $post->post_name;

      $path = 'products/'.$post_slug.'/';
      $data = get_seo_data($path);

      $custom_fields = get_fields(get_the_ID())['product_field'];
      $sort_section = $custom_fields['sort_selector_section'];

      $section = custom_section_items($custom_fields, $sort_section);
      
      $items = array(
        'id' => get_the_ID(),
        'title' => get_the_title(),
        'header' => $custom_fields['header_button'],
        'section' => $section,
        'seo_data' => $data,
      );
    }
    wp_reset_postdata();
  
    return new WP_REST_Response( $items, 200 );
  } catch (Exception $e) {
    return new WP_Error(500, $e->getMessage());
  }
  
}



// query product list
function get_products_list() {
  $args = array(
    'post_type' => 'products',
    'post_status' => 'publish',
    'order' => 'menu-order',
    'order_by' => 'acf',
    'posts_per_page' => 200
  );

  try {
    $query = new WP_Query( $args );
    $products = array();
  
    // return new WP_REST_Response( array( 'message' => 'Sorry, no posts matched your criteria.' ), 404 );
    if ( !$query->have_posts()) {
      return [];
    }

    while ( $query->have_posts()) {
      $query->the_post();
      $custom_fields = get_fields(get_the_ID());
      $products[] = array(
        'id' => get_the_ID(),
        'type' => $custom_fields['selector_type'],
        'image_showroom' => $custom_fields['image_showroom'],
        'person_image' => $custom_fields['person_image'],
        'person_video' => $custom_fields['person_video'],
      );

    }
    wp_reset_postdata();

    // return new WP_REST_Response( $products, 200 );
    return $products;
    
  } catch (Exception $e) {
    // return new WP_Error(500, $e->getMessage());
    return [];
  }

}



function get_showroom_infinity() {
  $args = array(
    'post_type' => 'page',
    'posts_per_page' => 1,
    'pagename' => 'showroom-infinity',
    'post_status' => 'publish',
  );

  try {
    $products = get_products_list();

    $path = 'showroom-infinity/';
    $data = get_seo_data($path);
    
    $showroom = array();
    $query = new WP_Query( $args );

    if ( !$query->have_posts()) {
      return new WP_REST_Response( array( 'message' => 'Sorry, no posts matched your criteria.' ), 404 );
    }

    $query->the_post();
    $custom_fields = get_fields(get_the_ID());
    $showroom = array(
      'image_showroom' => $custom_fields,
      'products' => $products,
      'seo_data' => $data,
    );

    return new WP_REST_Response($showroom, 200);
    
  } catch (Exception $e) {
    return new WP_Error(500, $e->getMessage());
  }
}





// query fields of home page
function get_home_item() {
  $args = array (
    'post_type' => 'page',
    'post_status' => 'publish',
    'pagename'=> 'home-managements',
    'posts_per_page' => 1,
  );
  try {
    $path = 'home-managements/';
    $data = get_seo_data($path);

    $items = array();
    $query = new WP_Query( $args );

    if ( !$query->have_posts()) {
      return new WP_REST_Response( array( 'message' => 'Sorry, no posts matched your criteria.' ), 404 );
    } 

    $query->the_post();

    $post_id = get_the_ID();
    $custom_fields = get_fields($post_id);
    $select_video = "";
    if ( $custom_fields['home_video'][0] ) {
      $select_video = $custom_fields['home_video'][0]['video'];
    }
    // $select_video = get_select_video($custom_fields);

    $items = array(
      'id' => $post_id,
      'video_url' => $select_video,
      'seo_data' => $data,
    );
    return new WP_REST_Response( $items, 200 );
  } catch (Exception $e) {
    return new WP_Error(500, $e->getMessage());
  }
  
}

// register custom api endpoints
function pluginname_register_api_endpoints() {
  register_rest_route( 'restapi/v2', '/products/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'get_product_detail',
    'args' => array(
						'id' => array(
							'validate_callback' => function($param, $request, $key) {
								return is_numeric( $param );
							}
						),
					),
  ));

  register_rest_route( 'restapi/v2', '/home-managements', array(
    'methods' => 'GET',
    'callback' => 'get_home_item',
  ));

  // register_rest_route( 'restapi/v2', '/videos', array(
  //   'methods' => 'GET',
  //   'callback' => 'get_selector_video',
  // ));

  register_rest_route( 'restapi/v2', '/products', array(
    'methods' => 'GET',
    'callback' => function() {
      return get_products_list();
    },
  ));

  register_rest_route('restapi/v2', '/showroom-infinity', array(
    'methods' => 'GET',
    'callback' => 'get_showroom_infinity',
  ));

  // register_rest_route('restapi/v2', 'seo', array(
  //   'methods' => 'GET',
  //   'callback' => 'get_seo_data',
  // ));

  // register_rest_route( 'restapi/v2', '/test', array(
  //   'methods' => 'GET',
  //   'callback' => 'fetch_from_service',
  // ));
}
add_action( 'rest_api_init', 'pluginname_register_api_endpoints' );


// function pluginprefix_activate() { 
// }
// register_activation_hook( __FILE__, 'pluginprefix_activate' );


 
// function pluginprefix_deactivate() {

// }
// register_deactivation_hook( __FILE__, 'pluginprefix_deactivate' );


// register route and query lists of videos to use in selector of home-managements 
// function get_selector_video() {
// 	$args = array (
//     'post_type' => 'page',
//     'post_status' => 'publish',
//     'title' => 'home-managements',
//   );

//   $videos = array();
//   $query = new WP_Query( $args );

//   if ($query->have_posts()) {
//     while ($query -> have_posts()) {
//       $query->the_post();
//       $custom_fields = get_fields(get_the_ID());

//       if (!empty($custom_fields['home_video']) && is_array($custom_fields['home_video'])) {
//         $home_video = $custom_fields['home_video'];
//         foreach ( $home_video as $video ) {
//           $videos[] = $video;
//         }
      
//       }	
//     }
//     wp_reset_postdata();
//   } else {
//     return new WP_REST_Response( array( 'message' => 'Sorry, no posts matched your criteria.' ), 404 );
//   }

// 	return new WP_REST_Response( $videos, 200 );
// }

// filter video which is selected
// function get_select_video($custom_fields) {
//   $home_video = $custom_fields['home_video'];
//   $select_video = "";

//   if ($custom_fields['video_id']) {
//     $selector = $custom_fields['video_id'];
//     foreach ( $home_video as $video ) {
//       $file_name = basename($video['video']);
//       if ( $file_name === $selector ) {
//         $select_video = $video['video'];
//       }
//     }
//   } else {
//     $select_video = $home_video[0]['video'];
//   }

//   return $select_video;
// }