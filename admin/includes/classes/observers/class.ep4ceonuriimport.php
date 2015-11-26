<?php

/**
 * Description of class.ep4ceonuri
 *
 * @author mc12345678
 */
class ep4ceonuri extends base {

//  private $_product = array();
private $ep4CEONURIDoesExist;

  function __construct() { // ep4ceonuri if this class has difficulty loading
//    global $zco_notifier;
    $notifyme = array();

    $notifyme[] = 'EP4_IMPORT_START';
    $notifyme[] = 'EP4_IMPORT_GENERAL_FILE_ALL';
    $notifyme[] = 'EP4_IMPORT_PRODUCT_DEFAULT_SELECT_FIELDS';
    $notifyme[] = 'EP4_IMPORT_PRODUCT_DEFAULT_SELECT_TABLES';
    $notifyme[] = 'EP4_IMPORT_AFTER_CATEGORY'

    $this->attach($this, $notifyme); 

  }

/* function EP4_IMPORT_START */
  function updateEP4ImportStart(&$callingClass, $notifier, $paramsArray){
    
    $this->ep4CEONURIDoesExist = false;
    
    if (false && ep_4_CEONURIExists() == true) {
      $this->ep4CEONURIDoesExist = true;
      require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.CeonURIMappingAdmin.php');
      require(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'extra_datafiles/ceon_uri_mapping_product_pages.php');
    }

  }

/* function EP4_IMPORT_GENERAL_FILE_ALL */
  function updateEP4ImportGeneralFileAll(&$callingClass, $notifier, $paramsArray){
    global $row, $pID;

    if ($ep4CEONURIDoesExist == true /* && false */) { // Not sure that this is fully developed yet and if correctly incorporated for use. 
      //Order of calls:
      //collect_info
      require_once(DIR_WS_CLASSES . 'class.EP4CeonURIMappingAdminProductPages.php');

      $ceon_uri_mapping_admin = new EP4CeonURIMappingAdminProductPages();

      //$prev_uri_mappings should = $uri_mappings, because previous mappings appears to be used to undo the new $uri_mappings.  These two values would be gathered from above.  $uri_mapping_autogen is used to automatically create a new mapping and is likely to be associated with an admin Constant.  Ideally, if the fields are present then if there is a value in the field will not auto create, if there is null then would want to autocreate.  If the fields are not present, then probably want a flag that says to autocreate the path assuming that it does not already exist.  $pID is the product id which should be gathered from above. "All" mappings need to be posted, ie, if there is more than one language, and only one mapping is passed in then the other mapping should be set to NULL at least eventually if there is nothing already there/rules of import... 
      // isset($pID) && (!isset($prev_uri_mappings) or !isset($uri_mappings) or !isset($uri_mapping_autogen)) This will 
      $pID = $row['products_id'];
      $ceon_uri_mapping_admin->collectInfoHandler($prev_uri_mappings, $uri_mappings, $uri_mapping_autogen, $pID);

      //This is an unnecessary action essentially, as the data is already above.  That said, there are "controls" in the step that may be applicable later...  echo $ceon_uri_mapping_admin->collectInfoBuildURIMappingFields(); //Posts/sets results from collectInfoBuildURIMappingFields	
      //product(or document_product or document_general or product_book or product_free_shipping or product_music)/preview_info
      // if information is posted then go through data and collect/interprelate
      $prev_uri_mappings = $ceon_uri_mapping_admin->_prev_uri_mappings;
      $uri_mapping_autogen = $ceon_uri_mapping_admin->_uri_mapping_autogen;
      $uri_mappings = $ceon_uri_mapping_admin->_uri_mappings;

//        $ceon_uri_mapping_admin->productPreviewProcessSubmission($current_category_id, $prev_uri_mappings, $uri_mappings, $uri_mapping_autogen, $products_name, $products_model, $master_category, $pID);

      // if information not posted, then collect data from get statement/ other data (This doesn't appear to be applicable as it doesn't seem that this condition will/could exist here.
      /* $ceon_uri_mapping_admin->productPreviewInitialLoad((int) $_GET['pID'],
        $zc_products->get_handler((int) $_GET['product_type'])); */

      // $i here is the language number and needs to be set/assigned.
//      $ceon_uri_mapping_admin->productPreviewOutputURIMappingInfo($languages[$i]);
      // The next is not necessary as it only outputs data that already exists.
      //echo $ceon_uri_mapping_admin->productPreviewBuildHiddenFields();
      //update_product

//        $uri_mappings = $ceon_uri_mapping_admin->_uri_mappings;
//        $prev_uri_mappings = $ceon_uri_mapping_admin->_prev_uri_mappings;
//        if ($need_to_update_uris) {
//          $ceon_uri_mapping_admin->updateProductHandler($products_id, $zc_products->get_handler($product_type), $prev_uri_mappings, $uri_mappings);
//        }
      //Variables that need to be created and code to do so:
      //New Category being created:
//		     require_once(DIR_WS_CLASSES . 'class.EP4CeonURIMappingAdminCategoryPages.php');
//			$ceon_uri_mapping_admin = new EP4CeonURIMappingAdminCategoryPages();
//			$ceon_uri_mapping_admin->addURIMappingFieldsToAddCategoryFieldsArray;
      //Edit a Category that exists:
//			require_once(DIR_WS_CLASSES . 'class.EP4CeonURIMappingAdminCategoryPages.php');
//			$ceon_uri_mapping_admin = new EP4CeonURIMappingAdminCategoryPages();
//			$ceon_uri_mapping_admin->addURIMappingFieldsToEditCategoryFieldsArray((int) $cInfo->categories_id/*This is the category ID of the existing category*/);
      //product is in the process of being copied (copy_to):
//			require_once(DIR_WS_CLASSES . 'class.CeonURIMappingAdminProductPages.php');
//			$ceon_uri_mapping_admin = new CeonURIMappingAdminProductPages();
//			$ceon_uri_mapping_admin->addURIMappingFieldsToProductCopyFieldsArray((int) $_GET['pID']);
    }
  }

/* function EP4_IMPORT_PRODUCT_DEFAULT_SELECT_FIELDS */
  function updateEP4ImportProductDefaultSelectFields(&$callingClass, $notifier, $paramsArray){
    global $sql;
    
    if ($this->ep4CEONURIDoesExist == true) {
      $sql .= ' c.uri as v_uri,
        c.language_id as v_language_id,
        c.associated_db_id as v_associated_db_id, ' . /* c.master_categories_id as v_master_categories_id, */ '
        c.main_page as v_main_page,
        c.query_string_parameters as v_query_string_parameters,
        c.associated_db_id as v_associated_db_id, ';
    }
  }

/* function EP4_IMPORT_PRODUCT_DEFAULT_SELECT_TABLES */
  function updateEP4ImportProductDefaultSelectTables(&$callingClass, $notifier, $paramsArray) {
    global $sql;
    
    if ($this->ep4CEONURIDoesExist == true) {
      $filenamelist = implode("','", $ceon_uri_mapping_product_pages);
      $sql .= " LEFT JOIN " . TABLE_CEON_URI_MAPPINGS . " as c 
        ON 
        p.products_id = c.associated_db_id AND
        c.main_page IN ('" . $filenamelist . "') AND
        c.current_uri = '1' ";
    }
  }

/* function EP4_IMPORT_AFTER_CATEGORY */
  function updateEP4ImportAfterCategory(&$callingClass, $notifier, $paramsArray) {
    global $v_categories_id, $pID, $current_category_id, $v_products_name, $v_products_model, $zc_products, $product_type, $theparent_id;

    if ($this->ep4CEONURIDoesExist == true /* && false */) { // Not sure that this is fully developed yet and if correctly incorporated for use. 
        
      $current_category_id = $v_categories_id;
      $master_category = zen_get_products_category_id($pID);
        // Because loading in new URIs, need to populate the $ceon_uri_mapping_admin->_current_uris with the language specific differences to populate the database with the new uris. if just populating database with file URIs (ie from import of file, then should set autogeneration to false for this evolution, otherwise true will create new URIs independent of the entered one(s), which may be desired for a blank designation?! Otherewise may need to establish some method of "deleting" a URI or setting it to blank. :/
      $ceon_uri_mapping_admin->productPreviewProcessSubmission($current_category_id, $v_products_name, $v_products_model, $master_category, $pID); // This is a shortened version of the one below that uses data setup from an earlier run and not updated with anything else captured above...  Ideally, the data collected from teh file needs to be transferred into the database, which I am not sure has actually happened yet.
      $uri_mappings = $ceon_uri_mapping_admin->_uri_mappings;
      $prev_uri_mappings = $ceon_uri_mapping_admin->_prev_uri_mappings;
      if (false && true /*|| $need_to_update_uris*/) {
        $ceon_uri_mapping_admin->updateProductHandler($products_id, $zc_products->get_handler($product_type), $prev_uri_mappings, $uri_mappings);
      }
    }
        
//        $ceon_uri_mapping_admin->productPreviewProcessSubmission($current_category_id, $prev_uri_mappings, $uri_mappings, $uri_mapping_autogen, $products_name, $products_model, $master_category, $pID);
    $parent_category_id = $theparent_id;
  }


