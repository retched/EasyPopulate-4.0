<?php
/**
 * Import file for EP4 
 * 
 * @EP4Bookx - EP4 CSV fork to import Bookx fields - tested with Zencart 1.5.4
 *
 * @version  0.9.0 - Still in development, make your changes in a local environment
 * @see Bookx module for ZenCart
 * @see Readme-EP4Bookx
 * @author mesnitu
 * @todo  export with support for languages
 * @todo  export assinged multiple authors
 * @todo  export assinged multiple genres
 * @todo  review the querys to have a standart 
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
 *
 */
	
/**
 * Edit link to books with missing fields
 * @var link
 */
 $edit_link = "<a href=" . zen_href_link('product_bookx.php','cPath='. zen_get_product_path($v_products_id) . '&product_type=6&pID='. $v_products_id .'&action=new_product') . ">". EASYPOPULATE_4_BOOKX_EDIT_LINK . "</a>";
 //
 
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::: BOOKX GENRE
if (isset($filelayout['v_bookx_genre_name']) ) {	
	if (isset($v_bookx_genre_name) /*&& ($v_bookx_genre_name !=='')*/ && (mb_strlen($v_bookx_genre_name) <= $bookx_genre_name_max_len) ) {
			// save time having a fallback genre name. No need to not upload a empty genre name. 
			// A default genre name is given by user config and warning with a link to edit the book.
			if (($v_bookx_genre_name == '' ) && (!empty($ep_bookx_fallback_genre_name))) {
					$v_bookx_genre_name = $ep_bookx_fallback_genre_name;
					//@reports
					$bookx_reports[BOX_CATALOG_PRODUCT_BOOKX_GENRES][] = 'FallBack Genre in : ' .sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 60)) . ' - ' .$edit_link;				
			}			
			$sql = "SELECT bookx_genre_id AS genreID FROM ".TABLE_PRODUCT_BOOKX_GENRES_DESCRIPTION." WHERE genre_description = '".addslashes(ep_4_curly_quotes($v_bookx_genre_name))."' LIMIT 1";
			$result = ep_4_query($sql);
			if ( $row = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result) )) { //update
				
				$v_genre_id = $row['genreID']; // this id goes into the product_bookx_genres_to_products table				
				
				$sql = ep_4_query("UPDATE ".TABLE_PRODUCT_BOOKX_GENRES_DESCRIPTION." SET genre_description = '".addslashes($v_bookx_genre_name)."' WHERE bookx_genre_id = '".$v_genre_id."'");
					
					 if ($sql==true) {
					 	// A default genre name
					 	//echo 'result';
					// 		zen_record_admin_activity('Updated Authors Books ' . (int)$v_author_id . ' via EP4.', 'info');
					 }
					//zen_record_admin_activity('Updated Genres  ' . addslashes($v_bookx_genre_name) . ' via EP4.', 'info');
					// @todo language			
			}	else { // It is set to autoincrement, do not need to fetch max id

					$sql = "INSERT INTO ".TABLE_PRODUCT_BOOKX_GENRES." (genre_sort_order, date_added, last_modified)
						VALUES ('0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
						$result = ep_4_query($sql);

					$v_genre_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment

						$sql2 = "INSERT INTO ".TABLE_PRODUCT_BOOKX_GENRES_DESCRIPTION." (bookx_genre_id, languages_id, genre_description,genre_image)
						VALUES ('".$v_genre_id ."', '".$epdlanguage_id."', '".$v_bookx_genre_name."',null)";
						$result = ep_4_query($sql2);			
					}

					if($result) {		
					//zen_record_admin_activity('Inserted Authors ' . $addslashes(ep_4_curly_quotes($v_bookx_author_name)) . ' via EP4.', 'info');								
					}
					
		// Genre to product
	$sql = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_GENRES_TO_PRODUCTS." WHERE (bookx_genre_id = '".$v_genre_id."') AND (products_id = '".$v_products_id."') LIMIT 1");		
		if ($sql->num_rows==0){
			$query = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_GENRES_TO_PRODUCTS." (bookx_genre_id, products_id) VALUES ('".$v_genre_id."', '". $v_products_id."')");			
			} else {
					$query = ep_4_query("UPDATE ".TABLE_PRODUCT_BOOKX_GENRES_TO_PRODUCTS." SET bookx_genre_id = '".$v_genre_id."' WHERE products_id = '". $v_products_id."'");
		} 
	} else { // $v_bookx_genre_name == '' or name length violation
		if ((mb_strlen($v_bookx_genre_name) > $bookx_genre_name_max_len)) {
			$display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_GENRE_NAME_LONG, $v_bookx_genre_name, $bookx_genre_name_max_len);
			$ep_error_count++;	
				
		}
		$v_genre_id = 0; 
	}
}// END: genre Name


