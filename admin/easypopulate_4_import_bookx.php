<?php 
/**
 * Import file for EP4 
 * @EP4Bookx - EP4 CSV fork to import Bookx fields - tested with Zencart 1.5.4
 *
 * @version  0.9.9 - Still in development, make your changes in a local environment
 * @see Bookx module for ZenCart
 * @see Readme-EP4Bookx
 * @author mesnitu
 * @todo  export with support for languages
 * @todo  review the querys to have a standart 
 * @todo improve the querys 
 *
 *$filelayout[] = 'v_bookx_subtitle';      
 *$filelayout[] = 'v_bookx_genre_name';
 *$filelayout[] = 'v_bookx_publisher_name'; // Publisher Name, no lang ID       
 *$filelayout[] = 'v_bookx_series_name'; // Series name, has Lang ID              
 *$filelayout[] = 'v_bookx_imprint'; // 
 *$filelayout[] = 'v_bookx_binding';
 *$filelayout[] = 'v_bookx_printing';
 *$filelayout[] = 'v_bookx_condition';
 *$filelayout[] = 'v_bookx_isbn';
 *$filelayout[] = 'v_bookx_size';
 *$filelayout[] = 'v_bookx_volume';
 *$filelayout[] = 'v_bookx_pages';
 *$filelayout[] = 'v_bookx_publishing_date';        
 *$filelayout[] = 'v_bookx_author_name';
 */

/**
 * Edit link to books with missing fields
 * @var link
 */
$edit_link = "<a href=".zen_href_link('product_bookx.php', 'cPath='.zen_get_product_path($v_products_id).'&product_type='.$bookx_product_type.'&pID='.$v_products_id.'&action=new_product').">".EASYPOPULATE_4_BOOKX_EDIT_LINK."</a>";

//::: BOOKX GENRE
if (isset($filelayout['v_bookx_genre_name'])) {
    if (isset($v_bookx_genre_name)) {
        //$genres_names_array = array(); // Start a empty array for the names
        $genres_names_array = mb_split('\x5e', $items[$filelayout['v_bookx_genre_name']]); // names to array

        $updated_id = array(); // Get the updated id's.   
        $inserted_id = array(); // Get the inserted id's 

        foreach($genres_names_array as $genre_name) {
            // A default genre name is given by user config and warning with a link to edit the book.
            if (($genre_name == '') && (!empty($ep_bookx_fallback_genre_name))) {
                $genre_name = $ep_bookx_fallback_genre_name;
                //@reports
                ($report_bookx_genre_name = false ? $bookx_reports[BOX_CATALOG_PRODUCT_BOOKX_GENRES][] = 'FallBack Genre in : '.sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 60)).' - '.$edit_link : '');
            } // done  
            if (mb_strlen($genre_name) <= $bookx_genre_name_max_len) { // verify each genre name

                $sql = "SELECT bookx_genre_id AS genreID FROM ".TABLE_PRODUCT_BOOKX_GENRES_DESCRIPTION." WHERE genre_description = '".addslashes(ep_4_curly_quotes($genre_name))."'";
                $result = ep_4_query($sql);

                $row_genre = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result));
                $v_genre_id = $row_genre['genreID'];

                if ($row_genre != '') {
                    $sql2 = ep_4_query("UPDATE ".TABLE_PRODUCT_BOOKX_GENRES_DESCRIPTION." SET genre_description = '".addslashes($genre_name)."' WHERE bookx_genre_id = '".$v_genre_id."'");
                    // $result = ep_4_query($sql2);
                } else {
                    $query = "INSERT INTO ".TABLE_PRODUCT_BOOKX_GENRES." (genre_sort_order, date_added, last_modified)
						VALUES ('0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
                    $result = ep_4_query($query);

                    $v_genre_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment

                    $query2 = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_GENRES_DESCRIPTION." (bookx_genre_id, languages_id, genre_description,genre_image)
						VALUES ('".$v_genre_id."', '".$epdlanguage_id."', '".$genre_name."',null)");
                    //$result2 = ep_4_query($query2);
                }
                // Genres To Products
                $genres_to_products = "SELECT * FROM ".TABLE_PRODUCT_BOOKX_GENRES_TO_PRODUCTS." WHERE (bookx_genre_id = '".$v_genre_id."') AND (products_id = '".$v_products_id."')";
                $result = ep_4_query($genres_to_products);

                $row_genre_to_products = ($ep_uses_mysqli ? mysqli_fetch_assoc($result) : mysql_fetch_assoc($result));

                if ($row_genre_to_products == '') {
                    $query = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_GENRES_TO_PRODUCTS." (bookx_genre_id, products_id) VALUES ('".$v_genre_id."', '".$v_products_id."')");
                    $updated_id[] = $v_genre_id;
                    //$result = ep_4_query($query);
                } else { // This only updates if there's duplicated genres in the same line 
                    $v_genre_id = $row_genre_to_products['bookx_genre_id'];
                    $updated_id[] = $row_genre_to_products['bookx_genre_id'];
                    $query = ep_4_query("UPDATE ".TABLE_PRODUCT_BOOKX_GENRES_TO_PRODUCTS." SET bookx_genre_id = '".$v_genre_id."' WHERE products_id = '".$v_products_id."' and primary_id ='".$primary_id."'");
                    //$result = ep_4_query($query);
                }
            } // ends if name lengh                
        } //ends foreach       
    } //ends second if
    $temp_del = array_merge($updated_id, $inserted_id); // Merge all the id's in the loop
    $q = ""; // empty string for the query
    foreach($temp_del as $value) {
        $q .= " AND bookx_genre_id != '".$value."'"; // construct the query with the id's
    }
    $delete = ep_4_query("DELETE FROM ".TABLE_PRODUCT_BOOKX_GENRES_TO_PRODUCTS." WHERE products_id='".$v_products_id."' ".$q."");

    unset($delete, $q, $temp_del, $updated_id, $inserted_id);

    $v_genre_id = 0;
} // ends genres

