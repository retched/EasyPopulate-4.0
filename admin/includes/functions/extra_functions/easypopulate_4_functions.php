<?php
// $Id: easypopulate_4_functions.php, v4.0.31URI 08-01-2015 mc12345678 $

function ep_4_curly_quotes($curly_text) {
	$ep_curly_quotes = (int)EASYPOPULATE_4_CONFIG_CURLY_QUOTES;
	$ep_char_92 = (int)EASYPOPULATE_4_CONFIG_CHAR_92;
	if ($ep_curly_quotes == 1) { // standard characters
		$clean_text = str_replace(array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
 						array("'", "'", '"', '"', '-', '--', '...'), $curly_text);
	} elseif ($ep_curly_quotes == 2) { // html
 		$clean_text = str_replace(array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
			array("&lsquo;", "&rsquo;", '&ldquo;', '&rdquo;', '&ndash;', '&mdash;', '&hellip;'), $curly_text);
 	} else { // do nothing
		$clean_text = $curly_text;
	}
	// deal with 0x92 ... a funky right-single-quote
	if ($ep_char_92 == 1) { // standard single quote
		$clean_text = str_replace("\x92", "'", $clean_text);
	} elseif ($ep_char_92 == 2) { // html right-single quote
		$clean_text = str_replace("\x92", "&rsquo;", $clean_text);
		//echo '<br>option 2 - html <br>';
	}
	return $clean_text;
}

// function to return field length
// uses $tbl = table name, $fld = field name
function ep4_zen_field_length($tbl, $fld) {
    global $db;
	$project = PROJECT_VERSION_MAJOR.'.'.PROJECT_VERSION_MINOR;
	$ep_uses_mysqli = ((PROJECT_VERSION_MAJOR > '1' || PROJECT_VERSION_MINOR >= '5.3') ? true : false);

	$meta = array();
	$result = ($ep_uses_mysqli ? mysqli_query("SELECT $fld FROM $tbl") : mysql_query("SELECT $fld FROM $tbl"));
	if (!$result) {
    	echo 'Could not run query: ' . ($ep_uses_mysqli ? mysqli_error($db->link) : mysql_error());
    	exit;
	}
	$length = ($ep_uses_mysqli ? mysqli_field_len($result, 0) : mysql_field_len($result, 0));
    return $length;
}

function ep_4_get_languages() {
	$project = PROJECT_VERSION_MAJOR.'.'.PROJECT_VERSION_MINOR;
	$ep_uses_mysqli = ((PROJECT_VERSION_MAJOR > '1' || PROJECT_VERSION_MINOR >= '5.3') ? true : false);
	$langcode = array();
	$languages_query = ep_4_query("SELECT languages_id, name, code FROM ".TABLE_LANGUAGES." ORDER BY sort_order");
	$i = 1;

	while ($ep_languages = ($ep_uses_mysqli ? mysqli_fetch_array($languages_query): mysql_fetch_array($languages_query))) {
		$ep_languages_array[$i++] = array(
			'id' => $ep_languages['languages_id'],
			'name' => $ep_languages['name'],
			'code' => $ep_languages['code']
			);
	}
	return $ep_languages_array;
}

function ep_4_SBA1Exists () {
	$project = PROJECT_VERSION_MAJOR.'.'.PROJECT_VERSION_MINOR;
	$ep_uses_mysqli = ((PROJECT_VERSION_MAJOR > '1' || PROJECT_VERSION_MINOR >= '5.3') ? true : false);
	// The current thought is to have one of these Exists files for each version of SBA to consider; however, they also all could fall under one SBA_Exists check provided some return is made and a comparison done on the other end about what was returned.  
	//Check to see if any version of Stock with attributes is installed (If so, and properly programmed, there should be a define for the table associated with the stock.  There may be more than one, and if so, they should all be verified for the particular SBA.
	if (defined('TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK')) {
		//Now that have identified that the table (applicable to mc12345678's store, has been identified as in existence, now need to look at the setup of the table (Number of columns and if each column identified below is in the table, or conversely if the table's column matches the list below.
		//Columns in table: stock_id, products_id, stock_attributes, quantity, and sort.
//		echo 'In<br />';
//		$colsarray = $db->Execute('SHOW COLUMNS FROM ' . TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK);
		$colsarray = ep_4_query('SHOW COLUMNS FROM ' . TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK);
//		echo 'After execute<br />';
		$numCols = ($ep_uses_mysqli ? mysqli_num_rows($colsarray) : mysql_num_rows($colsarray));
		if ($numCols == 5) {
			while ($row = ($ep_uses_mysqli ? mysqli_fetch_array($colsarray) : mysql_fetch_array($colsarray))){
				switch ($row['Field']) {
					case 'stock_id':
						break;
					case 'products_id':
						break;
					case 'stock_attributes':
						break;
					case 'quantity':
						break;
					case 'sort':
						break;
					default:
						return false;
						break;
						
				}
//				print_r($row);
/*				echo '4<br />';
				if ($row['Field'] == 'stock_id') {
					echo ' true <br />';
				} else {
					echo ' false <br />';
				}
				echo '3<br />'; */
			}
			return '1';
		} elseif ($numCols >= 6) {
      $desired = 0;
      $addToList = array();
			while ($row = ($ep_uses_mysqli ? mysqli_fetch_array($colsarray) : mysql_fetch_array($colsarray))){
				switch ($row['Field']) {
					case 'stock_id':
            $desired++;
            break;
					case 'products_id':
            $desired++;
						break;
					case 'stock_attributes':
            $desired++;
						break;
					case 'quantity':
            $desired++;
						break;
					case 'sort':
            $desired++;
						break;
          case 'customid';
            $desired++;
            break;
          default:
            $addToList = $row['Field'];
            break;
				}
      }
      if ($desired >= 6) {
        return '2';
      } else {
        return false;
      }
		} else {
      return false;
    }
//		$returnedcols = mysql_fetch_array($colsarray);
//		$colnames = array_keys($returnedcols);
/*		echo 'Num Rows: ' . $numCols . '<br />';
		if ($returnedcols['Field'] == 'stock_id') {
			echo 'true <br />';
		} else {
			echo 'false <br />';
		}
		echo '3<br />';
		echo $returnedcols . '111<br />';
		print_r($returnedcols);
		echo '3<br />'; */
		
/*		while ($row = mysql_fetch_array($colsarray)){
			print_r($row);
			echo '4<br />';
		}
		echo '4<br />';*/
		//return true;
	} else {
		return false;
	}
}

