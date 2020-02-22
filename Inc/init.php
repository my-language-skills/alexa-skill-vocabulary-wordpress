<?php

/**
 * Initial class of the plugin.
 * Initiates all other following classes.
 * 
 * @author @CharalamposTheodorou
 * @since 1.0
 * 
 * @package           AlexaVocabularyExport
 */

final class Init
{

    public function __construct()
    {
        require_once dirname(__FILE__) . '/Base/Enqueue.php';
        require_once dirname(__FILE__) . '/Page/AdminPage.php';
        $this->register_services();
    }
    /**
     * Stores all classes that are used inside an array.
     *
     * @author      @CharalamposTheodorou
     * @since       1.0
     *
     * @return array Full list of classes
     */
    public static function get_services() {
        return array(
            ExportPage::class,
        );
    }

    /**
     * Loops through all classes, initialize them,
     * and calls the register() method if it exists.
     *
     * @author      @CharalamposTheodorou
     * @since       1.0
     */
    public static function register_services() {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    /**
     * Initializes all class.
     *
     * @author      @CharalamposTheodorou
     * @since       1.0
     *
     * @param class $class class form services array
     *
     * @return class instance   new instance of the class
     */
    private static function instantiate($class) {
        return new $class();
    }
}
new Init();