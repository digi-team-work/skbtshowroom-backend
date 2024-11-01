<?php
/*
* Plugin Name: Custom Rest API
* Description: Rest API
* Version: 1
* Author: kanlayaphat
*/


function get_seo_data($type, $id)
{
	try {
		$args = array(
			'timeout' => 15,
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode(USERNAME . ':' . PASSWORD)
			)
		);

		// $url = $base_url.'wp-json/yoast/v1/get_head?url=http://skbt-main.local/onlineshowroom-backend/'.$path;
		$url = "";
		if ($type === 'products') {
			$url = WP_BASE_URL . 'wp-json/wp/v2/products/' . $id;
		} else {
			$url = WP_BASE_URL . 'wp-json/wp/v2/pages/?slug=' . $id;
		}
		$response = wp_remote_get($url, $args);
		if (is_wp_error($response)) {
			return [];
		}

		$body = wp_remote_retrieve_body($response);
		if (empty($body)) {
			return [];
		}

		$data = json_decode($body, true);
		if (isset($data)) {
			return $data;
		}
		return [];
	} catch (Exception $e) {
		return new WP_Error(500, $e->getMessage());
	}
}



// adjust string to object
function parse_key_value_string($inputString)
{
	$pairs = explode(' | ', $inputString);
	$result = array();
	foreach ($pairs as $pair) {
		$keyValue = explode(' - ', $pair, 2);
		if (count($keyValue) == 2) {
			$key = trim($keyValue[0]);
			$value = trim($keyValue[1]);
			$result[$key] = $value;
		}
	}
	return $result;
}



// get product detail in section6
function get_detail_object($related)
{
	$products_detail = [];
	$target = "";

	foreach ($related as $product) {
		$target = $product['target'];
		$get_detail = parse_key_value_string($product['product_detail']);
		$detail = $get_detail;

		$merge = array_merge(
			array('target' => $target),
			$detail
		);

		if ($merge["post_status"] == "publish") {
			$products_detail[] = $merge;
		}
	}
	return $products_detail;
}



