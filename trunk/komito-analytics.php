<?php
/**
 * Komito Analytics is an enhancement for the most popular web analytics software.
 *
 * PHP version 5
 *
 * @package KomitoAnalytics
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @author Valentin Podkamenyi <valentin@dtm.io>
 * @author Komito Analytics <support@komito.net>
 * @link https://wordpress.org/plugins/komito-analytics/
 * @link https://pear.php.net/manual/en/standards.php
 */

/**
 * Plugin Name: Komito Analytics
 * Plugin URI: https://komito.net
 * Description: Komito Analytics is an enhancement for the most popular web analytics software.
 * Version: 0.0.1
 * Author: Datamart
 * Author URI: https://profiles.wordpress.org/datamart
 * License: Apache License 2.0
 * License URI: https://www.apache.org/licenses/LICENSE-2.0
 * Tags: analytics, google-analytics, adobe-analytics, clicktale, event-tracking
 * Text Domain: komito-analytics
 * Domain Path: /languages
 */

define('KOMITO_TEXT_DOMAIN', 'komito-analytics');
define('KOMITO_PREFIX', 'komito_');
define('KOMITO_SCRIPT', 'https://komito.net/komito.js');
define('KOMITO_DEFAULT', 'defaults');

/**
 * Gets Komito Analytics options.
 * @link https://komito.net/integration/
 */
function get_komito_options() {
    return array(
        'trackTwitter' => array(
            'default' => 1, 'type' => 'integer',
            'description' => __('Track Twitter events if widget is presented on page.', KOMITO_TEXT_DOMAIN)),

        'trackFacebook' => array(
            'default' => 1, 'type' => 'integer',
            'description' => __('Track Facebook events if widget is presented on page.', KOMITO_TEXT_DOMAIN)),

        'trackLinkedIn' => array(
            'default' => 1, 'type' => 'integer',
            'description' => __('Track LinkedIn events if plugin is presented on page.', KOMITO_TEXT_DOMAIN)),

        'trackDownloads' => array(
            'default' => 1, 'type' => 'integer',
            'description' => __('Track files download links.', KOMITO_TEXT_DOMAIN)),

        'trackOutbound' => array(
            'default' => 1, 'type' => 'integer',
            'description' => __('Track outbound links.', KOMITO_TEXT_DOMAIN)),

        'trackForms' => array(
            'default' => 1, 'type' => 'integer',
            'description' => __('Track forms submissions.', KOMITO_TEXT_DOMAIN)),

        'trackUsers' => array(
            'default' => 1, 'type' => 'integer',
            'description' => __('Track pageviews by users logged in to social networks.', KOMITO_TEXT_DOMAIN)),

        'trackActions' => array(
            'default' => 1, 'type' => 'integer',
            'description' => __('Track "mailto", "tel", "sms" and "skype" actions.', KOMITO_TEXT_DOMAIN)),

        'trackPrint' => array(
            'default' => 1, 'type' => 'integer',
            'description' => __('Track page print actions.', KOMITO_TEXT_DOMAIN)),

        'trackMedia' => array(
            'default' => 1, 'type' => 'integer',
            'description' => __('Track HTML5 video, audio, Vimeo and YouTube players events.', KOMITO_TEXT_DOMAIN)),

        'trackScroll' => array(
            'default' => 1, 'type' => 'integer',
            'description' => __('Track scroll depth.', KOMITO_TEXT_DOMAIN)),

        'trackOrientation' => array(
            'default' => 1, 'type' => 'integer',
            'description' => __('Track orientation change on mobile devices.', KOMITO_TEXT_DOMAIN)),

        'trackAdblock' => array(
            'default' => 0, 'type' => 'integer',
            'description' => __('Track page views with blocked ads. (e.g. AdBlock tracker)', KOMITO_TEXT_DOMAIN)),

        'debugMode' => array(
            'default' => 0, 'type' => 'integer',
            'description' => __('Print all requests to console.', KOMITO_TEXT_DOMAIN))
    );
}

/**
 * Adds Komito Analytics script.
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 * @link https://komito.net/integration/
 */
function add_komito() {
    if (!get_option(KOMITO_PREFIX . KOMITO_DEFAULT, 1)) {
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
 * @link https://developer.wordpress.org/reference/functions/add_menu_page/
 * @link https://developer.wordpress.org/resource/dashicons
 */
function komito_menu() {
    add_menu_page(
        __('Komito Analytics Settings', KOMITO_TEXT_DOMAIN), // Page title
        __('Komito Analytics', KOMITO_TEXT_DOMAIN), // Menu title
        'administrator', // Capability
        'komito-settings', // Menu slug, URL
        'komito_settings_page', // Function name
        'dashicons-chart-area'); // Icon URL or a Dashicons string
}

/**
 * The function to be called to output the content for Komito settings page.
 * @link https://codex.wordpress.org/Function_Reference/settings_fields
 * @link https://codex.wordpress.org/Function_Reference/do_settings_sections
 * @link https://developer.wordpress.org/reference/functions/submit_button/
 * @link https://developer.wordpress.org/reference/functions/checked/
 */
function komito_settings_page() {
    $options = get_komito_options();
    ?>
    <div class="wrap">
      <h2><?php _e('Komito Analytics Settings', KOMITO_TEXT_DOMAIN) ?></h2>
      <form method="post" action="options.php" name="komito-form">
        <?php settings_fields('komito-settings-group'); ?>
        <div>
          <label><input type="checkbox"
            name="<?= KOMITO_PREFIX . KOMITO_DEFAULT ?>"
            value="1" <?php checked(get_option(KOMITO_PREFIX . KOMITO_DEFAULT), 1); ?>
            onclick="setDisabled_()">
            <?php _e('Use default Komito Analytics configuration settings.', KOMITO_TEXT_DOMAIN) ?></label>
          <p>
            <?php _e('For further information and instructions please see the', KOMITO_TEXT_DOMAIN) ?>
            <a href="https://komito.net/integration/"
               target="_blank"><?php _e('Komito Analytics integration page', KOMITO_TEXT_DOMAIN) ?></a>.
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
      var input = elements['<?= KOMITO_PREFIX . KOMITO_DEFAULT ?>'];
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
 * @link https://developer.wordpress.org/reference/functions/register_setting/
 */
function komito_settings() {
    $group = 'komito-settings-group';
    $options = get_komito_options();

    register_setting($group, KOMITO_PREFIX . KOMITO_DEFAULT, array('default' => 1));
    foreach ($options as $option => $args) {
        register_setting($group, KOMITO_PREFIX . $option, $args);
    }
}

/**
 * Loads a plugin's translated strings.
 * @link https://developer.wordpress.org/reference/functions/load_plugin_textdomain/
 * @link https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#loading-text-domain
 */
function komito_load_textdomain() {
    load_plugin_textdomain(KOMITO_TEXT_DOMAIN, FALSE, basename(dirname(__FILE__)) . '/languages/');
}

/**
 * Hooks a functions on to a specific actions.
 * @link https://developer.wordpress.org/reference/functions/add_action/
 */
add_action('wp_footer', 'add_komito');
add_action('admin_menu', 'komito_menu');
add_action('admin_init', 'komito_settings');
add_action('plugins_loaded', 'komito_load_textdomain');
?>
