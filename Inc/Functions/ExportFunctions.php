<?php
  /**
   * This class holds the functions responsible for exporting the csv file to json format.
   * 
   * @package AlexaVocabularyExport
   */

  class ExportFunctions
  {

      /** Category and Subcategory constants **/
      const NEW_CATEGORY = "initiate_category";
      const UPDATE_CATEGORY = "update_category";
      const NEW_SUBCATEGORY = "inititate_subcategory";
      const UPDATE_SUBCATEGORY = "update_subcategory";
      
      
      /** Language constants **/
      const ENGLISH = "english";
      const SPANISH = "spanish";
      const ITALIAN = "italian";
      const GERMAN = "german";
      const FRENCH = "french";

      /** INPUT FILE TYPE constants */
      const INPUT_FILE_TYPES = ["application/vnd.ms-excel"];

      /**
       * Starting method of the class/export process
       * Inititates all variables used.
       *
       * @author      @CharalamposTheodorou
       * @since       1.0
       *
       * @param    string   file_name   Name of csv file to export
       * @return   object               The encoded JSON format or an error message.
       */
      public static function controlProcess($file_name)
      {
        
        $sections =  Array(
          'serial_code' ,
          'word_code' ,
          'line_no' ,
          'index',
          'valid' ,
          'spanish_word' ,
          'spanish_phrase' ,
          'english_word' ,
          'english_phrase' ,
          'italian_word' ,
          'italian_phrase' ,
          'french_word' ,
          'french_phrase' ,
          'german_word' ,
          'german_phrase' 
        );

        $categories = Array();
        $categories_info = Array();
        //call to exportValue function with necessary parameters for handling the csv file and the transformation
        return ExportFunctions::exportValue($file_name,$sections,$categories,$categories_info);
      
      }

      /**
       * Takes as parameters the sections,categories,categories_info arrays and the file name.
       * Produces the json output that will be stored on the new content file.
       *
       * @author      @CharalamposTheodorou
       * @since       1.0
       *
       * @param   string file_name   Name of csv file to export
       * @param   array  sections    Array that handles the information for each line of the csv file
       * @param   array  categories  Empty array that will hold the information for all categories' subcategories.
       * @param   array  subcategories  Empty array that will hold the information for all categories.
       * 
       * @return  object encoded json format of the csv information.
       */
      public function exportValue($file_name = null,$sections,$categories,$categories_info)
      {
          $cat_index=0;
          $sub_index=0;
          $sub_sub_index =0;
          $cat_current;
          $sub_current;
          $sub_sub_current;
          $new_entry_category = Array();
          $new_entry_subcategory = Array();
          
          if (($handle = fopen($file_name, "r")) !== FALSE) 
          {
            while (($data = fgetcsv($handle, 1000, "\n")) !== FALSE) 
            {
              
              //creates the line array, keys are created before from sections and values from the csv line.
              $line_info= array_combine($sections,explode(";",$data[0]));
              //valid is only true when the line contains a valid example of data.
              $line_info['valid'] = $line_info['valid'] == "FALSO" ? "" : "TRUE";

              //serial_code exists in every row, except from the titles' line.
              if (is_numeric($line_info['serial_code']) || empty($line_info['serial_code']))
              {//not first line, headers
                if (empty($line_info['word_code']))
                {//category or subcategory title found
                  //break the index string into an array of positions
                  $index = explode(".",$line_info['index']);

                  //index shows when category or subcategory changes.
                  if (!empty($index[0]) && $index[0]>$cat_index)
                  {//category changed.. new category found
                    //create new entry for a new category.

                      $sub_index=0;
                      $sub_sub_index=0;
                      //new entry for a new category:
                      $new_entry_category = self::populateValues($line_info,self::NEW_CATEGORY);
                      if ($new_entry_category)
                      {//retyrned new entry for categories_info successfull. updating values.
                        $cat_index++;
                        $cat_current = $line_info['english_word'];
                        $categories_info[$cat_current]=$new_entry_category;

                        //need to add a new category array in $categories.
                        $categories[strtoupper($cat_current)] = Array();
                      }
                  }
                  else if(!empty($index[1]) && $index[1]>$sub_index)
                  {//new subcategory found
                      
                      $sub_index++;
                      $sub_sub_index=0;
                      //new entry for subcategory
                      //add new value to category entries and to category entity.

                      //updating value of categories_info array.
                      $update_entry = self::populateValues($line_info,self::UPDATE_CATEGORY,$categories_info[$cat_current]);
                      if($update_entry)
                      {//updating correctly.
                        $categories_info[$cat_current] = $update_entry;
                      }

                      //creating the subcategory info
                      $new_entry = self::populateValues($line_info,self::NEW_SUBCATEGORY);
                      if ($new_entry)
                      {//updating correctly.
                        $sub_current = $line_info['english_word'];
                        $categories[$cat_current][$sub_current] = $new_entry;
                      }
                      
                  }
                  else if (!empty($index[2]) && $index[2]>$sub_sub_index)
                  {//sub-subcategory found, treated as new subcategory
                      $sub_sub_index++;
                      
                      //new entry for subcategory
                      //add new value to category entries and to category entity.
                      $update_entry = self::populateValues($line_info,self::UPDATE_CATEGORY,$categories_info[$cat_current]);
                      if($update_entry)
                      {//updating correctly.
                        $categories_info[$cat_current] = $update_entry;
                      }

                      //creating the subcategory info
                      $new_entry = self::populateValues($line_info,self::NEW_SUBCATEGORY);
                      if ($new_entry)
                      {//updating correctly.
                        $sub_current = $line_info['english_word'];
                        $categories[$cat_current][$sub_current] = $new_entry;
                      }
                  }
                }
                else
                {//line of example here
                  if ($line_info['valid'] == "TRUE" )
                  {//when valid means that that example is checked and is okay for use.
                    $new_entry = self::populateValues($line_info,self::UPDATE_SUBCATEGORY,$categories[$cat_current][$sub_current]);
                    if ($new_entry)
                    {//valid to proceed.
                      $categories[$cat_current][$sub_current]=$new_entry;
                    }
                  }
                }

              }
            }
            fclose($handle);
            //to create the correct json format for all fields.
            $jsonFormat = ExportFunctions::returnJsonFormat($categories,$categories_info); 
            return $jsonFormat;
          }
          else
            return false;
          
          
      }

      /**
       * Uses the variables of categories_info and categories array to create the final content file.
       * After creation, the content file is transfered to front-end service to provide the download link 
       * of the final json file.
       * 
       * @author      @CharalamposTheodorou
       * @since       1.0
       *
       * @param   array  categories  Empty array that will hold the information for all categories' subcategories.
       * @param   array  subcategories  Empty array that will hold the information for all categories.
       * 
       * @return  object encoded json format of the csv information.
       * 
       */
      public function returnJsonFormat($categories,$categories_info)
      {
        $content['CONTENT']['CATEGORIES_INFO']=$categories_info;
        
        foreach ($categories as $key => $category)
        {//creating the categories sections that holds all information about their subcategories.
          if (!empty($category))
            $content['CONTENT'][str_replace(' ','_',$key)] = $category;
        }
        //encoding the array into a json format.
        $contentJson = json_encode($content);

        return $contentJson;
      }

      /**
       * Uses the variables of categories_info and categories array to create the final content file.
       * After creation, the content file is transfered to front-end service to provide the download link 
       * of the final json file.
       * 
       * @author      @CharalamposTheodorou
       * @since       1.0
       *
       * @param   string  path          Path to the folder that the file will be stored.
       * @param   string  file_name     Name of the file to stored
       * @param   object  uploadedfile  Contents to store to the new file
       *   
       * @return  boolean               True or false after storing and closing the file
       * 
       */
      public function uploadFileToUploads($path,$file_name,$uploadedfile)
      {
        //removes the previous .csv files from the /uploads/alexa-vocabulary-export folder
        ExportFunctions::removePreviousFile($path);

        $file = fopen($path.$file_name,"w");
        fwrite($file,$uploadedfile);

        return fclose($file.$file_name);
      }

      /**
       * Removes previous .csv files from the directory.
       * 
       * @author      @CharalamposTheodorou
       * @since       1.0
       *
       * @param   string  path          Path to the folder that the previous csv files will be removed/replaced.
       *   
       * @return  boolean               True or false if successful in deleting the files from the directory
       * 
       */
      public function removePreviousFile($path)
      {
        $filecount = 0;
        $files = glob($path . "*.csv");
        foreach($files as $file)
          return unlink($file);
      }

      /**
       * Takes the information of the current line from the csv file and according to the
       * option given, it populates a new entry for a new category or subcategory.
       * All checks for validation of the information is done here.
       * 
       * @author      Charalampos Theodorou
       * @since       1.0
       * 
       * @param   array   line_info   Holds all the information for a single line in the csv file.
       * @param   string  option      Which action to proceed with the new entry to the object created.
       * @param   array   update_info Array that holds the previous (empty at first) information of a category or subcategory.
       * 
       * @return  array               Array with updated information of a section (category/subcategory).
       */
      public function populateValues($line_info,$option,$update_info = [])
      {

        if (!empty($option) && $option == self::NEW_CATEGORY)
        {
          $new_entry = Array(
            'english_title' =>  self::provideSSMLtags($line_info['english_word'],self::ENGLISH),
            'spanish_title' => self::provideSSMLtags($line_info['spanish_word'],self::SPANISH),
            'german_title' => self::provideSSMLtags($line_info['german_word'],self::GERMAN),
            'french_title' => self::provideSSMLtags($line_info['french_word'],self::FRENCH),
            'italian_title' => self::provideSSMLtags($line_info['italian_word'],self::ITALIAN),
            'english_sub' => Array(),
            'spanish_sub' => Array(),
            'german_sub' => Array(),
            'french_sub' => Array(),
            'italian_sub' => Array()
          );
          
          return $new_entry;
        }
        else if (!empty($option) && $option == self::NEW_SUBCATEGORY)
        {
          $new_entry = Array(
            'english_title' => self::provideSSMLtags($line_info['english_word'],self::ENGLISH),
            'spanish_title' => self::provideSSMLtags($line_info['spanish_word'],self::SPANISH),
            'german_title' => self::provideSSMLtags($line_info['german_word'],self::GERMAN),
            'french_title' => self::provideSSMLtags($line_info['french_word'],self::FRENCH),
            'italian_title' => self::provideSSMLtags($line_info['italian_word'],self::ITALIAN),
            'english_word' => Array(),
            'spanish_word' => Array(),
            'german_word' => Array(),
            'french_word' => Array(),
            'italian_word' => Array(),
            'english_phrase' => Array(),
            'spanish_phrase' => Array(),
            'german_phrase' => Array(),
            'french_phrase' => Array(),
            'italian_phrase' => Array()
          );
          return $new_entry;
        }
        else if(!empty($option) && $option== self::UPDATE_CATEGORY)
        {//category created before, enter new values: at subcategory arrays.
          if (!empty($update_info))
          {//Array of data to update with new info
            array_push($update_info['english_sub'],self::provideSSMLtags($line_info['english_word'],self::ENGLISH));
            array_push($update_info['spanish_sub'],self::provideSSMLtags($line_info['spanish_word'],self::SPANISH));
            array_push($update_info['german_sub'],self::provideSSMLtags($line_info['german_word'],self::GERMAN));
            array_push($update_info['italian_sub'],self::provideSSMLtags($line_info['italian_word'],self::ITALIAN));
            array_push($update_info['french_sub'],self::provideSSMLtags($line_info['french_word'],self::FRENCH));

            return $update_info;
          }
          else
            return "Empty data";
        }
        else if (!empty($option) && $option == self::UPDATE_SUBCATEGORY)
        {//subcategory created before, enter new values: at subcategory arrays.
          if (!empty($update_info))
          {//Array of data that will be updated on subcategory object
            array_push($update_info['english_word'],self::provideSSMLtags($line_info['english_word'],self::ENGLISH));
            array_push($update_info['english_phrase'],self::provideSSMLtags($line_info['english_phrase'],self::ENGLISH));
            array_push($update_info['spanish_word'],self::provideSSMLtags($line_info['spanish_word'],self::SPANISH));
            array_push($update_info['spanish_phrase'],self::provideSSMLtags($line_info['spanish_phrase'],self::SPANISH));
            array_push($update_info['german_word'],self::provideSSMLtags($line_info['german_word'],self::GERMAN));
            array_push($update_info['german_phrase'],self::provideSSMLtags($line_info['german_phrase'],self::GERMAN));
            array_push($update_info['italian_word'],self::provideSSMLtags($line_info['italian_word'],self::ITALIAN));
            array_push($update_info['italian_phrase'],self::provideSSMLtags($line_info['italian_phrase'],self::ITALIAN));
            array_push($update_info['french_word'],self::provideSSMLtags($line_info['french_word'],self::FRENCH));
            array_push($update_info['french_phrase'],self::provideSSMLtags($line_info['french_phrase'],self::FRENCH));
            
            return $update_info;
          }
        }
        else
        {
          //option given wrong
          return false;
        }
          
      }

      /**
       * Takes language selection and text and returns ssml tag for language.
       * 
       * @author     Charalampos Theodorou 
       * @since      1.0
       * 
       * @param   string    response  The text to add the tags.
       * @param   string    language  Selection for language to add appropriate tags
       * 
       * @return  string              Updated string with tags.
       */
      public function provideSSMLtags($response,$language)
      {
        $ssml_text;
        if ($language == self::ENGLISH)
        {
            $ssml_text = '<lang xml:lang="en-US">'.$response.'</lang>';
        }
        elseif ($language == self::SPANISH)
        {
          $ssml_text = '<lang xml:lang="es-ES">'.$response.'</lang>';
        }
        elseif ($language == self::GERMAN)
        {
          $ssml_text = '<lang xml:lang="de-DE">'.$response.'</lang>';
        }
        elseif ($language == self::FRENCH)
        {
          $ssml_text = '<lang xml:lang="fr-FR">'.$response.'</lang>';
        }
        elseif ($language == self::ITALIAN)
        {
          $ssml_text = '<lang xml:lang="it-IT">'.$response.'</lang>';
        }
        return $ssml_text;
      }

      /**
       * Does necessary checks on input file to see if valid for upload.
       * 
       * @author     Charalampos Theodorou 
       * @since      1.0
       * 
       * @param   string    file_type Type of input file
       * @param   string    file_name Name of input file
       * 
       * @return  boolean             True or false if input file is appropriate for use.
       */
      public function checkInputType($file_type,$file_name)
      {
        foreach (self::INPUT_FILE_TYPES AS $type)
          if ( ($file_type == $type) && (strpos($file_name,".csv")!==false))
            return true;
        
        return false;
      }
  }

?>