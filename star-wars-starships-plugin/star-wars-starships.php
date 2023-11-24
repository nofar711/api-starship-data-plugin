<?php
/**
 * Plugin Name: Star Wars Starships
 * Description: Launch starships to every page on your website.
 * Version: 1.0
 * Author: Nofar Shlomo
 */


 /** Get data from API **/
function get_starships_from_swapi() {
    $api_url = 'https://swapi.dev/api/starships/';
    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        return array();
    }

    $body = wp_remote_retrieve_body($response);
    $starships = json_decode($body, true);

    return $starships['results'];
}


/** Create shortcode **/
function starships_shortcode() {
  ob_start();
  $selected_pages = get_option('star_wars_starships_pages', array());

  if (!empty($selected_pages) && is_page($selected_pages)) {
      $starships = get_starships_from_swapi();

      echo '<div class="starships-wapper">';
      if (!empty($starships)) {
          echo '<h3 class="starships-table-title colortext">Let\'s explore a galaxy far, far away which is full of Star Wars starships:</h3>';
          echo '<table class="starships-table">';
          echo 
          '<tr>
            <th>Name</th>
            <th>Starship Class</th>
            <th>Crew</th>
            <th>Cost in Credits</th>
          </tr>';

          foreach ($starships as $starship) {
              echo '<tr>';
              echo '<td>' . esc_html($starship['name']) . '</td>';
              echo '<td>' . esc_html($starship['starship_class']) . '</td>';
              echo '<td>' . esc_html($starship['crew']) . '</td>';
              echo '<td>' . esc_html($starship['cost_in_credits']) . '</td>';
              echo '</tr>';
          }
          echo '</table>';
      } else {
          echo '[Error] You have reached a black hole, no starships can be found.';
      }
      echo '</div>';
  }

  return ob_get_clean();
}

add_shortcode('star_wars_starships', 'starships_shortcode');


/** Add shortcode before content **/
function display_starships_before_content($content) {
  $selected_pages = get_option('star_wars_starships_pages', array());

  if (is_page($selected_pages) && !has_shortcode($content, 'star_wars_starships')) {
      $starships_content = '[star_wars_starships]';
      $content = $starships_content . $content;
  }
  return $content;
}

add_filter('the_content', 'display_starships_before_content');


/** Add plugin setting to sidenav **/
function star_wars_starships_settings() {
  register_setting('star_wars_starships_settings', 'star_wars_starships_page');
  add_settings_section('star_wars_starships_settings_section', 'Select Page', '__return_false', 'star_wars_starships_settings');
  add_settings_field('star_wars_starships_page', 'Page', 'star_wars_starships_page_callback', 'star_wars_starships_settings', 'star_wars_starships_settings_section');
}

add_action('admin_menu', 'star_wars_starships_add_admin_menu');

function star_wars_starships_add_admin_menu() {
  $icon_url = plugin_dir_url(__FILE__) . 'plugin-icon.svg';
  add_menu_page('Star Wars Starships', 'Star Wars Starships', 'manage_options', 'star_wars_starships', 'star_wars_starships_settings_page', $icon_url);

  add_action('admin_init', 'star_wars_starships_settings');
}


/** Plugin setting page **/
function star_wars_starships_settings_page() {
  ?>
  <div class="wrap">
      <h1 class="plugin-title">Star Wars Starships Settings</h1>
      <p class="plugin-description">Which page or pages would you like to launch the starships to?</p>

      <form method="post" action="">
          <?php
          $selected_pages = (array) get_option('star_wars_starships_pages', array());
          $pages = get_pages();
          foreach ($pages as $page) {
              echo '<input type="checkbox" name="star_wars_starships_pages[]" value="' . esc_attr($page->ID) . '" ' . checked(in_array($page->ID, $selected_pages), true, false) . ' />' . esc_html ($page->post_title) . '<br>';
          }
          ?>
          <button type="submit" name="remove_all_pages" class="remove-button">Remove All</button>
          <?php submit_button('Launch The Starships 🚀', 'primary', 'star_wars_starships_save_changes'); ?>
      </form>
  </div>
  <?php
}


/* Form submission */
function handle_star_wars_starships_form_submission() {
  if (isset($_POST['star_wars_starships_save_changes'])) {
      $selected_pages = isset($_POST['star_wars_starships_pages']) ? array_map('intval', $_POST['star_wars_starships_pages']) : array();
      update_option('star_wars_starships_pages', $selected_pages);

      // Update content on selected pages
      foreach ($selected_pages as $page_id) {
          $page_content = get_post_field('post_content', $page_id);
      }

  } elseif (isset($_POST['remove_all_pages'])) {

    // Remove the shortcode from all pages
    $all_pages = get_pages();
    foreach ($all_pages as $page) {
        $page_id = $page->ID;
        $page_content = get_post_field('post_content', $page_id);
    }
    update_option('star_wars_starships_pages', array());
}
}

add_action('admin_init', 'handle_star_wars_starships_form_submission');


/** Add style **/
function enqueue_plugin_styles() {
  if (is_admin()) {
      if (isset($_GET['page']) && $_GET['page'] === 'star_wars_starships') {
          wp_enqueue_style('star_wars_starships_admin_styles', plugin_dir_url(__FILE__) . 'admin.css');
      }
  } else {
      wp_enqueue_style('star_wars_starships_styles', plugin_dir_url(__FILE__) . 'style.css');
  }
}

add_action('admin_enqueue_scripts', 'enqueue_plugin_styles');
add_action('wp_enqueue_scripts', 'enqueue_plugin_styles');

