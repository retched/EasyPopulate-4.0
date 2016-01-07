<?php
/**
 * @EP4Bookx - EP4 CSV fork to import Bookx fields - tested with Zencart 1.5.4
 * @version  0.9.0 - Still in development, make your changes in a local environment
 * @see Bookx module for ZenCart
 * @see Readme-EP4Bookx
 *
 * @author mesnitu
 * @todo  export with support for languages
 * @todo  export assinged multiple authors
 * @todo  export assinged multiple genres
 */

// From TABLE_PRODUCT_BOOKX_EXTRA
if ($row['v_products_type'] == $bookx_product_type) {


if (isset($filelayout['v_bookx_isbn'])) {

    $sql = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_EXTRA."	 WHERE products_id = '".$row['v_products_id']."' LIMIT 1");
    $row_bookx_extra = ($ep_uses_mysqli ? mysqli_fetch_array($sql) : mysql_fetch_array($sql));

    if (($row_bookx_extra['isbn'] !='0') && ($row_bookx_extra['isbn'] !='') || ($row_bookx_extra['size'] !='0') && ($row_bookx_extra['size'] !='') || ($row_bookx_extra['pages'] !='0') && ($row_bookx_extra['pages'] !='') || ($row_bookx_extra['publishing_date'] !='0') && ($row_bookx_extra['publishing_date'] !='') || ($row['v_bookx_volume'] !='0') && ($row['v_bookx_volume'] !='0'))
        { // '0' is correct, but '' NULL is possible

            $row['v_bookx_isbn']  = $row_bookx_extra['isbn'];
            $row['v_bookx_size']  = $row_bookx_extra['size'];
            $row['v_bookx_pages'] = $row_bookx_extra['pages']; 
            $row['v_bookx_publishing_date'] = $row_bookx_extra['publishing_date'];
            $row['v_bookx_volume']  = $row_bookx_extra['volume'];
			} else {
				$row['v_bookx_isbn'] = '';
				$row['v_bookx_size'] = '';
				$row['v_bookx_pages'] = '';
                                $row['v_bookx_volume']  = '';
				$row['v_bookx_publishing_date'] = '';
			}

} //ends table extra

// From TABLE_PRODUCT_BOOKX_EXTRA_DESCRIPTION
if (isset($filelayout['v_bookx_subtitle'])) {
	/**
	 * We could use the Bookx Functions, Less code, but dependent of any unwanted changes
	 */
	$row['v_bookx_subtitle'] = bookx_get_products_subtitle($row['v_products_id'], $epdlanguage_id);

    // $sql = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_EXTRA_DESCRIPTION." WHERE products_id = '".$row['v_products_id']."' LIMIT 1");

    // $row_bookx_subtitle = ($ep_uses_mysqli ? mysqli_fetch_array($sql) : mysql_fetch_array($sql));
    // //pr($row_bookx_subtitle);
    //     if (($row_bookx_subtitle['products_subtitle'] != '0') && ($row_bookx_subtitle['products_subtitle'] != '')) {
    //         $row['v_bookx_subtitle']  = $row_bookx_subtitle['products_subtitle'];
    //     }
    //     else {
    //         $row['v_bookx_subtitle']  = '';
    //     }

}//ends book_extra_descritpion

// Publisher Name 
// See if product has a publisher in bookx_extra
if (isset($filelayout['v_bookx_publisher_name']) && ($row_bookx_extra['bookx_publisher_id'] != '0') && ($row_bookx_extra['bookx_publisher_id'] != '')) {
 	
 	$row['v_bookx_publisher_name'] = bookx_get_publisher_name($row_bookx_extra['bookx_publisher_id']);

// 	$sql = ep_4_query ("SELECT * FROM ".TABLE_PRODUCT_BOOKX_PUBLISHERS." WHERE bookx_publisher_id = '" . $row_bookx_extra['bookx_publisher_id'] . "' LIMIT 1 ");
//     $row_bookx_publisher_name = ($ep_uses_mysqli ? mysqli_fetch_array($sql) : mysql_fetch_array($sql));

// 	    $row['v_bookx_publisher_name'] = $row_bookx_publisher_name['publisher_name'];
// } else {
// 			$row['v_bookx_publisher_name'] = '';
// 		} 
//ends Bookx Publisher
}
// Series Name
if (isset($filelayout['v_bookx_series_name']) && ($row_bookx_extra['bookx_series_id'] != '0') && ($row_bookx_extra['bookx_series_id'] != '')) { // '0' is correct, but '' NULL is possible

 	
	$sql = ep_4_query ("SELECT * FROM ".TABLE_PRODUCT_BOOKX_SERIES_DESCRIPTION." WHERE bookx_series_id = '".$row_bookx_extra['bookx_series_id']."' LIMIT 1");
	$row_bookx_series = ($ep_uses_mysqli ? mysqli_fetch_array($sql) : mysql_fetch_array($sql));
		$row['v_bookx_series_name'] = $row_bookx_series['series_name'];

}	else {
	  $row['v_bookx_series_name'] = '';
	} 
//ends series name

// Imprints Name 

if (isset($filelayout['v_bookx_imprint_name']) && ($row_bookx_extra['bookx_imprint_id'] !== '') && ($row_bookx_extra['bookx_imprint_id'] !== '0')) { 

    $sql = ep_4_query("SELECT * FROM " . TABLE_PRODUCT_BOOKX_IMPRINTS . " WHERE bookx_imprint_id = '" . $row_bookx_extra['bookx_imprint_id'] . "' LIMIT 1");
    $row_bookx_imprint_name = ($ep_uses_mysqli ? mysqli_fetch_array($sql) : mysql_fetch_array($sql));
   	if ($sql->num_rows == 1) { // double check ? 
    $row['v_bookx_imprint_name'] = $row_bookx_imprint_name['imprint_name'];
	}	else {
		$row['v_bookx_imprint_name'] = '';
		} 
}
//ends Bookx imprint