function ep_4_CEONURIExists () {
	$project = PROJECT_VERSION_MAJOR.'.'.PROJECT_VERSION_MINOR;
	$ep_uses_mysqli = ((PROJECT_VERSION_MAJOR > '1' || PROJECT_VERSION_MINOR >= '5.3') ? true : false);
	// The current thought is to have one of these Exists files for each version of SBA to consider; however, they also all could fall under one SBA_Exists check provided some return is made and a comparison done on the other end about what was returned.  
	//Check to see if any version of Stock with attributes is installed (If so, and properly programmed, there should be a define for the table associated with the stock.  There may be more than one, and if so, they should all be verified for the particular SBA.
	if (defined('TABLE_CEON_URI_MAPPINGS')) {
		//Now that have identified that the table (applicable to mc12345678's store, has been identified as in existence, now need to look at the setup of the table (Number of columns and if each column identified below is in the table, or conversely if the table's column matches the list below.
		//Columns in table: stock_id, products_id, stock_attributes, quantity, and sort.
//		echo 'In<br />';
//		$colsarray = $db->Execute('SHOW COLUMNS FROM ' . TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK);
		$colsarray = ep_4_query('SHOW COLUMNS FROM ' . TABLE_CEON_URI_MAPPINGS);
//		echo 'After execute<br />';
		$numCols = ($ep_uses_mysqli ? mysqli_num_rows($colsarray) : mysql_num_rows($colsarray));
		if ($numCols == 9) {
			while ($row = ($ep_uses_mysqli ? mysqli_fetch_array($colsarray) : mysql_fetch_array($colsarray))){
				switch ($row['Field']) {
					case 'uri':
						break;
					case 'language_id':
						break;
					case 'current_uri':
						break;
					case 'main_page':
						break;
					case 'query_string_parameters':
						break;
					case 'associated_db_id':
						break;
					case 'alternate_uri':
						break;
					case 'redirection_type_code':
						break;
					case 'date_added':
						break;
					default:
						return false;
						break;
						
				}
			}
      
      if (file_exists(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.CeonURIMappingAdmin.php') ) {
        return true;
      } else {
        return false;
      }
		} else {
			return false;
		}
//		$returnedcols = mysql_fetch_array($colsarray);
//		$colnames = array_keys($returnedcols);
/*		echo 'Num Rows: ' . $numCols . '<br />';
		if ($returnedcols['Field'] == 'stock_id') {
			echo 'true <br />';
		} else {
			echo 'false <br />';
		}
		echo '3<br />';
		echo $returnedcols . '111<br />';
		print_r($returnedcols);
		echo '3<br />'; */
		
/*		while ($row = mysql_fetch_array($colsarray)){
			print_r($row);
			echo '4<br />';
		}
		echo '4<br />';*/
		//return true;
	} else {
		return false;
	}
}

function ep_4_set_filelayout($ep_dltype, &$filelayout_sql, $sql_filter, $langcode, $ep_supported_mods, $custom_fields) {
  global $db, $zco_notifier;
  
	$filelayout = array();
	switch($ep_dltype) {
	case 'SBAStock';
		$filelayout[] = 'v_products_model';
		$filelayout[] = 'v_status';
		foreach ($langcode as $key => $lang) { // create variables for each language id
			$l_id = $lang['id'];
			$filelayout[] = 'v_products_name_'.$l_id;
			$filelayout[] = 'v_products_description_'.$l_id;
			if ($ep_supported_mods['psd'] == true) { // products short description mod
				$filelayout[] = 'v_products_short_desc_'.$l_id;
			}
		}
   	$ep_4_SBAEnabled = ep_4_SBA1Exists();
    if ($ep_4_SBAEnabled == '2') {
      $filelayout[] = 'v_customid';
    }
		//$filelayout[] =	'v_products_options_values_name'; // options values name from table PRODUCTS_OPTIONS_VALUES
		$filelayout[] = 'v_SBA_tracked';
		$filelayout[] = 'v_table_tracker';
		$filelayout[] = 'v_products_attributes'; // options name from table 
		$filelayout[] = 'v_products_quantity';
		
		$filelayout_sql = 'SELECT
			p.products_id					as v_products_id,
			p.products_model				as v_products_model,';
		if (count($custom_fields) > 0) { // User Defined Products Fields
			foreach ($custom_fields as $field) {
				$filelayout_sql .= 'p.'.$field.' as v_'.$field.',';
			}
		}
		$filelayout_sql .= '
			p.products_quantity				as v_products_quantity,
			p.products_status				as v_status 
			FROM '
			.TABLE_PRODUCTS.' as p '
			//.TABLE_CATEGORIES.' as subc,'
			//.TABLE_PRODUCTS_TO_CATEGORIES.' as ptoc
			. ($sql_filter <> '' ? 'WHERE '. $sql_filter : '');
			//p.products_id = ptoc.products_id AND
			/*ptoc.categories_id = subc.categories_id '.$sql_filter;*/
		break;
		
	case 'full': // FULL products download
		$zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_START');
		if (ep_4_CEONURIExists() == true && !(EP4_AUTOCREATE_FROM_BLANK == '0' && EP4_AUTORECREATE_EXISTING == '0')) {
			$ep4CEONURIDoesExist = true;
			require(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'extra_datafiles/ceon_uri_mapping_product_pages.php');  // Brings in extra variables to support product page types.
		}
		// The file layout is dynamically made depending on the number of languages
		$filelayout[] = 'v_products_model';
		$filelayout[] = 'v_products_type'; // 4-23-2012
		$filelayout[] = 'v_products_image';
		foreach ($langcode as $key => $lang) { // create variables for each language id
			$l_id = $lang['id'];
			$filelayout[] = 'v_products_name_'.$l_id;
			$filelayout[] = 'v_products_description_'.$l_id;
			if ($ep_supported_mods['psd'] == true) { // products short description mod
				$filelayout[] = 'v_products_short_desc_'.$l_id;
			}
			$filelayout[] = 'v_products_url_'.$l_id;
		} 
		$zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_FILELAYOUT');
		if ($ep4CEONURIDoesExist == true && !(EP4_AUTOCREATE_FROM_BLANK == '0' && EP4_AUTORECREATE_EXISTING == '0')) {
			$filelayout[] =	'v_products_type';
			foreach ($langcode as $key => $lang) { // create variables for each language id
				$l_id = $lang['id'];
				$filelayout[] =	'v_uri_' . $l_id;
			}
			$filelayout[] =	'v_categories_id';
			$filelayout[] =	'v_main_page';
//Don't need for product			$filelayout[] =	'v_query_string_parameters';
			$filelayout[] =	'v_associated_db_id';
			$filelayout[] =	'v_master_categories_id';
		}
		
		$filelayout[] = 'v_specials_price';
		$filelayout[] = 'v_specials_date_avail';
		$filelayout[] = 'v_specials_expires_date';
		$filelayout[] = 'v_products_price';
		if ($ep_supported_mods['uom'] == true) { // price UOM mod
			$filelayout[] = 'v_products_price_uom';
		} 
		if ($ep_supported_mods['upc'] == true) { // UPC Mod
			$filelayout[] = 'v_products_upc'; 
		}
		if ($ep_supported_mods['gpc'] == true) { // Google Product Category for Google Merchant Center - chadd 10-1-2011
			$filelayout[] = 'v_products_gpc'; 
		}
		if ($ep_supported_mods['msrp'] == true) { // Requested Mod Support - Manufacturer's Suggest Retail Price
			$filelayout[] = 'v_products_msrp'; 
		}
		if ($ep_supported_mods['map'] == true) { // Requested Mod Support - Manufacturer's Advertised Price
      $filelayout[] = 'v_map_enabled';
      $filelayout[] = 'v_map_price';
    }
    if ($ep_supported_mods['gppi'] == true) { // Requested Mod Support - Group Pricing Per Item
			$filelayout[] = 'v_products_group_a_price';
			$filelayout[] = 'v_products_group_b_price';
			$filelayout[] = 'v_products_group_c_price';
			$filelayout[] = 'v_products_group_d_price';
		}
		if ($ep_supported_mods['excl'] == true) { // Exclusive Product Custom Mod
			$filelayout[] = 'v_products_exclusive'; 
		}
		if (count($custom_fields) > 0) { // User Defined Products Fields
			foreach ($custom_fields as $field) {
				$filelayout[] = 'v_'.$field;
			}
		}
		$filelayout[] = 'v_products_weight';
		$filelayout[] = 'v_product_is_call';
		$filelayout[] = 'v_products_sort_order';
		$filelayout[] = 'v_products_quantity_order_min';
		$filelayout[] = 'v_products_quantity_order_units';
		$filelayout[] = 'v_products_priced_by_attribute'; // 4-30-2012
		$filelayout[] = 'v_product_is_always_free_shipping'; // 4-30-2012
		$filelayout[] = 'v_date_avail'; // should be changed to v_products_date_available for clarity
		$filelayout[] = 'v_date_added'; // should be changed to v_products_date_added for clarity
		$filelayout[] = 'v_products_quantity';
		$filelayout[] = 'v_manufacturers_name';
		// NEW code for 'unlimited' category depth - 1 Category Column for each installed Language
		foreach ($langcode as $key => $lang) { // create categories variables for each language id
			$l_id = $lang['id'];
			$filelayout[] = 'v_categories_name_'.$l_id;
		} 
		$filelayout[] = 'v_tax_class_title';
		$filelayout[] = 'v_status'; // this should be v_products_status for clarity
		// metatags - 4-23-2012: added switch
		if ((int)EASYPOPULATE_4_CONFIG_META_DATA) {
			$filelayout[] = 'v_metatags_products_name_status';
			$filelayout[] = 'v_metatags_title_status';
			$filelayout[] = 'v_metatags_model_status';
			$filelayout[] = 'v_metatags_price_status';
			$filelayout[] = 'v_metatags_title_tagline_status';
			foreach ($langcode as $key => $lang) { // create variables for each language id
				$l_id = $lang['id'];
				$filelayout[] = 'v_metatags_title_'.$l_id;
				$filelayout[] = 'v_metatags_keywords_'.$l_id;
				$filelayout[] = 'v_metatags_description_'.$l_id;
			}
		}
		// music info - 4-23-2012
		// record_artist, record_artist_info
		// record_company, record_company_info
		// music_genre
		if ((int)EASYPOPULATE_4_CONFIG_MUSIC_DATA) {
			$filelayout[] = 'v_artists_name';
			$filelayout[] = 'v_artists_image';
			foreach ($langcode as $key => $lang) { // create variables for each language id
				$l_id = $lang['id'];
				$filelayout[] = 'v_artists_url_'.$l_id;
			}			
			$filelayout[] = 'v_record_company_name';
			$filelayout[] = 'v_record_company_image';
			foreach ($langcode as $key => $lang) { // create variables for each language id
				$l_id = $lang['id'];
				$filelayout[] = 'v_record_company_url_'.$l_id;
			}
			$filelayout[] = 'v_music_genre_name';
		}
		$filelayout_sql = 'SELECT ' . (($ep4CEONURIDoesExist && !(EP4_AUTOCREATE_FROM_BLANK == '0' && EP4_AUTORECREATE_EXISTING == '0')) ? 'DISTINCT' : '' ) .
'			   
			p.products_id					as v_products_id,
			p.products_model				as v_products_model,
			p.products_type					as v_products_type,
			p.products_image				as v_products_image,
			p.products_price				as v_products_price,';
		if ($ep_supported_mods['uom'] == true) { // price UOM mod
			$filelayout_sql .=  'p.products_price_uom as v_products_price_uom,'; // to soon be changed to v_products_price_uom
		} 
		if ($ep_supported_mods['upc'] == true) { // UPC Code mod
			$filelayout_sql .=  'p.products_upc as v_products_upc,'; 
		}
		if ($ep_supported_mods['gpc'] == true) { // Google Product Category for Google Merchant Center - chadd 10-1-2011
			$filelayout_sql .=  'p.products_gpc as v_products_gpc,'; 
		}
		if ($ep_supported_mods['msrp'] == true) { // Requested Mod Support - Manufacturer's Suggest Retail Price
			$filelayout_sql .=  'p.products_msrp as v_products_msrp,'; 
		}	
		if ($ep_supported_mods['map'] == true) { // Requested Mod Support - Manufacturer's Advertised Price
      $filelayout_sql .= 'p.map_enabled as v_map_enabled,';
      $filelayout_sql .= 'p.map_price as v_map_price,';
    }
		if ($ep_supported_mods['gppi'] == true) { // Requested Mod Support - Group Pricing Per Item
			$filelayout_sql .=  'p.products_group_a_price as v_products_group_a_price,';
			$filelayout_sql .=  'p.products_group_b_price as v_products_group_b_price,';
			$filelayout_sql .=  'p.products_group_c_price as v_products_group_c_price,';
			$filelayout_sql .=  'p.products_group_d_price as v_products_group_d_price,';
		}
		if ($ep_supported_mods['excl'] == true) { // Custom Mode for Exclusive Products Status VARCHAR(32)
			$filelayout_sql .=  'p.products_exclusive as v_products_exclusive,';
		}
		$zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_SQL_SELECT');
		if ($ep4CEONURIDoesExist == true && !(EP4_AUTOCREATE_FROM_BLANK == '0' && EP4_AUTORECREATE_EXISTING == '0')) {
/*			foreach ($langcode as $key => $lang) { // create variables for each language id
				$l_id = $lang['id'];
				$filelayout_sql .= ' c'.$l_id.'.uri as v_uri_'.$l_id.', ';
				$filelayout_sql .=	'c'.$l_id.'.main_page as v_main_page_'.$l_id. ', ';
				$filelayout_sql .=	'c'.$l_id.'.associated_db_id as v_associated_db_id_'.$l_id.', ';
			}*/
/*			$filelayout_sql .=	'c.uri as v_uri,';
			$filelayout_sql .=	'c.language_id as v_language_id,';
			$filelayout_sql .=	'c.current_uri as v_current_uri,';*/
			$filelayout_sql .=	'c.main_page as v_main_page,';
// Don't need for product			$filelayout_sql .=	'c.query_string_parameters as v_query_string_parameters,';
			$filelayout_sql .=	'c.associated_db_id as v_associated_db_id,';
			$filelayout_sql .=	'p.master_categories_id as v_master_categories_id,';
			$filelayout_sql .=  'ptoc.categories_id as v_categories_id,';
			//$filelayout_sql .=	'c.products_type as v_products_type,';
		}
		if (count($custom_fields) > 0) { // User Defined Products Fields
			foreach ($custom_fields as $field) {
				$filelayout_sql .= 'p.'.$field.' as v_'.$field.',';
			}
		}
		$filelayout_sql .= ' p.products_weight as v_products_weight,
			p.product_is_call				as v_product_is_call,
			p.products_sort_order			as v_products_sort_order, 
			p.products_quantity_order_min	as v_products_quantity_order_min,
			p.products_quantity_order_units	as v_products_quantity_order_units,
			p.products_priced_by_attribute	as v_products_priced_by_attribute,
			p.product_is_always_free_shipping	as v_product_is_always_free_shipping,			
			p.products_date_available		as v_date_avail,
			p.products_date_added			as v_date_added,
			p.products_tax_class_id			as v_tax_class_id,
			p.products_quantity				as v_products_quantity,
			p.master_categories_id				as v_master_categories_id,
			p.manufacturers_id				as v_manufacturers_id,
			subc.categories_id				as v_categories_id,
			p.products_status				as v_status,
			p.metatags_title_status         as v_metatags_title_status,
			p.metatags_products_name_status as v_metatags_products_name_status,
			p.metatags_model_status         as v_metatags_model_status,
			p.metatags_price_status         as v_metatags_price_status,
			p.metatags_title_tagline_status as v_metatags_title_tagline_status 
			FROM '
			.TABLE_CATEGORIES.' as subc, '
			.TABLE_PRODUCTS_TO_CATEGORIES.' as ptoc, '
			.TABLE_PRODUCTS.' as p ';
			$zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_SQL_TABLE');
			if ($ep4CEONURIDoesExist == true && !(EP4_AUTOCREATE_FROM_BLANK == '0' && EP4_AUTORECREATE_EXISTING == '0')) { 
				$filenamelist = implode("','", $ceon_uri_mapping_product_pages);
/*				foreach ($langcode as $key => $lang) { // create variables for each language id
					$l_id = $lang['id'];
					$filelayout_sql .= ' LEFT JOIN '.TABLE_CEON_URI_MAPPINGS.' as c'.$l_id.' 
					ON 
					p.products_id = c'.$l_id.'.associated_db_id AND 
					c'.$l_id.'.main_page IN (\''.$filenamelist.'\') AND
					c'.$l_id.'.language_id = '.$l_id.' AND 
					c'.$l_id.'.current_uri = \'1\' ';
				}*/

				$filelayout_sql .= 'LEFT JOIN '.TABLE_CEON_URI_MAPPINGS.' as c 
				ON 
				p.products_id = c.associated_db_id AND
				c.main_page IN (\''.$filenamelist.'\') AND
				c.current_uri = \'1\' ';
			}
			$filelayout_sql .= 'WHERE 
			p.products_id = ptoc.products_id AND ';
/*            if (sizeof($langcode) > 1 && (EP4_AUTOCREATE_FROM_BLANK == 1 && EP4_AUTORECREATE_EXISTING == '0' || EP4_REWRITE == '1')) { //Need to speed up processing by having less data to work on.
			  $filelayout_sql .= '
			c.uri is null AND ';
			} */
			$filelayout_sql .= '
			ptoc.categories_id = subc.categories_id '.$sql_filter;
		break;

	case 'featured': // added 5-2-2012
		$filelayout[] = 'v_products_model';
		$zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FEATURED_FILELAYOUT');
    if (EP4_DB_FILTER_KEY === 'products_id' || EP4_DB_FILTER_KEY === 'blank_new') {
      $filelayout[] = 'v_products_id';
    }
    $filelayout[] = 'v_status';
		$filelayout[] = 'v_featured_date_added';
		$filelayout[] = 'v_expires_date';
		$filelayout[] = 'v_date_status_change';
		$filelayout[] = 'v_featured_date_available';

		$filelayout_sql = 'SELECT
			p.products_id             as v_products_id,
			p.products_model          as v_products_model,
			f.featured_id             as v_featured_id,
			f.featured_date_added     as v_featured_date_added,
			f.featured_last_modified  as v_featured_date_modified,
			f.expires_date            as v_expires_date,
			f.date_status_change      as v_date_status_change,
			f.status                  as v_status,
			f.featured_date_available as v_featured_date_available
			FROM '
			.TABLE_PRODUCTS.' as p,'
			.TABLE_FEATURED.' as f
			WHERE
			p.products_id = f.products_id';
		break;	
	
	case 'priceqty':
		$filelayout[] = 'v_products_model';
		$filelayout[] = 'v_products_name';
		$filelayout[] = 'v_status'; // 11-23-2010 added product status to price quantity option
		$filelayout[] = 'v_specials_price';
		$filelayout[] = 'v_specials_date_avail';
		$filelayout[] = 'v_specials_expires_date';
		$filelayout[] = 'v_products_price';
		if ($ep_supported_mods['uom'] == true) { // price UOM mod
			$filelayout[] = 'v_products_price_uom';
		}
		if ($ep_supported_mods['msrp'] == true) { // Manufacturer's Suggested Retail Price
			$filelayout[] = 'v_products_msrp'; 
		}
		if ($ep_supported_mods['map'] == true) { // Requested Mod Support - Manufacturer's Advertised Price
      $filelayout[] = 'v_map_enabled';
      $filelayout[] = 'v_map_price';
    }
		$filelayout[] = 'v_products_quantity';
		$filelayout_sql = 'SELECT
			p.products_id     as v_products_id,
			d.products_name   as v_products_name,
			p.products_status as v_status,
			p.products_model  as v_products_model,
			p.products_price  as v_products_price,
			p.manufacturers_id	as v_manufacturers_id,
			subc.categories_id	as v_categories_id,';
		if ($ep_supported_mods['uom'] == true) { // price UOM mod
			$filelayout_sql .= 'p.products_price_uom as v_products_price_uom,';
		}
		if ($ep_supported_mods['msrp'] == true) { // Requested Mod Support - Manufacturer's Suggest Retail Price
			$filelayout_sql .=  'p.products_msrp as v_products_msrp,'; 
		}	
		if ($ep_supported_mods['map'] == true) { // Requested Mod Support - Manufacturer's Advertised Price
      $filelayout_sql .= 'p.map_enabled as v_map_enabled,';
      $filelayout_sql .= 'p.map_price as v_map_price,';
    }
		$filelayout_sql .= 'p.products_tax_class_id as v_tax_class_id,
			p.products_quantity as v_products_quantity
			FROM '		
			.TABLE_PRODUCTS.' as p,'
			.TABLE_PRODUCTS_DESCRIPTION.' as d,'
			.TABLE_CATEGORIES.' as subc,'
			.TABLE_PRODUCTS_TO_CATEGORIES.' as ptoc
			WHERE
			p.products_id = ptoc.products_id AND
			d.products_id = p.products_id AND
			ptoc.categories_id = subc.categories_id '.$sql_filter; // added filter 4-13-2012	
		break;
		
	// Quantity price breaks file layout
	case 'pricebreaks':
		$filelayout[] =	'v_products_model';
		$filelayout[] = 'v_status'; // 11-23-2010 added product status to price quantity option
		$filelayout[] =	'v_products_price';
		if ($ep_supported_mods['uom'] == true) { // price UOM mod
			$filelayout[] = 'v_products_price_uom';
		}
		if ($ep_supported_mods['msrp'] == true) { // Manufacturer's Suggested Retail Price
			$filelayout[] = 'v_products_msrp'; 
		}
		if ($ep_supported_mods['map'] == true) { // Requested Mod Support - Manufacturer's Advertised Price
      $filelayout[] = 'v_map_enabled';
      $filelayout[] = 'v_map_price';
    }
		$filelayout[] =	'v_products_discount_type';
		$filelayout[] =	'v_products_discount_type_from';
		// discount quantities base on $max_qty_discounts	
		// must be a better way to get the maximum discounts used at any given time
		for ($i=1;$i<EASYPOPULATE_4_CONFIG_MAX_QTY_DISCOUNTS+1;$i++) {
			// $filelayout[] = 'v_discount_id_' . $i; // chadd - no longer needed
			$filelayout[] = 'v_discount_qty_'.$i;
			$filelayout[] = 'v_discount_price_'.$i;
		}
		$filelayout_sql = 'SELECT
			p.products_id     as v_products_id,
			p.products_status as v_status,
			p.products_model  as v_products_model,
			p.products_price  as v_products_price,
			p.manufacturers_id	as v_manufacturers_id,
			subc.categories_id	as v_categories_id,';
		if ($ep_supported_mods['uom'] == true) { // price UOM mod
			$filelayout_sql .= 'p.products_price_uom as v_products_price_uom,';
		}
		if ($ep_supported_mods['msrp'] == true) { // Requested Mod Support - Manufacturer's Suggest Retail Price
			$filelayout_sql .=  'p.products_msrp as v_products_msrp,'; 
		}	
		if ($ep_supported_mods['map'] == true) { // Requested Mod Support - Manufacturer's Advertised Price
      $filelayout_sql .= 'p.map_enabled as v_map_enabled,';
      $filelayout_sql .= 'p.map_price as v_map_price,';
    }
		$filelayout_sql .= 'p.products_discount_type as v_products_discount_type,
			p.products_discount_type_from as v_products_discount_type_from
			FROM '
			.TABLE_PRODUCTS.' as p,'
			.TABLE_CATEGORIES.' as subc,'
			.TABLE_PRODUCTS_TO_CATEGORIES.' as ptoc
			WHERE
			p.products_id = ptoc.products_id AND
			ptoc.categories_id = subc.categories_id '.$sql_filter; // added filter 4-13-2012		
	break;	

	case 'category': 
       $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CATEGORY_FILELAYOUT');
		if (ep_4_CEONURIExists() == true && !(EP4_AUTOCREATE_CAT_FROM_BLANK == '0' && EP4_AUTORECREATE_CAT_EXISTING == '0')) {
			$ep4CEONURIDoesExist = true;
			require(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'extra_datafiles/ceon_uri_mapping_product_pages.php');  // Brings in extra variables to support product page types.
		}
		// The file layout is dynamically made depending on the number of languages
		$filelayout[] = 'v_products_model';
    if (EP4_DB_FILTER_KEY != 'products_model') {
      $filelayout[] = 'v_' . (EP4_DB_FILTER_KEY === 'blank_new' ? 'products_id' : EP4_DB_FILTER_KEY);
    }
    // NEW code for unlimited category depth - 1 Category Column for each installed Language
		foreach ($langcode as $key => $lang) { // create categories variables for each language id
			$l_id = $lang['id'];
			$filelayout[] = 'v_categories_name_'.$l_id;
		} 
		if ($ep4CEONURIDoesExist == true && !(EP4_AUTOCREATE_CAT_FROM_BLANK == '0' && EP4_AUTORECREATE_CAT_EXISTING == '0')) {
			foreach ($langcode as $key => $lang) { // create variables for each language id
				$l_id = $lang['id'];
				$filelayout[] =	'v_uri_' . $l_id;
			}
			$filelayout[] =	'v_categories_id';
			$filelayout[] =	'v_main_page';
//Don't need for product			$filelayout[] =	'v_query_string_parameters';
			$filelayout[] =	'v_associated_db_id';
			$filelayout[] =	'v_master_categories_id';
		}
		$filelayout_sql = 'SELECT
			p.products_id      as v_products_id,
			p.products_model   as v_products_model,
			subc.categories_id as v_categories_id
			FROM '
			.TABLE_PRODUCTS.'   as p,'
			.TABLE_CATEGORIES.' as subc,'
			.TABLE_PRODUCTS_TO_CATEGORIES.' as ptoc      
			WHERE
			p.products_id = ptoc.products_id AND
			ptoc.categories_id = subc.categories_id';
		break;

    // Categories Meta Data - added 12-02-2010
	// 12-10-2010 removed array_merge() for better performance
	case 'categorymeta':
	    $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CATEGORYMETA_FILELAYOUT');
		if (ep_4_CEONURIExists() == true && !(EP4_AUTOCREATE_CAT_FROM_BLANK == '0' && EP4_AUTORECREATE_CAT_EXISTING == '0')) {
			$ep4CEONURIDoesExist = true;
			require(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'extra_datafiles/ceon_uri_mapping_product_pages.php');  // Brings in extra variables to support product page types.
		}
		$fileMeta = array();
		$filelayout = array();
		$filelayout[] = 'v_categories_id';
		$filelayout[] = 'v_categories_image';
    foreach ($langcode as $key => $lang) { // create categories variables for each language id
			$l_id = $lang['id'];
			$filelayout[] = 'v_categories_name_'.$l_id;
			$filelayout[] = 'v_categories_description_'.$l_id;
			$filelayout[] = 'v_uri_' . $l_id;
		} 
		if ($ep4CEONURIDoesExist == true && !(EP4_AUTOCREATE_CAT_FROM_BLANK == '0' && EP4_AUTORECREATE_CAT_EXISTING == '0')) {
			$filelayout[] =	'v_categories_id';
			$filelayout[] =	'v_main_page';
//Don't need for product			$filelayout[] =	'v_query_string_parameters';
			$filelayout[] =	'v_associated_db_id';
			$filelayout[] =	'v_master_categories_id';
		}
		foreach ($langcode as $key => $lang) { // create metatags variables for each language id
			$l_id = $lang['id'];
			$filelayout[]   = 'v_metatags_title_'.$l_id;
			$filelayout[]   = 'v_metatags_keywords_'.$l_id;
			$filelayout[]   = 'v_metatags_description_'.$l_id;
		} 
    $filelayout[] = 'v_sort_order';
		$filelayout_sql = 'SELECT
			c.categories_id    AS v_categories_id,
			c.categories_image AS v_categories_image,
      c.sort_order    as v_sort_order
			FROM '
			.TABLE_CATEGORIES.' AS c';
		break;
	
	case 'CEON_EZPages':
		if (ep_4_CEONURIExists() == true && !(EP4_AUTOCREATE_EZ_FROM_BLANK == '0' && EP4_AUTORECREATE_EZ_EXISTING == '0')) {
			$ep4CEONURIDoesExist = true;
			require(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'extra_datafiles/ceon_uri_mapping_product_pages.php');  // Brings in extra variables to support product page types.
		}
//		$fileMeta = array();
		$filelayout = array();
		$filelayout[] = 'v_pages_id';
		$filelayout[] = 'v_languages_id';
		$filelayout[] = 'v_pages_title';
		$filelayout[] = 'v_alt_url';
		$filelayout[] = 'v_alt_url_external';
		$filelayout[] = 'v_pages_html_text';
		$filelayout[] = 'v_status_header';
		$filelayout[] = 'v_status_sidebox';
		$filelayout[] = 'v_status_footer';
		$filelayout[] = 'v_status_toc';
		$filelayout[] = 'v_header_sort_order';
		$filelayout[] = 'v_sidebox_sort_order';
		$filelayout[] = 'v_footer_sort_order';
		$filelayout[] = 'v_toc_sort_order';
		$filelayout[] = 'v_page_open_new_window';
		$filelayout[] = 'v_page_is_ssl';
		$filelayout[] = 'v_toc_chapter';

		if ($ep4CEONURIDoesExist == true && !(EP4_AUTOCREATE_EZ_FROM_BLANK == '0' && EP4_AUTORECREATE_EZ_EXISTING == '0')) {
			foreach ($langcode as $key => $lang) { // create categories variables for each language id
				$l_id = $lang['id'];
	//			$filelayout[] = 'v_categories_name_'.$l_id;
	//			$filelayout[] = 'v_categories_description_'.$l_id;
				$filelayout[] = 'v_uri_' . $l_id;
			}
//			$filelayout[] =	'v_categories_id';
			$filelayout[] =	'v_main_page';
//Don't need for product			$filelayout[] =	'v_query_string_parameters';
			$filelayout[] =	'v_associated_db_id';
//			$filelayout[] =	'v_master_categories_id';
			$filelayout[] =	'v_alternate_uri';
			$filelayout[] =	'v_redirection_type_code';
		}
		foreach ($langcode as $key => $lang) { // create metatags variables for each language id
			$l_id = $lang['id'];
			$filelayout[]   = 'v_metatags_title_'.$l_id;
			$filelayout[]   = 'v_metatags_keywords_'.$l_id;
			$filelayout[]   = 'v_metatags_description_'.$l_id;
		} 
		$filelayout_sql = 'SELECT ' . (($ep4CEONURIDoesExist && !(EP4_AUTOCREATE_EZ_FROM_BLANK == '0' && EP4_AUTORECREATE_EZ_EXISTING == '0')) ? 'DISTINCT' : '');
		if ($ep4CEONURIDoesExist == true && !(EP4_AUTOCREATE_EZ_FROM_BLANK == '0' && EP4_AUTORECREATE_EZ_EXISTING == '0')) {
/*			$filelayout_sql .=	'c.uri as v_uri,';
			$filelayout_sql .=	'c.language_id as v_language_id,';
			$filelayout_sql .=	'c.current_uri as v_current_uri,';*/
//			$filelayout_sql .=	'ez.main_page as v_main_page,';
// Don't need for product			$filelayout_sql .=	'c.query_string_parameters as v_query_string_parameters,';
//			$filelayout_sql .=	'c.associated_db_id as v_associated_db_id,';
//			$filelayout_sql .=	'c.master_categories_id as v_master_categories_id,';
			//$filelayout_sql .=  'ptoc.categories_id as v_categories_id,';
			//$filelayout_sql .=	'c.products_type as v_products_type,';
		}
			$filelayout_sql .=  '		ez.pages_id AS v_pages_id, 
		ez.languages_id AS v_languages_id, 
		ez.pages_title AS v_pages_title, 
		ez.alt_url AS v_alt_url, 
		ez.alt_url_external AS v_alt_url_external, 
		ez.pages_html_text AS v_pages_html_text, 
		ez.status_header AS v_status_header, 
		ez.status_sidebox AS v_status_sidebox, 
		ez.status_footer AS v_status_footer, 
		ez.status_toc AS v_status_toc, 
		ez.header_sort_order AS v_header_sort_order, 
		ez.sidebox_sort_order AS v_sidebox_sort_order, 
		ez.footer_sort_order AS v_footer_sort_order, 
		ez.toc_sort_order AS v_toc_sort_order, 
		ez.page_open_new_window AS v_page_open_new_window, 
		ez.page_is_ssl AS v_page_is_ssl, 
		ez.toc_chapter AS v_toc_chapter 
			FROM '
			.TABLE_EZPAGES.' AS ez';
/*			if ($ep4CEONURIDoesExist == true) { 
//				$filenamelist = implode("','", $ceon_uri_mapping_product_pages);
				$filelayout_sql .= 'LEFT JOIN '.TABLE_CEON_URI_MAPPINGS.' as c 
				ON 
				p.products_id = c.associated_db_id AND
				c.main_page IN (\''.FILENAME_EZPAGES.'\') AND
				c.current_uri = \'1\' ';
			}*/
		break;
		
	case 'attrib_detailed':
		$filelayout[] =	'v_products_attributes_id';
		$filelayout[] =	'v_products_id';
		$filelayout[] =	'v_products_model'; // product model from table PRODUCTS
		$filelayout[] =	'v_options_id';
		$filelayout[] =	'v_products_options_name'; // options name from table PRODUCTS_OPTIONS
		$filelayout[] =	'v_products_options_type'; // 0-drop down, 1=text , 2=radio , 3=checkbox, 4=file, 5=read only 
		$filelayout[] =	'v_options_values_id';
		$filelayout[] =	'v_products_options_values_name'; // options values name from table PRODUCTS_OPTIONS_VALUES
		$filelayout[] =	'v_options_values_price';
    if ($ep_supported_mods['dual']) {
      $filelayout[] = 'v_options_values_price_w';
    }
		$filelayout[] =	'v_price_prefix';
		$filelayout[] =	'v_products_options_sort_order';
		$filelayout[] =	'v_product_attribute_is_free';
		$filelayout[] =	'v_products_attributes_weight';
		$filelayout[] =	'v_products_attributes_weight_prefix';
		$filelayout[] =	'v_attributes_display_only';
		$filelayout[] =	'v_attributes_default';
		$filelayout[] =	'v_attributes_discounted';
		$filelayout[] =	'v_attributes_image';
		$filelayout[] =	'v_attributes_price_base_included';
		$filelayout[] =	'v_attributes_price_onetime';
		$filelayout[] =	'v_attributes_price_factor';
		$filelayout[] =	'v_attributes_price_factor_offset';
		$filelayout[] =	'v_attributes_price_factor_onetime';
		$filelayout[] =	'v_attributes_price_factor_onetime_offset';
		$filelayout[] =	'v_attributes_qty_prices';
		$filelayout[] =	'v_attributes_qty_prices_onetime';
		$filelayout[] =	'v_attributes_price_words';
		$filelayout[] =	'v_attributes_price_words_free';
		$filelayout[] =	'v_attributes_price_letters';
		$filelayout[] =	'v_attributes_price_letters_free';
		$filelayout[] =	'v_attributes_required';
// table TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD		
		$filelayout[] =	'v_products_attributes_filename';
		$filelayout[] =	'v_products_attributes_maxdays';
		$filelayout[] =	'v_products_attributes_maxcount';
		
		
		// a = table PRODUCTS_ATTRIBUTES
		// p = table PRODUCTS
		// o = table PRODUCTS_OPTIONS
		// v = table PRODUCTS_OPTIONS_VALUES
		// d = table PRODUCTS_ATTRIBUTES_DOWNLOAD
		$filelayout_sql = 'SELECT
			a.products_attributes_id            as v_products_attributes_id,
			a.products_id                       as v_products_id,
			p.products_model				    as v_products_model,
			a.options_id                        as v_options_id,
			o.products_options_id               as v_products_options_id,
			o.products_options_name             as v_products_options_name,
			o.products_options_type             as v_products_options_type,
			a.options_values_id                 as v_options_values_id,
			v.products_options_values_id        as v_products_options_values_id,
			v.products_options_values_name      as v_products_options_values_name,
			a.options_values_price              as v_options_values_price, ';
    if ($ep_supported_mods['dual']) {
$filelayout_sql .= '
      a.options_values_price_w            as v_options_values_price_w,
      ';
    }
$filelayout_sql .= '
			a.price_prefix                      as v_price_prefix,
			a.products_options_sort_order       as v_products_options_sort_order,
			a.product_attribute_is_free         as v_product_attribute_is_free,
			a.products_attributes_weight        as v_products_attributes_weight,
			a.products_attributes_weight_prefix as v_products_attributes_weight_prefix,
			a.attributes_display_only           as v_attributes_display_only,
			a.attributes_default                as v_attributes_default,
			a.attributes_discounted             as v_attributes_discounted,
			a.attributes_image                  as v_attributes_image,
			a.attributes_price_base_included    as v_attributes_price_base_included,
			a.attributes_price_onetime          as v_attributes_price_onetime,
			a.attributes_price_factor           as v_attributes_price_factor,
			a.attributes_price_factor_offset    as v_attributes_price_factor_offset,
			a.attributes_price_factor_onetime   as v_attributes_price_factor_onetime,
			a.attributes_price_factor_onetime_offset      as v_attributes_price_factor_onetime_offset,
			a.attributes_qty_prices             as v_attributes_qty_prices,
			a.attributes_qty_prices_onetime     as v_attributes_qty_prices_onetime,
			a.attributes_price_words            as v_attributes_price_words,
			a.attributes_price_words_free       as v_attributes_price_words_free,
			a.attributes_price_letters          as v_attributes_price_letters,
			a.attributes_price_letters_free     as v_attributes_price_letters_free,
			a.attributes_required               as v_attributes_required 
			FROM '
			.TABLE_PRODUCTS_ATTRIBUTES.     ' as a,'
			.TABLE_PRODUCTS.                ' as p,'
			.TABLE_PRODUCTS_OPTIONS.        ' as o,'
			.TABLE_PRODUCTS_OPTIONS_VALUES. ' as v
			WHERE
			a.products_id       = p.products_id AND
			a.options_id        = o.products_options_id AND
			a.options_values_id = v.products_options_values_id AND
			o.language_id       = v.language_id AND
			o.language_id       = 1 ORDER BY a.products_id, a.options_id, v.products_options_values_id';
 		break;


	case 'attrib_basic': // simplified sinlge-line attributes ... eventually!
		// $filelayout[] =	'v_products_attributes_id';
		// $filelayout[] =	'v_products_id';
		$filelayout[] =	'v_products_model'; // product model from table PRODUCTS
		$filelayout[] =	'v_products_options_type'; // 0-drop down, 1=text , 2=radio , 3=checkbox, 4=file, 5=read only 
		foreach ($langcode as $key => $lang) { // create categories variables for each language id
			$l_id = $lang['id'];
			$filelayout[] = 'v_products_options_name_'.$l_id;
		} 
		foreach ($langcode as $key => $lang) { // create categories variables for each language id
			$l_id = $lang['id'];
			$filelayout[] = 'v_products_options_values_name_'.$l_id;
		}
		// a = table PRODUCTS_ATTRIBUTES
		// p = table PRODUCTS
		// o = table PRODUCTS_OPTIONS
		// v = table PRODUCTS_OPTIONS_VALUES
		$filelayout_sql = 'SELECT
			a.products_attributes_id            as v_products_attributes_id,
			a.products_id                       as v_products_id,
			a.options_id                        as v_options_id,
			a.options_values_id                 as v_options_values_id,
			p.products_model				    as v_products_model,
			o.products_options_id               as v_products_options_id,
			o.products_options_name             as v_products_options_name,
			o.products_options_type             as v_products_options_type,
			v.products_options_values_id        as v_products_options_values_id,
			v.products_options_values_name      as v_products_options_values_name,
			v.language_id                       as v_language_id
			FROM '
			.TABLE_PRODUCTS_ATTRIBUTES.     ' as a,'
			.TABLE_PRODUCTS.                ' as p,'
			.TABLE_PRODUCTS_OPTIONS.        ' as o,'
			.TABLE_PRODUCTS_OPTIONS_VALUES. ' as v
			WHERE
			a.products_id       = p.products_id AND
			a.options_id        = o.products_options_id AND
			a.options_values_id = v.products_options_values_id AND
			o.language_id       = v.language_id ORDER BY a.products_id, a.options_id, v.language_id, v.products_options_values_id';
 		break;

	case 'SBA_basic': // simplified sinlge-line attributes ... eventually!
		// $filelayout[] =	'v_products_attributes_id';
		// $filelayout[] =	'v_products_id';
		$filelayout[] =	'v_products_model'; // product model from table PRODUCTS
		// p = table PRODUCTS
		// o = table PRODUCTS_OPTIONS
		// v = table PRODUCTS_OPTIONS_VALUES
		$filelayout_sql = 'SELECT
			a.products_attributes_id            as v_products_attributes_id,
			a.products_id                       as v_products_id,
			a.options_id                        as v_options_id,
			a.options_values_id                 as v_options_values_id,
			p.products_model				    as v_products_model,
			o.products_options_id               as v_products_options_id,
			o.products_options_name             as v_products_options_name,
			o.products_options_type             as v_products_options_type,
			v.products_options_values_id        as v_products_options_values_id,
			v.products_options_values_name      as v_products_options_values_name,
			v.language_id                       as v_language_id
			FROM '
			.TABLE_PRODUCTS_ATTRIBUTES.     ' as a,'
			.TABLE_PRODUCTS.                ' as p,'
			.TABLE_PRODUCTS_OPTIONS.        ' as o,'
			.TABLE_PRODUCTS_OPTIONS_VALUES. ' as v
			WHERE
			a.products_id       = p.products_id AND
			a.options_id        = o.products_options_id AND
			a.options_values_id = v.products_options_values_id AND
			o.language_id       = v.language_id ORDER BY a.products_id, a.options_id, v.language_id, v.products_options_values_id';
 		break;
		
	case 'SBA_detailed':
		$filelayout[] =	'v_stock_id'; // stock id from SBA table
		$filelayout[] =	'v_products_id';
		$filelayout[] =	'v_stock_attributes'; 
		$filelayout[] =	'v_products_model'; // product model from table PRODUCTS
		$filelayout[] =	'v_quantity';
   	$ep_4_SBAEnabled = ep_4_SBA1Exists();
    if ($ep_4_SBAEnabled == '2') {
      $filelayout[] = 'v_customid';
    }
		$filelayout[] =	'v_sort';
		$filelayout[] =	'v_products_name'; // product name from table PRODUCTS
		$filelayout[] =	'v_products_options_name'; // options name from table PRODUCTS_OPTIONS
		$filelayout[] =	'v_products_options_values_name'; // options values name from table PRODUCTS_OPTIONS_VALUES
		$filelayout[] =	'v_products_attributes_id';
		$filelayout[] =	'v_products_options_type'; // 0-drop down, 1=text , 2=radio , 3=checkbox, 4=file, 5=read only 
		$filelayout[] =	'v_options_id';
		$filelayout[] =	'v_options_values_id';
//		$filelayout[] =	'v_options_values_price';
//		$filelayout[] =	'v_price_prefix';
//		$filelayout[] =	'v_products_options_sort_order';
//		$filelayout[] =	'v_product_attribute_is_free';
//		$filelayout[] =	'v_products_attributes_weight';
//		$filelayout[] =	'v_products_attributes_weight_prefix';
//		$filelayout[] =	'v_attributes_display_only';
//		$filelayout[] =	'v_attributes_default';
//		$filelayout[] =	'v_attributes_discounted';
//		$filelayout[] =	'v_attributes_image';
//		$filelayout[] =	'v_attributes_price_base_included';
//		$filelayout[] =	'v_attributes_price_onetime';
//		$filelayout[] =	'v_attributes_price_factor';
//		$filelayout[] =	'v_attributes_price_factor_offset';
//		$filelayout[] =	'v_attributes_price_factor_onetime';
//		$filelayout[] =	'v_attributes_price_factor_onetime_offset';
//		$filelayout[] =	'v_attributes_qty_prices';
//		$filelayout[] =	'v_attributes_qty_prices_onetime';
//		$filelayout[] =	'v_attributes_price_words';
//		$filelayout[] =	'v_attributes_price_words_free';
//		$filelayout[] =	'v_attributes_price_letters';
//		$filelayout[] =	'v_attributes_price_letters_free';
//		$filelayout[] =	'v_attributes_required';
// table TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD		
//		$filelayout[] =	'v_products_SBA_filename';
		$filelayout[] =	'v_products_attributes_filename';
		$filelayout[] =	'v_products_attributes_maxdays';
		$filelayout[] =	'v_products_attributes_maxcount';
		
		// a = table PRODUCTS_ATTRIBUTES
		// p = table PRODUCTS
		// o = table PRODUCTS_OPTIONS
		// v = table PRODUCTS_OPTIONS_VALUES
		// d = table PRODUCTS_ATTRIBUTES_DOWNLOAD
		// s = table PRODUCTS_WITH_ATTRIBUTES_STOCK
		// pd = table PRODUCTS_DESCRIPTIONS
		$filelayout_sql = 'SELECT DISTINCT
			a.products_attributes_id            as v_products_attributes_id,
			a.products_id                       as v_products_id,
			p.products_model				    as v_products_model,
			a.options_id                        as v_options_id,
			o.products_options_id               as v_products_options_id,
			o.products_options_name             as v_products_options_name,
			o.products_options_type             as v_products_options_type,
			a.options_values_id                 as v_options_values_id,
			v.products_options_values_id        as v_products_options_values_id,
			v.products_options_values_name      as v_products_options_values_name,'./*
			a.options_values_price              as v_options_values_price,
			a.price_prefix                      as v_price_prefix,
			a.products_options_sort_order       as v_products_options_sort_order,
			a.product_attribute_is_free         as v_product_attribute_is_free,
			a.products_attributes_weight        as v_products_attributes_weight,
			a.products_attributes_weight_prefix as v_products_attributes_weight_prefix,
			a.attributes_display_only           as v_attributes_display_only,
			a.attributes_default                as v_attributes_default,
			a.attributes_discounted             as v_attributes_discounted,
			a.attributes_image                  as v_attributes_image,
			a.attributes_price_base_included    as v_attributes_price_base_included,
			a.attributes_price_onetime          as v_attributes_price_onetime,
			a.attributes_price_factor           as v_attributes_price_factor,
			a.attributes_price_factor_offset    as v_attributes_price_factor_offset,
			a.attributes_price_factor_onetime   as v_attributes_price_factor_onetime,
			a.attributes_price_factor_onetime_offset      as v_attributes_price_factor_onetime_offset,
			a.attributes_qty_prices             as v_attributes_qty_prices,
			a.attributes_qty_prices_onetime     as v_attributes_qty_prices_onetime,
			a.attributes_price_words            as v_attributes_price_words,
			a.attributes_price_words_free       as v_attributes_price_words_free,
			a.attributes_price_letters          as v_attributes_price_letters,
			a.attributes_price_letters_free     as v_attributes_price_letters_free,
			a.attributes_required               as v_attributes_required, */
			's.stock_id					 as v_stock_id,
			s.stock_attributes				 as v_stock_attributes,
			s.quantity					 as v_quantity,
			s.sort						 as v_sort,
			pd.products_name				 as v_products_name' . ( $ep_4_SBAEnabled == '2' ? ',
        s.customid            as v_customid ' : ' ') .
				'FROM '
			.TABLE_PRODUCTS_ATTRIBUTES.     ' as a,'
			.TABLE_PRODUCTS.                ' as p,'
			.TABLE_PRODUCTS_OPTIONS.        ' as o,'
			.TABLE_PRODUCTS_OPTIONS_VALUES. ' as v,'
			.TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK. ' as s,'
			.TABLE_PRODUCTS_DESCRIPTION.	  ' as pd
			WHERE
			a.products_id       = p.products_id AND
			pd.products_id		= p.products_id AND
			a.options_id        = o.products_options_id AND
			a.options_values_id = v.products_options_values_id AND
			o.language_id       = v.language_id AND
			o.language_id       = 1 AND
			s.products_id		= p.products_id AND
			s.stock_attributes	= a.products_attributes_id
			ORDER BY a.products_id, a.options_id, v.products_options_values_id';
 		break;

	case 'CEON_URI_active_all':
		$filelayout[] =	'v_uri';
		$filelayout[] =	'v_language_id';
		$filelayout[] =	'v_current_uri';
		$filelayout[] =	'v_main_page';
		$filelayout[] =	'v_query_string_parameters';
		$filelayout[] =	'v_associated_db_id';
		$filelayout[] =	'v_date_added';
		$filelayout[] =	'v_products_model'; // product model from table PRODUCTS translated from I think v_associated_db_id
		// p = table PRODUCTS
		// c = table CEON_URI_MAPPINGS
		// o = table PRODUCTS_OPTIONS
		// v = table PRODUCTS_OPTIONS_VALUES
		$filelayout_sql = 'SELECT
			c.uri						 as v_uri,
			c.language_id					 as v_language_id,
			c.current_uri					 as v_current_uri,
			c.main_page					 as v_main_page,
			c.query_string_parameters		 as v_query_string_parameters,
			c.associated_db_id				 as v_associated_db_id,
			c.date_added					 as v_date_added ' . /*
			p.products_model				 as v_products_model */'
			FROM '
			.TABLE_CEON_URI_MAPPINGS.	  ' as c
			 WHERE
			c.current_uri		= 1 
			ORDER BY c.main_page, c.associated_db_id, c.date_added'; 
			   /*AND
			a.products_id       = p.products_id AND
			a.options_id        = o.products_options_id AND
			a.options_values_id = v.products_options_values_id AND
			o.language_id       = v.language_id ORDER BY a.products_id, a.options_id, v.language_id, v.products_options_values_id'; */
		break;
		
	case 'options':
		$filelayout[] =	'v_products_options_id';
		$filelayout[] =	'v_language_id';
		$filelayout[] =	'v_products_options_name';
		$filelayout[] =	'v_products_options_sort_order';
		$filelayout[] =	'v_products_options_type';
		$filelayout[] =	'v_products_options_length';
		$filelayout[] =	'v_products_options_comment';
		$filelayout[] =	'v_products_options_size';
		$filelayout[] =	'v_products_options_images_per_row';
		$filelayout[] =	'v_products_options_images_style';
		$filelayout[] =	'v_products_options_rows';
		// o = table PRODUCTS_OPTIONS
		$filelayout_sql = 'SELECT
			o.products_options_id             AS v_products_options_id,
			o.language_id                     AS v_language_id,
			o.products_options_name           AS v_products_options_name,
			o.products_options_sort_order     AS v_products_options_sort_order,
			o.products_options_type           AS v_products_options_type,
			o.products_options_length         AS v_products_options_length,
			o.products_options_comment        AS v_products_options_comment,
			o.products_options_size           AS v_products_options_size,
			o.products_options_images_per_row AS v_products_options_images_per_row,
			o.products_options_images_style   AS v_products_options_images_style,
			o.products_options_rows           AS v_products_options_rows '
			.' FROM '
			.TABLE_PRODUCTS_OPTIONS. ' AS o';
		break;
	
	case 'values':
		$filelayout[] =	'v_products_options_values_id';
		$filelayout[] =	'v_language_id';
		$filelayout[] =	'v_products_options_values_name';
		$filelayout[] =	'v_products_options_values_sort_order';
		// v = table PRODUCTS_OPTIONS_VALUES
		$filelayout_sql = 'SELECT
			v.products_options_values_id         AS v_products_options_values_id,
			v.language_id                        AS v_language_id,
			v.products_options_values_name       AS v_products_options_values_name,
			v.products_options_values_sort_order AS v_products_options_values_sort_order '
			.' FROM '
			.TABLE_PRODUCTS_OPTIONS_VALUES. ' AS v'; 
		break;

	case 'optionvalues':
		$filelayout[] =	'v_products_options_values_to_products_options_id';
		$filelayout[] =	'v_products_options_id';
		$filelayout[] =	'v_products_options_name';
		$filelayout[] =	'v_products_options_values_id';
		$filelayout[] =	'v_products_options_values_name';
		// o = table PRODUCTS_OPTIONS
		// v = table PRODUCTS_OPTIONS_VALUES
		// otv = table PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS
		$filelayout_sql = 'SELECT
			otv.products_options_values_to_products_options_id AS v_products_options_values_to_products_options_id,   	    	 
			otv.products_options_id           AS v_products_options_id,
			o.products_options_name           AS v_products_options_name,
			otv.products_options_values_id    AS v_products_options_values_id,
			v.products_options_values_name    AS v_products_options_values_name '
			.' FROM '
			.TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS. ' AS otv, '
			.TABLE_PRODUCTS_OPTIONS.        ' AS o, '
			.TABLE_PRODUCTS_OPTIONS_VALUES. ' AS v 
			WHERE 
			otv.products_options_id        = o.products_options_id AND
			otv.products_options_values_id = v.products_options_values_id'; 
		break;

  case 'orders_3': // No attributes
  case 'orders_1': // Export All
  case 'orders_2': // New Export all
  case 'orders_4': // Attributes Only
    // Filelayout is the same for orders_1 and orders_2
    $filelayout[] =	'v_date_purchased'; 
    $filelayout[] =	'v_orders_status_name';
    $filelayout[] =	'v_orders_id' ; 
    $filelayout[] =	'v_customers_id'; 
    $filelayout[] =	'v_customers_name'; 
    $filelayout[] =	'v_customers_company'; 
    $filelayout[] =	'v_customers_street_address'; 
    $filelayout[] =	'v_customers_suburb'; 
    $filelayout[] =	'v_customers_city'; 
    $filelayout[] =	'v_customers_postcode'; 
    //'v_customers_state'; 
    $filelayout[] =	'v_customers_country'; 
    $filelayout[] =	'v_customers_telephone'; 
    $filelayout[] =	'v_customers_email_address'; 
    $filelayout[] =	'v_products_model'; 
    $filelayout[] =	'v_products_name'; 
    if ($ep_dltype != 'orders_3') {
    $filelayout[] =	'v_products_options'; 
    $filelayout[] =	'v_products_options_values';
    }
    $filelayout[] = 'v_products_comments';

    // 'all types of query'
    $filelayout_sql = "SELECT DISTINCT 
      zo.orders_id as v_orders_id,
      zop.products_id as v_products_id,
      customers_id as v_customers_id,
      customers_name as v_customers_name,
      customers_company as v_customers_company,
      customers_street_address as v_customers_street_address,
      customers_suburb as v_customers_suburb,
      customers_city as v_customers_city,
      customers_postcode as v_customers_postcode,
      customers_country as v_customers_country,
      customers_telephone as v_customers_telephone,
      customers_email_address as v_customers_email_address,
      date_purchased as v_date_purchased,
      orders_status_name as v_orders_status_name,
      products_model as v_products_model,
      products_name as v_products_name,
      " . ( $ep_dltype != 'orders_3' ?
	  "products_options as v_products_options,
      products_options_values as v_products_options_values,
	  " : "") . 
	  "zo.order_total as v_total_cost,
      osh.comments as V_orders_comments 
      FROM " . TABLE_ORDERS . " zo, " . ($ep_dltype != 'orders_3' ? TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " opa, " : "") . 
	  TABLE_ORDERS_PRODUCTS . " zop, " . TABLE_ORDERS_STATUS." zos, " .
	  TABLE_ORDERS_STATUS_HISTORY . " osh 
      WHERE zo.orders_id = zop.orders_id AND
	  osh.orders_id = zo.orders_id 
      " . (($ep_dltype == 'orders_2' || $ep_dltype == 'orders_4') ? " AND zos.orders_status_id != :orders_status_id: " : "") . 
	  ($ep_dltype != 'orders_3' ? " AND zop.orders_products_id = opa.orders_products_id" : "") . "
      AND zo.orders_status = zos.orders_status_id 
		";
    $filelayout_sql = $db->bindVars($filelayout_sql, ':orders_status_id:', $_POST['configuration[order_status]'], 'integer');

//    echo $filelayout[] = $filelayout_sql;
    break;
  default:
    $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CASE_DEFAULT');
    break;
	}
  
return $filelayout;;
}

if (!function_exists(zen_get_sub_categories)) {
	function zen_get_sub_categories(&$categories, $categories_id) {
		global $db;
		$project = PROJECT_VERSION_MAJOR.'.'.PROJECT_VERSION_MINOR;
		$ep_uses_mysqli = ((PROJECT_VERSION_MAJOR > '1' || PROJECT_VERSION_MINOR >= '5.3') ? true : false);
		$sub_categories_query = ($ep_uses_mysqli ? mysqli_query($db->link, "SELECT categories_id FROM ".TABLE_CATEGORIES.
			" WHERE parent_id = '".(int)$categories_id."'") : mysql_query("SELECT categories_id FROM ".TABLE_CATEGORIES.
			" WHERE parent_id = '".(int)$categories_id."'"));
		while ($sub_categories = ($ep_uses_mysqli ? mysqli_fetch_array($sub_categories_query) : mysql_fetch_array($sub_categories_query))) {
			if ($sub_categories['categories_id'] == 0) return true;
			$categories[sizeof($categories)] = $sub_categories['categories_id'];
			if ($sub_categories['categories_id'] != $categories_id) {
				zen_get_sub_categories($categories, $sub_categories['categories_id']);
			}
		}
	}
}

function ep_4_get_uploaded_file($filename) {
	if (isset($_FILES[$filename])) {
		//global $_FILES;
		$uploaded_file = array('name' => $_FILES[$filename]['name'],
		'type' => $_FILES[$filename]['type'],
		'size' => $_FILES[$filename]['size'],
		'tmp_name' => $_FILES[$filename]['tmp_name']);
	} elseif (isset($_POST[$filename])) {
		$uploaded_file = array('name' => $_POST[$filename],
		);
	} elseif (isset($GLOBALS['HTTP_POST_FILES'][$filename])) {
		global $HTTP_POST_FILES;
		$uploaded_file = array('name' => $HTTP_POST_FILES[$filename]['name'],
		'type' => $HTTP_POST_FILES[$filename]['type'],
		'size' => $HTTP_POST_FILES[$filename]['size'],
		'tmp_name' => $HTTP_POST_FILES[$filename]['tmp_name']);
	} elseif (isset($GLOBALS['HTTP_POST_VARS'][$filename])) {
		global $HTTP_POST_VARS;
		$uploaded_file = array('name' => $HTTP_POST_VARS[$filename],
		);
	} else {
		$uploaded_file = array('name' => $GLOBALS[$filename . '_name'],
		'type' => $GLOBALS[$filename . '_type'],
		'size' => $GLOBALS[$filename . '_size'],
		'tmp_name' => $GLOBALS[$filename]);
	}
return $uploaded_file;
}

// the $filename parameter is an array with the following elements: name, type, size, tmp_name
function ep_4_copy_uploaded_file($filename, $target) {
	if (substr($target, -1) != '/') $target .= '/';
	$target .= $filename['name'];
	move_uploaded_file($filename['tmp_name'], $target);
}

function ep_4_get_tax_class_rate($tax_class_id) {
	global $db;
	$tax_multiplier = 0;
	$project = PROJECT_VERSION_MAJOR.'.'.PROJECT_VERSION_MINOR;
	$ep_uses_mysqli = ((PROJECT_VERSION_MAJOR > '1' || PROJECT_VERSION_MINOR >= '5.3') ? true : false);
	 
	$tax_query = ($ep_uses_mysqli ? mysqli_query($db->link, "SELECT SUM(tax_rate) AS tax_rate FROM ".TABLE_TAX_RATES.
		" WHERE tax_class_id = '".zen_db_input($tax_class_id)."' GROUP BY tax_priority") : mysql_query("SELECT SUM(tax_rate) AS tax_rate FROM ".TABLE_TAX_RATES.
		" WHERE tax_class_id = '".zen_db_input($tax_class_id)."' GROUP BY tax_priority"));
	if (($ep_uses_mysqli ? mysqli_num_rows($tax_query): mysql_num_rows($tax_query))) {
		while ($tax = ($ep_uses_mysqli ? mysqli_fetch_array($tax_query) : mysql_fetch_array($tax_query))) {
			$tax_multiplier += $tax['tax_rate'];
		}
	}
	return $tax_multiplier;
}

function ep_4_get_tax_title_class_id($tax_class_title) {
	global $db;
	$project = PROJECT_VERSION_MAJOR.'.'.PROJECT_VERSION_MINOR;
	$ep_uses_mysqli = ((PROJECT_VERSION_MAJOR > '1' || PROJECT_VERSION_MINOR >= '5.3') ? true : false);
	$classes_query = ($ep_uses_mysqli ? mysqli_query($db->link, "SELECT tax_class_id FROM ".TABLE_TAX_CLASS.
		" WHERE tax_class_title = '".zen_db_input($tax_class_title)."'") : mysql_query("SELECT tax_class_id FROM ".TABLE_TAX_CLASS.
		" WHERE tax_class_title = '".zen_db_input($tax_class_title)."'"));
	$tax_class_array = ($ep_uses_mysqli ? mysqli_fetch_array($classes_query) : mysql_fetch_array($classes_query));
	$tax_class_id = $tax_class_array['tax_class_id'];
	return $tax_class_id ;
}

function print_el_4($item2) {
	$output_display = substr(strip_tags($item2), 0, 10)." | ";
	return $output_display;
}

function print_el1_4($item2) {
	$output_display = sprintf("| %'.4s ", substr(strip_tags($item2), 0, 80));
	return $output_display;
}

// this function needs further review
function smart_tags_4($string,$tags,$crsub,$doit) {
	if ($doit == true) {
		foreach ($tags as $tag => $new) {
			$tag = '/('.$tag.')/';
			$string = preg_replace($tag,$new,$string);
		}
	}
	// we remove problem characters here anyway as they are not wanted..
	$string = preg_replace("/(\r\n|\n|\r)/", "", $string);
	// $crsub is redundant - may add it again later though..
	return $string;
}

function ep_4_check_table_column($table_name,$column_name) {
	global $db;
	$project = PROJECT_VERSION_MAJOR.'.'.PROJECT_VERSION_MINOR;
	$ep_uses_mysqli = ((PROJECT_VERSION_MAJOR > '1' || PROJECT_VERSION_MINOR >= '5.3') ? true : false);
	$sql = "SHOW COLUMNS FROM ".$table_name;
	$result = ep_4_query($sql);
	while ($row = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result))) {
		$column = $row['Field'];
		if ($column == $column_name) {
			return true;
		}
	}
	return false;
}

