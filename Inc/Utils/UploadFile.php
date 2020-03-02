<?php 
/**
 * 
 * Responsible to listen for the uploaded file post request.
 * Stores the file in the /uploads/alexa-vocabulary-export directory.
 * 
 * 
 * @package AlexaVocabularyExport
 */

//waits for the upload form submition
if (isset($_POST['ave_upload_btn']))
{
    //if file with correct input name is found then begin upload process
    if ($_FILES[AdminExportPage::EXP_INPUT_CSV])
    {
        
        //get the array for the uploaded file information.
        $uploadedfile = $_FILES[AdminExportPage::EXP_INPUT_CSV];
        
        //path to upload the file
        $path = BaseController::getUploadsFolderPath();
        
        //retrieve the contents of the file to upload from php temp upload page.
        $csv_data=file_get_contents($uploadedfile['tmp_name']);

        //uploading the csv contents to the new file created in the given directory.
        ExportFunctions::uploadFileToUploads($path,$uploadedfile['name'],$csv_data).':';
    }
}