//::: Publisher Name
if (isset($filelayout['v_bookx_publisher_name']) ) {
	
	if ( isset($v_bookx_publisher_name) && ($v_bookx_publisher_name != '') && (mb_strlen($v_bookx_publisher_name) <= $bookx_publisher_name_max_len) ) {			
			$sql = "SELECT bookx_publisher_id AS publisherID FROM ".TABLE_PRODUCT_BOOKX_PUBLISHERS." WHERE publisher_name = '".addslashes(ep_4_curly_quotes($v_bookx_publisher_name))."' LIMIT 1";
			$result = ep_4_query($sql);		
			if ( $row = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result) )) {
						$v_publisher_id = $row['publisherID']; // this id goes into the product_bookx_extra table
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
								$sql2 = "INSERT INTO ". TABLE_PRODUCT_BOOKX_PUBLISHERS_DESCRIPTION ." (bookx_publisher_id, languages_id, publisher_url, publisher_description) VALUES ('". $v_publisher_id ."', '". $epdlanguage_id ."', NULL,NULL)";
							}
							$result = ep_4_query($sql2);
								// Report
								zen_record_admin_activity('Inserted Publishers ' . $v_bookx_publisher_name . ' via EP4.', 'info');
								}
		} else { // $v_bookx_publisher_name length violation
			if ($v_bookx_publisher_name =='') { // check and warn of empty publisher name(still updates)
			//pr("EMPTY ISBN");
			$bookx_reports[BOX_CATALOG_PRODUCT_BOOKX_PUBLISHERS][] = sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 60)) . ' - ' .$edit_link;
		}			
		if (mb_strlen($v_bookx_publisher_name) > $bookx_pubisher_name_max_len) {
			$display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_PUBLISHER_NAME_LONG, $v_bookx_publisher_name, $bookx_publisher_name_max_len);
			$ep_error_count++;
			
		}
		$v_publisher_id = 0; 
	}
}// eof Publisher Name



// Series Names 
if (isset($filelayout['v_bookx_series_name']) ) {
	if (($v_bookx_series_name != '') && (mb_strlen($v_bookx_series_name) <= $bookx_series_name_max_len) ) {
		$sql_series_name = "SELECT bookx_series_id AS seriesID FROM ".TABLE_PRODUCT_BOOKX_SERIES_DESCRIPTION." WHERE series_name ='".addslashes(ep_4_curly_quotes($v_bookx_series_name))."' LIMIT 1";
		$result = ep_4_query($sql_series_name);
			if ( $row = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result) )) { //update			
				$v_series_id = $row['seriesID']; // Goes to bookx_extra
		
				$sql_series_update = "UPDATE ".TABLE_PRODUCT_BOOKX_SERIES_DESCRIPTION." SET languages_id = '".$epdlanguage_id."', series_image =NULL, series_name = '".addslashes(ep_4_curly_quotes($v_bookx_series_name))."', series_description =NULL WHERE bookx_series_id = '".$v_series_id."'";
				$result = ep_4_query($sql_series_update);
				
			}
			else  {
				$sql_series_new_id = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_SERIES." (series_sort_order, date_added, last_modified) VALUES (0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
				$v_series_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment
				
				$sql_series_name = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_SERIES_DESCRIPTION." (bookx_series_id, languages_id,series_image,series_name,series_description) VALUES ('".$v_series_id."', '".$epdlanguage_id."', NULL, '".addslashes(ep_4_curly_quotes($v_bookx_series_name))."', NULL)");				
			}
	}
	else { // Empty series file fields 		
		if ($v_bookx_series_name =='')  {
			
			$bookx_reports[BOX_CATALOG_PRODUCT_BOOKX_SERIES][] = sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 60)) . ' - ' .$edit_link;
			}

			if(mb_strlen($v_bookx_series_name) > $bookx_series_name_max_len) {
			$display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_SERIES_NAME_LONG, $v_bookx_series_name, $bookx_series_name_max_len);
			$ep_error_count++;	
					 
		}
		$v_series_id = 0;
	}
}// eof series Name

