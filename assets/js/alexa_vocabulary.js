jQuery(function(jQuery)
{
    jQuery('#btn_export').on("click",function()
    {
        var data = {
            'action': "testFunc"
        };
        var export_values = function(response)
        {
            var content =JSON.parse(response);

            var file_output = "module.exports = \n{\n\tCONTENT:\n\t{"; 
           
            var full_content_array = extract_array_info(content.CONTENT);
            full_content_array.forEach(function(content_array_section)
            {
                file_output += "\n\t\t"+content_array_section[0]+":\n\t\t[";//name of the section
                //current array_section
                var array_section = extract_array_info(content_array_section[1]);   
                array_section.forEach(function(array_section_field)
                {
                    file_output += "\n\t\t\t{";
                    var array_section_field_data = extract_array_info(array_section_field[1]);
                    array_section_field_data.forEach(function(data_entry)
                    {
                        file_output += "\n\t\t\t\t"+data_entry[0]+":\t";
                        
                        if (typeof(data_entry[1]) === "string")
                        {//proceed normally to add the data entry.
                            file_output += "'"+data_entry[1]+"',";
                        }
                        else
                        {//type is of array, need to extract every value
                            var data_entry_array = extract_array_info(data_entry[1]);
                            file_output += "[";
                            data_entry_array.forEach(function(data_entry)
                            {
                                file_output += "'"+escape_characters(data_entry[1])+"',";
                            });
                            file_output +="],";
                        }
                    });
                    file_output += "\n\t\t\t},";
                }); 

                file_output += "\n\t\t],";
            });
            
            file_output += "\n\t}\n}";
            
            var dataStr = "data:text/js;charset=utf-8," +encodeURIComponent(file_output);
        
            var downloadAnchor = document.createElement('a');
            downloadAnchor.setAttribute("href", dataStr);
            downloadAnchor.setAttribute("download","content.js");
            document.body.appendChild(downloadAnchor);
            downloadAnchor.click();
            downloadAnchor.remove();       
        }
        jQuery.post("functions.php",data)
            .done(
                function(response){
                    export_values(response);
                }
            )
            .fail(
                function(xhr,testStatus,errorThrown){
                    console.log(xhr.responseText);
                        console.log(textStatus);
                        console.log(errorThrown);
                }
            );
        var extract_array_info = function(content)
        {
           var content_array = Object.keys(content).map(function(key)
           {
                return [key,content[key]];
           });
           return content_array;
        }

        var escape_characters = function(data_string)
        {
            while (data_string.includes("'"))
            {
                data_string = data_string.replace("'","APOSTROPHE_HERE");
            }
            while (data_string.includes("'"))
            {
                data_string = data_string.replace("APOSTROPHE_HERE","\'");
            }
            return data_string;
        }
    });
});