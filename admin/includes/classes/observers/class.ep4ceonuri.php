<?php

/**
 * Description of class.golf
 *
 * @author mc12345678
 */
class ep4ceonuri extends base {

//  private $_product = array();

  // The below variable $sendTo should be updated to reflect the specific recipient(s) to whom an extra
  //   notification will be sent.  
  //   Format: 'Name to be displayed <emailaddress@location.com>, next name <emailaddress2@location2.com>'
  //   Message will be sent to each recipient individually and no other recipient will know that any other received
  //     the message.
//  private $_sendTo = 'Pete <pete_m@comcast.com>';


  function ep4ceonuri() {
    global $zco_notifier;
    $notifyme = array();

    $notifyme[] = 'EP4_EXPORT_FILE_ARRAY_START';
    $notifyme[] = 'EP4_EXPORT_CASE_EXPORT_FILE_END';
    $notifyme[] = 'EP4_EXPORT_WHILE_START';
    $notifyme[] = 'EP4_EXPORT_LOOP_FULL_OR_SBASTOCK';
    $notifyme[] = 'EP4_EXPORT_LOOP_FULL_OR_SBASTOCK_LOOP';
    $notifyme[] = 'EP4_EXPORT_LOOP_FULL_OR_SBASTOCK_END';
	$notifyme[] = 'EP4_EXPORT_SPECIALS_AFTER';


    $zco_notifier->attach($this, $notifyme); 

  }

/*    $this->notify('NOTIFY_ORDER_INVOICE_CONTENT_READY_TO_SEND', array('zf_insert_id' => $zf_insert_id, 'text_email' => $email_order, 'html_email' => $html_msg));
    zen_mail($this->customer['firstname'] . ' ' . $this->customer['lastname'], $this->customer['email_address'], EMAIL_TEXT_SUBJECT . EMAIL_ORDER_NUMBER_SUBJECT . $zf_insert_id, $email_order, STORE_NAME, EMAIL_FROM, $html_msg, 'checkout', $this->attachArray);
    NOTIFY_ORDER_INVOICE_CONTENT_FOR_ADDITIONAL_EMAILS
    $this->notify('NOTIFY_ORDER_AFTER_SEND_ORDER_EMAIL', array($zf_insert_id, $email_order, $extra_info, $html_msg));
    NOTIFY_ORDER_AFTER_SEND_ORDER_EMAIL*/
    