//::: Publisher Name
if (isset($filelayout['v_bookx_publisher_name'])) {

    if ($bookx_default_publisher_name != '' ? $v_bookx_publisher_name = $bookx_default_publisher_name : $v_bookx_publisher_name = '');

    if (isset($v_bookx_publisher_name) && ($v_bookx_publisher_name != '') && (mb_strlen($v_bookx_publisher_name) <= $bookx_publisher_name_max_len)) {

        $sql = "SELECT bookx_publisher_id AS publisherID FROM ".TABLE_PRODUCT_BOOKX_PUBLISHERS." WHERE publisher_name = '".addslashes(ep_4_curly_quotes($v_bookx_publisher_name))."' LIMIT 1";
        $result = ep_4_query($sql);
        if ($row_publisher = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result))) {
            $v_publisher_id = $row_publisher['publisherID']; // this id goes into the product_bookx_extra table
            $sql = "UPDATE ".TABLE_PRODUCT_BOOKX_PUBLISHERS." SET 
						publisher_name = '".addslashes($v_bookx_publisher_name)."',
						last_modified = CURRENT_TIMESTAMP
						WHERE bookx_publisher_id = '".$v_publisher_id."'";
            $result = ep_4_query($sql);
            if ($result) {
                //	zen_record_admin_activity('Updated Publishers  ' . (int)$v_publisher_id . ' via EP4.', 'info');
            }
        } else { // It is set to autoincrement							
            $sql = "INSERT INTO ".TABLE_PRODUCT_BOOKX_PUBLISHERS." (publisher_name, publisher_image, publisher_sort_order, date_added, last_modified)
							VALUES ('".addslashes(ep_4_curly_quotes($v_bookx_publisher_name))."', null,0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
            $result = ep_4_query($sql);
            $v_publisher_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment
            if ($result) {
                $sql2 = "INSERT INTO ".TABLE_PRODUCT_BOOKX_PUBLISHERS_DESCRIPTION." (bookx_publisher_id, languages_id, publisher_url, publisher_description) VALUES ('".$v_publisher_id."', '".$epdlanguage_id."', NULL,NULL)";
            }
            $result = ep_4_query($sql2);
            // Report
            zen_record_admin_activity('Inserted Publishers '.$v_bookx_publisher_name.' via EP4.', 'info');
        }
    } else { // $v_bookx_publisher_name length violation
        if ($v_bookx_publisher_name == '' && $report_bookx_publisher_name == true) { // check and warn of empty publisher name(still updates)
            $bookx_reports[BOX_CATALOG_PRODUCT_BOOKX_PUBLISHERS][] = sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 60)).' - '.$edit_link;
        }
        if (mb_strlen($v_bookx_publisher_name) > $bookx_pubisher_name_max_len) {
            $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_PUBLISHER_NAME_LONG, $v_bookx_publisher_name, $bookx_publisher_name_max_len);
            $ep_error_count++;
        }
        $v_publisher_id = 0;
    }
} // eof Publisher Name