// Bookx Binding 
if (isset($filelayout['v_bookx_binding']) && ($row_bookx_extra['bookx_binding_id'] != '0') && ($row_bookx_extra['bookx_binding_id'] != '')) { // '0' is correct, but '' NULL is possible

	$sql = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_BINDING_DESCRIPTION." WHERE bookx_binding_id = '".$row_bookx_extra['bookx_binding_id']."' LIMIT 1");
		$row_bookx_binding = ($ep_uses_mysqli ? mysqli_fetch_array($sql) : mysql_fetch_array($sql));
			$row['v_bookx_binding'] = $row_bookx_binding['binding_description'];
}	else {
		$row['v_bookx_binding'] = '';
	}
//ends binding


// Bookx Printing
if (isset($filelayout['v_bookx_printing']) && ($row_bookx_extra['bookx_printing_id'] != '0') && ($row_bookx_extra['bookx_printing_id'] != '')) { // '0' is correct, but '' NULL is possible
	
	$sql = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_PRINTING_DESCRIPTION." WHERE bookx_printing_id = '".$row_bookx_extra['bookx_printing_id']."' LIMIT 1");	
	$row_bookx_printing = ($ep_uses_mysqli ? mysqli_fetch_array($sql) : mysql_fetch_array($sql));
	
			$row['v_bookx_printing'] = $row_bookx_printing['printing_description'];
	
}	else {
		$row['v_bookx_printing'] = '';
	}
// ends Printing

// Bookx Condition $filelayout[] = 'v_bookx_condition';
if (isset($filelayout['v_bookx_condition']) && ($row_bookx_extra['bookx_condition_id'] != '0') && ($row_bookx_extra['bookx_condition_id'] != '')) { // '0' is correct, but '' NULL is possible

	$sql = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_CONDITIONS_DESCRIPTION." WHERE bookx_condition_id = '".$row_bookx_extra['bookx_condition_id']."' LIMIT 1");
		$row_bookx_condition = ($ep_uses_mysqli ? mysqli_fetch_array($sql) : mysql_fetch_array($sql));

			$row['v_bookx_condition'] = $row_bookx_condition['condition_description'];

}	else {
		$row['v_bookx_condition'] = '';
	}
//ends Condition

// Genre name
if (isset($filelayout['v_bookx_genre_name']))  {
	$sql = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_GENRES_TO_PRODUCTS." WHERE products_id = '".$row['v_products_id']."' LIMIT 1");
	$row_bookx_genre_to_product = ($ep_uses_mysqli ? mysqli_fetch_array($sql) : mysql_fetch_array($sql));

		if (($row_bookx_genre_to_product['bookx_genre_id'] != '0') && ($row_bookx_genre_to_product['bookx_genre_id'] != '')) { // '0' is correct, but '' NULL is possible
		$sql_description = ep_4_query ("SELECT * FROM ".TABLE_PRODUCT_BOOKX_GENRES_DESCRIPTION." WHERE bookx_genre_id = '".$row_bookx_genre_to_product['bookx_genre_id']."' LIMIT 1");
		$row_bookx_genre_description  = ($ep_uses_mysqli ? mysqli_fetch_array($sql_description) : mysql_fetch_array($sql_description));
		$row['v_bookx_genre_name'] = $row_bookx_genre_description['genre_description'];

		}else {
			$row['v_bookx_genre_name'] = '';
		}
} //ends Genre name


/**
 * The Author 
 * We also get the default_type
 */
if (isset($filelayout['v_bookx_author_name']))  {

	$sql = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_AUTHORS_TO_PRODUCTS." WHERE products_id = '".$row['v_products_id']."' LIMIT 5");
	$row_bookx_authors_to_products = ($ep_uses_mysqli ? mysqli_fetch_array($sql) : mysql_fetch_array($sql));
	//pr($row_bookx_authors_to_products);
	// Get the Author Type Descritpion 
	//$bookx_author_type = $row_bookx_authors_to_products['bookx_author_type_id'];

	if (($row_bookx_authors_to_products['bookx_author_id'] != '0') && ($row_bookx_authors_to_products['bookx_author_id'] != '')) { // '0' is correct, but '' NULL is possible

		$sql_authors_name = ep_4_query ("SELECT * FROM ".TABLE_PRODUCT_BOOKX_AUTHORS." WHERE bookx_author_id = '".$row_bookx_authors_to_products['bookx_author_id']."' LIMIT 5");
			$row_bookx_authors_name  = ($ep_uses_mysqli ? mysqli_fetch_array($sql_authors_name ) : mysql_fetch_array($sql_authors_name ));
			pr($row_bookx_authors_name);
			//die();
		$row['v_bookx_author_name'] = $row_bookx_authors_name['author_name'];

    } else {         
    	$row['v_bookx_author_name'] = '';     
    }
}

 /**
  * Bookx Author Type
  * use the Bookx function to get the type
  */
	if (isset($filelayout['v_bookx_author_type']) && ($row_bookx_authors_name['author_default_type'] != '0') && ($row_bookx_authors_name['author_default_type'] != '')) { // '0' is correct, but '' NULL is possible
		
			$row['v_bookx_author_type'] = bookx_get_author_type_description($row_bookx_authors_to_products['bookx_author_type_id'], $epdlanguage_id);
	}	else {
			$row['v_bookx_author_type'] = '';
		}
			

} //ends product bookx export