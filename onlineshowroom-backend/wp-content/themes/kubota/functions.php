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

/* ---------------------- custom function ---------------------- */

// add related products in product detail section6
function fetch_product_ids_from_api() {
    $response = wp_remote_get('https://kubota.campaignserv.com/api/skl/product/showroom-list');
    if (is_wp_error($response)) {
        return [];
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!is_array($data)) {
        return [];
    }

    $options = [];
    foreach ($data as $product) {
        if (isset($product['id']) && isset($product['title'])) {
            $options[$product['id']] = $product['post_type']. ' - ' . $product['title'];
        }
    }

    return $options;
}

function populate_acf_product_id_field($field) {
		if ( is_admin() && get_post_type() === 'products') {
			$products = fetch_product_ids_from_api();

			if (!empty($products)) {
					$field['choices'] = $products;
			}
		}

    return $field;
}
add_filter('acf/load_field/name=product_id', 'populate_acf_product_id_field');



// text field successfully
// update section number of sort selector section
function update_section_number($number, $post_id) {
	update_row(
		'sort_selector_section', 
		$number,
		array(
			'section_number' => 'Section'.$number
		),
		$post_id
	 );
}

// hook when create new post in post type products
function add_acf_repeater_product($post_id, $post, $update) {
	if (is_admin() && $post->post_type === 'products') {
		if ($update) {
			return;
		}
		$count = 1;
		while ($count <= 6) {
			update_section_number($count, $post_id);
			$count = $count + 1;
		}
	}
}
add_action('wp_insert_post', 'add_acf_repeater_product', 10, 3);



// add options for selector video in home-managements
// function fetch_video_fields() {
// 	try {
// 		$response = wp_remote_get(WP_BASE_URL_PROD.'wp-json/restapi/v2/videos');
// 		if (is_wp_error($response)) {
// 			return [];
// 		} 
	
// 		$body = wp_remote_retrieve_body($response);
// 		$data = json_decode($body, true);

// 		if (!is_array($data)) {
// 			return [];
// 		} 
	
// 		$options = [];
// 		foreach ($data as $video) {
// 			if (isset($video)) {
// 				$file_name = basename($video['video']);
// 				$options[$file_name] = $file_name;
// 			} 
// 		}
	
// 		return $options;
// 	} catch (Exception $ex) {
// 		return $ex;
// 	}

// }

// add options in video selector field
// function populate_acf_selector_field($field) {
// 	if (is_admin() && get_post_type() === 'page') {
// 		$selector = fetch_video_fields();
// 		if (isset($selector)) {
// 			$field['choices'] = $selector;
// 		} 
// 	}
// 	return $field;
// }
// add_filter('acf/load_field/name=video_id', 'populate_acf_selector_field');

// function readonly_selector_section($field) {
// 	if ( is_admin() && get_post_type() === 'products') {
		
// 		if ( $field['value'] && strpos($field['value'][0]['section_number'], 'Section')) {
// 			$field['readonly'] = true;
// 			return $field;
// 		} else {
// 			$field['readonly'] = false;
// 			return $field;
// 		}
// 	}
// }
// add_filter('acf/load_value/name=sort_selector_section', 'readonly_selector_section');









