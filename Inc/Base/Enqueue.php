<?php
/**
 * This class is responsible to Enqueue all scripts and
 * css files for the plugin.
 * 
 * @author          @CharalamposTheodorou
 * @since           1.0
 * 
 * @package         AlexaVocabularyExport
 */
 
require_once dirname(__FILE__) . '/BaseController.php';
class Enqueue extends BaseController 
{
    const DIRECTORY_CSS = 'assets/css/';
    const DIRECTORY_JS = 'assets/js/';

    const STYLE_NAME = 'alexa_vocabulary.css';
    
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
        
        wp_enqueue_script("jquery");
        wp_register_script(self::SCRIPT_NAME, $this->plugin_url .self::DIRECTORY_JS . self::SCRIPT_NAME);
        wp_localize_script( self::SCRIPT_NAME, 'scriptsURL',array('ajax'=> admin_url('admin-ajax.php')));
        wp_enqueue_script( self::SCRIPT_NAME);
    }
}   