function ep_4_remove_product($product_model) {
 	global $db, $ep_debug_logging, $ep_debug_logging_all, $ep_stack_sql_error;
	$project = PROJECT_VERSION_MAJOR.'.'.PROJECT_VERSION_MINOR;
	$ep_uses_mysqli = ((PROJECT_VERSION_MAJOR > '1' || PROJECT_VERSION_MINOR >= '5.3') ? true : false);
	$sql = "SELECT products_id FROM ".TABLE_PRODUCTS;
  switch (EP4_DB_FILTER_KEY) {
    case 'products_model':
      $sql .= " WHERE products_model = :products_model:";
      $sql = $db->bindVars($sql, ':products_model:', $product_model, 'string');
      break;
    case 'blank_new':
    case 'products_id':
      $sql .= " WHERE products_id = :products_id:";
      $sql = $db->bindVars($sql, ':products_id:', $product_model, 'string');
      break;
    default:
      $sql .= " WHERE products_model = :products_model:";
      $sql = $db->bindVars($sql, ':products_model:', $product_model, 'string');
      break;
  }
	$products = $db->Execute($sql);
	if (($ep_uses_mysqli ? mysqli_errno($db->link) : mysql_errno())) {
		$ep_stack_sql_error = true;
		if ($ep_debug_logging == true) {
			$string = "MySQL error ".($ep_uses_mysqli ? mysqli_errno($db->link) : mysql_errno()).": ".($ep_uses_mysqli ? mysqli_error($db->link) : mysql_error())."\nWhen executing:\n$sql\n";
			write_debug_log($string);
		}
	} elseif ($ep_debug_logging_all == true) {
		$string = "MySQL PASSED\nWhen executing:\n$sql\n";
		write_debug_log($string);
	}
	while (!$products->EOF) {
		zen_remove_product($products->fields['products_id']);
		$products->MoveNext();
	}
	return;
}

