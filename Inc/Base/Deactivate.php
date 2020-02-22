<?php

/**
 * Deactivation class for the plugin
 * 
 * @author          @CharalamposTheodorou
 * @since           1.0
 * 
 * @package         AlexaVocabularyExport
 */



class Deactivate {
    
    public function __construct()
    {
        $this->deactivate();
    }
    public static function deactivate()
    {
        flush_rewrite_rules();
    }
}
new Deactivate();