// Series Names 
if (isset($filelayout['v_bookx_series_name'])) {

    ($bookx_default_series_name != '' ? $v_bookx_series_name = $bookx_default_series_name : $v_bookx_series_name = '');
    if (($v_bookx_series_name != '') && (mb_strlen($v_bookx_series_name) <= $bookx_series_name_max_len)) {

        $sql_series_name = "SELECT bookx_series_id AS seriesID FROM ".TABLE_PRODUCT_BOOKX_SERIES_DESCRIPTION." WHERE series_name ='".addslashes(ep_4_curly_quotes($v_bookx_series_name))."' LIMIT 1";
        $result = ep_4_query($sql_series_name);
        if ($row_series = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result))) { //update			
            $v_series_id = $row_series['seriesID']; // Goes to bookx_extra

            $sql_series_update = "UPDATE ".TABLE_PRODUCT_BOOKX_SERIES_DESCRIPTION." SET languages_id = '".$epdlanguage_id."', series_image =NULL, series_name = '".addslashes(ep_4_curly_quotes($v_bookx_series_name))."', series_description =NULL WHERE bookx_series_id = '".$v_series_id."'";
            $result = ep_4_query($sql_series_update);

        } else {
            $sql_series_new_id = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_SERIES." (series_sort_order, date_added, last_modified) VALUES (0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
            $v_series_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment

            $sql_series_name = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_SERIES_DESCRIPTION." (bookx_series_id, languages_id,series_image,series_name,series_description) VALUES ('".$v_series_id."', '".$epdlanguage_id."', NULL, '".addslashes(ep_4_curly_quotes($v_bookx_series_name))."', NULL)");
        }
    } else { // Empty series file fields 		
        if ($v_bookx_series_name == '' && $report_bookx_series_name == true) {
            $bookx_reports[BOX_CATALOG_PRODUCT_BOOKX_SERIES][] = sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 60)).' - '.$edit_link;
        }
        if (mb_strlen($v_bookx_series_name) > $bookx_series_name_max_len) {
            $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_SERIES_NAME_LONG, $v_bookx_series_name, $bookx_series_name_max_len);
            $ep_error_count++;
        }
        $v_series_id = 0;
    }
} // eof series Name

//:::: Binding Cover type
if (isset($filelayout['v_bookx_binding'])) {

    ($bookx_default_binding != '' ? $v_bookx_binding = $bookx_default_binding : $v_bookx_binding = '');

    if (($v_bookx_binding != '') && (mb_strlen($v_bookx_binding) <= $bookx_binding_name_max_len)) {

        $sql = "SELECT bookx_binding_id AS bindingID FROM ".TABLE_PRODUCT_BOOKX_BINDING_DESCRIPTION." WHERE binding_description ='".addslashes(ep_4_curly_quotes($v_bookx_binding))."' LIMIT 1";
        $result = ep_4_query($sql);
        if ($row_binding = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result))) { //update			
            $v_binding_id = $row_binding['bindingID']; // Goes to bookx_extra
            $sql = "UPDATE ".TABLE_PRODUCT_BOOKX_BINDING_DESCRIPTION." SET languages_id = '".$epdlanguage_id."', binding_description = '".addslashes(ep_4_curly_quotes($v_bookx_binding))."' WHERE bookx_binding_id = '".$v_binding_id."'";
            $result = ep_4_query($sql);

        } else {
            $sql_binding_id = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_BINDING." (binding_sort_order) VALUES (0)");
            $v_binding_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment
            $sql_binding_name = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_BINDING_DESCRIPTION." (bookx_binding_id, languages_id, binding_description) VALUES ('".$v_binding_id."', '".$epdlanguage_id."','".addslashes(ep_4_curly_quotes($v_bookx_binding))."')");
        }
    } else { // Empty binding file fields 				
        if ($v_bookx_binding == '' && $report_bookx_binding == true) {
            $bookx_reports[BOX_CATALOG_PRODUCT_BOOKX_BINDING][] = sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 60)).' - '.$edit_link;
        }
        if (mb_strlen($v_bookx_binding) > $bookx_binding_name_max_len) {
            $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_BINDING_NAME_LONG, $v_bookx_binding, $bookx_binding_name_max_len);
            $ep_error_count++;
        }
        $v_binding_id = 0;
    }
} // eof binding cover 