function ep_4_rmv_chars($filelayout, $active_row, $csv_delimiter = "^") {
//  $datarow = ep_4_rmv_chars($filelayout, $active_row, $csv_delimiter);
  $dataRow = '';

  $problem_chars = array("\r", "\n", "\t"); // carriage return, newline, tab
  foreach ($filelayout as $key => $value) {
//		$thetext = $active_row[$key];
    // remove carriage returns, newlines, and tabs - needs review
    $thetext = str_replace($problem_chars, ' ', $active_row[$key]);
    // encapsulate data in quotes, and escape embedded quotes in data
    $dataRow .= '"' . str_replace('"', '""', $thetext) . '"' . $csv_delimiter;
  }
  // Remove trailing tab, then append the end-of-line
  $dataRow = rtrim($dataRow, $csv_delimiter) . "\n";

  return $dataRow;
}


// DEPRECATED: no calls to this function!
// reset products master categories ID - I do not believe this works correctly - chadd
/*
function ep_4_update_cat_ids() { 
	global $db;
	$sql = "SELECT products_id FROM ".TABLE_PRODUCTS;
	$check_products = $db->Execute($sql);
	while (!$check_products->EOF) {
		$sql = "SELECT products_id, categories_id FROM ".TABLE_PRODUCTS_TO_CATEGORIES.
			" WHERE products_id='".$check_products->fields['products_id']."'";
		$check_category = $db->Execute($sql);
		$sql = "UPDATE ".TABLE_PRODUCTS." SET master_categories_id='".$check_category->fields['categories_id'].
			"' WHERE products_id='".$check_products->fields['products_id']."'";
		$update_viewed = $db->Execute($sql);
		$check_products->MoveNext();
	}
}
*/

