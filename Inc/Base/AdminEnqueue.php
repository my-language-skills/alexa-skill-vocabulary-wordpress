<?php
/**
 * This class is responsible to Enqueue all admin scripts and
 * styles for the plugin.
 * 
 * @author          @CharalamposTheodorou
 * @since           1.0
 * 
 * @package AlexaVocabularyExport
 */

class AdminEnqueue extends BaseController 
{
    /* DIRECTORY CONSTANTS */
    const DIRECTORY_CSS = 'assets/css/';
    const DIRECTORY_JS = 'assets/js/';

    /* STYLES NAME CONSTANTS */
    const STYLE_NAME = 'alexa_vocabulary.css';
    
    /* SCRIPTS NAME CONSTANTS */
    const SCRIPT_NAME = 'alexa_vocabulary.js';

    /**
     * Initialize the enqueue of styles and scripts.
     *
     * @author      @CharalamposTheodorou
     * @since       1.0
     */
    public function register()
    {
        self::setAdminEnqueue();
    }

    /**
     * Call the Admin WordPress enqueue hook.
     *
     * @author      @CharalamposTheodorou
     * @since       1.0
     */
    public function setAdminEnqueue()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueueAdmin'));
    }

     /**
     * Enqueues all scripts and css files for the plugin.
     *
     * @author      @CharalamposTheodorou
     * @since       1.0
     */
    public function enqueueAdmin()
    {
        //CSS enqueue
        wp_enqueue_style(self::STYLE_NAME,$this->plugin_url . self::DIRECTORY_CSS . self::STYLE_NAME);
        
        //JS enqueue
        wp_enqueue_script("jQuery-validation", '//ajax.aspnetcdn.com/ajax/jquery.validate/1.7/jquery.validate.min.js', array('jquery'), 0.1, false);
        wp_enqueue_script(self::SCRIPT_NAME, $this->plugin_url .self::DIRECTORY_JS . self::SCRIPT_NAME);
        //in order to handle the request given from client side we have to localize the script that all requests will be handled
        // by the global varible scriptsURL that the apropriate classes and methods will be registered to that.
        wp_localize_script( self::SCRIPT_NAME, 'scriptsURL',array('ajax'=> admin_url('admin-ajax.php')));
        
    }
}   