	// 'EP4_EXPORT_FILE_ARRAY_START'
  function updateEP4ExportFileArrayStart(&$callingClass, $notifier, $paramsArray) { // mc12345678 doesn't work on ZC 1.5.1 and below
    global $ep_dltype, $filelayout_sql, $ep_uses_mysqli, $filelayout, $row;
// Need to identify the extent of the array to make the SBA_basic table.
  if ($ep_dltype == 'SBA_basic') {
    // these variablels are for the Attrib_Basic Export
    $active_products_id = ""; // start empty
    $active_options_id = ""; // start empty
    $active_language_id = ""; // start empty
    $active_row = array(); // empty array
    $last_products_id = "";
  
    $result7 = ep_4_query($filelayout_sql);
    $NumProducts = 0;
    $Values = 0;
    $MaxValues = 0;
    $Options = 0;
    $MaxOptions = 0;
    $SBABasicArray = array();
    $SBABasicArray['NumProducts'] = $NumProducts;
  
    while ($row = ($ep_uses_mysqli ? mysqli_fetch_array($result7) : mysql_fetch_array($result7))) {
      if ($row['v_products_id'] == $active_products_id) {
        if ($row['v_options_id'] == $active_options_id) { //On a given Option but new value.
          $Values++;
          if ($Values > $MaxValues) {
            $MaxValues = $Values;
          }
          //Set Number of Values +1 for current Value of Current Option of a Product (ProdXOpYNumVals)
          $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'NumVals'] ++;
  //				echo 'Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'NumVals' . ' 1<br />';
          //Set Value Name for current Value of Current Option of a Product (ProdXOpYValZ = Value)
          $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'Val' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'NumVals']] = $row['v_products_options_values_name'];
  //				echo 'Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'Val' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'NumVals'] . ' 2<br />';
          $active_row['v_products_options_name_' /* . $l_id */] = $row['v_products_options_name'];
          $active_row['v_products_options_values_name_' /* . $l_id */] .= "," . $row['v_products_options_values_name'];
          $active_row['v_products_options_type'] = $row['v_products_options_type'];
          continue;
        } else { // same product, new Option  - only executes once on new option
          // Clean the texts that could break CSV file formatting
          $Options++;
          if ($Options > $MaxOptions) {
            $MaxOptions = $Options;
          }
          $ep_export_count++;
  
          //Set NumOptions to 0 for current product (ProdXNumOps)
          $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] ++;
  //				echo 'Prod' . $SBABasicArray['NumProducts'] . 'NumOps' . ' 1<br />';
          //Set Option Name for current Option of a Product (ProdXOpY = Option)
          $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps']] = $row['v_products_options_name'];
          //Set Option Type for current Option of a Product (ProdXOpYType = Option Type)
          $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'Type'] = $row['v_products_options_type'];    //Set Value Name for current Value of Current Option of a Product (ProdXOpYValZ = Value)
          $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'Val' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'NumVals']] = $row['v_products_options_values_name'];
          $active_options_id = $row['v_options_id'];
  //				$active_language_id = $row['v_language_id'];
  //				$l_id = $row['v_language_id'];
          $active_row['v_products_options_name_' /* . $l_id */] = $row['v_products_options_name'];
          $active_row['v_products_options_values_name_' /* . $l_id */] = $row['v_products_options_values_name'];
          $active_row['v_products_options_type'] = $row['v_products_options_type'];
          continue; // loop - for more products_options_values_name on same v_products_id/v_options_id combo
        }
      } else { // new combo or different product or first time through while-loop
        if ($active_row['v_products_model'] <> $last_products_id) {
          if ($ep_export_count > 0) {
            $SBABasicArray['NumProducts'] ++;
            $Products ++;
          }
          $ep_export_count++;
          $last_products_id = $active_row['v_products_model'];
        } elseif ($active_row['v_products_model'] == "" && $ep_export_count == 0) {
          $Products ++;
          $SBABasicArray['NumProducts'] ++;
          $ep_export_count++;
        }

        //Set NumOptions to 1 for current product (ProdXNumOps)
        $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] = 1;
        //Set Product ID to current product ('ProdXName' = products_id)
        $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'ID'] = $row['v_products_id'];
        //Set Product Model to current product ('ProdXName' = products_id)
        $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Model'] = $row['v_products_model'];
        $Options = 1;
        //Set Option Name for current Option of a Product (ProdXOpY = Option)
        $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps']] = $row['v_products_options_name'];
        //Set Option Type for current Option of a Product (ProdXOpYType = Option Type)
        $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'Type'] = $row['v_products_options_type'];
        //Set Number of Values to 1 for current Value of Current Option of a Product (ProdXOpYNumVals)
        $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'NumVals'] = 1;
        $Values = 1;
        //Set Value Name for current Value of Current Option of a Product (ProdXOpYValZ = Value)
        $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'Val' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'NumVals']] = $row['v_products_options_values_name'];
        // get current row of data
        $active_products_id = $row['v_products_id'];
        $active_options_id = $row['v_options_id'];
        $active_language_id = $row['v_language_id'];
  
        $active_row['v_products_model'] = $row['v_products_model'];
        $active_row['v_products_options_type'] = $row['v_products_options_type'];
  
//			$l_id = $row['v_language_id'];
        $active_row['v_products_options_name_' /* . $l_id */] = $row['v_products_options_name'];
        $active_row['v_products_options_values_name_' /* . $l_id */] = $row['v_products_options_values_name'];
      } // end of special case 'attrib_basic'
    }
    //Add the applicable headers to the file layout
    for ($i = 1; $i <= $MaxOptions; $i++) {
      $filelayout[] = 'v_products_options_type_' . $i; // 0-drop down, 1=text , 2=radio , 3=checkbox, 4=file, 5=read only 
      $filelayout[] = 'v_products_options_name_' . $i; // (Actually want to add these in as the highest order of the options is identified and then also the values
      $filelayout[] = 'v_products_options_values_name_' . $i;
    }
  }
  }


  function updateEP4ExportCaseExportFileEnd(&$callingClass, $notifier, $paramsArray) {
    global $ep_dltype, $EXPORT_FILE;
	
    if ($ep_dltype == 'SBA_basic') {
      $EXPORT_FILE = 'SBA-Basic-EP';
    } elseif ($ep_dltype == 'CEON_URI_active_all') { // mc12345678 - Added to export CEON URIs.
      $EXPORT_FILE = 'CEON-URI-aa-EP';
    } elseif ($ep_dltype == 'CEON_EZPages') { //mc12345678 - Added to export EZ Pages for CEON URIs.
      $EXPORT_FILE = 'CEON-URI-ez-EP';
    }
  }