// DEPRECATED: no calls to this function!
// Better to run: zen_update_products_price_sorter($v_products_id);
// after each new or updated product
/*
function ep_4_update_prices() { 
	global $db;
	$sql = "SELECT products_id FROM ".TABLE_PRODUCTS;
	$update_prices = $db->Execute($sql);
	while (!$update_prices->EOF) {
		zen_update_products_price_sorter($update_prices->fields['products_id']);
		$update_prices->MoveNext();
	}
}
*/

// DEPRECATED: no calls to this function
// I am writing all my own attribute processing code
/*function ep_4_update_attributes_sort_order() {
	global $db;
	$all_products_attributes = $db->Execute("select p.products_id, pa.products_attributes_id from ".
		TABLE_PRODUCTS." p, ".
		TABLE_PRODUCTS_ATTRIBUTES." pa "."
		where p.products_id = pa.products_id");
	while (!$all_products_attributes->EOF) {
		$count++;
		//$product_id_updated .= ' - ' . $all_products_attributes->fields['products_id'] . ':' . $all_products_attributes->fields['products_attributes_id'];
		zen_update_attributes_products_option_values_sort_order($all_products_attributes->fields['products_id']);
		$all_products_attributes->MoveNext();
	}
}*/

function write_debug_log_4($string) {
	global $ep_debug_log_path;
	$logFile = $ep_debug_log_path.'ep_debug_log.txt';
	$fp = fopen($logFile,'ab');
	fwrite($fp, $string);
	fclose($fp);
	return;
}

