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
    'image_background_mobile' => $banner['banner_type'] === 'image' ? $banner['banner_image_mobile'] : false,
  );


  // section 1
  if ( $custom_fields['status_section1']) {
    $section1 = $custom_fields['section-1'];
    $pre_section['Image Grid Section'] = array_merge(
      array('widget' => 'section-1'),
      array('section_1' => $section1['section1_field']),
    );
  }

  // section 2
  if ( $custom_fields['status_section2'] ) {
    $section2 = $custom_fields['section-2'];
    $pre_section['Image Slide Section'] = array_merge(
      array('widget' => 'section-2'),
      $section2
    );
  }

  // section 3
  if ( $custom_fields['status_section3'] ) {
    $section3 = $custom_fields['section-3'];
    $pre_section['Video Section'] = array(
      'widget' => 'section-3',
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
  }

  // section 6
  if ( $custom_fields['status_section6']) {
    $section6 = $custom_fields['section-6'];
    $related = $section6['related_product'];
    $products_detail = [];

    foreach ( $related as $product ) {
      $product_id = intval($product['product_id']);
      $response = wp_remote_get('https://kubota.campaignserv.com/api/skl/product/showroom-detail?id='.$product_id);
      if (is_wp_error($response)) {
          return;
      }
      $body = wp_remote_retrieve_body($response);
      $data = json_decode($body, true);
      if (!is_array($data)) {
          return;
      }
      $products_detail[] = $data[0];
    }

    $pre_section['Related Product Section'] = array(
      'widget' => 'section-6',
      'title' => $section6['title'],
      'lists' => $products_detail,
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
      $header = $custom_fields['header_button'];

      $items = array(
        'id' => get_the_ID(),
        'title' => get_the_title(),
        'header' => $header,
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

    $count_person = 0;
    while ( $query->have_posts()) {
      $query->the_post();
      $custom_fields = get_fields(get_the_ID());
      if ( $custom_fields['selector_type'] === 'product' ) {
        $products[] = array(
          'id' => get_the_ID(),
          'type' => $custom_fields['selector_type'],
          'image_showroom' => $custom_fields['product_field']['image_showroom'],
        );
      } else if ( $custom_fields['selector_type'] === 'person' && $count_person === 0) {
        $products[] = array(
          'id' => get_the_ID(),
          'type' => $custom_fields['selector_type'],
          'person' => $custom_fields['person_field'],
        );
        $count_person = 1;
      }
      

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

    $image_showroom = array();
    foreach ($custom_fields as $key => $value) {
      if ($key !== "sound_showroom") {
          $image_showroom[$key] = $value;
      }
  }

    $showroom = array(
      'image_showroom' => $image_showroom,
      'sound_showroom' => $custom_fields['sound_showroom'],
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
    'permission_callback' => '__return_true',
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
    'permission_callback' => '__return_true',
  ));

  register_rest_route( 'restapi/v2', '/products', array(
    'methods' => 'GET',
    'callback' => 'get_products_list',
    'permission_callback' => '__return_true',
  ));

  register_rest_route('restapi/v2', '/showroom-infinity', array(
    'methods' => 'GET',
    'callback' => 'get_showroom_infinity',
    'permission_callback' => '__return_true',
  ));
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