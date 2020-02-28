# alexa-skill-vocabulary-wordpress
Wordpress plugin for creating the content for the Amazon Alexa Skill "Lexicon".


## Description
This plugin creates an admin settings page that is responsible to upload a file in a csv format and gives the option to export it, creating a ".js" file modified in a JSON format that will be used as the content file for the alexa skill.

## Input File Instructions
- File must be in a '.csv' format and to be of type "application/vnd.ms-excel".
- The file's content must be seperated by a semicolon ';' instead of a come ','.
- The plugin checks for special characters that alexa will have an issue with handling. Current configured special characters are:
    - ":" -> for Categories Title this is transformed to "_".
    - "'" -> for all values in the content this is transformed to "\\'" so it can escape when reading the content file.

## Process Walkthrough
- User must select an input file from local storage.
- This is checked if valid type and format.
- After successfully checked, then the user can upload the file.
- After successfully uploading the file, then option for exporting the file is enabled.
- The user will then click the export button and an option to save localy the content file will appear on screen.
- This walkthrough is when the user goes through the process for the first time.
- In later attempts, the file will already be uploaded and the user can proceed with the following options:
    - Remove the file -> Deletes file from /uploads folder.
    - Upload new file -> Replaces the old file with the new one.
    - Export the already uploaded file -> Creates download window for JSON format file.