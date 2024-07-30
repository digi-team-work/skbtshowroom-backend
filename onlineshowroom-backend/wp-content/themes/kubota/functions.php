<?php
/**
 * Twenty Twenty-Four functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Twenty Twenty-Four
 * @since Twenty Twenty-Four 1.0
 */

use ACP\Search\Helper\MetaQuery\Comparison\IsEmpty;

/**
 * Register block styles.
 */



if ( ! function_exists( 'twentytwentyfour_block_styles' ) ) :
	/**
	 * Register custom block styles
	 *
	 * @since Twenty Twenty-Four 1.0
	 * @return void
	 */
	function twentytwentyfour_block_styles() {

		register_block_style(
			'core/details',
			array(
				'name'         => 'arrow-icon-details',
				'label'        => __( 'Arrow icon', 'twentytwentyfour' ),
				/*
				 * Styles for the custom Arrow icon style of the Details block
				 */
				'inline_style' => '
				.is-style-arrow-icon-details {
					padding-top: var(--wp--preset--spacing--10);
					padding-bottom: var(--wp--preset--spacing--10);
				}

				.is-style-arrow-icon-details summary {
					list-style-type: "\2193\00a0\00a0\00a0";
				}

				.is-style-arrow-icon-details[open]>summary {
					list-style-type: "\2192\00a0\00a0\00a0";
				}',
			)
		);
		register_block_style(
			'core/post-terms',
			array(
				'name'         => 'pill',
				'label'        => __( 'Pill', 'twentytwentyfour' ),
				/*
				 * Styles variation for post terms
				 * https://github.com/WordPress/gutenberg/issues/24956
				 */
				'inline_style' => '
				.is-style-pill a,
				.is-style-pill span:not([class], [data-rich-text-placeholder]) {
					display: inline-block;
					background-color: var(--wp--preset--color--base-2);
					padding: 0.375rem 0.875rem;
					border-radius: var(--wp--preset--spacing--20);
				}

				.is-style-pill a:hover {
					background-color: var(--wp--preset--color--contrast-3);
				}',
			)
		);
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfour' ),
				/*
				 * Styles for the custom checkmark list block style
				 * https://github.com/WordPress/gutenberg/issues/51480
				 */
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
		register_block_style(
			'core/navigation-link',
			array(
				'name'         => 'arrow-link',
				'label'        => __( 'With arrow', 'twentytwentyfour' ),
				/*
				 * Styles for the custom arrow nav link block style
				 */
				'inline_style' => '
				.is-style-arrow-link .wp-block-navigation-item__label:after {
					content: "\2197";
					padding-inline-start: 0.25rem;
					vertical-align: middle;
					text-decoration: none;
					display: inline-block;
				}',
			)
		);
		register_block_style(
			'core/heading',
			array(
				'name'         => 'asterisk',
				'label'        => __( 'With asterisk', 'twentytwentyfour' ),
				'inline_style' => "
				.is-style-asterisk:before {
					content: '';
					width: 1.5rem;
					height: 3rem;
					background: var(--wp--preset--color--contrast-2, currentColor);
					clip-path: path('M11.93.684v8.039l5.633-5.633 1.216 1.23-5.66 5.66h8.04v1.737H13.2l5.701 5.701-1.23 1.23-5.742-5.742V21h-1.737v-8.094l-5.77 5.77-1.23-1.217 5.743-5.742H.842V9.98h8.162l-5.701-5.7 1.23-1.231 5.66 5.66V.684h1.737Z');
					display: block;
				}

				/* Hide the asterisk if the heading has no content, to avoid using empty headings to display the asterisk only, which is an A11Y issue */
				.is-style-asterisk:empty:before {
					content: none;
				}

				.is-style-asterisk:-moz-only-whitespace:before {
					content: none;
				}

				.is-style-asterisk.has-text-align-center:before {
					margin: 0 auto;
				}

				.is-style-asterisk.has-text-align-right:before {
					margin-left: auto;
				}

				.rtl .is-style-asterisk.has-text-align-left:before {
					margin-right: auto;
				}",
			)
		);
	}
endif;

add_action( 'init', 'twentytwentyfour_block_styles' );

/**
 * Enqueue block stylesheets.
 */

