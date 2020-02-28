<?php

/**
 * Activation class for the plugin
 * When plugin is activatated this triggers to create this class and initiate the Admin Page creation.
 * 
 * @author          @CharalamposTheodorou
 * @since           1.0
 * 
 * @package AlexaVocabularyExport
 */


class Activate {
    
    /**
     * Constructor method for this class.
     *
     * @author      @CharalamposTheodorou
     * @since       1.0
     *
     */
    public function __construct()
    {
        $this->activatePlugin();
    }

    /**
     * Inititates the admin menu in settings. 
     * Only available to "administrators"
     *
     * @author      @CharalamposTheodorou
     * @since       1.0
     *
     */
    public static function activatePlugin()
    {
        flush_rewrite_rules();
        AdminExportPage::init();
    }
}