<?php

/**
 * Initial class of the plugin.
 * Initiates all other following classes.
 * 
 * @author @CharalamposTheodorou
 * @since 1.0
 * 
 * @package AlexaVocabularyExport
 */

final class Init
{

    /**
     * Constructor for this class.
     * Requires all necessary .php files and classes for the rest of the plugin process
     *
     * @author      @CharalamposTheodorou
     * @since       1.0
     *
     */
    public function __construct()
    {
        require_once dirname(__FILE__) . '/Base/BaseController.php';
        require_once dirname(__FILE__) . '/Page/AdminExportPage.php';
        require_once dirname(__FILE__) . '/Base/AdminEnqueue.php';
        require_once dirname(__FILE__) . '/Functions/ExportFunctions.php';
        require_once dirname(__FILE__) . '/Ajax/AjaxApi.php';
        require_once dirname(__FILE__) . '/Utils/UploadFile.php';
        self::register_services();
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
            BaseController::class,
            AdminEnqueue::class,
            AdminExportPage::class,
            ExportFunctions::class,
            AjaxApi::class,
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