if ( ! function_exists( 'twentytwentyfour_block_stylesheets' ) ) :
	/**
	 * Enqueue custom block stylesheets
	 *
	 * @since Twenty Twenty-Four 1.0
	 * @return void
	 */
	function twentytwentyfour_block_stylesheets() {
		/**
		 * The wp_enqueue_block_style() function allows us to enqueue a stylesheet
		 * for a specific block. These will only get loaded when the block is rendered
		 * (both in the editor and on the front end), improving performance
		 * and reducing the amount of data requested by visitors.
		 *
		 * See https://make.wordpress.org/core/2021/12/15/using-multiple-stylesheets-per-block/ for more info.
		 */
		wp_enqueue_block_style(
			'core/button',
			array(
				'handle' => 'twentytwentyfour-button-style-outline',
				'src'    => get_parent_theme_file_uri( 'assets/css/button-outline.css' ),
				'ver'    => wp_get_theme( get_template() )->get( 'Version' ),
				'path'   => get_parent_theme_file_path( 'assets/css/button-outline.css' ),
			)
		);
	}
endif;

add_action( 'init', 'twentytwentyfour_block_stylesheets' );

/**
 * Register pattern categories.
 */

if ( ! function_exists( 'twentytwentyfour_pattern_categories' ) ) :
	/**
	 * Register pattern categories
	 *
	 * @since Twenty Twenty-Four 1.0
	 * @return void
	 */
	function twentytwentyfour_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfour_page',
			array(
				'label'       => _x( 'Pages', 'Block pattern category', 'twentytwentyfour' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfour' ),
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfour_pattern_categories' );





/* ------------------------------------ custom function ---------------------------------- */

// get field key from field name and post id
function acf_get_field_key( $field_name, $post_id ) {
	global $wpdb;
	$acf_fields = $wpdb->get_results( $wpdb->prepare( "SELECT ID,post_parent,post_name FROM $wpdb->posts WHERE post_excerpt=%s AND post_type=%s" , $field_name , 'acf-field' ) );
	// get all fields with that name.
	switch ( count( $acf_fields ) ) {
		case 0: // no such field
			return false;
		case 1: // just one result. 
			return $acf_fields[0]->post_name;
	}
	// result is ambiguous
	// get IDs of all field groups for this post
	$field_groups_ids = array();
	$field_groups = acf_get_field_groups( array(
		'post_id' => $post_id,
	) );
	foreach ( $field_groups as $field_group )
		$field_groups_ids[] = $field_group['ID'];
	
	// Check if field is part of one of the field groups
	// Return the first one.
	foreach ( $acf_fields as $acf_field ) {
		if ( in_array($acf_field->post_parent,$field_groups_ids) )
			return $acf_field->post_name;
	}
	return false;
}

// add related products in product detail section6
function fetch_product_ids_from_api($select) {
    $response = wp_remote_get(API_KUBOTA);
    if (is_wp_error($response)) {
        return [];
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!is_array($data)) {
        return [];
    }

	if ($select === 'options') {
		$options = [];
		foreach ($data as $product) {
			if (isset($product['id']) && isset($product['title'])) {
				$options[$product['id']] = $product['post_type']. ' - ' . $product['title'];
			}
		}
		return $options;
	} else  {
		return $data;
	}
}

function populate_acf_product_id_field($field) {
	if ( is_admin() ) {
		$products = fetch_product_ids_from_api('options');

		if (!empty($products)) {
				$field['choices'] = $products;
		} 
	}
    return $field;
}
add_filter('acf/load_field/name=product_id', 'populate_acf_product_id_field');



/* ------------------------------------- sort selector section --------------------------- */

// update section number of sort selector section
function update_section_number($post_id) {
	$section = array(
		1 => 'Image Grid Section',
		2 => 'Image Slide Section',
		3 => 'Video Section',
		4 => 'Promotion Section',
		5 => 'Booking Section',
		6 => 'Related Product Section',
	);
	$sort_selector_section = array();
	for ($i = 1; $i <= 6; $i++) {
		$sort_selector_section[] = array(
			'section_number' => $section[$i]
		);
	}
	update_field('product_field', 
		array(
			'sort_selector_section' => $sort_selector_section
		), 
		$post_id
	);
}

// hook when create new post and update post in post type products
function add_acf_repeater_product($post_id, $post, $update) {
	if (is_admin() && $post->post_type === 'products') {
		if ($update) {
			return;
		}
		update_section_number($post_id);
		update_post_meta($post_id, '_initialized', true);
	}
}
add_action('wp_insert_post', 'add_acf_repeater_product', 10, 3);

// set readonly in sort selector sevtion 
function readonly_selector_section($field) {
	if ( is_admin() && get_post_type() === 'products') {
		$post = get_post();
		if ($post) {
			$initialized = get_post_meta($post->ID, '_initialized', true);
			if ( $initialized ) {
				$field['readonly'] = 1;
				return $field;
			}
		}
	}
}
add_filter('acf/prepare_field/name=section_number', 'readonly_selector_section');



/* ------------------------------------- related product in section6 --------------------------- */

// adjust data from object to string for saving in textarea field
// related product in section6
function create_detail_string($data) {
	$detail = "";
	if (isset($data)) {
		foreach ($data as $key => $value) {
			$before = $detail;
			$new = $key." - ".$value." | ";
			$detail = $before.$new;
		}
	}
	return $detail;
}

// update product_detail ( textarea field )
function acf_get_product_detail($post_id) {
	$post_type = get_post_type($post_id);
	if (is_admin() && $post_type === 'products') {
		$products = fetch_product_ids_from_api('all');
		if (empty($products)) {
			return;
		}
		$values = $_POST['acf'];

		// get field keys of related field
		$related_fields = array('product_field', 'section-6', 'related_product', 'product_id', 'product_detail');
		$related_key = [];
		foreach ($related_fields as $key) {
			$related_key[$key] = acf_get_field_key($key, $post_id);
		}
		
		// $product_field_key = acf_get_field_key('product_field', $post_id);
		// $section6_key = acf_get_field_key('section-6', $post_id);
		// $related_key = acf_get_field_key('related_product', $post_id);
		// $id_key = acf_get_field_key('product_id', $post_id);
		// $detail_key = acf_get_field_key('product_detail', $post_id);
		
		$section6 = $values[$related_key['product_field']][$related_key['section-6']];
		// $section6 = $values[$related_key][$section6_key];

		$products_id = array_column($products, 'id');
		foreach ($section6[$related_key['related_product']] as &$product) {
			$get_id = $product[$related_key['product_id']];
			$id = intval($get_id);
			$index = array_search($id, $products_id);
			$data = create_detail_string($products[$index]);
			$product[$related_key['product_detail']] = $data;		
		}
		update_field(
			$related_key['product_field'],
			array(
				$related_key['section-6'] => $section6
			),
			$post_id
		);
		// foreach ($section6[$related_key] as &$product) {
		// 	$get_id = $product[$id_key];
		// 	$id = intval($get_id);
		// 	$index = array_search($id, $products_id);
		// 	$data = create_detail_string($products[$index]);
		// 	$product[$detail_key] = $data;		
		// }
		// update_field(
		// 	$product_field_key,
		// 	array(
		// 		$section6_key => $section6
		// 	),
		// 	$post_id
		// );
	}
}
add_action('acf/save_post', 'acf_get_product_detail', 20);



/* --------------------------------------- wordpress hook -------------------------------------- */
function display_message_on_non_admin_pages() {
	$request_uri = $_SERVER['REQUEST_URI'];
    if (!is_admin() 
		&& strpos($request_uri, '/onlineshowroom-backend/rest-api/docs/') === false 
		&& strpos($request_uri, '/onlineshowroom-backend/phpmyadmin/') === false
		) {
        status_header(200); // Set the HTTP status code to 200
        header('Content-Type: text/plain'); // Set the content type to plain text
        echo 'api running';
        exit;
    }
}
add_action('template_redirect', 'display_message_on_non_admin_pages');



function increase_per_page_max($params){
    $params['per_page']['maximum'] = 200;
    return $params;
}

add_filter('rest_products_collection_params', 'increase_per_page_max');



// chnage attachment url to cdn
function url_to_cdn( $url ) {
	$domain = IMAGE_URL;
    if($url){
        $value = str_replace($domain, IMAGE_CDN, $url);
		return $value;
    }
	return $url;
}
add_filter('wp_get_attachment_url', 'url_to_cdn');



// adjust preview url of preview button in adamin page
function the_preview_fix() {
	$type = get_post_type();
	$current_page_id = get_the_ID();
	$current_page = get_post($current_page_id);
	$slug = $current_page->post_name;
    // $slug = basename(get_permalink());
	if ($type === 'products') {
		return "https://skbt-main.digi-team.work/onlineshowroom/product/".$slug; 
	}
}
add_filter( 'preview_post_link', 'the_preview_fix' );
// add_filter( 'post_type_link', 'the_preview_fix', 10, 2 );