// modify data that is queried be seperated in many sections
function custom_section_items($custom_fields, $sort_section)
{
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
	if ($custom_fields['status_section1']) {
		$section1 = $custom_fields['section-1'];
		$pre_section['Image Grid Section'] = array_merge(
			array('widget' => 'section-1'),
			array('section_1' => $section1['section1_field']),
		);
	}

	// section 2
	if ($custom_fields['status_section2']) {
		$section2 = $custom_fields['section-2'];
		$pre_section['Image Slide Section'] = array_merge(
			array('widget' => 'section-2'),
			$section2
		);
	}

	// section 3
	if ($custom_fields['status_section3']) {
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
	if ($custom_fields['status_section5']) {
		$section5 = $custom_fields['section-5'];
		$pre_section['Booking Section'] = array_merge(
			array('widget' => 'section-5'),
			$section5
		);
	}

	// section 6
	if ($custom_fields['status_section6']) {
		$section6 = $custom_fields['section-6'];
		$related = $section6['related_product'];
		$products_detail = get_detail_object($related);

		if (!empty($products_detail)) {
			$pre_section['Related Product Section'] = array(
				'widget' => 'section-6',
				'title' => $section6['title'],
				'lists' => $products_detail,
			);
		}
	}

	// sort sections
	$count = 0;
	while ($count < 6) {
		$seq = $sort_section[$count]['section_number'];
		if ($pre_section[$seq]) {
			$section[] = $pre_section[$seq];
		}
		$count++;
	}
	return $section;
}



// query fields of product detail page and modify to seven sections
function get_product_detail(WP_REST_Request $request)
{
	$param = $request->get_param('slug');
	// $post_id = intval($param);
	$args = array(
		'name' => $param,
		'post_type' => 'products',
		'post_status' => array('publish', 'draft'),
		'posts_per_page' => 1,
	);

	try {
		$query = new WP_Query($args);

		$items = array();
		$section = array();

		if (!$query->have_posts()) {
			return new WP_REST_Response(array('message' => 'Sorry, no posts matched your criteria.'), 404);
		}

		while ($query->have_posts()) {
			$query->the_post();

			global $post;
			$post_slug = $post->post_name;

			$data = get_seo_data('products', get_the_ID());
			$custom_fields = get_fields(get_the_ID())['product_field'];
			$sort_section = $custom_fields['sort_selector_section'];

			$section = custom_section_items($custom_fields, $sort_section);
			$header = $custom_fields['header_button'];

			$items = array(
				'id' => get_the_ID(),
				'title' => get_the_title(),
				'slug' => $post_slug,
				'header' => $header,
				'section' => $section,
				'seo_data' => $data['yoast_head_json'] ? $data['yoast_head_json'] : $data,
			);
		}
		wp_reset_postdata();

		$result = new WP_REST_Response($items, 200);
		$result->set_headers(array('Cache-Control' => 'max-age=3600'));
		return $result;
	} catch (Exception $e) {
		return new WP_Error(500, $e->getMessage());
	}
}



// query product list
function get_products_list()
{
	$args = array(
		'post_type' => 'products',
		'post_status' => 'publish',
		'order' => 'menu-order',
		'order_by' => 'acf',
		'posts_per_page' => 200
	);

	try {
		$query = new WP_Query($args);
		$products = array();

		// return new WP_REST_Response( array( 'message' => 'Sorry, no posts matched your criteria.' ), 404 );
		if (!$query->have_posts()) {
			return [];
		}

		$count_person = 0;
		while ($query->have_posts()) {
			$query->the_post();
			$custom_fields = get_fields(get_the_ID());
			if ($custom_fields['selector_type'] === 'product') {
				$post = get_post(get_the_ID());
				$slug = $post->post_name;
				$products[] = array(
					'id' => get_the_ID(),
					'type' => $custom_fields['selector_type'],
					'image_showroom' => $custom_fields['product_field']['image_showroom'],
					'slug' => $slug,
				);
			} else if ($custom_fields['selector_type'] === 'person' && $count_person === 0) {
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


/* ------------------------------------------- page ----------------------------------------- */

function get_showroom_infinity()
{
	$args = array(
		'post_type' => 'page',
		'posts_per_page' => 1,
		'pagename' => 'showroom-infinity',
		'post_status' => 'publish',
	);

	try {
		$products = get_products_list();

		// $path = 'showroom-infinity/';
		// $data = get_seo_data($path);
		$data = get_seo_data('showroom-infinity', 'showroom-infinity');


		$showroom = array();
		$query = new WP_Query($args);

		if (!$query->have_posts()) {
			return new WP_REST_Response(array('message' => 'Sorry, no posts matched your criteria.'), 404);
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
			'sound_showroom' => $custom_fields['sound_showroom'] ? $custom_fields['sound_showroom'] : false,
			'products' => $products,
			'seo_data' => $data[0]['yoast_head_json'] ? $data[0]['yoast_head_json'] : $data,
		);

		$result = new WP_REST_Response($showroom, 200);
		$result->set_headers(array('Cache-Control' => 'max-age=3600'));
		return $result;
	} catch (Exception $e) {
		return new WP_Error(500, $e->getMessage());
	}
}



// query fields of home page
function get_home_item()
{
	$args = array(
		'post_type' => 'page',
		'post_status' => 'publish',
		'pagename' => 'home-managements',
		'posts_per_page' => 1,
	);
	try {
		// $path = 'home-managements/';
		// $data = get_seo_data($path);
		$data = get_seo_data('home-managements', 'home-managements');

		$items = array();
		$query = new WP_Query($args);

		if (!$query->have_posts()) {
			return new WP_REST_Response(array('message' => 'Sorry, no posts matched your criteria.'), 404);
		}

		$query->the_post();

		$post_id = get_the_ID();
		$custom_fields = get_fields($post_id);
		$select_video = "";
		if (is_array($custom_fields['home_video']) && $custom_fields['home_video'][0]) {
			$select_video = $custom_fields['home_video'][0]['video'];
		}

		$items = array(
			'id' => $post_id,
			'video_url' => $select_video === "" ? false : $select_video,
			'seo_data' =>  $data[0]['yoast_head_json'] ? $data[0]['yoast_head_json'] : $data,
		);
		$result = new WP_REST_Response($items, 200);
		$result->set_headers(array('Cache-Control' => 'max-age=3600'));
		return $result;
	} catch (Exception $e) {
		return new WP_Error(500, $e->getMessage());
	}
}



// register custom api endpoints
function pluginname_register_api_endpoints()
{
	register_rest_route('restapi/v2', '/products/(?P<slug>[^/]+)', array(
		'methods' => 'GET',
		'callback' => 'get_product_detail',
		'permission_callback' => '__return_true',
	));

	register_rest_route('restapi/v2', '/home-managements', array(
		'methods' => 'GET',
		'callback' => 'get_home_item',
		'permission_callback' => '__return_true',
	));

	register_rest_route('restapi/v2', '/products', array(
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
add_action('rest_api_init', 'pluginname_register_api_endpoints');