// EP4_EXPORT_WHILE_START
  function updateEP4ExportWhileStart(&$callingClass, $notifier, $paramsArray) {
    global $ep4CEONURIDoesExist;

    //Start CEON modification - mc12345678
    if (ep_4_CEONURIExists() == true) {
      $ep4CEONURIDoesExist = true;
      //May need to limit these loadings so that applicable to action being taken instead of loading them all.. (Memory hog if all loaded all the time and may have some sort of conflict).  Could use if statements here to load them.
      require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.CeonURIMappingAdmin.php');
      require_once(DIR_FS_ADMIN . DIR_WS_CLASSES . 'class.EP4CeonURIMappingAdminProductPages.php');
      require_once(DIR_FS_ADMIN . DIR_WS_CLASSES . 'class.EP4CeonURIMappingAdminCategoryPages.php');
      require_once(DIR_FS_ADMIN . DIR_WS_CLASSES . 'class.EP4CeonURIMappingAdminEZPagePages.php');
    } //End CEON modification - mc12345678

  }

  //$zco_notifier->notify('EP4_EXPORT_LOOP_FULL_OR_SBASTOCK');
  function updateEP4ExportLoopFullOrSBAStock(&$callingClass, $notifier, $paramsArray) {
    global $ep4CEONURIDoesExist, $prev_uri_mappings, $uri_mappings;
	
	    //Start of CEON URI Rewriter Not 100% sure that the following assignment is necessary; however, it works and does not break anything... - mc12345678
    if ($ep4CEONURIDoesExist == true) {
      $prev_uri_mappings = array();
      $uri_mappings = array();
    }
  }

  //  $zco_notifier->notify('EP4_EXPORT_LOOP_FULL_OR_SBASTOCK_LOOP');
  function updateEP4ExportLoopFullOrSBAStockLoop(&$callingClass, $notifier, $paramsArray) {
    global $products_name, $lid2, $row;
	
    $products_name[$lid2] = $row['v_products_name_' . $lid2]; // CEON Needed
  }

