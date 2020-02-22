<?php
/**
* The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @package           AlexaVocabularyExport
 *
 * @wordpress-plugin
 * Plugin Name:       Alexa Vocabulary Export
 * Plugin URI:        D:\xampp\htdocs\wordpress\wp-content\plugins\Alexa-Vocabulary-Export\alexa-vocabulary-export.php
 * Description:       This plugin creates an admin accessable page where only admins can add a new csv file and exports
 *                    it in a JSON format, that will be used on the Alexa skill for learning vocabulary in different languages.
 * area.
 * Version:           1.0
 * Author:            My language skills team
 * Author URI:        https://github.com/my-language-skills/
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       alexa-vocabulary-export
 */

// If this file is called directly, abort!
defined('ABSPATH') or die('Hey, what are you doing here? You silly human!');

// Define CONSTANTS
define( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PLUGIN', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation. Starting point of process
 *
 * @since 1.0.0
 */
function alexa_vocabulary_export_activation()
{
    //flush_rewrite_rules();
    require_once dirname(__FILE__) . '/Inc/Base/Activate.php';
    //Inc\Base\Activate::activate();
}
register_activation_hook( __FILE__, 'alexa_vocabulary_export_activation' );


/**
 * The code that runs during plugin deactivation
 *
 * @since 1.0.0
 */
function alexa_vocabulary_export_deactivation() {
    require_once dirname(__FILE__) . '/Inc/Base/Deactivate.php';
}
register_deactivation_hook(__FILE__, 'alexa_vocabulary_export_deactivation');


/**
 * Initialize all the core classes of the plugin
 *
 * @since 1.0.0
 */
if (!class_exists('Init')) {

    require_once dirname(__FILE__) . '/Inc/init.php';
}