//:::: Printing type
if (isset($filelayout['v_bookx_printing'])) {
    if (($v_bookx_printing != '') && (mb_strlen($v_bookx_printing) <= $bookx_printing_name_max_len)) {
        $sql = "SELECT bookx_printing_id AS printingID FROM ".TABLE_PRODUCT_BOOKX_PRINTING_DESCRIPTION." WHERE printing_description ='".addslashes(ep_4_curly_quotes($v_bookx_printing))."' LIMIT 1";
        $result = ep_4_query($sql);
        if ($row_printing = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result))) { //update			
            $v_printing_id = $row_printing['printingID']; // Goes to bookx_extra
            $sql = "UPDATE ".TABLE_PRODUCT_BOOKX_PRINTING_DESCRIPTION." SET languages_id = '".$epdlanguage_id."', printing_description = '".addslashes(ep_4_curly_quotes($v_bookx_printing))."' WHERE bookx_printing_id = '".$v_printing_id."'";
            $result = ep_4_query($sql);
        } else {
            $sql_printing_id = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_PRINTING." (printing_sort_order) VALUES (0)");
            $v_printing_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment
            $sql_printing_name = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_PRINTING_DESCRIPTION." (bookx_printing_id, languages_id, printing_description) VALUES ('".$v_printing_id."', '".$epdlanguage_id."','".addslashes(ep_4_curly_quotes($v_bookx_printing))."')");
        }
    } else { // Empty printing file fields 		
        if ($v_bookx_printing == '' && $report_bookx_printing == true) {
            $bookx_reports[BOX_CATALOG_PRODUCT_BOOKX_PRINTING][] = sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 60)).' - '.$edit_link;
        }
        if (mb_strlen($v_bookx_printing) > $bookx_printing_name_max_len) {
            $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_PRINTING_NAME_LONG, $v_bookx_printing, $bookx_printing_name_max_len);
            $ep_error_count++;
        }
        $v_printing_id = 0;
    }
} // ends printing type

//:::: Book Condition 
if (isset($filelayout['v_bookx_condition'])) {
    ($bookx_default_condition != '' ? $v_bookx_condition = $bookx_default_condition : $v_bookx_condition = '');
    if (($v_bookx_condition != '') && (mb_strlen($v_bookx_condition) <= $bookx_condition_name_max_len)) {
        $sql = "SELECT bookx_condition_id AS conditionID FROM ".TABLE_PRODUCT_BOOKX_CONDITIONS_DESCRIPTION." WHERE condition_description ='".addslashes(ep_4_curly_quotes($v_bookx_condition))."' LIMIT 1";
        $result = ep_4_query($sql);
        // Goes to bookx_extra	

        if ($row_condition = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result))) { //update			

            $v_condition_id = $row_condition['conditionID'];

            $sql = "UPDATE ".TABLE_PRODUCT_BOOKX_CONDITIONS_DESCRIPTION." SET languages_id = '".$epdlanguage_id."', condition_description = '".addslashes(ep_4_curly_quotes($v_bookx_condition))."' WHERE bookx_condition_id = '".$v_condition_id."'";
            $result = ep_4_query($sql);

        } else {
            $sql_condition_id = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_CONDITIONS." (condition_sort_order) VALUES (0)");

            $v_condition_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment

            $sql_condition_name = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_CONDITIONS_DESCRIPTION." (bookx_condition_id, languages_id, condition_description) VALUES ('".$v_condition_id."', '".$epdlanguage_id."','".addslashes(ep_4_curly_quotes($v_bookx_condition))."')");
        }
    } else { // Empty condition file fields 		
        if ($v_bookx_condition == '' && $report_bookx_condition == true) {
            $bookx_reports[BOX_CATALOG_PRODUCT_BOOKX_CONDITIONS][] = sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 60)).' - '.$edit_link;
        }
        if (mb_strlen($v_bookx_condition) > $bookx_condition_name_max_len) {
            $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_CONDITION_NAME_LONG, $v_bookx_condition, $bookx_condition_name_max_len);
            $ep_error_count++;
        }
        $v_condition_id = 0;
    }
} // ends bookx condition