//    $zco_notifier->notify('EP4_EXPORT_LOOP_FULL_OR_SBASTOCK_END');
  function updateEP4ExportLoopFullOrSBAStockEnd(&$callingClass, $notifier, $paramsArray) {
    global $db, $ep_uses_mysqli, $ep4CEONURIDoesExist, $langcode, $prev_uri_mappings, $uri_mappings, $row, $messageStack, $ceon_uri_mapping_admin;

    //Start of CEON URI Addon - mc12345678
    if ($ep4CEONURIDoesExist == true && !(EP4_AUTOCREATE_FROM_BLANK == '0' && EP4_AUTORECREATE_EXISTING == '0')) {
    foreach ($langcode as $key => $lang) {
      $lid = $lang['id'];
        $ceon_uri_mapping_admin = new EP4CeonURIMappingAdminProductPages();

        //$prev_uri_mappings should = $uri_mappings, because previous mappings appears to be used to undo the new $uri_mappings.  These two values would be gathered from above.  $uri_mapping_autogen is used to automatically create a new mapping and is likely to be associated with an admin Constant.  Ideally, if the fields are present then if there is a value in the field will not auto create, if there is null then would want to autocreate.  If the fields are not present, then probably want a flag that says to autocreate the path assuming that it does not already exist.  $pID is the product id which should be gathered from above. "All" mappings need to be posted, ie, if there is more than one language, and only one mapping is passed in then the other mapping should be set to NULL at least eventually if there is nothing already there/rules of import... 

        foreach ($langcode as $key2 => $lang2) {
          $lid2 = $lang2['id'];
          $prev_uri_mappings[$lid2] = NULL;
          $uri_mappings[$lid2] = NULL;
          $ceon_uri_mapping_admin->_prev_uri_mappings[$lid2] = $prev_uri_mappings[$lid2];
          $ceon_uri_mapping_admin->_uri_mappings[$lid2] = $uri_mappings[$lid2];
        } //Cycle through Languages

        $pID = $row['v_products_id'];
        $ceon_uri_mapping_admin->collectInfoHandler($prev_uri_mappings, $uri_mappings, NULL, $pID); // Automatically pulls the most recent information from the database regardless of other settings.  This will return data for a product that has already had a uri rewritten.
        //Determine if the URIs should be autogenerated from CEON's methodology.
        // When this is true, then future URIs will follow the rules associated with that plugin.
        $uri_mapping_autogen = ((!zen_not_null($ceon_uri_mapping_admin->_uri_mappings[$lid]) && EP4_AUTOCREATE_FROM_BLANK == '1') || EP4_AUTORECREATE_EXISTING == '1' || (EP4_AUTORECREATE_EXISTING == '2' && (EP4_AUTOCREATE_FROM_BLANK == '1' || (EP4_AUTOCREATE_FROM_BLANK == '0' && zen_not_null($ceon_uri_mapping_admin->_uri_mappings[$lid])))));
        /*
         * Rewriting: EP4_REWRITE
0 off no rewrites                       			EP4_AUTOCREATE_FROM_BLANK = '0' & EP4_AUTORECREATE_EXISTING = '0'
1 Rewrite unwritten (blank only)				EP4_AUTOCREATE_FROM_BLANK = '1' & EP4_AUTORECREATE_EXISTING = '0'
2 Rewrite Unwritten and written (all - blank & and existing)	EP4_AUTOCREATE_FROM_BLANK = '1' & EP4_AUTORECREATE_EXISTING = '1' || '2'
3 Rewrite only existing (existing only)				EP4_AUTOCREATE_FROM_BLANK = '0' & EP4_AUTORECREATE_EXISTING = '2'

         */
        $products_model = $row['v_products_model'];
        $current_category_id = $row['v_categories_id'];
        $master_category = $row['v_master_categories_id']; //mastercategory($pID);
        $ceon_uri_mapping_admin->_uri_mapping_autogen = $uri_mapping_autogen;
        $returned = $ceon_uri_mapping_admin->productPreviewProcessSubmission($current_category_id, $products_name, $products_model, $master_category, $pID/* , $uri_mapping_autogen */);  // This returns a rewritten URI if it is to be rewritten which includes all languages being rewritten (autogen).
        // The below values are what are sent on to be updated.  These values must be what is desired to go forward if an update is to occur.
        $prev_uri_mappings = $ceon_uri_mapping_admin->_prev_uri_mappings;
        $uri_mappings = $ceon_uri_mapping_admin->_uri_mappings;

        // Update all - No changes to the current data are necessary,
        //  resulting from recreate == 1, or 
        //  (recreate == 2 and from blank == 1)
        // Update existing only (recreate == 2 and from blank == 0) then
        //	blanks from previous copied to current.
        // Update blanks only (recreate == 0 and from blank == 1) then
        //	copy prev existing to current, leaving blanks in prev.
        if (EP4_AUTOCREATE_FROM_BLANK == '0' && (EP4_AUTORECREATE_EXISTING == '2')) {
          //Cycle through languages, where previous is not blank, current = previous
          foreach ($langcode as $key2 => $lang2) {
            $lid2 = $lang2['id'];
            if (!zen_not_null($prev_uri_mappings[$lid2])) {
              $uri_mappings[$lid2] = $prev_uri_mappings[$lid2];
            }
          } //Cycle through Languages
        }

        if (EP4_AUTOCREATE_FROM_BLANK == '1' && (EP4_AUTORECREATE_EXISTING == '0')) {
          //Cycle through languages, where previous is not blank, current = previous
          foreach ($langcode as $key2 => $lang2) {
            $lid2 = $lang2['id'];
            if (zen_not_null($prev_uri_mappings[$lid2])) {
              $uri_mappings[$lid2] = $prev_uri_mappings[$lid2];
            }
          } //Cycle through Languages
        }

        $sqlselectpt = 'SELECT pt.type_handler FROM ' . TABLE_PRODUCT_TYPES . ' as pt INNER JOIN ' . TABLE_PRODUCTS . ' as p ON pt.type_id = p.products_type WHERE p.products_id = :products_id:;';
        $sqlselectpt = $db->bindVars($sqlselectpt, ':products_id:', $row['v_products_id'], 'integer');
        $resultselectpt = ep_4_query($sqlselectpt);
        $rowselectpt = ($ep_uses_mysqli ? mysqli_fetch_array($resultselectpt) : mysql_fetch_array($resultselectpt));
//Capture the data for the record before it is updated.  I guess, it could always be captured here, and then overwritten below if so desired.
        // Check to see if mapping is supposed to be autogenerated from previous data.
        //Effective desire: if the option is to autogenerate data, and
        // there is data to be updated then send it through.
        if (!(EP4_EXPORT_ONLY) && $uri_mapping_autogen && ($returned != $ceon_uri_mapping_admin->_prev_uri_mappings)) {
          $ceon_uri_mapping_admin->updateProductHandler($pID, $rowselectpt['type_handler'], $prev_uri_mappings, $uri_mappings);
        } // autogenerate if supposed to autogenerate.
        //Populate the output with the autogenerated data.
        //May need to add a switch to not update the exported data to show the new record(s).  This should be opposite or different than the switch above that would export the pre-autogeneration data.  Now, if the record is not autogenerated then this below should probably also be run, but if the item is not autogenerated then would want to export the below.  So this set of code could 
        $row['v_main_page'] = $rowselectpt['type_handler'] . '_info';
        $row['v_associated_db_id'] = $pID;
        $row['v_date_added'] = $row['v_date_added'];
        $row['v_products_model'] = $row['v_products_model'];

        /* EXPORT_ONLY Intention is to provide an option to export data only to the spreadsheet.  If any of the update options is selected, then export the resulting updated data but do not update the info. */
        if (EP4_EXPORT_ONLY) {
          $row['v_uri_' . $lid] = $uri_mappings[$lid];
        } else {
          $row['v_uri_' . $lid] = $ceon_uri_mapping_admin->_uri_mappings[$lid];
        }
    } // foreach
      } // End of CEON Insert for Export mc12345678
    $messageStack->reset();
  }


