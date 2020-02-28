<?php 
/**
 * This class is responsible to catch all the ajax POST requests from the client.
 * Checks for appropriate input file type and format.
 * Updates the plugin option for the new csv file uploaded.(current_file_uploaded)
 * Starts the process for the exportation of the csv file to JSON format.
 * 
 * @package AlexaVocabularyExport
 */

final class AjaxResponse extends BaseController 
{
    /** MESSAGES RESPONSES */
    const FILE_NOT_CSV = "File uploaded is not in correct format. ";
    const FILE_TYPE_OKAY = " is accepted. ";
    const OPTION_UPDATE = "'s value in options is updated to ";
    const ERROR_EXPORT = "File not correctly exported. Something wrong with the uploaded csv file. ";
    const AJAX_NO_POST = "The request was not containing the necessary data. ";
    const SUCCESSFULL = "Exportation to JSON is successful. ";
    const DELETE_ERROR = "Can not delete the file. Something wrong with the location";
    const DELETE_SUCC = "File succesfully removed. ";
    /** FORMATS */
    const FILE_FORMAT = "File must be in a '.csv' extension or to have type of '".ExportFunctions::INPUT_FILE_TYPES[0]."'. ";

    /**
     * Takes an array (default value is null) and updates the fields of the returned array so it will
     * match for the appropriate respone object to client side.
     * 
     * @author          @CharalamposTheodorou
     * @since           1.0
     * 
     * @param  Array    $args           Array of arguments to return as the request response. Default value is empty
     * 
     * @return Array    $ajax_response  Array of configured values from given array.
     */
    public function create_response($args = null )
    {
        $ajax_response = Array(
            "proceed" => $args['proceed'] ? $args['proceed'] : '', //current test successful or not
            "error" => $args['error'] ? $args['error'] : '', //error messages to test at front end
            "display_error" => $args['display_error'] ? $args['display_error'] :'', //true or false to show errors
            "data" => $args['data'] ? $args['data'] : '', //data to transfer to front end
            "message" => $args['message'] ? $args['message'] :'', //string response for the user
            "display_message" => $args['display_message'] ? $args['display_message'] :'', // true or false to show the message
        );
        return $ajax_response;
        wp_die();
    }

   /**
     * This function handles the request to remove the csv file stored in "/uploads/alexa-vocabulary-export" folder.
     * File name is given through the $_POST global variable.
     * 
     * @author          @CharalamposTheodorou
     * @since           1.0
     * 
     */
    public function ajaxRemoveFile()
    {
        $args = Array();
        //if given the appropriate data in request body.
        if (isset($_POST['file_name']) && !empty($_POST['file_name']))
        {   //returns true if the deletion of the file was successful
            if (ExportFunctions::removePreviousFile(parent::getUploadsFolderPath()))
            {
                $args['message'] = self::DELETE_SUCC;
                $args['display_message'] = true;
                $args['proceed'] = true;
            }
            else
            {
                $args['error'] = self::DELETE_ERROR;
                $args['display_error'] = true;
            }
        }
        else
        {
            $args['error'] = self::AJAX_NO_POST;
            $args['display_error'] = true;
        }
        //response to client side.
        print_r(json_encode(self::create_response($args),JSON_UNESCAPED_SLASHES));
        wp_die();
    }
    
    /**
     * This function handles the request to check the file selected for upload.
     * Checks for the ".csv" extension and a specific file type.
     * File name and File type are given through the $_POST global variable
     * 
     * @author          @CharalamposTheodorou
     * @since           1.0
     * 
     */
    public function ajaxCheckInputFile()
    {
        //to create the new response for this check
        $args = Array();
        //if given the appropriate data in request body.
        if (isset($_POST['file_type']) && !empty($_POST['file_type']) && isset($_POST['file_name']) && !empty($_POST['file_name']))
            if (ExportFunctions::checkInputType($_POST['file_type'],$_POST['file_name']))
            {//returns true if all checks on file type are successful.
                $args['message'] = $_POST['file_name'].self::FILE_TYPE_OKAY;
                $args['display_message'] = true;
                $args['proceed'] = true;
                
                
            }
            else
            {
                $args['error'] = self::FILE_NOT_CSV.self::FILE_FORMAT;
                $args['display_error'] = true;
            }
        else
        {
            $args['error'] = self::AJAX_NO_POST;
            $args['display_error'] = true;
        }
        //response to client side.
        print_r(json_encode(self::create_response($args),JSON_UNESCAPED_SLASHES));
        wp_die();
    }

    /**
     * This function handles the request to export the uploaded csv file to a new JSON-format file
     * with .js extension that will be used for the alexa skill as content file.
     * 
     * @author          @CharalamposTheodorou
     * @since           1.0
     * 
     */
    public function ajaxExportJsonFile()
    {
        $args = Array();
        //if given the appropriate data in request body.
        if (isset($_POST['file_name']) && !empty($_POST['file_name']))
        {
            //gets the .csv file path
            $file_path = parent::getUploadsFolderPath().$_POST['file_name'];
            //this function returns the transformed contents of the .csv file in json format.
            $export_response = ExportFunctions::controlProcess($file_path);
            //check if returned export value is correctly configured.
            if ($export_response)
            {
                $args['data'] =  $export_response;
                $args['proceed'] = true;
                $args['message'] = self::SUCCESSFULL;
            }
            else
            {
                $args['error'] =  self::ERROR_EXPORT;
                $args['display_error'] = true;
            }
            
        }
        //response to client side.
        print_r(json_encode(self::create_response($args),JSON_UNESCAPED_SLASHES));
        wp_die();
    }
    
    /**
     * This function handles the request to update the settings options for this plugin.
     * Updates the value of the "current_uploaded_file".
     * 
     * @author          @CharalamposTheodorou
     * @since           1.0
     * 
     */
    public function ajaxUpdateCurrentUploadField()
    {
        //to create the new response for this check
        $args = Array();
        //if given the appropriate data in request body.
        if (isset($_POST['value']) && !empty($_POST['value']))
        {
            //gets the option array from database
            $settings_options = get_option(AdminExportPage::OPTION_NAME);
            if ($settings_options)
            {//option is loaded correctly. proceed to update.
                //updates the values and sanitizes everything.
                 $options = array(
                    AdminExportPage::EXP_CUR_NAME => sanitize_text_field($_POST['value'])
                );
                $args['message'] = $_POST['field'].self::OPTION_UPDATE.$_POST['value'];
                $args['display_message'] = true;
                $args['proceed'] = true;
                //updates the value of the settings opitons.
                update_option(AdminExportPage::OPTION_NAME,$options);
            }
        }
        else
        {
            $args['error'] = self::AJAX_NO_POST;
            $args['display_error'] = true;
        }
        //response to client side.        
        print_r(json_encode(self::create_response($args),JSON_UNESCAPED_SLASHES));
        wp_die();
    }
}