<?php 
/**
 * @EP4Bookx - EP4 CSV fork to import Bookx fields - tested with Zencart 1.5.4
 * @version  0.9.9 - Still in development, make your changes in a local environment
 * @see Bookx module for ZenCart
 * @see Readme-EP4Bookx
 *
 * @author mesnitu
 * @todo  export with support for languages
 */

if ($row['v_products_type'] == $bookx_product_type) { // check bookx product type

    if (isset($filelayout['v_bookx_isbn'])) {

        $sql = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_EXTRA."	 WHERE products_id = '".$row['v_products_id']."' LIMIT 1");
        $row_bookx_extra = ($ep_uses_mysqli ? mysqli_fetch_array($sql) : mysql_fetch_array($sql));

        if (($row_bookx_extra['isbn'] != '0') && ($row_bookx_extra['isbn'] != '') || ($row_bookx_extra['size'] != '0') && ($row_bookx_extra['size'] != '') || ($row_bookx_extra['pages'] != '0') && ($row_bookx_extra['pages'] != '') || ($row_bookx_extra['publishing_date'] != '0') && ($row_bookx_extra['publishing_date'] != '') || ($row['v_bookx_volume'] != '0') && ($row['v_bookx_volume'] != '0')) { // '0' is correct, but '' NULL is possible

            $row['v_bookx_isbn'] = $row_bookx_extra['isbn'];
            $row['v_bookx_size'] = $row_bookx_extra['size'];
            $row['v_bookx_pages'] = $row_bookx_extra['pages'];
            $row['v_bookx_publishing_date'] = $row_bookx_extra['publishing_date'];
            $row['v_bookx_volume'] = $row_bookx_extra['volume'];
        } else {
            $row['v_bookx_isbn'] = '';
            $row['v_bookx_size'] = '';
            $row['v_bookx_pages'] = '';
            $row['v_bookx_volume'] = '';
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

    } //ends book_extra_descritpion

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

        $sql = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_SERIES_DESCRIPTION." WHERE bookx_series_id = '".$row_bookx_extra['bookx_series_id']."' LIMIT 1");
        $row_bookx_series = ($ep_uses_mysqli ? mysqli_fetch_array($sql) : mysql_fetch_array($sql));
        $row['v_bookx_series_name'] = $row_bookx_series['series_name'];

    } else {
        $row['v_bookx_series_name'] = '';
    }
    //ends series name

    // Imprints Name 
    if (isset($filelayout['v_bookx_imprint_name']) && ($row_bookx_extra['bookx_imprint_id'] !== '') && ($row_bookx_extra['bookx_imprint_id'] !== '0')) {

        $sql = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_IMPRINTS." WHERE bookx_imprint_id = '".$row_bookx_extra['bookx_imprint_id']."' LIMIT 1");
        $row_bookx_imprint_name = ($ep_uses_mysqli ? mysqli_fetch_array($sql) : mysql_fetch_array($sql));
        if ($sql->num_rows == 1) { // double check ? 
            $row['v_bookx_imprint_name'] = $row_bookx_imprint_name['imprint_name'];
        } else {
            $row['v_bookx_imprint_name'] = '';
        }
    }
    //ends Bookx imprint

    // Bookx Binding 
    if (isset($filelayout['v_bookx_binding']) && ($row_bookx_extra['bookx_binding_id'] != '0') && ($row_bookx_extra['bookx_binding_id'] != '')) { // '0' is correct, but '' NULL is possible

        $sql = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_BINDING_DESCRIPTION." WHERE bookx_binding_id = '".$row_bookx_extra['bookx_binding_id']."' LIMIT 1");
        $row_bookx_binding = ($ep_uses_mysqli ? mysqli_fetch_array($sql) : mysql_fetch_array($sql));
        $row['v_bookx_binding'] = $row_bookx_binding['binding_description'];
    } else {
        $row['v_bookx_binding'] = '';
    }
    //ends binding

    // Bookx Printing
    if (isset($filelayout['v_bookx_printing']) && ($row_bookx_extra['bookx_printing_id'] != '0') && ($row_bookx_extra['bookx_printing_id'] != '')) { // '0' is correct, but '' NULL is possible

        $sql = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_PRINTING_DESCRIPTION." WHERE bookx_printing_id = '".$row_bookx_extra['bookx_printing_id']."' LIMIT 1");
        $row_bookx_printing = ($ep_uses_mysqli ? mysqli_fetch_array($sql) : mysql_fetch_array($sql));

        $row['v_bookx_printing'] = $row_bookx_printing['printing_description'];

    } else {
        $row['v_bookx_printing'] = '';
    }
    // ends Printing

    // Bookx Condition $filelayout[] = 'v_bookx_condition';
    if (isset($filelayout['v_bookx_condition']) && ($row_bookx_extra['bookx_condition_id'] != '0') && ($row_bookx_extra['bookx_condition_id'] != '')) { // '0' is correct, but '' NULL is possible

        $sql = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_CONDITIONS_DESCRIPTION." WHERE bookx_condition_id = '".$row_bookx_extra['bookx_condition_id']."' LIMIT 1");
        $row_bookx_condition = ($ep_uses_mysqli ? mysqli_fetch_array($sql) : mysql_fetch_array($sql));

        $row['v_bookx_condition'] = $row_bookx_condition['condition_description'];

    } else {
        $row['v_bookx_condition'] = '';
    }
    //ends Condition

    // Genre name
    if (isset($filelayout['v_bookx_genre_name'])) {
        //$category_delimiter = '^' // stick to the same delimiter for genres and authors, already presente in EP4 
        $genreID_array = array(); // Creates a empty array to get the genres_id for the loop
        $genre_names_array = array(); // Creates a empty array to get the genres_names
        // first query to get products_id related genre_id
        $sql_genres_to_products = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_GENRES_TO_PRODUCTS." WHERE products_id = '".$row['v_products_id']."'");
        $count_genres = $sql_genres_to_products->num_rows; // count the num_rows (not in use, but possibly a way, to just loop, if the num_rows > 1)
        if ($sql_genres_to_products->num_rows != 0 || $sql_genres_to_products->num_rows != '') {
            // makes a index into $genre_array[] with all the genre_id related to the book 
            while ($row_bookx_genres_to_products = ($ep_uses_mysqli ? mysqli_fetch_assoc($sql_genres_to_products) : mysql_fetch_assoc($sql_genres_to_products))) {
                $genreID_array[] = $row_bookx_genres_to_products['bookx_genre_id']; // we have all book genres_id
            } //ends while 
            foreach($genreID_array as $key => $value) { // start looping
                //query genre name by the values in the genreID_array
                $sql_genres_names = ep_4_query("SELECT genre_description FROM ".TABLE_PRODUCT_BOOKX_GENRES_DESCRIPTION." WHERE bookx_genre_id = '".$value."'");
                $genre_name = ($ep_uses_mysqli ? mysqli_fetch_array($sql_genres_names) : mysql_fetch_array($sql_genres_names));
                $genre_names_array[] = $genre_name['genre_description'];

            } //ends foreach 

            $row['v_bookx_genre_name'] = implode($category_delimiter, $genre_names_array);
        } else {
            //nothing there
            $row['v_bookx_genre_name'] = '';
        }
    } //ends Genre name

    /**
     * The Author
     * We also get the default_type
     */
    if (isset($filelayout['v_bookx_author_name'])) {
        $authorID_array = array(); // Creates a empty array to get the genres_id for the loop
        $author_names_array = array(); // Creates a empty array to get the genres_names
        $author_typeID_array = array(); // We start here authors types array. Same stuff. If there's more than on author, there's a change they could be of difeerent types
        $sql_authors_to_products = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_AUTHORS_TO_PRODUCTS." WHERE products_id = '".$row['v_products_id']."'");

        $count_authors = $sql_authors_to_products->num_rows; // count the num_rows (not in use, but possibly a way, to just loop, if the num_rows > 1)

        if ($sql_authors_to_products->num_rows != 0 || $sql_authors_to_products->num_rows != '') {

            while ($row_bookx_authors_to_products = ($ep_uses_mysqli ? mysqli_fetch_assoc($sql_authors_to_products) : mysql_fetch_assoc($sql_authors_to_products))) {

                $authorID_array[] = $row_bookx_authors_to_products['bookx_author_id']; // we have all book authors_id
                $author_typeID_array[] = $row_bookx_authors_to_products['bookx_author_type_id'];

            } //ends while 

            foreach($authorID_array as $key => $value) { // start looping
                //query genre name by the values in the genreID_array
                $sql_authors_names = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_AUTHORS." WHERE bookx_author_id = '".$value."'");

                $row_author_name = ($ep_uses_mysqli ? mysqli_fetch_array($sql_authors_names) : mysql_fetch_array($sql_authors_names));

                $author_names_array[] = $row_author_name['author_name'];

            } //ends foreach 

            $row['v_bookx_author_name'] = implode($category_delimiter, $author_names_array);

        } else {
            $row['v_bookx_author_name'] = '';
        }
    } // ends authors if

    /**
     * Bookx Author Type
     */
    if (isset($filelayout['v_bookx_author_type']) && ($row_author_name['author_default_type'] != '0') && ($row_author_name['author_default_type'] != '')) { // '0' is correct, but '' NULL is possible

        $author_type_name_array = array();

        foreach($author_typeID_array as $typeID) { // start looping
            //query genre name by the values in the genreID_array

            $sql_author_type_name = ep_4_query("SELECT * FROM ".TABLE_PRODUCT_BOOKX_AUTHOR_TYPES_DESCRIPTION." WHERE bookx_author_type_id = '".$typeID."'");

            $row_author_type_name = ($ep_uses_mysqli ? mysqli_fetch_array($sql_author_type_name) : mysql_fetch_array($sql_author_type_name));

            $author_type_name_array[] = $row_author_type_name['type_description'];

        } //ends foreach           
        $row['v_bookx_author_type'] = implode($category_delimiter, $author_type_name_array);
    } else {
        $row['v_bookx_author_type'] = '';
    }
} //ends product bookx export
