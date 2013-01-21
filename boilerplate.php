<?php

/**
 * Plugin name: Boilerplate
 * Author: Topsitemakers
 * Author URI: http://www.topsitemakers.com/
 * Description: Custom plugin boilerplate.
 * Version: 1.0
 *
 * Plugin and custom plugin framework created by Topsitemakers
 * http://www.topsitemakers.com/
 */

// Sanity check
if (!defined('ABSPATH')) die('Direct access is not allowed.');

// Helper functions
include('includes/helper-functions.php');

// Constants
include('includes/constants.php');

// User list table class
include('includes/user-list.class.php');

/**
 * Install function
 * Create necessary tables and setup default variables.
 */
function boilerplate_install() {
  
  global $wpdb;
  
  // Define table names
  $table_name_sample   = $wpdb->prefix . 'boilerplate_sample';
  
  // Check if the tables already exist
  if (
    ($wpdb->get_var("SHOW TABLES LIKE '" . $table_name_sample . "'") != $table_name_sample)
    ) {
    // Table SQL
    $table_sample = "CREATE TABLE " . $table_name_sample . "(
                            sid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                            time INT NOT NULL,
                            text VARCHAR(8) NOT NULL);";
    
    // Get the upgrade PHP and create the tables
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($table_sample);
  }
  
  // Vars
  add_option(BOILERPLATE_SHORTNAME.'sample_variable', '0');

}

/**
 * Main init function
 */
function boilerplate_init() {
  
  // Start the session if it hasn't been started yet
  if (!session_id()) {
    session_start();
  }

  // Add necessary scripts
  wp_enqueue_script('boilerplate-ajax-request', WP_PLUGIN_URL . '/boilerplate/assets/boilerplate.js', array('jquery'));
  wp_localize_script('boilerplate-ajax-request', 'Boilerplate', array(
    'URL' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('boilerplate-post-nonce'),
  ));
  
  // Hook our cron
  if (!wp_next_scheduled('boilerplate_execute_cron')) {
    wp_schedule_event(current_time('timestamp'), 'every_minute', 'boilerplate_execute_cron')
  }
  
}

/**
 * Cron function
 */
function boilerplate_cron() {

  //   

}

/**
 * Administrator init function
 */
function boilerplate_admin_init(){
  
  // Add admin CSS and JS
  wp_enqueue_style('boilerplate-style-admin', WP_PLUGIN_URL . '/boilerplate/assets/boilerplate-admin.css');
  wp_enqueue_style('thickbox');
  wp_enqueue_script('boilerplate-script-admin', WP_PLUGIN_URL . '/boilerplate/assets/boilerplate-admin.js', array('jquery'));
  wp_enqueue_script('media-upload');
  
}

/**
 * Admin menu links
 */
function boilerplate_admin_menu(){
  
  // Main settings
  add_menu_page('General', 'General', BOILERPLATE_PERMISSIONS, 'boilerplate/admin-pages/options.php');
  
  // Submenu items
  add_submenu_page('boilerplate/admin-pages/options.php', 'General', 'General', BOILERPLATE_PERMISSIONS, 'boilerplate/admin-pages/options.php');
  
}

/**
 * AJAX Callback function
 */
function boilerplate_ajax() {
  
  // Check if the nonces match
  if (!wp_verify_nonce($_POST['nonce'], 'boilerplate-post-nonce')) die('Disallowed action.');
  
  $op = filter_input(INPUT_POST, 'op', FILTER_SANITIZE_STRING);
  if (!$op) die('Disallowed operation.');

  global $wpdb;
  switch ($op) {

    /**
     * Perform an action
     */
    case 'settings':
      break;
    
  }

  // This is required
  exit;
  
}

/**
 * Custom cron schedule periods
 */
function boilerplate_cron_schedules() {
  return array(
    'every_minute' => array(
      'interval' => 60,
      'display' => 'Every minute',
    ),
  );
}

/**
 * Start and/or recreate the session
 */
function boilerplate_recreate_session() {
  if (session_id()) {
    session_destroy();
    session_start();
  }
  else {
    session_start();
  }
}

/**
 * Actions
 */
add_action('init', 'boilerplate_init');
add_action('admin_init', 'boilerplate_admin_init');
add_action('admin_menu', 'boilerplate_admin_menu');
add_action('wp_ajax_nopriv_boilerplate_ajax', 'boilerplate_ajax');
add_action('wp_ajax_boilerplate_ajax', 'boilerplate_ajax');
add_action('boilerplate_execute_cron', 'boilerplate_cron');
// Action callbacks for recreating the session
add_action('init', 'boilerplate_recreate_session');
add_action('wp_login', 'boilerplate_recreate_session');
add_action('wp_logout', 'boilerplate_recreate_session');

/**
 * Filters
 */
add_filter('cron_schedules', 'boilerplate_cron_schedules');

/**
 * Registers
 */
register_activation_hook(__FILE__, 'boilerplate_install');