//  $zco_notifier->notify('EP4_EXPORT_SPECIALS_AFTER');
  function updateEP4ExportSpecialsAfter(&$callingClass, $notifier, $paramsArray) {
    global $ep_dltype, $ep4CEONURIDoesExist, $ceon_uri_EZmapping_admin, $langcode, $row, $thecategory_id, $theparent_id;
  // EZ-Pages - mc12345678
  if ($ep_dltype == 'CEON_EZPages' && !(EP4_AUTOCREATE_EZ_FROM_BLANK == '0' && EP4_AUTORECREATE_EZ_EXISTING == '0')) {
    if ($ep4CEONURIDoesExist == true) {
      $EZ_prev_uri_mappings = array();
      $EZ_uri_mappings = array();
    }

//		foreach ($langcode as $key2 => $lang2) {
//			$lid2 = $lang2['id'];
//			$sql2 = 'SELECT * FROM ' . TABLE_PRODUCTS_DESCRIPTION . ' WHERE products_id = ' . $row['v_products_id'] . ' AND language_id = ' . $lid2 . ' LIMIT 1 ';
//			$result2 = ep_4_query($sql2);
//			$row2 = ($ep_uses_mysqli ? mysqli_fetch_array($result2) : mysql_fetch_array($result2));
//			$row['v_products_name_' . $lid2] = $row2['products_name'];
//			$products_name[$lid2] = $row['v_products_name_' . $lid2];
//		} // End modification for CEON URI Rewriter mc12345678

    if ($ep4CEONURIDoesExist == true) {
      $ceon_uri_EZmapping_admin = new EP4CeonURIMappingAdminEZPagePages();

      foreach ($langcode as $key2 => $lang2) {
        $lid2 = $lang2['id'];
        $EZ_prev_uri_mappings[$lid2] = NULL;
        $EZ_uri_mappings[$lid2] = NULL;
        $ceon_uri_EZmapping_admin->_prev_uri_mappings[$lid2] = $EZ_prev_uri_mappings[$lid2];
        $ceon_uri_EZmapping_admin->_uri_mappings[$lid2] = $EZ_uri_mappings[$lid2];
      } //Cycle through Languages

      $ezID = $row['v_pages_id'];

      $EZ_uri_mappings = $ceon_uri_EZmapping_admin->configureEnvironment($ezID, $EZ_prev_uri_mappings, $EZ_uri_mappings); // Populates past
//				$EZ_prev_uri_mappings = $ceon_uri_EZmapping_admin->$_prev_uri_mappings;
//				$EZ_uri_mappings = $ceon_uri_EZmapping_admin->$_uri_mappings;
      $EZ_prev_uri_mappings = $EZ_uri_mappings;

      $page_title = $row['v_pages_title'];

      /* $page_titles_array; // Need to populate this/identify how to... */
      $page_titles_array = NULL;

      $EZ_uri_mappings = $ceon_uri_EZmapping_admin->insertUpdateHandler($ezID, $page_title, $EZ_prev_uri_mappings, $EZ_uri_mappings, $page_titles_array);


      if (true /* Write to file */) {
        foreach ($langcode as $key2 => $lang2) {
          $row['v_uri_' . $lang2['id']] = $EZ_uri_mappings[$lang2['id']];
        }
        $row['v_main_page'] = FILENAME_EZPAGES;
        $row['v_associated_db'] = $ezID;
        $row['v_alternate_url'] = (zen_not_null($row['v_alternate_url']) ? $row['v_alt_url'] : $row['v_alt_url_external']);
        $row['v_redirection_type_code'] = $row['v_redirection_type_code'];
        $row['v_date_added'] = $row['v_date_added'];
      }


      /* if (!(EP4_EXPORT_ONLY) && $uri_mapping_autogen && ($returned != $ceon_uri_EZmapping_admin->_prev_uri_mappings)) {
        $ceon_uri_EZmapping_admin->updateProductHandler($pID, $rowselectpt['type_handler'], $prev_uri_mappings, $uri_mappings);
        } // autogenerate if supposed to autogenerate.

        $row['v_main_page'] = FILENAME_EZPAGES;
        $row['v_associated_db_id'] = $ezID;
        $row['v_date_added'] = $row['v_date_added'];
        //				$row['v_products_model'] = $row['v_products_model']; */
    } // End of CEON Insert for Export mc12345678
  } //End EZ-Pages - mc12345678

	if ($ep_dltype == 'categorymeta') {
		// names and descriptions require that we loop thru all languages that are turned on in the store
    if ($ep4CEONURIDoesExist == true && !(EP4_AUTOCREATE_CAT_FROM_BLANK == '0' && EP4_AUTORECREATE_CAT_EXISTING == '0')) {
			$thecategory_id = $row['v_categories_id']; // starting category_id
			$ceon_uri_cat_mapping = new EP4CeonURIMappingAdminCategoryPages();
			foreach ($langcode as $key2 => $lang2) {
				$categories_name[$lang2['id']] = '';
				$cat_prev_uri_mappings[$lang2['id']] = NULL;
				$cat_uri_mappings[$lang2['id']] = NULL;
			}

			$cat_uri_mappings = $ceon_uri_cat_mapping->addURIMappingFieldsToEditCategoryFieldsArray ($thecategory_id);
			$cat_prev_uri_mappings = $cat_uri_mappings;

			$sql2 = 'SELECT * FROM ' . TABLE_CATEGORIES_DESCRIPTION . ' WHERE categories_id = ' . $thecategory_id . ' ORDER BY language_id';
			$result2 = ep_4_query($sql2);
			while ($row2 = ($ep_uses_mysqli ? mysqli_fetch_array($result2) : mysql_fetch_array($result2))) {
				$categories_name[$row2['language_id']] = $row2['categories_name'];
			} //while
			$sql3 = 'SELECT parent_id FROM ' . TABLE_CATEGORIES . ' WHERE categories_id = ' . $thecategory_id;
			$result3 = ep_4_query($sql3);
			$row3 = ($ep_uses_mysqli ? mysqli_fetch_array($result3) : mysql_fetch_array($result3));
			$theparent_id = $row3['parent_id'];


			$cat_uri_mappings = $ceon_uri_cat_mapping->insertUpdateHandler($thecategory_id, $theparent_id, $cat_prev_uri_mappings, $cat_uri_mappings, $categories_name, true);

			if (true /*Write to file*/) {
				foreach ($langcode as $key2 => $lang2) {
					$row['v_uri_' . $lang2['id']] = $cat_uri_mappings[$lang2['id']];
				}
				$row['v_main_page'] = FILENAME_DEFAULT;
				$row['v_associated_db'] = NULL;
				$row['v_master_categories_id'] = $theparent_id;
			}

		}

    }
  }

