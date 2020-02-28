<?php
/**
 * The BaseController class permit to retrieve information
 * about the plugin path, plugin url and plugin initial
 * function.
 *
 * @author      @CharalamposTheodorou
 * @since       1.0
 *
 * @package AlexaVocabularyExport
 */

 
class BaseController {
    /* PUBLIC variables for plugin path,url and directory */
    public $plugin_path;
    public $plugin_url;
    public $plugin;

    /**
     * Constructor method for this class.
     * Here are initialized main variables.
     *
     * @author      @CharalamposTheodorou
     * @since       1.0
     */
    public function __construct() {
        $this->plugin_path = plugin_dir_path($this->dirname_r(__FILE__, 2));
        $this->plugin_url = plugin_dir_url($this->dirname_r(__FILE__, 2));
        $this->plugin = plugin_basename($this->dirname_r(__FILE__, 3)) . '/alexa-vocabulary-export.php';
    }

    /**
     * Get the PATH of the plugin.
     *
     * @author      @CharalamposTheodorou
     * @since       1.0
     *
     * @return string the path
     */
    public static function getPluginPath() {
        return plugin_dir_path(self::dirname_r(__FILE__, 2));
    }

    /**
     * Get the URL of the plugin.
     *
     * @author      @CharalamposTheodorou
     * @since       1.0
     *
     * @return string the url
     */
    public static function getPluginUrl() {
        return plugin_dir_url(self::dirname_r(__FILE__, 2));
    }
    
    /**
     * Retrieve the PATH of the folder that we will
     * save the json file, if is not existing we will
     * create it.
     * path = ... /wp-content/uploads/alexa-vocabulary-export
     *
     * @author      @CharalamposTheodorou
     * @since       1.0.0
     *
     * @return string the path of the json folder.
     */
    public function getUploadsFolderPath() {
        $path = wp_upload_dir()['basedir'] . '/alexa-vocabulary-export/';

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }
    /**
     * Returns a parent directory's path, implemented
     * because in php minor of 7 return a warming about
     * the second parameter that is not necessary.
     * 
     * @author      @CharalamposTheodorou
     * @since       1.0
     * 
     * @param   string      $path     A path.
     * @param   int         $levels   The number of parent directories to go up.
     *
     * @return string       path to directory
     */
    public static function dirname_r($path, $levels = 1) {
        if ($levels > 1) {
            return dirname(self::dirname_r($path, --$levels));
        } else {
            return dirname($path);
        }
    }
}