//:::: Binding Cover type
if (isset($filelayout['v_bookx_binding']) ) {
	if (($v_bookx_binding != '') && (mb_strlen($v_bookx_binding) <= $bookx_binding_name_max_len) ) {
		$sql = "SELECT bookx_binding_id AS bindingID FROM ".TABLE_PRODUCT_BOOKX_BINDING_DESCRIPTION." WHERE binding_description ='".addslashes(ep_4_curly_quotes($v_bookx_binding))."' LIMIT 1";
		$result = ep_4_query($sql);
			if ( $row = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result) )) { //update			
				$v_binding_id = $row['bindingID']; // Goes to bookx_extra
				$sql = "UPDATE ".TABLE_PRODUCT_BOOKX_BINDING_DESCRIPTION." SET languages_id = '".$epdlanguage_id."', binding_description = '".addslashes(ep_4_curly_quotes($v_bookx_binding))."' WHERE bookx_binding_id = '".$v_binding_id."'";
				$result = ep_4_query($sql);
							
			}
			else  {
				$sql_binding_id = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_BINDING." (binding_sort_order) VALUES (0)");
				$v_binding_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment
				$sql_binding_name = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_BINDING_DESCRIPTION." (bookx_binding_id, languages_id, binding_description) VALUES ('".$v_binding_id."', '".$epdlanguage_id."','".addslashes(ep_4_curly_quotes($v_bookx_binding))."')");				
			}
	}
	else { // Empty binding file fields 				
		if ($v_bookx_binding =='') {

			$bookx_reports[BOX_CATALOG_PRODUCT_BOOKX_BINDING][] = sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 60)) . ' - ' .$edit_link;
		}
 
		if (mb_strlen($v_bookx_binding) > $bookx_binding_name_max_len) {
			
			$display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_BINDING_NAME_LONG, $v_bookx_binding, $bookx_binding_name_max_len);
			$ep_error_count++;	
				 
		}
		$v_binding_id = 0;
	}
}// eof binding cover 


//:::: Printing type
if (isset($filelayout['v_bookx_printing']) ) {
	if (($v_bookx_printing != '') && (mb_strlen($v_bookx_printing) <= $bookx_printing_name_max_len) ) {
		$sql = "SELECT bookx_printing_id AS printingID FROM ".TABLE_PRODUCT_BOOKX_PRINTING_DESCRIPTION." WHERE printing_description ='".addslashes(ep_4_curly_quotes($v_bookx_printing))."' LIMIT 1";
		$result = ep_4_query($sql);
			if ( $row = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result) )) { //update			
				$v_printing_id = $row['printingID']; // Goes to bookx_extra
				$sql = "UPDATE ".TABLE_PRODUCT_BOOKX_PRINTING_DESCRIPTION." SET languages_id = '".$epdlanguage_id."', printing_description = '".addslashes(ep_4_curly_quotes($v_bookx_printing))."' WHERE bookx_printing_id = '".$v_printing_id."'";
				$result = ep_4_query($sql);				
			}
			else  {
				$sql_printing_id = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_PRINTING." (printing_sort_order) VALUES (0)");
				$v_printing_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment
				$sql_printing_name = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_PRINTING_DESCRIPTION." (bookx_printing_id, languages_id, printing_description) VALUES ('".$v_printing_id."', '".$epdlanguage_id."','".addslashes(ep_4_curly_quotes($v_bookx_printing))."')");				
			}
	}
	else { // Empty printing file fields 		
		if ($v_bookx_printing =='') {
			$bookx_reports[BOX_CATALOG_PRODUCT_BOOKX_PRINTING][] = sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 60)) . ' - ' .$edit_link;
		}
		if(mb_strlen($v_bookx_printing) > $bookx_printing_name_max_len)  {
			$display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_PRINTING_NAME_LONG, $v_bookx_printing, $bookx_printing_name_max_len);
			$ep_error_count++;	
				 
		}
		$v_printing_id = 0;
	}
}// ends printing type