//  $zco_notifier->notify('EP4_EXPORT_FULL_OR_CAT_FULL_AFTER');
  function updateEP4ExportFullOrCatFullAfter(&$callingClass, $notifier, $paramsArray) {
    global $db, $ep4CEONURIDoesExist, $ep_dltype, $langcode, $thecategory_id, $theparent_id, $row;

    if ($ep4CEONURIDoesExist == true && $ep_dltype == 'category' && !(EP4_AUTOCREATE_CAT_FROM_BLANK == '0' && EP4_AUTORECREATE_CAT_EXISTING == '0')) {
      $ceon_uri_cat_mapping = new EP4CeonURIMappingAdminCategoryPages();
      foreach ($langcode as $key2 => $lang2) {
        $categories_name[$lang2['id']] = '';
        $cat_prev_uri_mappings[$lang2['id']] = NULL;
        $cat_uri_mappings[$lang2['id']] = NULL;
      }

      $cat_uri_mappings = $ceon_uri_cat_mapping->addURIMappingFieldsToEditCategoryFieldsArray($thecategory_id);
      $cat_prev_uri_mappings = $cat_uri_mappings;

      $sql2 = 'SELECT * FROM ' . TABLE_CATEGORIES_DESCRIPTION . ' WHERE categories_id = ' . $thecategory_id . ' ORDER BY language_id';
      $result2 = ep_4_query($sql2);
      while ($row2 = ($ep_uses_mysqli ? mysqli_fetch_array($result2) : mysql_fetch_array($result2))) {
        $categories_name[$row2['language_id']] = $row2['categories_name'];
      } //while
      $sql3 = 'SELECT parent_id FROM ' . TABLE_CATEGORIES . ' WHERE categories_id = ' . $thecategory_id;
      $result3 = ep_4_query($sql3);
      $row3 = ($ep_uses_mysqli ? mysqli_fetch_array($result3) : mysql_fetch_array($result3));
      $theparent_id = $row3['parent_id'];


      $cat_uri_mappings = $ceon_uri_cat_mapping->insertUpdateHandler($thecategory_id, $theparent_id, $cat_prev_uri_mappings, $cat_uri_mappings, $categories_name, true);

      if (true /* Write to file */) {
        foreach ($langcode as $key2 => $lang2) {
          $row['v_uri_' . $lang2['id']] = $cat_uri_mappings[$lang2['id']];
        }
        $row['v_main_page'] = FILENAME_DEFAULT;
        $row['v_associated_db'] = NULL;
        $row['v_master_categories_id'] = $theparent_id;
      }
    }
  }

  function update(&$callingClass, $notifier, $paramsArray) {
    if ($notifier == 'NOTIFY_ORDER_AFTER_SEND_ORDER_EMAIL') {
      $product = array();
      $sendEmail = false;
      
        // Need to only modify the below list of product to include the product_id for the product to notify about.
        /*if (SEND_EMAIL_PRODUCT_DISTRIBUTION != '') {
          $product[38] = true;
          $product[39] = true;
          $product[40] = true;
          $product[45] = true;
          $product[48] = true;
        }*/
        
      if (sizeof($callingClass->products) > 0 ) {
        for ($i=0, $n = sizeof($callingClass->products); $i<$n; $i++) {
          if ($this->_product[zen_get_prid($callingClass->products[$i]['id'])] == true) {
            $sendEmail = true;
            $sendTo = $this->_sendTo;
            break;
          }
        }
      }
      
      if ($sendEmail) {
        list($zf_insert_id, $email_order, $extra_info, $html_msg) = $paramsArray;
        zen_mail('', $sendTo, SEND_EXTRA_NEW_ORDERS_EMAILS_TO_SUBJECT . ' ' . EMAIL_TEXT_SUBJECT . EMAIL_ORDER_NUMBER_SUBJECT . $zf_insert_id, $email_order . $extra_info['TEXT'], STORE_NAME, EMAIL_FROM, $html_msg, 'checkout_extra', $callingClass->attachArray);
      }
    }
  }
}
