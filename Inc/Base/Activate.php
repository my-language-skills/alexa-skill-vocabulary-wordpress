<?php

/**
 * Activation class for the plugin
 * 
 * @author          @CharalamposTheodorou
 * @since           1.0
 * 
 * @package         AlexaVocabularyExport
 */


class Activate {
    public function __construct()
    {
        $this->activate();
    }
    
    public static function activate()
    {
        flush_rewrite_rules();
    }
}
new Activate();