//:::: Book Condition 
if (isset($filelayout['v_bookx_condition']) ) {

	if (($v_bookx_condition != '') && (mb_strlen($v_bookx_condition) <= $bookx_condition_name_max_len) ) {
		$sql = "SELECT bookx_condition_id AS conditionID FROM ".TABLE_PRODUCT_BOOKX_CONDITIONS_DESCRIPTION." WHERE condition_description ='".addslashes(ep_4_curly_quotes($v_bookx_condition))."' LIMIT 1";
		$result = ep_4_query($sql);
		 // Goes to bookx_extra	
			if ( $row = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result) )) { //update			
				
			$v_condition_id = $row['conditionID'];	
				
				$sql = "UPDATE ".TABLE_PRODUCT_BOOKX_CONDITIONS_DESCRIPTION." SET languages_id = '".$epdlanguage_id."', condition_description = '".addslashes(ep_4_curly_quotes($v_bookx_condition))."' WHERE bookx_condition_id = '".$v_condition_id."'";
				$result = ep_4_query($sql);	

			}	else  {
				$sql_condition_id = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_CONDITIONS." (condition_sort_order) VALUES (0)");
				
				$v_condition_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment
				
				$sql_condition_name = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_CONDITIONS_DESCRIPTION." (bookx_condition_id, languages_id, condition_description) VALUES ('".$v_condition_id."', '".$epdlanguage_id."','".addslashes(ep_4_curly_quotes($v_bookx_condition))."')");				
				}
	}
	else { // Empty condition file fields 		
		if ($v_bookx_condition =='') {

			$bookx_reports[BOX_CATALOG_PRODUCT_BOOKX_CONDITIONS][] = sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 60)) . ' - ' .$edit_link;
		}
			if (mb_strlen($v_bookx_condition) > $bookx_condition_name_max_len) {
			$display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_CONDITION_NAME_LONG, $v_bookx_condition, $bookx_condition_name_max_len);
			$ep_error_count++;			 
		}
		$v_condition_id = 0;
	}
}// ends bookx condition


//:::: Book Imprint 
if (isset($filelayout['v_bookx_imprint_name']) ) {
				
	if ( isset($v_bookx_imprint_name) && ($v_bookx_imprint_name != '') && (mb_strlen($v_bookx_imprint_name) <= $bookx_imprint_name_max_len) ) {			
			$sql = "SELECT bookx_imprint_id AS imprintID FROM ".TABLE_PRODUCT_BOOKX_IMPRINTS." WHERE imprint_name = '".addslashes(ep_4_curly_quotes($v_bookx_imprint_name))."' LIMIT 1";
			$result = ep_4_query($sql);		
			if ( $row = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result) )) {
						$v_imprint_id = $row['imprintID']; // this id goes into the product_bookx_extra table
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
								$sql2 = "INSERT INTO ". TABLE_PRODUCT_BOOKX_IMPRINTS_DESCRIPTION ." (bookx_imprint_id, languages_id, imprint_description) VALUES ('". $v_imprint_id ."', '". $epdlanguage_id ."', NULL)";
							}
							$result = ep_4_query($sql2);
								// Report
								zen_record_admin_activity('Inserted imprints ' . $v_bookx_imprint_name . ' via EP4.', 'info');
								}
		} else { // $v_bookx_imprint_name length violation
		if ($v_bookx_imprint_name =='') { // check and warn of empty imprint name(still updates)
			$bookx_reports[BOX_CATALOG_PRODUCT_BOOKX_IMPRINTS][] = sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 60)) . ' - ' .$edit_link ;
			}
		if (mb_strlen($v_bookx_imprint_name) > $bookx_imprint_name_max_len) {
			$display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_IMPRINTS_NAME_LONG, $v_bookx_imprint_name, $bookx_imprint_name_max_len);
			$ep_error_count++;
			
		}
		$v_imprint_id = 0; 
	}
}// eof Publisher Name



//:::: Author types $filelayout[] = 'v_bookx_author_type';
if (isset($filelayout['v_bookx_author_type']) ) {


	if (($v_bookx_author_type != '') && (mb_strlen($v_bookx_author_type) <= $bookx_author_types_name_max_len) ) {
		$sql = "SELECT bookx_author_type_id AS author_typeID FROM ".TABLE_PRODUCT_BOOKX_AUTHOR_TYPES_DESCRIPTION." WHERE type_description ='".addslashes(ep_4_curly_quotes($v_bookx_author_type))."' LIMIT 1";

		$result = ep_4_query($sql);
			if ( $row = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result) )) { //update			
				
				$v_author_type_id = $row['author_typeID']; // Goes to authors default_type
				
				$sql = "UPDATE ".TABLE_PRODUCT_BOOKX_AUTHOR_TYPES_DESCRIPTION." SET languages_id = '".$epdlanguage_id."', type_description = '".addslashes(ep_4_curly_quotes($v_bookx_author_type))."' WHERE bookx_author_type_id = '".$v_author_type_id."'";
				$result = ep_4_query($sql);				
			}
			else  {
				$sql_author_type_id = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_AUTHOR_TYPES." (type_sort_order) VALUES (0)");
				$v_author_type_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment
				$sql_author_type_name = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_AUTHOR_TYPES_DESCRIPTION." (bookx_author_type_id, languages_id, type_description,type_image) VALUES ('".$v_author_type_id."', '".$epdlanguage_id."','".addslashes(ep_4_curly_quotes($v_bookx_author_type))."', null)");				
			}
	}
	else { // Empty author_type file fields
		
		if ($v_bookx_author_type =='') { // check and warn of empty imprint name(still updates)
			
			$bookx_reports[BOX_CATALOG_PRODUCT_BOOKX_AUTHOR_TYPES][] = sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 60)) . ' - '.$edit_link;
			//echo $edit_link;
		} 		
		if ((mb_strlen($v_bookx_author_type) > $bookx_author_types_name_max_len)) {
			
			$display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_AUTHOR_TYPES_NAME_LONG, $v_bookx_author_type, $bookx_author_type_name_max_len);
			$ep_error_count++;	
				 
		}
		$v_author_type_id = 0;
	}
}// ends bookx author type

