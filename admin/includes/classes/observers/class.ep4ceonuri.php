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

    $zco_notifier->attach($this, $notifyme); 

  }

/*    $this->notify('NOTIFY_ORDER_INVOICE_CONTENT_READY_TO_SEND', array('zf_insert_id' => $zf_insert_id, 'text_email' => $email_order, 'html_email' => $html_msg));
    zen_mail($this->customer['firstname'] . ' ' . $this->customer['lastname'], $this->customer['email_address'], EMAIL_TEXT_SUBJECT . EMAIL_ORDER_NUMBER_SUBJECT . $zf_insert_id, $email_order, STORE_NAME, EMAIL_FROM, $html_msg, 'checkout', $this->attachArray);
    NOTIFY_ORDER_INVOICE_CONTENT_FOR_ADDITIONAL_EMAILS
    $this->notify('NOTIFY_ORDER_AFTER_SEND_ORDER_EMAIL', array($zf_insert_id, $email_order, $extra_info, $html_msg));
    NOTIFY_ORDER_AFTER_SEND_ORDER_EMAIL*/
    
	// 'EP4_EXPORT_FILE_ARRAY_START'
  function updateEP4ExportFileArrayStart(&$callingClass, $notifier, $paramsArray) { // mc12345678 doesn't work on ZC 1.5.1 and below
    global $ep_dltype, $filelayout_sql, $ep_uses_mysqli, $filelayout;
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