function ep_4_query($query) {
	global $ep_debug_logging, $ep_debug_logging_all, $ep_stack_sql_error, $db;
	$project = PROJECT_VERSION_MAJOR.'.'.PROJECT_VERSION_MINOR;
	$ep_uses_mysqli = ((PROJECT_VERSION_MAJOR > '1' || PROJECT_VERSION_MINOR >= '5.3') ? true : false);
	$result = ($ep_uses_mysqli ? mysqli_query($db->link, $query) : mysql_query($query));
	if (($ep_uses_mysqli ? mysqli_errno($db->link) : mysql_errno())) {
		$ep_stack_sql_error = true;
		if ($ep_debug_logging == true) {
			$string = ($ep_uses_mysqli ? "MySQLi" : "MySQL") . " error ".($ep_uses_mysqli ? mysqli_errno($db->link) : mysql_errno() ) . ": ".($ep_uses_mysqli ? mysqli_error($db->link) : $mysql_error())."\nWhen executing:\n$query\n";
			write_debug_log_4($string);
		}
	} elseif ($ep_debug_logging_all == true) {
		$string = ($ep_uses_mysqli ? "MySQLi" : "MySQL") . " PASSED\nWhen executing:\n$query\n";
		write_debug_log_4($string);
	}
	return $result;
}

