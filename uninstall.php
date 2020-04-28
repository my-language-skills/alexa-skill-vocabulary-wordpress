<?php
/**
 * When Plugin uninstall process starts this is triggered to delete all plugin options from database 
 * and removes all files from the /uploads folder.
 * 
 * @package AlexaVocabularyExport
 */


if (!defined('WP_UNINSTALL_PLUGIN'))
{
    exit;
}

//here we delete the settings option created by the plugin.
delete_site_option('ave_options');
delete_option('ave_options');

require_once dirname(__FILE__) . '/Inc/Base/BaseController.php';
require_once dirname(__FILE__) . '/Inc/Functions/ExportFunctions.php';
//remove csv file from uploads.
ExportFunctions::removePreviousFile(BaseController::getUploadsFolderPath());
//removes directory in uploads
WP_Filesystem_Base::rmdir(BaseController::getUploadsFolderPath());