// Author Names
if (isset($filelayout['v_bookx_author_name']) ) {
	
	if (isset($v_bookx_author_name) && ($v_bookx_author_name !=='') && (mb_strlen($v_bookx_author_name) <= $bookx_author_name_max_len) ) {

		$sql_author_id = "SELECT bookx_author_id AS authorID FROM ".TABLE_PRODUCT_BOOKX_AUTHORS." WHERE author_name = '".addslashes(ep_4_curly_quotes($v_bookx_author_name))."' LIMIT 1";
		$result = ep_4_query($sql_author_id);
	

			if ( $row = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result) )) {	
			
			$v_author_id = $row['authorID']; // this id goes into the product_bookx_authors_to_products table

		

				$sql = "UPDATE ".TABLE_PRODUCT_BOOKX_AUTHORS." SET author_name = '".addslashes(ep_4_curly_quotes($v_bookx_author_name))."',
				last_modified = CURRENT_TIMESTAMP WHERE bookx_author_id = '".$v_author_id."'";


				$result = ep_4_query($sql);
				if ($result) {
					zen_record_admin_activity('Updated Authors  ' . (int)$v_author_id . ' via EP4.', 'info');
				}
						// @todo language
						
			}	else { // It is set to autoincrement, do not need to fetch max id
					
					
				
					$sql = "INSERT INTO ".TABLE_PRODUCT_BOOKX_AUTHORS." (author_name, author_image, author_image_copyright, author_default_type, author_sort_order, author_url, date_added, last_modified)
			 		VALUES ('".addslashes(ep_4_curly_quotes(rtrim($v_bookx_author_name)))."', null,null,'".$v_author_type_id ."',null,null, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
					$result = ep_4_query($sql);
			 		

			 		$v_author_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment
					
			 			if ($result) {
			  			//zen_record_admin_activity('Inserted Authors ' . addslashes(ep_4_curly_quotes($v_bookx_author_name)) . ' via EP4.', 'info');
			 			}
			 		$sql2 = "INSERT INTO ".TABLE_PRODUCT_BOOKX_AUTHORS_DESCRIPTION." (bookx_author_id, languages_id, author_description) VALUES ('".$v_author_id."', '".$epdlanguage_id."', null)";
			 		$result = ep_4_query($sql2);
					 		
			
			// @todo language
				}

				// Author to Products 
	$sql_author_to_product = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_AUTHORS_TO_PRODUCTS." WHERE (products_id = '".$v_products_id."') and (bookx_author_id = '".$v_author_id."') LIMIT 1");
 	
 		if ($sql_author_to_product->num_rows == 0) {
 			$sql =  ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_AUTHORS_TO_PRODUCTS." (bookx_author_id,products_id, bookx_author_type_id) VALUES ('".$v_author_id."', '".$v_products_id."', '".$v_author_type_id."') ");
 			
 				if ($sql) {
                     zen_record_admin_activity('Updated Authors Books ' . (int)$v_author_id . ' via EP4.', 'info');
                   }
		} else {
				$sql = ep_4_query("UPDATE ".TABLE_PRODUCT_BOOKX_AUTHORS_TO_PRODUCTS." SET bookx_author_id = '".$v_author_id."' WHERE products_id = '".$v_products_id."'");
			
					if ($result) {
                     zen_record_admin_activity('Updated Authors Books ' . (int)$v_author_id . ' via EP4.', 'info');
                   	}
		}




	}	else { // $v_bookx_author_name == '' or name length violation
			if ($v_bookx_author_name =='') { // check and warn of empty imprint name(still updates)
			$bookx_reports[BOX_CATALOG_PRODUCT_BOOKX_AUTHORS][] = sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 60)) . ' - ' .$edit_link;
		}
			if (mb_strlen($v_bookx_author_name) > $bookx_author_name_max_len) {
		 	$display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_AUTHOR_NAME_LONG, $v_bookx_author_name, $bookx_author_name_max_len);
		 	$ep_error_count++;
				
			}
			$v_author_id = 0; 
		}

		
}// END: Author Name