  function update(&$callingClass, $notifier, $paramsArray) {

/* function EP4_IMPORT_START */
    if ($notifier == 'EP4_IMPORT_START') {
//      $this->updateEP4ExtraFunctionsSetFilelayoutFullStart($callingClass, $notifier, $paramsArray);
    }

/* function EP4_IMPORT_GENERAL_FILE_ALL */
    //$zco_notifier->notify('EP4_IMPORT_GENERAL_FILE_ALL');
    if ($notifier == 'EP4_IMPORT_GENERAL_FILE_ALL') {
//      $this->updateEP4ExtraFunctionsSetFilelayoutFullFilelayout($callingClass, $notifier, $paramsArray);
    }
  
/* function EP4_IMPORT_PRODUCT_DEFAULT_SELECT_FIELDS */
  // $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_SQL_SELECT');
    if ($notifier == 'EP4_IMPORT_PRODUCT_DEFAULT_SELECT_FIELDS') {
//      $this->updateEP4ExtraFunctionsSetFilelayoutFullSQLSelect($callingClass, $notifier, $paramsArray);
    }

/* function EP4_IMPORT_PRODUCT_DEFAULT_SELECT_TABLES */
  // $zco_notifier->notify('EP4_IMPORT_PRODUCT_DEFAULT_SELECT_TABLES');
    if ($notifier == 'EP4_IMPORT_PRODUCT_DEFAULT_SELECT_TABLES') {
//      $this->updateEP4ExtraFunctionsSetFilelayoutFullSQLTable($callingClass, $notifier, $paramsArray);
    }
  
/* function EP4_IMPORT_AFTER_CATEGORY */
  // $zco_notifier->notify('EP4_IMPORT_AFTER_CATEGORY');
    if ($notifier == 'EP4_IMPORT_AFTER_CATEGORY') {
//      $this->updateEP4ExtraFunctionsSetFilelayoutCategoryFilelayout($callingClass, $notifier, $paramsArray);
    }
  } // EOF Update()
}
