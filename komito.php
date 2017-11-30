<?php
/**
 * @package KomitoAnalytics
 * @version 0.0.1
 */

/**
 * Plugin Name: Komito Analytics
 * Plugin URI: https://komito.net
 * Description: Komito Analytics is an enhancement for the most popular web analytics software.
 * Version: 0.0.1
 * Author: Valentin Podkamennyi
 * Author URI: https://profiles.wordpress.org/vpodk
 * License: Apache License 2.0
 * License URI: https://www.apache.org/licenses/LICENSE-2.0
 * Tags: analytics, google-analytics, adobe-analytics, clicktale, event-tracking
 * Text Domain: komito
 */

define('KOMITO_PREFIX', 'komito_');
define('KOMITO_SCRIPT', 'https://komito.net/komito.js');
define('DEFAULTS_KEYS', 'defaults');

/**
 * Gets Komito Analytics options.
 * @see https://komito.net/integration/
 */
function get_komito_options() {
  return array(
    'trackTwitter' => array('default' => 1, 'type' => 'integer',
      'description' => 'Track Twitter events if widget is presented on page.'),

    'trackFacebook' => array('default' => 1, 'type' => 'integer',
      'description' => 'Track Facebook events if widget is presented on page.'),

    'trackLinkedIn' => array('default' => 1, 'type' => 'integer',
      'description' => 'Track LinkedIn events if plugin is presented on page.'),

    'trackDownloads' => array('default' => 1, 'type' => 'integer',
      'description' => 'Track files download links.'),

    'trackOutbound' => array('default' => 1, 'type' => 'integer',
      'description' => 'Track outbound links.'),

    'trackForms' => array('default' => 1, 'type' => 'integer',
      'description' => 'Track forms submissions.'),

    'trackUsers' => array('default' => 1, 'type' => 'integer',
      'description' => 'Track pageviews by users logged in to social networks.'),

    'trackActions' => array('default' => 1, 'type' => 'integer',
      'description' => 'Track "mailto", "tel", "sms" and "skype" actions.'),

    'trackPrint' => array('default' => 1, 'type' => 'integer',
      'description' => 'Track page print actions.'),

    'trackMedia' => array('default' => 1, 'type' => 'integer',
      'description' => 'Track HTML5 video, audio, Vimeo and YouTube players events.'),

    'trackScroll' => array('default' => 1, 'type' => 'integer',
      'description' => 'Track scroll depth.'),

    'trackOrientation' => array('default' => 1, 'type' => 'integer',
      'description' => 'Track orientation change on mobile devices.'),

    'debugMode' => array('default' => 0, 'type' => 'integer',
      'description' => 'Print all requests to console.')
  );
}

/**
 * Adds Komito Analytics script.
 * @see https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 * @see https://komito.net/integration/
 */
function add_komito() {
  if (!get_option(KOMITO_PREFIX . DEFAULTS_KEYS, 1)) {
    $options = get_komito_options();
    $output = array();

    foreach ($options as $option => $args) {
      $value = esc_attr(get_option(KOMITO_PREFIX . $option, $args['default']));
      array_push($output, $option . ':' . (empty($value) ? 0 : $value));
    }

    echo '<script>var _komito=_komito||{' . join(',', $output) . '};</script>';
  }
  echo '<script src="' . KOMITO_SCRIPT . '" async></script>';
  // wp_enqueue_script('komito', KOMITO_SCRIPT, $in_footer=true);
}

/**
 * Adds a top-level menu page.
 * @see https://developer.wordpress.org/reference/functions/add_menu_page/
 * @see https://developer.wordpress.org/resource/dashicons
 */
function komito_menu() {
  add_menu_page(
    'Komito Analytics Settings', // Page title
    'Komito Analytics', // Menu title
    'administrator', // Capability
    'komito-settings', // Menu slug, URL
    'komito_settings_page', // Function name
    'dashicons-chart-area'); // Icon URL or a Dashicons string
}

/**
 * The function to be called to output the content for Komito settings page.
 * @see https://codex.wordpress.org/Function_Reference/settings_fields
 * @see https://codex.wordpress.org/Function_Reference/do_settings_sections
 * @see https://developer.wordpress.org/reference/functions/submit_button/
 * @see https://developer.wordpress.org/reference/functions/checked/
 */
function komito_settings_page() {
  $options = get_komito_options();
  ?>
  <div class="wrap">
    <h2>Komito Analytics Settings</h2>
    <form method="post" action="options.php" name="komito-form">
      <?php settings_fields('komito-settings-group'); ?>
      <div>
        <label><input type="checkbox"
          name="<?= KOMITO_PREFIX . DEFAULTS_KEYS ?>"
          value="1" <?php checked(get_option(KOMITO_PREFIX . DEFAULTS_KEYS), 1); ?>
          onclick="setDisabled_()">Use default Komito Analytics configuration settings.</label>
        <p>
          For further information and instructions please see the
          <a href="https://komito.net/integration/"
             target="_blank">Komito Analytics integration page</a>.
        </p>
      </div>
      <hr/>
      <div class="card">
        <?php foreach ($options as $option => $args) { ?>
            <p>
              <label><input type="checkbox"
                value="1" <?php checked(get_option(KOMITO_PREFIX . $option), 1); ?>
                name="<?= KOMITO_PREFIX . $option ?>"><?= $args['description'] ?>
              </label>
            </p>
        <?php } ?>
      </div>
      <?php submit_button(); ?>
    </form>
  </div>
  <script>
  function setDisabled_() {
    var form = document.forms['komito-form'];
    var elements = form.elements;
    var input = elements['<?= KOMITO_PREFIX . DEFAULTS_KEYS ?>'];
    var length = elements.length;
    var types = {'checkbox': '', 'text': ''};
    var element;
    for (; length;) {
      element = elements[--length];
      if (element.name != input.name && element.type in types) {
        element.disabled = input.checked;
      }
    }
  }
  setDisabled_();
  </script>
  <?php
}

/**
 * Registers Komito Analytics setting.
 * @see https://developer.wordpress.org/reference/functions/register_setting/
 */
function komito_settings() {
  $group = 'komito-settings-group';
  $options = get_komito_options();

  register_setting($group, KOMITO_PREFIX . DEFAULTS_KEYS, array('default' => 1));
  foreach ($options as $option => $args) {
    register_setting($group, KOMITO_PREFIX . $option, $args);
  }
}

/**
 * Hooks a functions on to a specific actions.
 * @see https://developer.wordpress.org/reference/functions/add_action/
 */
add_action('wp_footer', 'add_komito');
add_action('admin_menu', 'komito_menu');
add_action('admin_init', 'komito_settings');
?>