function install_easypopulate_4() {
	global $db, $zco_notifier;
	$project = PROJECT_VERSION_MAJOR.'.'.PROJECT_VERSION_MINOR;
	if ( (substr($project,0,5) == "1.3.8") || (substr($project,0,5) == "1.3.9") ) {
		$db->Execute("INSERT INTO ".TABLE_CONFIGURATION_GROUP." (configuration_group_title, configuration_group_description, sort_order, visible) VALUES ('Easy Populate 4', 'Configuration Options for Easy Populate 4', '1', '1')");
		$group_id = mysql_insert_id();
		$db->Execute("UPDATE ".TABLE_CONFIGURATION_GROUP." SET sort_order = ".$group_id." WHERE configuration_group_id = ".$group_id);
		$db->Execute("INSERT INTO ".TABLE_CONFIGURATION." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES 
			('Uploads Directory',                  'EASYPOPULATE_4_CONFIG_TEMP_DIR', 'temp/', 'Name of directory for your uploads  as compared to the setting of Uploads Directory Admin/Catalog.<br /><br />Default is to use YOUR_ADMIN/temp/ by entering temp/ below.<br /><b>Caution:</b> the admin directory folder name should not be entered here as it will be stored in the database.  If the admin directory is to be used please set/verify Uploads Directory Admin/Catalog is set to true.<br /><br />(default is to use the YOUR_ADMIN directory and the below value of: temp/).', ".$group_id.", '10', NULL, now(), NULL, NULL),
			('Uploads Directory Admin/Catalog',                  'EP4_ADMIN_TEMP_DIRECTORY', 'true', 'Should the admin directory be used to store the export and import files for EP4?<br /><br />This switch affects how Uploads Directory is used.<br /><br />true (default) or<br />false. ', ".$group_id.", '20', NULL, now(), NULL, 'zen_cfg_select_option(array(\"true\", \"false\"),'),
			('Import/Export Primary Key', 'EP4_DB_FILTER_KEY', 'products_model', 'Select the primary key that is to be used for import of the data.<br /><br />The default for Easy Populate v4 is products_model.<br /><br /> The field products_model is independent of the store, while products_id will require/generate the product information associated with that products_id and could lead to duplication of product. Choosing blank_new will import by products_id and create new products when the products_id is not entered/blank.<br /><br />products_model (default)<br />products_id<br />blank_new', ".$group_id.", '30', NULL, now(), NULL, 'zen_cfg_select_option(array(\'products_model\', \'products_id\', \'blank_new\'),'),
			('Upload File Date Format',            'EASYPOPULATE_4_CONFIG_FILE_DATE_FORMAT', 'm-d-y', 'Choose order of date values that corresponds to your uploads file, usually generated by MS Excel. Raw dates in your uploads file (Eg 2005-09-26 09:00:00) are not affected, and will upload as they are.', ".$group_id.", '40', NULL, now(), NULL, 'zen_cfg_select_option(array(\"m-d-y\", \"d-m-y\", \"y-m-d\"),'),
			('Default Raw Time',                   'EASYPOPULATE_4_CONFIG_DEFAULT_RAW_TIME', '09:00:00', 'If no time value stipulated in upload file, use this value. Useful for ensuring specials begin after a specific time of the day (default: 09:00:00)', ".$group_id.", '50', NULL, now(), NULL, NULL),
			('Upload/Download Prices Include Tax', 'EASYPOPULATE_4_CONFIG_PRICE_INC_TAX', 'false', 'Choose to include or exclude tax, depending on how you manage prices outside of Zen Cart.', ".$group_id.", '60', NULL, now(), NULL, 'zen_cfg_select_option(array(\"true\", \"false\"),'),
			('Verbose Feedback',                   'EASYPOPULATE_4_CONFIG_VERBOSE', 'true', 'When importing, report all messages. Set to false for only warnings and errors. (default: true).', ".$group_id.", '70', NULL, now(), NULL, 'zen_cfg_select_option(array(\"true\", \"false\"),'),
			('Show all EP4 Filetypes with Files',       'EP4_SHOW_ALL_FILETYPES', 'true', 'When looking at the EP4 Tools screen, should the filename prefix for all specific file types be displayed for all possible file types (true [default]), should only the method(s) that will be used to process the files present be displayed (false), or should there be no assistance be provided on filenaming on the main page (Hidden) like it was until this feature was added? (true, false, or Hidden)', ".$group_id.", '80', NULL, now(), NULL, 'zen_cfg_select_option(array(\"true\", \"false\", \"Hidden\"),'),
      ('Replace Blank Image', 'EP4_REPLACE_BLANK_IMAGE', 'false', 'On import, if the image information is blank, then update the image path to the path of the blank image (true)? Otherwise the image path will remain blank (false <Default>).<br /><br />false (Default)<br />true.', ".$group_id.", '90', NULL, now(), NULL, 'zen_cfg_select_option(array(\'false\', \'true\'),'),
			('Make Zero Qty Products Inactive',    'EASYPOPULATE_4_CONFIG_ZERO_QTY_INACTIVE', 'false', 'When uploading, make the status Inactive for products with zero qty (default: false).', ".$group_id.", '100', NULL, now(), NULL, 'zen_cfg_select_option(array(\"true\", \"false\"),'),
			('Smart Tags Replacement of Newlines', 'EASYPOPULATE_4_CONFIG_SMART_TAGS', 'true', 'Allows your description fields in your uploads file to have carriage returns and/or new-lines converted to HTML line-breaks on uploading, thus preserving some rudimentary formatting - Note: this legacy code is disabled until further review. (default: true).', ".$group_id.", '110', NULL, now(), NULL, 'zen_cfg_select_option(array(\"true\", \"false\"),'),
			('Advanced Smart Tags',                'EASYPOPULATE_4_CONFIG_ADV_SMART_TAGS', 'false', 'Allow the use of complex regular expressions to format descriptions, making headings bold, add bullets, etc. Note: legacy code is disabled until further review. (default: false).', ".$group_id.", '120', NULL, now(), NULL, 'zen_cfg_select_option(array(\"true\", \"false\"),'),
			('Debug Logging',                      'EASYPOPULATE_4_CONFIG_DEBUG_LOGGING', 'true', 'Allow Easy Populate to generate an error log on errors only (default: true)', ".$group_id.", '130', NULL, now(), NULL, 'zen_cfg_select_option(array(\"true\", \"false\"),'),
			('Maximum Quantity Discounts',         'EASYPOPULATE_4_CONFIG_MAX_QTY_DISCOUNTS', '3', 'Maximum number of quantity discounts (price breaks). Is the number of discount columns in downloaded file (default: 3).', ".$group_id.", '140', NULL, now(), NULL, NULL),
			('Split On Number of Records',         'EASYPOPULATE_4_CONFIG_SPLIT_RECORDS', '2000', 'Number of records to split csv files. Used to break large import files into smaller files. Useful on servers with limited resourses. (default: 2000).', ".$group_id.", '150', NULL, now(), NULL, NULL),
			('Script Execution Time',              'EASYPOPULATE_4_CONFIG_EXECUTION_TIME', '60', 'Number of seconds for script to run before timeout. May not work on some servers. (default: 60).', ".$group_id.", '160', NULL, now(), NULL, NULL),
			('Convert Curly Quotes, etc.',         'EASYPOPULATE_4_CONFIG_CURLY_QUOTES', '0', 'Convert Curly Quotes, Em-Dash, En-Dash and Ellipsis characters in Products Description (default 0).<br><br>0=No Change<br>1=Replace with Basic Characters<br>3=Replace with HMTL equivalants', ".$group_id.", '170', NULL, now(), NULL, 'zen_cfg_select_option(array(\"0\", \"1\", \"2\"),'),
			('Convert Character 0x92',             'EASYPOPULATE_4_CONFIG_CHAR_92', '1', 'Convert Character 0x92 characters in Product Names &amp; Descriptions (default 1).<br><br>0=No Change<br>1=Replace with Standard Single Quote<br>2=Replace with HMTL equivalant', ".$group_id.", '180', NULL, now(), NULL, 'zen_cfg_select_option(array(\"0\", \"1\", \"2\"),'),
			('Enable Products Meta Data',          'EASYPOPULATE_4_CONFIG_META_DATA', '1', 'Enable Products Meta Data Columns (default 1).<br><br>0=Disable<br>1=Enable', ".$group_id.", '190', NULL, now(), NULL, 'zen_cfg_select_option(array(\"0\", \"1\"),'), 
			('Enable Products Music Data',         'EASYPOPULATE_4_CONFIG_MUSIC_DATA', '0', 'Enable Products Music Data Columns (default 0).<br><br>0=Disable<br>1=Enable', ".$group_id.", '200', NULL, now(), NULL, 'zen_cfg_select_option(array(\"0\", \"1\"),'),
			('User Defined Products Fields',       'EASYPOPULATE_4_CONFIG_CUSTOM_FIELDS', '', 'User Defined Products Table Fields (comma delimited, no spaces)', ".$group_id.", '210', NULL, now(), NULL, NULL),
			('Export URI with Prod and or Cat',       'EASYPOPULATE_4_CONFIG_EXPORT_URI', '0', 'Export the current products or categories URI when exporting data? (Yes - 1 or no - 0)', ".$group_id.", '220', NULL, now(), NULL, 'zen_cfg_select_option(array(\"0\", \"1\"),'),
			('AutoCreate URL For CEON When URL Doesn\'t Exist','EP4_AUTOCREATE_FROM_BLANK','1','Enable Autogeneration of URIs with CEON (When it is installed) if a URI does not currently exist for the product upon export of the database?<br/><br/>(Default - Yes)',".$group_id.", '230', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No'')),'), 
			('AutoCreate URL For CEON - All Products','EP4_AUTORECREATE_EXISTING','0','Enable Autogeneration of URIs with CEON (When it is installed) for all products on export?<br /><br />No - Do not alter products based on this setting.<br /><br />Yes - Assign all products the default CEON URI.<br /><br />Mixed - Assign the default CEON URIs for products already assigned a URI and by the setting of AutoCreate URL For CEON When URL Doesn\'t Exist.<br /><br/><br/>(Default - No)',".$group_id.", '240', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No''),array(''id''=>''2'',''text''=>''Mixed'')),'),
			('Export URL Information From CEON - All Products','EP4_EXPORT_ONLY','0','Export CEON URI autogenerated URIs Only? (Do not store them.)<br /><br />No - Allow autogeneration of the URIs to update the database (URIs will still be exported.)<br /><br />Yes - Export the URIs in accordance with the autogeneration rules.  Choosing this option will prevent updating the database with these options.<br /><br /><br/>(Default - No)',".$group_id.", '250', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No'')),'), 
			('AutoCreate Category URL For CEON When URL Doesn\'t Exist','EP4_AUTOCREATE_CAT_FROM_BLANK','1','Enable Autogeneration of Category URIs with CEON (When it is installed) if a URI does not currently exist for the category upon export of the database?<br/><br/>(Default - Yes)',".$group_id.", '260', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No'')),'), 
			('AutoCreate Category URL For CEON - All Categories','EP4_AUTORECREATE_CAT_EXISTING','0','Enable Autogeneration of Category URIs with CEON (When it is installed) for all products on export?<br /><br />No - Do not alter categories based on this setting.<br /><br />Yes - Assign all categories the default CEON URI.<br /><br />Mixed - Assign the default CEON URIs for categories already assigned a URI and by the setting of AutoCreate URL For CEON When URL Doesn\'t Exist.<br /><br/><br/>(Default - No)',".$group_id.", '270', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No''),array(''id''=>''2'',''text''=>''Mixed'')),'),
			('Export URL Information From CEON - All Categories','EP4_EXPORT_CAT_ONLY','0','Export CEON URI autogenerated category URIs Only? (Do not store them.)<br /><br />No - Allow autogeneration of the Category URIs to update the database (URIs will still be exported.)<br /><br />Yes - Export the URIs in accordance with the autogeneration rules.  Choosing this option will prevent updating the database with these options.<br /><br /><br/>(Default - No)',".$group_id.", '280', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No'')),'),
			('AutoCreate EZ-Page URL For CEON When URL Doesn\'t Exist','EP4_AUTOCREATE_EZ_FROM_BLANK','1','Enable Autogeneration of EZ-Page URIs with CEON (When it is installed) if a URI does not currently exist for the EZ-Page upon export of the database?<br/><br/>(Default - Yes)',".$group_id.", '290', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No'')),'), 
			('AutoCreate EZ-Page URL For CEON - All EZ-Pages','EP4_AUTORECREATE_EZ_EXISTING','0','Enable Autogeneration of EZ-Page URIs with CEON (When it is installed) for all EZ-Pages on export?<br /><br />No - Do not alter EZ-Pages based on this setting.<br /><br />Yes - Assign all EZ-Pages the default CEON URI.<br /><br />Mixed - Assign the default CEON URIs for EZ-Pages already assigned a URI and by the setting of AutoCreate URL For CEON When URL Doesn\'t Exist.<br /><br/><br/>(Default - No)',".$group_id.", '300', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No''),array(''id''=>''2'',''text''=>''Mixed'')),'),
			('Export URL Information From CEON - All EZ-Pages','EP4_EXPORT_EZ_ONLY','0','Export CEON URI autogenerated EZ-Page URIs Only? (Do not store them.)<br /><br />No - Allow autogeneration of the EZ-Page URIs to update the database (URIs will still be exported.)<br /><br />Yes - Export the URIs in accordance with the autogeneration rules.  Choosing this option will prevent updating the database with these options.<br /><br /><br/><br/>(Default - No)',".$group_id.", '310', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No'')),')
		");
	} elseif (PROJECT_VERSION_MAJOR > '1' || PROJECT_VERSION_MINOR >= '5.0') {
		$db->Execute("INSERT INTO ".TABLE_CONFIGURATION_GROUP." (configuration_group_title, configuration_group_description, sort_order, visible) VALUES ('Easy Populate 4', 'Configuration Options for Easy Populate 4', '1', '1')");
		if (PROJECT_VERSION_MAJOR > '1' || PROJECT_VERSION_MINOR >= '5.3') {
			$group_id = mysqli_insert_id($db->link);
		} else {
			$group_id = mysql_insert_id();
		}
		$db->Execute("UPDATE ".TABLE_CONFIGURATION_GROUP." SET sort_order = ".$group_id." WHERE configuration_group_id = ".$group_id);
		
        zen_register_admin_page('easypopulate_4_config', 'BOX_TOOLS_EASYPOPULATE_4','FILENAME_CONFIGURATION', 'gID='.$group_id, 'configuration', 'Y', 97);
		$db->Execute("INSERT INTO ".TABLE_CONFIGURATION." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES 
			('Uploads Directory',                  'EASYPOPULATE_4_CONFIG_TEMP_DIR', 'temp/', 'Name of directory for your uploads  as compared to the setting of Uploads Directory Admin/Catalog.<br /><br />Default is to use YOUR_ADMIN/temp/ by entering temp/ below.<br /><b>Caution:</b> the admin directory folder name should not be entered here as it will be stored in the database.  If the admin directory is to be used please set/verify Uploads Directory Admin/Catalog is set to true.<br /><br />(default is to use the YOUR_ADMIN directory and the below value of: temp/).', ".$group_id.", '10', NULL, now(), NULL, NULL),
			('Uploads Directory Admin/Catalog',                  'EP4_ADMIN_TEMP_DIRECTORY', 'true', 'Should the admin directory be used to store the export and import files for EP4?<br /><br />This switch affects how Uploads Directory is used.<br /><br />true (default) or<br />false. ', ".$group_id.", '20', NULL, now(), NULL, 'zen_cfg_select_option(array(\"true\", \"false\"),'),
			('Import/Export Primary Key', 'EP4_DB_FILTER_KEY', 'products_model', 'Select the primary key that is to be used for import of the data.<br /><br />The default for Easy Populate v4 is products_model.<br /><br /> The field products_model is independent of the store, while products_id will require/generate the product information associated with that products_id and could lead to duplication of product. Choosing blank_new will import by products_id and create new products when the products_id is not entered/blank.<br /><br />products_model (default)<br />products_id<br />blank_new', ".$group_id.", '30', NULL, now(), NULL, 'zen_cfg_select_option(array(\'products_model\', \'products_id\', \'blank_new\'),'),
			('Upload File Date Format',            'EASYPOPULATE_4_CONFIG_FILE_DATE_FORMAT', 'm-d-y', 'Choose order of date values that corresponds to your uploads file, usually generated by MS Excel. Raw dates in your uploads file (Eg 2005-09-26 09:00:00) are not affected, and will upload as they are.', ".$group_id.", '40', NULL, now(), NULL, 'zen_cfg_select_option(array(\"m-d-y\", \"d-m-y\", \"y-m-d\"),'),
			('Default Raw Time',                   'EASYPOPULATE_4_CONFIG_DEFAULT_RAW_TIME', '09:00:00', 'If no time value stipulated in upload file, use this value. Useful for ensuring specials begin after a specific time of the day (default: 09:00:00)', ".$group_id.", '50', NULL, now(), NULL, NULL),
			('Upload/Download Prices Include Tax', 'EASYPOPULATE_4_CONFIG_PRICE_INC_TAX', 'false', 'Choose to include or exclude tax, depending on how you manage prices outside of Zen Cart.', ".$group_id.", '60', NULL, now(), NULL, 'zen_cfg_select_option(array(\"true\", \"false\"),'),
			('Verbose Feedback',                   'EASYPOPULATE_4_CONFIG_VERBOSE', 'true', 'When importing, report all messages. Set to false for only warnings and errors. (default: true).', ".$group_id.", '70', NULL, now(), NULL, 'zen_cfg_select_option(array(\"true\", \"false\"),'),
			('Show all EP4 Filetypes with Files',       'EP4_SHOW_ALL_FILETYPES', 'true', 'When looking at the EP4 Tools screen, should the filename prefix for all specific file types be displayed for all possible file types (true [default]), should only the method(s) that will be used to process the files present be displayed (false), or should there be no assistance be provided on filenaming on the main page (Hidden) like it was until this feature was added? (true, false, or Hidden)', ".$group_id.", '80', NULL, now(), NULL, 'zen_cfg_select_option(array(\"true\", \"false\", \"Hidden\"),'),
      ('Replace Blank Image', 'EP4_REPLACE_BLANK_IMAGE', 'false', 'On import, if the image information is blank, then update the image path to the path of the blank image (true)? Otherwise the image path will remain blank (false <Default>).<br /><br />false (Default)<br />true.', ".$group_id.", '90', NULL, now(), NULL, 'zen_cfg_select_option(array(\'false\', \'true\'),'),
			('Make Zero Qty Products Inactive',    'EASYPOPULATE_4_CONFIG_ZERO_QTY_INACTIVE', 'false', 'When uploading, make the status Inactive for products with zero qty (default: false).', ".$group_id.", '100', NULL, now(), NULL, 'zen_cfg_select_option(array(\"true\", \"false\"),'),
			('Smart Tags Replacement of Newlines', 'EASYPOPULATE_4_CONFIG_SMART_TAGS', 'true', 'Allows your description fields in your uploads file to have carriage returns and/or new-lines converted to HTML line-breaks on uploading, thus preserving some rudimentary formatting - Note: this legacy code is disabled until further review. (default: true).', ".$group_id.", '110', NULL, now(), NULL, 'zen_cfg_select_option(array(\"true\", \"false\"),'),
			('Advanced Smart Tags',                'EASYPOPULATE_4_CONFIG_ADV_SMART_TAGS', 'false', 'Allow the use of complex regular expressions to format descriptions, making headings bold, add bullets, etc. Note: legacy code is disabled until further review. (default: false).', ".$group_id.", '120', NULL, now(), NULL, 'zen_cfg_select_option(array(\"true\", \"false\"),'),
			('Debug Logging',                      'EASYPOPULATE_4_CONFIG_DEBUG_LOGGING', 'true', 'Allow Easy Populate to generate an error log on errors only (default: true)', ".$group_id.", '130', NULL, now(), NULL, 'zen_cfg_select_option(array(\"true\", \"false\"),'),
			('Maximum Quantity Discounts',         'EASYPOPULATE_4_CONFIG_MAX_QTY_DISCOUNTS', '3', 'Maximum number of quantity discounts (price breaks). Is the number of discount columns in downloaded file (default: 3).', ".$group_id.", '140', NULL, now(), NULL, NULL),
			('Split On Number of Records',         'EASYPOPULATE_4_CONFIG_SPLIT_RECORDS', '2000', 'Number of records to split csv files. Used to break large import files into smaller files. Useful on servers with limited resourses. (default: 2000).', ".$group_id.", '150', NULL, now(), NULL, NULL),
			('Script Execution Time',              'EASYPOPULATE_4_CONFIG_EXECUTION_TIME', '60', 'Number of seconds for script to run before timeout. May not work on some servers. (default: 60).', ".$group_id.", '160', NULL, now(), NULL, NULL),
			('Convert Curly Quotes, etc.',         'EASYPOPULATE_4_CONFIG_CURLY_QUOTES', '0', 'Convert Curly Quotes, Em-Dash, En-Dash and Ellipsis characters in Products Description (default 0).<br><br>0=No Change<br>1=Replace with Basic Characters<br>3=Replace with HMTL equivalants', ".$group_id.", '170', NULL, now(), NULL, 'zen_cfg_select_option(array(\"0\", \"1\", \"2\"),'),
			('Convert Character 0x92',             'EASYPOPULATE_4_CONFIG_CHAR_92', '1', 'Convert Character 0x92 characters in Product Names &amp; Descriptions (default 1).<br><br>0=No Change<br>1=Replace with Standard Single Quote<br>2=Replace with HMTL equivalant', ".$group_id.", '180', NULL, now(), NULL, 'zen_cfg_select_option(array(\"0\", \"1\", \"2\"),'),
			('Enable Products Meta Data',          'EASYPOPULATE_4_CONFIG_META_DATA', '1', 'Enable Products Meta Data Columns (default 1).<br><br>0=Disable<br>1=Enable', ".$group_id.", '190', NULL, now(), NULL, 'zen_cfg_select_option(array(\"0\", \"1\"),'), 
			('Enable Products Music Data',         'EASYPOPULATE_4_CONFIG_MUSIC_DATA', '0', 'Enable Products Music Data Columns (default 0).<br><br>0=Disable<br>1=Enable', ".$group_id.", '200', NULL, now(), NULL, 'zen_cfg_select_option(array(\"0\", \"1\"),'),
			('User Defined Products Fields',       'EASYPOPULATE_4_CONFIG_CUSTOM_FIELDS', '', 'User Defined Products Table Fields (comma delimited, no spaces)', ".$group_id.", '210', NULL, now(), NULL, NULL),
			('Export URI with Prod and or Cat',       'EASYPOPULATE_4_CONFIG_EXPORT_URI', '0', 'Export the current products or categories URI when exporting data? (Yes - 1 or no - 0)', ".$group_id.", '220', NULL, now(), NULL, 'zen_cfg_select_option(array(\"0\", \"1\"),'),
			('AutoCreate URL For CEON When URL Doesn\'t Exist','EP4_AUTOCREATE_FROM_BLANK','1','Enable Autogeneration of URIs with CEON (When it is installed) if a URI does not currently exist for the product upon export of the database?<br/><br/>(Default - Yes)',".$group_id.", '230', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No'')),'), 
			('AutoCreate URL For CEON - All Products','EP4_AUTORECREATE_EXISTING','0','Enable Autogeneration of URIs with CEON (When it is installed) for all products on export?<br /><br />No - Do not alter products based on this setting.<br /><br />Yes - Assign all products the default CEON URI.<br /><br />Mixed - Assign the default CEON URIs for products already assigned a URI and by the setting of AutoCreate URL For CEON When URL Doesn\'t Exist.<br /><br/><br/>(Default - No)',".$group_id.", '240', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No''),array(''id''=>''2'',''text''=>''Mixed'')),'),
			('Export URL Information From CEON - All Products','EP4_EXPORT_ONLY','0','Export CEON URI autogenerated URIs Only? (Do not store them.)<br /><br />No - Allow autogeneration of the URIs to update the database (URIs will still be exported.)<br /><br />Yes - Export the URIs in accordance with the autogeneration rules.  Choosing this option will prevent updating the database with these options.<br /><br /><br/>(Default - No)',".$group_id.", '250', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No'')),'), 
			('AutoCreate Category URL For CEON When URL Doesn\'t Exist','EP4_AUTOCREATE_CAT_FROM_BLANK','1','Enable Autogeneration of Category URIs with CEON (When it is installed) if a URI does not currently exist for the category upon export of the database?<br/><br/>(Default - Yes)',".$group_id.", '260', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No'')),'), 
			('AutoCreate Category URL For CEON - All Categories','EP4_AUTORECREATE_CAT_EXISTING','0','Enable Autogeneration of Category URIs with CEON (When it is installed) for all products on export?<br /><br />No - Do not alter categories based on this setting.<br /><br />Yes - Assign all categories the default CEON URI.<br /><br />Mixed - Assign the default CEON URIs for categories already assigned a URI and by the setting of AutoCreate URL For CEON When URL Doesn\'t Exist.<br /><br/><br/>(Default - No)',".$group_id.", '270', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No''),array(''id''=>''2'',''text''=>''Mixed'')),'),
			('Export URL Information From CEON - All Categories','EP4_EXPORT_CAT_ONLY','0','Export CEON URI autogenerated category URIs Only? (Do not store them.)<br /><br />No - Allow autogeneration of the Category URIs to update the database (URIs will still be exported.)<br /><br />Yes - Export the URIs in accordance with the autogeneration rules.  Choosing this option will prevent updating the database with these options.<br /><br /><br/>(Default - No)',".$group_id.", '280', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No'')),'),
			('AutoCreate EZ-Page URL For CEON When URL Doesn\'t Exist','EP4_AUTOCREATE_EZ_FROM_BLANK','1','Enable Autogeneration of EZ-Page URIs with CEON (When it is installed) if a URI does not currently exist for the EZ-Page upon export of the database?<br/><br/>(Default - Yes)',".$group_id.", '290', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No'')),'), 
			('AutoCreate EZ-Page URL For CEON - All EZ-Pages','EP4_AUTORECREATE_EZ_EXISTING','0','Enable Autogeneration of EZ-Page URIs with CEON (When it is installed) for all EZ-Pages on export?<br /><br />No - Do not alter EZ-Pages based on this setting.<br /><br />Yes - Assign all EZ-Pages the default CEON URI.<br /><br />Mixed - Assign the default CEON URIs for EZ-Pages already assigned a URI and by the setting of AutoCreate URL For CEON When URL Doesn\'t Exist.<br /><br/><br/>(Default - No)',".$group_id.", '300', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No''),array(''id''=>''2'',''text''=>''Mixed'')),'),
			('Export URL Information From CEON - All EZ-Pages','EP4_EXPORT_EZ_ONLY','0','Export CEON URI autogenerated EZ-Page URIs Only? (Do not store them.)<br /><br />No - Allow autogeneration of the EZ-Page URIs to update the database (URIs will still be exported.)<br /><br />Yes - Export the URIs in accordance with the autogeneration rules.  Choosing this option will prevent updating the database with these options.<br /><br /><br/><br/>(Default - No)',".$group_id.", '310', NULL, now(), NULL, 'zen_cfg_select_drop_down(array(array(''id''=>''1'',''text''=>''Yes''),array(''id''=>''0'',''text''=>''No'')),')
		");
	} else { // unsupported version 
		// i should do something here!
	} 
    $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_INSTALL_END');
}

