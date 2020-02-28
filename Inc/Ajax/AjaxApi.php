<?php
/**
 * This class is responsible to add actions to all functions
 * and to create the classes for the ajax responses.
 * 
 * @author          @CharalamposTheodorou
 * @since           1.0
 * 
 * @package AlexaVocabularyExport
 */

 class AjaxApi extends BaseController
 {
    /**
     * Constructor method of the AjaxApi class.
     * 
     * @author          @CharalamposTheodorou
     * @since           1.0
     * 
     * 
     */
    public function __construct()
    {
        require_once dirname(__FILE__) . '/AjaxResponse.php';
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
            AjaxResponse::class,
        );
    }

    /**
     * Loops through all classes, initialize them,
     * and calls the register() method if it exists.
     *
     * @author      @CharalamposTheodorou
     * @since       1.0
     * 
     */
    public static function register_services() {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);
            $class_methods = get_class_methods($service);
            foreach ($class_methods as $method_name) {
                add_action("wp_ajax_$method_name", array($service, $method_name));
                add_action("wp_ajax_nopriv_$method_name", array($service, $method_name));
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