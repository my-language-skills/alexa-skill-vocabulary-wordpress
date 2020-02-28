jQuery(function(event)
{
    /**
     * @description Here's wrap the code that permit to simplify an ajax call.
     *
     * @author     Charalampos Theodorou 
     * @since      1.0
     * @param {array} data, that will send with the ajax call.
     * @param {function} func, that will be execute after the success of the ajax call,
     */
    var ajaxCall = function (data, func) {
        jQuery.post(
            scriptsURL.ajax,
            data)
            .done(
                function (response) {
                    func(response);
                }
            )
            .fail(
                function (xhr, textStatus, errorThrown) {
                    console.log(xhr.responseText);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            );
    }

    var csv_file_name;
    var file_uploaded;
    //upload button and export should be disabled at first. After something is uploaded correctly.
    jQuery('#upload_btn').prop('disabled',true);
    jQuery('#export_btn').prop('disabled',true);
    if (jQuery('#current_upload_field').val() != "No file uploaded")
        jQuery('#export_btn').prop('disabled',false);
    var upload_clicked = false;
    var export_clicked = false;

    /**
     * @description Event listener for upload button on click.
     *
     * @author     Charalampos Theodorou 
     * @since      1.0
     */
    jQuery('#upload_btn').on("click",function()
    {
        upload_clicked = true;
    });

    /**
     * @description Event listener for the remove file button
     *
     * @author     Charalampos Theodorou 
     * @since      1.0
     */
    jQuery('input[type="button"]').on("click",function()
    {
        if (jQuery('#current_upload_field').val() == "No file uploaded")
        {
            jQuery('#file-upload-response').html("Remove what? Nothing is uploaded.");
        }
        else
        {
            var reply = function(response)
            {
                var data = JSON.parse(response);
                if (data.proceed)
                {
                    jQuery('#file-upload-response').html(data.message);
                    jQuery('#current_upload_field').val('No file uploaded');
                    var reply_nested = function(response)
                    {//response from the request to update the settings options.
                        var response = JSON.parse(response);
                    }
                    var data = {
                        'action': "ajaxUpdateCurrentUploadField",
                        'value': 'No file uploaded',
                    };
                    //ajax request to remove the current uploaded field from the settings options.
                    ajaxCall(data,reply_nested);
                }
                else
                {//error triggered here
                    jQuery('#file-upload-response').html(data.error);
                }
            }
            var data ={
                'action':'ajaxRemoveFile',
                'file name' :jQuery('#current_upload_field').val(),
            };
            //ajax request to remove the current uploaded csv file
            ajaxCall(data,reply);
        }
    });

    /**
     * @description Event listener for export button on click.
     *
     * @author     Charalampos Theodorou 
     * @since      1.0
     */
    jQuery('#export_btn').on("click",function()
    {
        export_clicked = true;
    });

    /**
     * @description Event listener for form submition on admin settings page
     *
     * @author     Charalampos Theodorou 
     * @since      1.0
     */
    jQuery('form').on("submit",function(response)
    {   //upload button clicked
        if (upload_clicked)
        {//update the current uploaded field
            var reply = function(response)
            {   
                var response = JSON.parse(response);
                if (response.proceed)
                {//successfull update of the field.
                    jQuery('#current_upload_field').prop('value',csv_file_name);
                }
                else
                {//error triggered
                    jQuery('#file-upload-response').html(data.error);
                }
            }
            var data = {
                'action': "ajaxUpdateCurrentUploadField",
                'value': csv_file_name,
            };
            //ajax call to update options
            ajaxCall(data,reply);
        }//export button clicked
        else if (export_clicked)
        {
            jQuery('#file-upload-response').html("Please wait a few seconds until the file is ready");
            var reply = function(response)
            {
                var data = JSON.parse(response);
                if (data.proceed && data.data!=false)
                {//moving to exportation function..
                    jQuery('#file-upload-response').html(data.message);
                    exportFunction(data.data);
                }
                else
                {//error triggered
                    jQuery('#file-upload-response').html(data.error);
                }
            }
            var data = {
                'action' : "ajaxExportJsonFile",
                'file name' : jQuery('#current_upload_field').val(),
            };
            //ajax request to start the exportation proces of the csv file.
            ajaxCall(data,reply);
            //this will prevent to submit the form and refresh the page.
            //this allows for the process to finish before page refreshes.
            return false;
        }
    });
    
    /**
     * @description Event listener for file input field on change
     *
     * @author     Charalampos Theodorou 
     * @since      1.0
     */
    jQuery('input[type="file"]').on("change",function(e)
    {
        //checking if name exists => file exists.
        if (e.target.files[0].name)
        {   
            file_uploaded = e.target.files[0];
            var reply = function(response)
            {
                var ajax_response = JSON.parse(response);
                if (ajax_response.proceed)
                {//valid to continue. test successful
                    jQuery('#file-upload-response').html(ajax_response.message);
                    jQuery('#upload_btn').prop('disabled',false);
                }   
                else
                {//something wrong with file
                    jQuery('#file-upload-response').html(ajax_response.error);
                    jQuery('#upload_btn').prop('disabled',true);
                    
                }
            }
            //string of the file name to export
            csv_file_name = e.target.files[0].name;
            //string of the file type to export
            csv_file_type = e.target.files[0].type;

            var data = 
            {
                'action': "ajaxCheckInputFile",
                'file_type': csv_file_type,
                'file_name': csv_file_name,
            };
            //ajax request to check input file
            ajaxCall(data,reply);

        }
        else
        {//file is not found by the event listener. This should never be triggered.
            jQuery('#file-upload-response').html("File Not Found in Upload Field");
        }
        
        
    });

    /**
     * @description Handles the exportation of the transformed data. Creates the correct json format
     *              and stores the new file.
     *
     * @author     Charalampos Theodorou 
     * @since      1.0
     */
    var exportFunction = function(data)
    {
        var content =JSON.parse(data);
        var file_output = "module.exports = \n{\n\tCONTENT:\n\t{"; 
        //restructures the info in an array for better use of keys and values.
        var full_content_array = extract_array_info(content.CONTENT);
        //for each array key -> category level.
        full_content_array.forEach(function(content_array_section)
        {   
            //escape the string from the special characters
            file_output += "\n\t\t"+escape_characters(content_array_section[0],":")+":\n\t\t[";//name of the section
            //get the info for a specific section -> category information
            var array_section = extract_array_info(content_array_section[1]);   
            array_section.forEach(function(array_section_field)
            {//for each field in a category section.
                file_output += "\n\t\t\t{";
                var array_section_field_data = extract_array_info(array_section_field[1]);
                
                array_section_field_data.forEach(function(data_entry)
                {//for each field in subcategory section.
                    file_output += "\n\t\t\t\t"+data_entry[0]+":\t";
                    
                    if (typeof(data_entry[1]) === "string")
                    {//proceed normally to add the data entry.
                        file_output += "'"+escape_characters(data_entry[1],"'")+"',";
                    }
                    else
                    {//type is of array, need to extract every value
                        var data_entry_array = extract_array_info(data_entry[1]);
                        file_output += "[";
                        data_entry_array.forEach(function(data_entry)
                        {//for each example (word/phrase) in subcategory section
                            file_output += "'"+escape_characters(data_entry[1],"'")+"',";
                        });
                        file_output +="],";
                    }
                });
                file_output += "\n\t\t\t},";
            }); 

            file_output += "\n\t\t],";
        });
        
        file_output += "\n\t}\n}";
        
        //necessary code to create the new file to store the json content.
        var dataStr = "data:text/js;charset=utf-8," +encodeURIComponent(file_output);
    
        var downloadAnchor = document.createElement('a');
        downloadAnchor.setAttribute("href", dataStr);
        downloadAnchor.setAttribute("download","content.js");
        document.body.appendChild(downloadAnchor);
        downloadAnchor.click();
        downloadAnchor.remove();       
    }

    /**
     * @description takes an array of information and reforms it for later use.
     *
     * @author     Charalampos Theodorou 
     * @since      1.0
     * 
     * @param   {array} content array of info
     * 
     * @return  {array}         array of restructed keys and values.
     */
    var extract_array_info = function(content)
    {
        var content_array = Object.keys(content).map(function(key)
        {
            return [key,content[key]];
        });
        return content_array;
    }

    /**
     * @description takes an array of information and reforms it for later use.
     *
     * @author     Charalampos Theodorou 
     * @since      1.0
     * 
     * @param   {string}    data_string    string value that will be checked for special characters
     * @param   {string}    special_char   string that contains the character to replace.
     * @return  {string}                   string value with replaced characters 
     */
    var escape_characters = function(data_string,special_char)
    {
        if (special_char == ":")
            data_string = data_string.replace(/:/g,"_");
        else if (special_char == "'")
            data_string = data_string.replace(/'/g,"\\'");
        return data_string;
    }
    
});