function remove_easypopulate_4() {
	global $db;
	$project = PROJECT_VERSION_MAJOR.'.'.PROJECT_VERSION_MINOR;
	$ep_uses_mysqli = ((PROJECT_VERSION_MAJOR > '1' || PROJECT_VERSION_MINOR >= '5.3') ? true : false);
	if ( (substr($project,0,5) == "1.3.8") || (substr($project,0,5) == "1.3.9") ) {
		$sql = "SELECT configuration_group_id FROM ".TABLE_CONFIGURATION_GROUP." WHERE configuration_group_title = 'Easy Populate 4' LIMIT 1";
		$result = ep_4_query($sql);
		if (mysql_num_rows($result)) { 
			$ep_group_id =  mysql_fetch_array($result);
			$db->Execute("DELETE FROM ".TABLE_CONFIGURATION." WHERE configuration_group_id = ".$ep_group_id[0]);
			$db->Execute("DELETE FROM ".TABLE_CONFIGURATION_GROUP." WHERE configuration_group_id = ".$ep_group_id[0]);
		}
	} elseif (substr($project,0,3) == "1.5") {
		$sql = "SELECT configuration_group_id FROM ".TABLE_CONFIGURATION_GROUP." WHERE configuration_group_title = 'Easy Populate 4' LIMIT 1";
		$result = ep_4_query($sql);
		if (($ep_uses_mysqli ? mysqli_num_rows($result) : mysql_num_rows($result))) { 
			$ep_group_id =  ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result));
			$db->Execute("DELETE FROM ".TABLE_CONFIGURATION." WHERE configuration_group_id = ".$ep_group_id[0]);
			$db->Execute("DELETE FROM ".TABLE_CONFIGURATION_GROUP." WHERE configuration_group_id = ".$ep_group_id[0]);
			$db->Execute("DELETE FROM ".TABLE_ADMIN_PAGES." WHERE page_key = 'easypopulate_4'");
			$db->Execute("DELETE FROM ".TABLE_ADMIN_PAGES." WHERE page_key = 'easypopulate_4_config'");
		}
	} else { // unsupported version 
	} 
}

function ep_4_chmod_check($tempdir) {
	global $messageStack;
  if (!@file_exists((EP4_ADMIN_TEMP_DIRECTORY !== 'true' ? /* Storeside */ DIR_FS_CATALOG : /* Admin side */ DIR_FS_ADMIN) . $tempdir . ".")) { // directory does not exist
		$messageStack->add(sprintf(EASYPOPULATE_4_MSGSTACK_TEMP_FOLDER_MISSING, $tempdir, (EP4_ADMIN_TEMP_DIRECTORY !== 'true' ? /* Storeside */ DIR_FS_CATALOG : /* Admin side */ DIR_FS_ADMIN)), 'warning');
		$chmod_check = false;
	} else { // directory exists, test is writeable
		if (!@is_writable((EP4_ADMIN_TEMP_DIRECTORY !== 'true' ? /* Storeside */ DIR_FS_CATALOG : /* Admin side */ DIR_FS_ADMIN) . $tempdir . ".")) { // directory does not exist
			$messageStack->add(sprintf(EASYPOPULATE_4_MSGSTACK_TEMP_FOLDER_NOT_WRITABLE, $tempdir, (EP4_ADMIN_TEMP_DIRECTORY !== 'true' ? /* Storeside */ DIR_FS_CATALOG : /* Admin side */ DIR_FS_ADMIN)), 'warning');
			$chmod_check = false;
		} else { 
			$chmod_check = true;
		}
	}
	return $chmod_check;
}

// The following functions are for testing purposes only
// available zen functions of use..
/*
function zen_get_category_name($category_id, $language_id)
function zen_get_category_description($category_id, $language_id)
function zen_get_products_name($product_id, $language_id = 0)
function zen_get_products_description($product_id, $language_id)
function zen_get_products_model($products_id)
*/

function register_globals_vars_check_4 () {
	echo phpversion();
	echo '<br>register_globals = ', ini_get('register_globals'), '<br>';
	print "_GET: "; print_r($_GET); echo '<br />';
	print "_POST: "; print_r($_POST); echo '<br />';
	print "_FILES: "; print_r($_FILES); echo '<br />';
	print "_COOKIE: "; print_r($_COOKIE); echo '<br />';
	print "GLOBALS: "; print_r($GLOBALS); echo '<br />';
	print "_REQUEST: "; print_r($_REQUEST); echo '<br /><br />';
	global $HTTP_POST_FILES;
	print "HTTP_POST_FILES: "; print_r($HTTP_POST_FILES); echo '<br />';
}