//:::: Book Imprint 
if (isset($filelayout['v_bookx_imprint_name'])) {

    ($bookx_default_imprint_name != '' ? $v_bookx_imprint_name = $bookx_default_imprint_name : $v_bookx_imprint_name = '');
    if (isset($v_bookx_imprint_name) && ($v_bookx_imprint_name != '') && (mb_strlen($v_bookx_imprint_name) <= $bookx_imprint_name_max_len)) {
        $sql = "SELECT bookx_imprint_id AS imprintID FROM ".TABLE_PRODUCT_BOOKX_IMPRINTS." WHERE imprint_name = '".addslashes(ep_4_curly_quotes($v_bookx_imprint_name))."' LIMIT 1";
        $result = ep_4_query($sql);
        if ($row_imprint = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result))) {
            $v_imprint_id = $row_imprint['imprintID']; // this id goes into the product_bookx_extra table
            $sql = "UPDATE ".TABLE_PRODUCT_BOOKX_IMPRINTS." SET 
						imprint_name = '".addslashes($v_bookx_imprint_name)."',
						last_modified = CURRENT_TIMESTAMP
						WHERE bookx_imprint_id = '".$v_imprint_id."'";
            $result = ep_4_query($sql);

            if ($result) {
                //	zen_record_admin_activity('Updated imprints  ' . (int)$v_imprint_id . ' via EP4.', 'info');
            }
        } else { // It is set to autoincrement							
            $sql = "INSERT INTO ".TABLE_PRODUCT_BOOKX_IMPRINTS." (imprint_name, imprint_image, imprint_sort_order, date_added, last_modified)
							VALUES ('".addslashes(ep_4_curly_quotes($v_bookx_imprint_name))."', null,0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
            $result = ep_4_query($sql);
            $v_imprint_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment
            if ($result) {
                $sql2 = "INSERT INTO ".TABLE_PRODUCT_BOOKX_IMPRINTS_DESCRIPTION." (bookx_imprint_id, languages_id, imprint_description) VALUES ('".$v_imprint_id."', '".$epdlanguage_id."', NULL)";
            }
            $result = ep_4_query($sql2);
            // Report
            zen_record_admin_activity('Inserted imprints '.$v_bookx_imprint_name.' via EP4.', 'info');
        }
    } else { // $v_bookx_imprint_name length violation
        if ($v_bookx_imprint_name == '' && $report_bookx_imprint_name == true) { // check and warn of empty imprint name(still updates)
            $bookx_reports[BOX_CATALOG_PRODUCT_BOOKX_IMPRINTS][] = sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 60)).' - '.$edit_link;
        }
        if (mb_strlen($v_bookx_imprint_name) > $bookx_imprint_name_max_len) {
            $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_IMPRINTS_NAME_LONG, $v_bookx_imprint_name, $bookx_imprint_name_max_len);
            $ep_error_count++;
        }
        $v_imprint_id = 0;
    }
} // ends imprint

