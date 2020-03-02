<?php

/**
 * This class creates the admin menu page and initiates and stores the settings options for the plugin.
 * 
 * @author @CharalamposTheodorou
 * @since 1.0
 * 
 * @package AlexaVocabularyExport
 */

class AdminExportPage
{
    //CLASS CONSTS
    const ALLOWED_ROLE = "administrator";
    //TOP LEVEL SETTINGS CONSTS
    const OPTION_GROUP = "option_group";
    const OPTION_NAME = "ave_options";
    //SECTIONS CONSTS
    const SECTION_EXPORT = "export_section";

    //SETTINGS CONSTS
    const PAGE_NAME = "export_settings_page";
    
    //FIELD CONSTS
    const EXP_INPUT_CSV = "csv_input_field";
    const EXP_CUR_NAME = "current_upload_field";
    const EXP_REMOVE_BTN = "remove_button";

    //options var
    private $options;

    /**
     * Constructor method for this class.
     * 
     * @author     Charalampos Theodorou 
     * @since      1.0
     * 
     */
    public function __construct()
    { 
        add_action('admin_init',array($this,'initPage'));
        add_action('admin_menu',array($this,'add_admin_menu'));
    }

    /**
     * Initializes the settings options array
     * 
     * @author     Charalampos Theodorou 
     * @since      1.0
     * 
     */
    public static function init()
    {
        //get the previous values of options array
        $options = get_option(self::OPTION_NAME);
        $exp_cur_name = $options[self::EXP_CUR_NAME];
        //check if any information changed from previous form submit.
        $defaults = array(
            self::EXP_CUR_NAME => $exp_cur_name ? $exp_cur_name : 'No file uploaded',
        );
        //update the options values.
        update_option(self::OPTION_NAME,$defaults);
    }
    
    /**
     * Creates the admin menu page and callback functions for the rest of the process.
     * 
     * @author     Charalampos Theodorou 
     * @since      1.0
     * 
     */
    public function add_admin_menu()
    {
        add_submenu_page(
            'options-general.php',
            'Alexa Vocabulary Export',
            'Alexa Vocabulary Export',
            'manage_options',
            'alexa_vocabulary_export',
            array($this,'page_display'),
            101);
    }
    
    /**
     * This method creates the design of the admin settings page.
     * 
     * @author     Charalampos Theodorou 
     * @since      1.0
     * 
     */
    public function page_display()
    {
        if (!self::userAllowed())
        {
            echo 'This page is not accessable to you bruh';
        }
        else
        {
            //getting the options settings..
            $this->options = get_option(self::OPTION_NAME);
            ?>
            <div class = "wrap">
                <br>
                <form method="post" action="options.php" enctype="multipart/form-data">
                    <div class = "tabcontent">
                    <?php
                    //wordpress functions for creating the options sections fields.
                    settings_fields(self::OPTION_GROUP);
                    do_settings_sections(self::PAGE_NAME);
                    ?>
                    <div id="file-upload-response">
                    </div>
                    <?php
                    //submit button for uploading a file
                    submit_button(__('Upload File','alexa-vocabulary-export'),'button custom_btn','ave_upload_btn');
                    //submit button for exporting a file
                    submit_button(__('Export Json','alexa-vocabulary-export'),'button custom_btn','ave_export_btn');
                    ?> 
                    </div>
                </form>
            </div>
            <?php
        }
    }

