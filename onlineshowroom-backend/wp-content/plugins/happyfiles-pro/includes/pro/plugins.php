<?php
namespace HappyFiles;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handle Plugins folders
 */
class Pro_Plugins {
	public static $plugin_ids = [];
	public static $plugin_ids_db_key = 'happyfiles_plugin_ids';

	public function __construct() {
		add_filter( 'all_plugins', [$this, 'filter_plugins'] );
		add_filter( 'happyfiles/data', [$this, 'filter_happyfiles_data'] );
	}

	/**
	 * Provide plugin IDs to app (HappyFiles.pluginIds)
	 */
	public function filter_happyfiles_data( $data ) {
		global $pagenow;

		if ( ! is_admin() || $pagenow !== 'plugins.php' ) {
			return $data;
		}

		$data['pluginIds'] = get_option( self::$plugin_ids_db_key, [] );

		return $data;
	}

	/**
	 * Filter plugins list on "Plugins" admin screen according to HappyFiles open ID
	 */
	public function filter_plugins( $plugins ) {
		global $pagenow;

		if ( ! is_admin() || $pagenow !== 'plugins.php' || ! in_array( 'plugins', Settings::$enabled_post_types ) ) {
			return $plugins;
		}

		$taxonomy = Data::$taxonomy;

		// STEP: Generate plugin IDs
		$plugin_ids_from_db = self::generate_plugin_ids( $plugins );

		$open_id = ! empty( $_GET[$taxonomy] ) ? $_GET[$taxonomy] : Data::$open_id;

		// Return: All folders selected
		if ( $open_id == -2 ) {
			return $plugins;
		}

		// Uncategorized: Remove plugins that are assigned to a HappyFiles folder
		if ( $open_id == -1 ) {
			foreach ( $plugins as $filename => $plugin ) {
				$plugin_id = ! empty( $plugin_ids_from_db[$filename] ) ? $plugin_ids_from_db[$filename] : false;

				if ( $plugin_id && count( wp_get_object_terms( $plugin_id, $taxonomy ) ) ) {
					unset( $plugins[$filename] );
				}
			}

			return $plugins;
		}

		$open_plugin_ids = get_objects_in_term( $open_id, $taxonomy );

		// Plugin has open folder ID not set: Remove plugin from array
		foreach ( $plugin_ids_from_db as $filename => $plugin_id ) {
			if ( ! in_array( $plugin_id, $open_plugin_ids ) && isset( $plugins[$filename] ) ) {
				unset( $plugins[$filename] );
			}
		}

		return $plugins;
	}

	/**
	 * Generate plugin IDs
	 * 
	 * Necessary as plugins don't have an ID like posts or users.
	 */
	public static function generate_plugin_ids( $plugins ) {
		$plugin_ids = get_option( self::$plugin_ids_db_key, [] );
		$last_plugin_id = is_array( $plugin_ids ) ? count( $plugin_ids ) : 0;

		foreach ( $plugins as $file_name => $plugins ) {
			// Skip if ID for this plugin is already stored in db options table
			if ( isset( $plugin_ids[ $file_name ] ) ) {
				continue;
			}

			$last_plugin_id++;

			$plugin_ids[ $file_name ] = $last_plugin_id;
		}

		update_option( self::$plugin_ids_db_key, $plugin_ids );

		self::$plugin_ids = $plugin_ids;

		return $plugin_ids;
	}
}