// Author Names and types -- Not so fancy , but the only way I could get there
if (isset($filelayout['v_bookx_author_name'])) {

    if (!empty($bookx_default_author_name) && $v_bookx_author_name == '') {
        $v_bookx_author_name = $bookx_default_author_name;
        $items[$filelayout['v_bookx_author_name']] = $bookx_default_author_name;
    }
    if (!empty($bookx_default_author_type) && $v_bookx_author_type == '') {
        $v_bookx_author_type = $bookx_default_author_type;
        $items[$filelayout['v_bookx_author_type']] = $bookx_default_author_type;
    }

    $authors_array = mb_split('\x5e', $items[$filelayout['v_bookx_author_name']]); // names to array
    $author_types_array = mb_split('\x5e', $items[$filelayout['v_bookx_author_type']]); // types to array
    (empty($v_bookx_author_type) ? $first_type = '' : $first_type = $author_types_array['0']);

    // The first author_type. This makes possible to have more authors then types. 
    // ie: authorA^authorB^authorC => writer
    // and not authorA^authorB^authorC => writer^writer^writer
    // Compare arrays before combine then for the loop     
    if (count($authors_array > count($author_types_array))) { // More authors than types 
        for ($i = count($author_types_array); $i < count($authors_array); $i++) {
            $author_types_array[] = $first_type; // add remaining type to array            
        }
    }
    if ((count($authors_array) < count($author_types_array))) {
        // This is a error. Dont' import
        $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_SUBTITLE_NAME_LONG, $v_bookx_subtitle, $bookx_subtitle_name_max_len);
        $ep_error_count++;
    }
    // Merge the two arrays
    $combine_array = array_combine($authors_array, $author_types_array);
    //Everything ok, start the loop
    // starts two arrays to get the updated and insertd id's during the loop
    $updated_id = array(); // Get the updated id's.   
    $inserted_id = array(); // Get the inserted id's 
    //Start the loop 
    foreach($combine_array as $v_bookx_author_name => $v_bookx_author_type) {
        // Check all names lengh first
        if (isset($v_bookx_author_name) && ($v_bookx_author_name !== '') && (mb_strlen($v_bookx_author_name) <= $bookx_author_name_max_len) || isset($v_bookx_author_name) && ($v_bookx_author_type != '') && (mb_strlen($v_bookx_author_type) <= $bookx_author_types_name_max_len)) {

            // First see if the author already has a default type 
            $sql_author_id = ep_4_query("SELECT bookx_author_id AS authorID, author_default_type FROM ".TABLE_PRODUCT_BOOKX_AUTHORS." WHERE author_name = '".addslashes(ep_4_curly_quotes($v_bookx_author_name))."' ");

            $row_author_id = ($ep_uses_mysqli ? mysqli_fetch_array($sql_author_id) : mysqli_fetch_array($sql_author_id));
            $v_author_id = $row_author_id['authorID'];

            $sql = "SELECT bookx_author_type_id AS author_typeID FROM ".TABLE_PRODUCT_BOOKX_AUTHOR_TYPES_DESCRIPTION." WHERE type_description ='".addslashes(ep_4_curly_quotes($v_bookx_author_type))."' LIMIT 1";
            $result = ep_4_query($sql);

            $row_author_type = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result));
            $v_author_type_id = $row_author_type['author_typeID']; // Goes to authors default_type
            if ($row_author_type == '') { // insert 
                $sql_author_type_id = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_AUTHOR_TYPES." (type_sort_order) VALUES ('0')");
                $v_author_type_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment

                $sql_author_type_name = "INSERT INTO ".TABLE_PRODUCT_BOOKX_AUTHOR_TYPES_DESCRIPTION." (bookx_author_type_id, languages_id, type_description,type_image) VALUES ('".$v_author_type_id."', '".$epdlanguage_id."','".addslashes(ep_4_curly_quotes($v_bookx_author_type))."', null)";
                //pr($sql_author_type_name);
                $result = ep_4_query($sql_author_type_name);
            }
            elseif($row_author_type != '' && addslashes(ep_4_curly_quotes($v_bookx_author_type)) == addslashes(ep_4_curly_quotes($v_bookx_author_type))) {
                // Nirvana
            } else { // update 
                $sql_update = ep_4_query("UPDATE ".TABLE_PRODUCT_BOOKX_AUTHOR_TYPES_DESCRIPTION." SET languages_id = '".$epdlanguage_id."', type_description = '".addslashes(ep_4_curly_quotes($v_bookx_author_type))."' WHERE type_description = '".addslashes(ep_4_curly_quotes($v_bookx_author_type))."'");
            }
            // We go for the authors names
            if ($row_author_id != '') {
                $sql = ep_4_query("UPDATE ".TABLE_PRODUCT_BOOKX_AUTHORS." SET author_name = '".addslashes(ep_4_curly_quotes($v_bookx_author_name))."', author_default_type = '".$v_author_type_id."',
				last_modified = CURRENT_TIMESTAMP WHERE bookx_author_id = '".$v_author_id."'");
                if ($sql) {
                    //zen_record_admin_activity('Updated Authors  '.(int) $v_author_id.' via EP4.', 'info');
                }
                // @todo language

            } else { // It is set to autoincrement, do not need to fetch max id                
                $sql = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_AUTHORS." (author_name, author_image, author_image_copyright, author_default_type, author_sort_order, author_url, date_added, last_modified)
			 		VALUES ('".addslashes(ep_4_curly_quotes(rtrim($v_bookx_author_name)))."', null,null,'".$v_author_type_id."','0',null, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");

                $v_author_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment

                if ($sql) {
                    //zen_record_admin_activity('Inserted Authors ' . addslashes(ep_4_curly_quotes($v_bookx_author_name)) . ' via EP4.', 'info');
                }
                $sql2 = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_AUTHORS_DESCRIPTION." (bookx_author_id, languages_id, author_description) VALUES ('".$v_author_id."', '".$epdlanguage_id."', null)");
                // @todo language
            }
            // Author to Products 
            $sql_author_to_product = "SELECT * FROM ".TABLE_PRODUCT_BOOKX_AUTHORS_TO_PRODUCTS." WHERE (products_id = '".$v_products_id."') and (bookx_author_id = '".$v_author_id."')";
            $result = ep_4_query($sql_author_to_product);

            $row_author_to_product = ($ep_uses_mysqli ? mysqli_fetch_assoc($result) : mysqli_fetch_assoc($result));
            //pr($row_author_to_product);
            if ($row_author_to_product == '') {

                $sql = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_AUTHORS_TO_PRODUCTS." (bookx_author_id,products_id, bookx_author_type_id) VALUES ('".$v_author_id."', '".$v_products_id."', '".$v_author_type_id."')");

                $inserted_id[] = $v_author_id; // Goes to array
                if ($sql) {
                    // zen_record_admin_activity('Updated Authors Books '.(int) $v_author_id.' via EP4.', 'info');
                }
            } else {
                $v_author_id = $row_author_to_product['bookx_author_id'];
                $primary_id = $row_author_to_product['primary_id'];
                $updated_id[] = $row_author_to_product['bookx_author_id']; // Goes to array

                $sql2 = ep_4_query("UPDATE ".TABLE_PRODUCT_BOOKX_AUTHORS_TO_PRODUCTS." SET bookx_author_id = '".$v_author_id."' WHERE products_id = '".$v_products_id."' and primary_id='".$primary_id."'");
                if ($result) {
                    // zen_record_admin_activity('Updated Authors Books '.(int) $v_author_id.' via EP4.', 'info');
                }
            } //ends else     
        } else {
            //@reports
            if ($v_bookx_author_name == '' && $report_bookx_author_name == true) { // check and warn of empty imprint name(still updates)
                $bookx_reports[BOX_CATALOG_PRODUCT_BOOKX_AUTHORS][] = sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 60)).' - '.$edit_link;
            }
            if (mb_strlen($author) > $bookx_author_name_max_len) {
                $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_AUTHOR_NAME_LONG, $author, $bookx_author_name_max_len);
                $ep_error_count++;
            }
        }
    } //ends foreach   
    $temp_del = array_merge($updated_id, $inserted_id); // Merge all the id's in the loop
    $q = ""; // empty string for the query
    foreach($temp_del as $key => $value) {
        $q .= " AND bookx_author_id != '".$value."'"; // construct the query with the id's
    }
    $delete = ep_4_query("DELETE FROM ".TABLE_PRODUCT_BOOKX_AUTHORS_TO_PRODUCTS." WHERE products_id='".$v_products_id."' ".$q."");
    unset($delete, $q, $temp_del, $updated_id, $inserted_id);
    $v_author_type_id = 0;
    $v_author_id = 0;
} // ends if 