    /**
     * Creates the section/page/fields settings
     * 
     * @author     Charalampos Theodorou 
     * @since      1.0
     * 
     */
    public function initPage()
    {
        register_setting(
            self::OPTION_GROUP, //Option group
            self::OPTION_NAME, //Option name
            array($this,'sanitize') //Sanitize
        );

        /* MAIN OPTIONS */
        add_settings_section(
            self::SECTION_EXPORT, // ID
            __('Vocabulary Export Settings','alexa-vocabulary-export'), //Title
            array($this,'displaySectionInfo'), //Callback
            self::PAGE_NAME // Page
        );
        
        /* --> Current Upload Field */
        add_settings_field(
            ''. self::EXP_CUR_NAME . '', //ID
            __('Current Uploaded File:','alexa-vocabulary-export'),//Title
            array($this,'curUploadedCallback'), //Callback
            self::PAGE_NAME, //Page
            self::SECTION_EXPORT //Section
        );

        /* --> Remove Upload Button Field */
        add_settings_field(
            ''. self::EXP_REMOVE_BTN . '', //ID
            __('','alexa-vocabulary-export'), //Title
            array($this,'removeBtnCallback'), //Callback
            self::PAGE_NAME, //Page
            self::SECTION_EXPORT //Section
        );

        /* --> Input Csv Field */
        add_settings_field(
            ''. self::EXP_INPUT_CSV . '', //ID
            __('','alexa-vocabulary-export'), //Title
            array($this,'csvInputCallback'), //Callback
            self::PAGE_NAME, //Page
            self::SECTION_EXPORT // Section
        );
        
    }

    /**
     * Callback function for the remove button field
     * 
     * @author     Charalampos Theodorou 
     * @since      1.0
     * 
     */
    public function removeBtnCallback()
    {
        printf(
            '<input id="%s" type="button" name="%s[%s]" value="%s" />',
            self::EXP_REMOVE_BTN,
            self::OPTION_NAME,
            self::EXP_REMOVE_BTN,
            isset($this->options[self::EXP_REMOVE_BTN]) ? esc_attr($this->options[self::EXP_REMOVE_BTN]): 'Remove File'
        );
    }

    /**
     * Callback function for the current uploaded field
     * 
     * @author     Charalampos Theodorou 
     * @since      1.0
     * 
     */
    public function curUploadedCallback()
    {
        
        printf(
            '<input id="%s" type="text" name="%s[%s]" value="%s" readonly/>',
            self::EXP_CUR_NAME,
            self::OPTION_NAME,
            self::EXP_CUR_NAME,
            isset($this->options[self::EXP_CUR_NAME]) ? esc_attr($this->options[self::EXP_CUR_NAME]): ''
        );
    }

    /**
     * Callback function for the file input field
     * 
     * @author     Charalampos Theodorou 
     * @since      1.0
     * 
     */
    public function csvInputCallback()
    {
        printf(
            '<input id="%s" type="file" name="%s" value="%s"/>',
            self::EXP_INPUT_CSV,
            self::EXP_INPUT_CSV,
            isset($this->options[self::EXP_INPUT_CSV]) ? esc_attr($this->options[self::EXP_INPUT_CSV]) : ''
        );
    }
    
    /**
     * Callback function for displaying the section fields
     * 
     * @author     Charalampos Theodorou 
     * @since      1.0
     * 
     */
    public function displaySectionInfo()
    {
        _e(' Upload the csv file of the vocabulary level you want to export to Json format.','alexa-vocabulary-export');
        _e(' This is used as the content file for the alexa skill "English Vocabulary"','alexa-vocabulary-export');
    }

    
    /**
     * Sanitization function for the section fields
     * 
     * @author     Charalampos Theodorou 
     * @since      1.0
     * 
     * @return     array    Sanitized options array
     */
    public function sanitize($input)
    {
        $new_input = array();
        
        if (isset($input[self::EXP_CUR_NAME]))
            $new_input[self::EXP_CUR_NAME] = 
                sanitize_text_field( 
                    $input[self::EXP_CUR_NAME] && $input[self::EXP_CUR_NAME] != '' ?
                        $input[self::EXP_CUR_NAME] : ''
                );   
        return $new_input;
    }

     /**
     * Checks if current logged user has appropriate roles to access this page
     * 
     * @author     Charalampos Theodorou 
     * @since      1.0
     * 
     * @return      boolean     True or false if current user is allowed to view this.
     */
    public function userAllowed()
    {
        $user_id = get_current_user_id();
        $user_meta = get_userdata($user_id);

        //loop through all the roles of the user, if the admin role found then proceed.
        foreach($user_meta->roles AS $role)
        {
            if ($role == self::ALLOWED_ROLE)
                return true;
        }
        return false;
    }
}