// //:: Ultimas entradas na tabela PRODUCTS BOOKX EXTRA
if (isset($v_bookx_isbn)) {

		$sql = "SELECT * FROM " . TABLE_PRODUCT_BOOKX_EXTRA . " WHERE products_id = '".$v_products_id."' LIMIT 1";
		$result = ep_4_query($sql);

		if ($v_bookx_isbn =='') { // check and warn of empty ISBN (still updates)
			//@fixme - This should act has other warnings. 
			$bookx_reports[LABEL_BOOKX_ISBN][] = sprintf(substr(strip_tags($v_products_name[$epdlanguage_id]), 0, 10)).'...'. $edit_link;
			$ep_error_count++;
	
		} else {
			if ( $row = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result)) == 0)  { // NÃO existe registo
			
				$query = "INSERT INTO " . TABLE_PRODUCT_BOOKX_EXTRA." (products_id, 
				bookx_publisher_id, bookx_series_id,bookx_imprint_id, bookx_binding_id, bookx_printing_id, bookx_condition_id, publishing_date, pages, volume, size, isbn) VALUES ('".$v_products_id."','".$v_publisher_id."','".$v_series_id."',
				'".$v_imprint_id."','". $v_binding_id ."','" . $v_printing_id ."','". $v_condition_id ."','".$v_bookx_publishing_date."','".$v_bookx_pages."','".$v_bookx_volume ."','".$v_bookx_size."','".$v_bookx_isbn."')";
				$result = ep_4_query($query);

				// Theres' always a insert record to bookx_extra_descritpion, even if no subtitle is given
				$sql = ep_4_query("INSERT INTO ".TABLE_PRODUCT_BOOKX_EXTRA_DESCRIPTION." (products_id, languages_id, products_subtitle) VALUES ('".$v_products_id."', '".$epdlanguage_id."', null)"); 

			}
				else {
				
				$query = "UPDATE " . TABLE_PRODUCT_BOOKX_EXTRA . " SET bookx_publisher_id = '".$v_publisher_id."',bookx_series_id = '".$v_series_id."',bookx_imprint_id = '".$v_imprint_id."',bookx_binding_id = '".$v_binding_id."',bookx_printing_id ='".$v_printing_id."',bookx_condition_id= '".$v_condition_id."',
				publishing_date = '".$v_bookx_publishing_date."',pages = '".$v_bookx_pages."',volume = '".$v_bookx_volume ."',size = '".$v_bookx_size."',isbn = '".addslashes($v_bookx_isbn) . "' WHERE products_id = '".$v_products_id."'";
				$result = ep_4_query($query);
				} 		
		}
		
}//ends Bookx Extra 

/*
 *  Needs review
 *  A book ID was inserted in last query, just need to update the subtitle
 */
// Bookx Extra Description 
if (isset($filelayout['v_bookx_subtitle'])) {

	$sql = ep_4_query("SELECT products_subtitle AS subtitle FROM ".TABLE_PRODUCT_BOOKX_EXTRA_DESCRIPTION." WHERE (products_subtitle = '".$v_bookx_subtitle."') and (products_id = '".$v_products_id."') LIMIT 1");

		if ( ($v_bookx_subtitle !='') || ($sql->num_rows == 0) && (mb_strlen($v_bookx_subtitle <= $bookx_subtitle_max_len))) {

			$sql = ep_4_query("UPDATE ".TABLE_PRODUCT_BOOKX_EXTRA_DESCRIPTION." SET languages_id = '".$epdlanguage_id."', products_subtitle = '".addslashes(ep_4_curly_quotes($v_bookx_subtitle))."'  WHERE products_id = '".$v_products_id."'");
		}
	else {

		if (mb_strlen($v_bookx_subtitle > $bookx_subtitle_max_len)) {
		$display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_BOOKX_SUBTITLE_NAME_LONG, $v_bookx_subtitle, $bookx_subtitle_name_max_len);
		 	$ep_error_count++;
		 }
	}
}
