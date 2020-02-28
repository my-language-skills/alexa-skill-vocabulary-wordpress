<?php

/**
 * Deactivation class for the plugin
 * 
 * @author          @CharalamposTheodorou
 * @since           1.0
 * 
 * @package AlexaVocabularyExport
 */



class Deactivate {

    /**
     * Constructor method for this class
     *
     * @author      @CharalamposTheodorou
     * @since       1.0
     *
     */
    public function __construct()
    {
        $this->deactivatePlugin();
    }

    /**
     * Deactivation method for the plugin.
     *
     * @author      @CharalamposTheodorou
     * @since       1.0
     *
     */
    public static function deactivatePlugin()
    {
        flush_rewrite_rules();
    }
}