//:: All now for PRODUCTS BOOKX EXTRA + BOOKX_EXTRA_DESCRIPTION
if (isset($v_bookx_isbn)) {

    if (strlen($v_bookx_subtitle) <= $bookx_subtitle_name_max_len) { //@fixme - there's some strande beahvior with this check
        $sql = "SELECT * FROM ".TABLE_PRODUCT_BOOKX_EXTRA." be INNER JOIN ".TABLE_PRODUCT_BOOKX_EXTRA_DESCRIPTION." bed on be.products_id = bed.products_id WHERE be.products_id = '".$v_products_id."' AND bed.products_id = '".$v_products_id."'";
        $result = ep_4_query($sql);
        //pr($sql);
        if ($v_bookx_isbn == '' && $report_bookx_isbn == true) { // check and warn for empty ISBN (still updates)
            //@fixme - This should act has other warnings. 
            $bookx_reports[LABEL_BOOKX_ISBN][] = sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 10)).'...'.$edit_link;
            $ep_error_count++;
        } else {
            if ($row_extra = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result)) == 0) { // NÃO existe registo

                $query = "INSERT INTO ".TABLE_PRODUCT_BOOKX_EXTRA." (products_id, 
				bookx_publisher_id, bookx_series_id,bookx_imprint_id, bookx_binding_id, bookx_printing_id, bookx_condition_id, publishing_date, pages, volume, size, isbn) VALUES ('".$v_products_id."','".$v_publisher_id."','".$v_series_id."',
				'".$v_imprint_id."','".$v_binding_id."','".$v_printing_id."','".$v_condition_id."','".$v_bookx_publishing_date."','".$v_bookx_pages."','".$v_bookx_volume."','".$v_bookx_size."','".$v_bookx_isbn."')";
                $result = ep_4_query($query);
                //For PRODUCT_BOOKX_EXTRA_DESCRIPTION
                $sql = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_EXTRA_DESCRIPTION." (products_id, languages_id, products_subtitle) VALUES ('".$v_products_id."', '".$epdlanguage_id."', '".addslashes(ep_4_curly_quotes($v_bookx_subtitle))."')");

            } else {

                $query = "UPDATE ".TABLE_PRODUCT_BOOKX_EXTRA." SET bookx_publisher_id = '".$v_publisher_id."',bookx_series_id = '".$v_series_id."',bookx_imprint_id = '".$v_imprint_id."',bookx_binding_id = '".$v_binding_id."',bookx_printing_id ='".$v_printing_id."',bookx_condition_id= '".$v_condition_id."',
				publishing_date = '".$v_bookx_publishing_date."',pages = '".$v_bookx_pages."',volume = '".$v_bookx_volume."',size = '".$v_bookx_size."',isbn = '".addslashes($v_bookx_isbn)."' WHERE products_id = '".$v_products_id."'";
                $result = ep_4_query($query);
                //For PRODUCT_BOOKX_EXTRA_DESCRIPTION
                $sql = "UPDATE ".TABLE_PRODUCT_BOOKX_EXTRA_DESCRIPTION." SET languages_id = '".$epdlanguage_id."', products_subtitle = '".addslashes(ep_4_curly_quotes($v_bookx_subtitle))."'  WHERE products_id = '".$v_products_id."'";
                $result = ep_4_query($sql);

            }
        } // ends else 
    } else {
        // @reports bookx subtitle
        if (mb_strlen($v_bookx_subtitle > $bookx_subtitle_name_max_len)) {
            $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_SUBTITLE_NAME_LONG, $v_bookx_subtitle, $bookx_subtitle_name_max_len);
            $ep_error_count++;
        }
    } //ends Bookx Extra 
}
