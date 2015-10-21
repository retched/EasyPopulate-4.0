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

    $notifyme[] = 'EP4_START';
    $notifyme[] = 'EP4_FILENAMES';
    $notifyme[] = 'EP4_LINK_SELECTION_END';
    $notifyme[] = 'EP4_EXPORT_FILE_ARRAY_START';
    $notifyme[] = 'EP4_EXPORT_CASE_EXPORT_FILE_END';
    $notifyme[] = 'EP4_EXPORT_WHILE_START';
    $notifyme[] = 'EP4_EXPORT_LOOP_FULL_OR_SBASTOCK';
    $notifyme[] = 'EP4_EXPORT_LOOP_FULL_OR_SBASTOCK_LOOP';
    $notifyme[] = 'EP4_EXPORT_LOOP_FULL_OR_SBASTOCK_END';
    $notifyme[] = 'EP4_EXPORT_SPECIALS_AFTER';
    $notifyme[] = 'EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_START';
    $notifyme[] = 'EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_FILELAYOUT';
    $notifyme[] = 'EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_SQL_SELECT';
    $notifyme[] = 'EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_SQL_TABLE';
    $notifyme[] = 'EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CATEGORY_FILELAYOUT';
    $notifyme[] = 'EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CATEGORY_SQL_SELECT';
    $notifyme[] = 'EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CATEGORYMETA_FILELAYOUT';
    $notifyme[] = 'EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CASE_DEFAULT';
    $notifyme[] = 'EP4_EXTRA_FUNCTIONS_INSTALL_END';
    $notifyme[] = 'EP4_EXPORT_FULL_OR_CAT_FULL_AFTER';

    $this->attach($this, $notifyme); 

  }

/* Function run/called by notifier: EP4_START*/
  function updateEP4Start(&$callingClass, $notifier, $paramsArray){
    global $curver;
    $curver .= "<br />";
    $curver .= "w/ CEON URI v1.1";
    if (ep_4_CEONURIExists() == true) {
      $this->ep4CEONURIDoesExist = true;
      require(DIR_FS_ADMIN . DIR_WS_LANGUAGES . $_SESSION['language'] . '/easypopulate_4_ceon.php');
    }
  }

  // $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_START');
  function updateEP4ExtraFunctionsSetFilelayoutFullStart(&$callingClass, $notifier, $paramsArray) {
    global $ceon_uri_mapping_product_pages, $ceon_uri_mapping_product_related_pages;
    if (ep_4_CEONURIExists() == true && !(EP4_AUTOCREATE_FROM_BLANK == '0' && EP4_AUTORECREATE_EXISTING == '0')) {
      $this->ep4CEONURIDoesExist = true;
      require(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'extra_datafiles/ceon_uri_mapping_product_pages.php');  // Brings in extra variables to support product page types.
    }
  }

    //$zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_FILELAYOUT');
  function updateEP4ExtraFunctionsSetFilelayoutFullFilelayout(&$callingClass, $notifier, $paramsArray) {
    global $filelayout, $langcode;

    if ($this->ep4CEONURIDoesExist == true && !(EP4_AUTOCREATE_FROM_BLANK == '0' && EP4_AUTORECREATE_EXISTING == '0')) {
      $filelayout[] =  'v_products_type';
      foreach ($langcode as $key => $lang) { // create variables for each language id
        $l_id = $lang['id'];
        $filelayout[] =  'v_uri_' . $l_id;
      }
    $filelayout[] =  'v_categories_id';
    $filelayout[] =  'v_main_page';
//Don't need for product      $filelayout[] =  'v_query_string_parameters';
    $filelayout[] =  'v_associated_db_id';
    $filelayout[] =  'v_master_categories_id';
    }
  }

  // $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_SQL_SELECT');
  function updateEP4ExtraFunctionsSetFilelayoutFullSQLSelect(&$callingClass, $notifier, $paramsArray) {
    global $filelayout_sql;
    
    if ($this->ep4CEONURIDoesExist == true && !(EP4_AUTOCREATE_FROM_BLANK == '0' && EP4_AUTORECREATE_EXISTING == '0')) {
/*      foreach ($langcode as $key => $lang) { // create variables for each language id
        $l_id = $lang['id'];
        $filelayout_sql .= ' c'.$l_id.'.uri as v_uri_'.$l_id.', ';
        $filelayout_sql .=  'c'.$l_id.'.main_page as v_main_page_'.$l_id. ', ';
        $filelayout_sql .=  'c'.$l_id.'.associated_db_id as v_associated_db_id_'.$l_id.', ';
      }*/
/*      $filelayout_sql .=  'c.uri as v_uri,';
      $filelayout_sql .=  'c.language_id as v_language_id,';
      $filelayout_sql .=  'c.current_uri as v_current_uri,';*/
      $filelayout_sql .=  'c.main_page as v_main_page,';
// Don't need for product      $filelayout_sql .=  'c.query_string_parameters as v_query_string_parameters,';
      $filelayout_sql .=  'c.associated_db_id as v_associated_db_id,';
      $filelayout_sql .=  'p.master_categories_id as v_master_categories_id,';
      $filelayout_sql .=  'ptoc.categories_id as v_categories_id,';
      //$filelayout_sql .=  'c.products_type as v_products_type,';
    }
  }

  // $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_SQL_TABLE');
  function updateEP4ExtraFunctionsSetFilelayoutFullSQLTable(&$callingClass, $notifier, $paramsArray) {
    global $ceon_uri_mapping_product_pages, $ceon_uri_mapping_product_related_pages, $filelayout_sql, $filenamelist;
    
    if ($this->ep4CEONURIDoesExist == true && !(EP4_AUTOCREATE_FROM_BLANK == '0' && EP4_AUTORECREATE_EXISTING == '0')) { 
      $filenamelist = implode("','", $ceon_uri_mapping_product_pages);
/*        foreach ($langcode as $key => $lang) { // create variables for each language id
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
  }
  
  // $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CATEGORY_FILELAYOUT');
  function updateEP4ExtraFunctionsSetFilelayoutCategoryFilelayout(&$callingClass, $notifier, $paramsArray) {
    global $ceon_uri_mapping_product_pages, $ceon_uri_mapping_product_related_pages;

    if (ep_4_CEONURIExists() == true && !(EP4_AUTOCREATE_CAT_FROM_BLANK == '0' && EP4_AUTORECREATE_CAT_EXISTING == '0')) {
      $this->ep4CEONURIDoesExist = true;
      require(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'extra_datafiles/ceon_uri_mapping_product_pages.php');  // Brings in extra variables to support product page types.
    }
  }

  //  $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CATEGORY_SQL_SELECT');
  function updateEP4ExtraFunctionsSetFilelayoutCategorySQLSelect(&$callingClass, $notifier, $paramsArray) {
    global $langcode, $filelayout;
    
    if ($this->ep4CEONURIDoesExist == true && !(EP4_AUTOCREATE_CAT_FROM_BLANK == '0' && EP4_AUTORECREATE_CAT_EXISTING == '0')) {
      foreach ($langcode as $key => $lang) { // create variables for each language id
        $l_id = $lang['id'];
        $filelayout[] =  'v_uri_' . $l_id;
      }
      $filelayout[] =  'v_categories_id';
      $filelayout[] =  'v_main_page';
//Don't need for product      $filelayout[] =  'v_query_string_parameters';
      $filelayout[] =  'v_associated_db_id';
      $filelayout[] =  'v_master_categories_id';
    }
  }

  //  $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CATEGORYMETA_FILELAYOUT');
  function updateEP4ExtraFunctionsSetFilelayoutCategorymetaFilelayout(&$callingClass, $notifier, $paramsArray) {
    global $ceon_uri_mapping_product_pages, $ceon_uri_mapping_product_related_pages;
    
    if (ep_4_CEONURIExists() == true && !(EP4_AUTOCREATE_CAT_FROM_BLANK == '0' && EP4_AUTORECREATE_CAT_EXISTING == '0')) {
      $this->ep4CEONURIDoesExist = true;
      require(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'extra_datafiles/ceon_uri_mapping_product_pages.php');  // Brings in extra variables to support product page types.
    }
  }

    // $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CASE_DEFAULT');
  function updateEP4ExtraFunctionsSetFilelayoutCaseDefault(&$callingClass, $notifier, $paramsArray) {
    global $db, $ep_dltype, $filelayout, $filelayout_sql, $langcode, $ceon_uri_mapping_product_pages, $ceon_uri_mapping_product_related_pages;
    switch($ep_dltype) {
    
  case 'CEON_EZPages':
    if (ep_4_CEONURIExists() == true && !(EP4_AUTOCREATE_EZ_FROM_BLANK == '0' && EP4_AUTORECREATE_EZ_EXISTING == '0')) {
      $this->ep4CEONURIDoesExist = true;
      require(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'extra_datafiles/ceon_uri_mapping_product_pages.php');  // Brings in extra variables to support product page types.
    }
//    $fileMeta = array();
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

    if ($this->ep4CEONURIDoesExist == true && !(EP4_AUTOCREATE_EZ_FROM_BLANK == '0' && EP4_AUTORECREATE_EZ_EXISTING == '0')) {
      foreach ($langcode as $key => $lang) { // create categories variables for each language id
        $l_id = $lang['id'];
  //      $filelayout[] = 'v_categories_name_'.$l_id;
  //      $filelayout[] = 'v_categories_description_'.$l_id;
        $filelayout[] = 'v_uri_' . $l_id;
      }
//      $filelayout[] =  'v_categories_id';
      $filelayout[] =  'v_main_page';
//Don't need for product      $filelayout[] =  'v_query_string_parameters';
      $filelayout[] =  'v_associated_db_id';
//      $filelayout[] =  'v_master_categories_id';
      $filelayout[] =  'v_alternate_uri';
      $filelayout[] =  'v_redirection_type_code';
    }
    foreach ($langcode as $key => $lang) { // create metatags variables for each language id
      $l_id = $lang['id'];
      $filelayout[]   = 'v_metatags_title_'.$l_id;
      $filelayout[]   = 'v_metatags_keywords_'.$l_id;
      $filelayout[]   = 'v_metatags_description_'.$l_id;
    } 
    $filelayout_sql = 'SELECT ' . (($this->ep4CEONURIDoesExist && !(EP4_AUTOCREATE_EZ_FROM_BLANK == '0' && EP4_AUTORECREATE_EZ_EXISTING == '0')) ? 'DISTINCT' : '');
    if ($this->ep4CEONURIDoesExist == true && !(EP4_AUTOCREATE_EZ_FROM_BLANK == '0' && EP4_AUTORECREATE_EZ_EXISTING == '0')) {
/*      $filelayout_sql .=  'c.uri as v_uri,';
      $filelayout_sql .=  'c.language_id as v_language_id,';
      $filelayout_sql .=  'c.current_uri as v_current_uri,';*/
//      $filelayout_sql .=  'ez.main_page as v_main_page,';
// Don't need for product      $filelayout_sql .=  'c.query_string_parameters as v_query_string_parameters,';
//      $filelayout_sql .=  'c.associated_db_id as v_associated_db_id,';
//      $filelayout_sql .=  'c.master_categories_id as v_master_categories_id,';
      //$filelayout_sql .=  'ptoc.categories_id as v_categories_id,';
      //$filelayout_sql .=  'c.products_type as v_products_type,';
    }
      $filelayout_sql .=  '    ez.pages_id AS v_pages_id, 
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
/*      if ($ep4CEONURIDoesExist == true) { 
//        $filenamelist = implode("','", $ceon_uri_mapping_product_pages);
        $filelayout_sql .= 'LEFT JOIN '.TABLE_CEON_URI_MAPPINGS.' as c 
        ON 
        p.products_id = c.associated_db_id AND
        c.main_page IN (\''.FILENAME_EZPAGES.'\') AND
        c.current_uri = \'1\' ';
      }*/
    break;
    
  case 'SBA_basic': // simplified sinlge-line attributes ... eventually!
    // $filelayout[] =  'v_products_attributes_id';
    // $filelayout[] =  'v_products_id';
    $filelayout[] =  'v_products_model'; // product model from table PRODUCTS
    // p = table PRODUCTS
    // o = table PRODUCTS_OPTIONS
    // v = table PRODUCTS_OPTIONS_VALUES
    $filelayout_sql = 'SELECT
      a.products_attributes_id            as v_products_attributes_id,
      a.products_id                       as v_products_id,
      a.options_id                        as v_options_id,
      a.options_values_id                 as v_options_values_id,
      p.products_model            as v_products_model,
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

  case 'CEON_URI_active_all':
    $filelayout[] =  'v_uri';
    $filelayout[] =  'v_language_id';
    $filelayout[] =  'v_current_uri';
    $filelayout[] =  'v_main_page';
    $filelayout[] =  'v_query_string_parameters';
    $filelayout[] =  'v_associated_db_id';
    $filelayout[] =  'v_date_added';
    $filelayout[] =  'v_products_model'; // product model from table PRODUCTS translated from I think v_associated_db_id
    // p = table PRODUCTS
    // c = table CEON_URI_MAPPINGS
    // o = table PRODUCTS_OPTIONS
    // v = table PRODUCTS_OPTIONS_VALUES
    $filelayout_sql = 'SELECT
      c.uri             as v_uri,
      c.language_id           as v_language_id,
      c.current_uri           as v_current_uri,
      c.main_page           as v_main_page,
      c.query_string_parameters     as v_query_string_parameters,
      c.associated_db_id         as v_associated_db_id,
      c.date_added           as v_date_added ' . /*
      p.products_model         as v_products_model */'
      FROM '
      .TABLE_CEON_URI_MAPPINGS.    ' as c
       WHERE
      c.current_uri    = 1 
      ORDER BY c.main_page, c.associated_db_id, c.date_added'; 
         /*AND
      a.products_id       = p.products_id AND
      a.options_id        = o.products_options_id AND
      a.options_values_id = v.products_options_values_id AND
      o.language_id       = v.language_id ORDER BY a.products_id, a.options_id, v.language_id, v.products_options_values_id'; */
    break;
    }
  }

  //  $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_INSTALL_END');
  function updateEP4ExtraFunctionsInstallEnd(&$callingClass, $notifier, $paramsArray) {
    global $db, $group_id, $project;
  
    if ( (substr($project,0,5) == "1.3.8") || (substr($project,0,5) == "1.3.9") ) {
      $db->Execute("INSERT INTO ".TABLE_CONFIGURATION." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES 
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
      $db->Execute("INSERT INTO ".TABLE_CONFIGURATION." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES 
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
  }

    // $zco_notifier->notify('EP4_LINK_SELECTION_END');
  function updateEP4LinkSelectionEnd(&$callingClass, $notifier, $paramsArray) {
    
    /* Begin CEON URI addition */ if ($this->ep4CEONURIDoesExist == true) { ?>    <br /><b>CEON URI Export/Import Options</b><br />
          <a href="easypopulate_4.php?export=CEON_URI_active_all"><b>CEON URI Active Data Table</b> (basic single-line)</a><br />
          <a href="easypopulate_4.php?export=CEON_detailed"><b>Detailed CEON URI Data</b> (detailed multi-line)</a><br />
          <a href="easypopulate_4.php?export=CEON_EZPages"><b>EZ Pages CEON Data</b> (Export)</a><br />
        <?php } else { ?>CEON URI Mapping is not Installed. <br />
        <?php } /* End CEON URI Addition */
  }
  
  // $zco_notifier->notify('EP4_FILENAMES');
  function updateEP4Filenames(&$callingClass, $notifier, $paramsArray) {
    global $filenames;
    
    $filenames = array_merge($filenames,
      array('ceon-uri-aa-ep' => CEON_URI_AA_EP_DESC,
      'ceon-uri-ez-ep' => CEON_URI_EZ_EP_DESC)
    );

  }

  
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
  //        echo 'Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'NumVals' . ' 1<br />';
          //Set Value Name for current Value of Current Option of a Product (ProdXOpYValZ = Value)
          $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'Val' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'NumVals']] = $row['v_products_options_values_name'];
  //        echo 'Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'Val' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'NumVals'] . ' 2<br />';
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
  //        echo 'Prod' . $SBABasicArray['NumProducts'] . 'NumOps' . ' 1<br />';
          //Set Option Name for current Option of a Product (ProdXOpY = Option)
          $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps']] = $row['v_products_options_name'];
          //Set Option Type for current Option of a Product (ProdXOpYType = Option Type)
          $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'Type'] = $row['v_products_options_type'];    //Set Value Name for current Value of Current Option of a Product (ProdXOpYValZ = Value)
          $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'Val' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'Op' . $SBABasicArray['Prod' . $SBABasicArray['NumProducts'] . 'NumOps'] . 'NumVals']] = $row['v_products_options_values_name'];
          $active_options_id = $row['v_options_id'];
  //        $active_language_id = $row['v_language_id'];
  //        $l_id = $row['v_language_id'];
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
  
//      $l_id = $row['v_language_id'];
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

  // 'EP4_EXPORT_CASE_EXPORT_FILE_END'
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

    //Start CEON modification - mc12345678
    if (ep_4_CEONURIExists() == true) {
      $this->ep4CEONURIDoesExist = true;
      //May need to limit these loadings so that applicable to action being taken instead of loading them all.. (Memory hog if all loaded all the time and may have some sort of conflict).  Could use if statements here to load them.
      require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.CeonURIMappingAdmin.php');
      require_once(DIR_FS_ADMIN . DIR_WS_CLASSES . 'class.EP4CeonURIMappingAdminProductPages.php');
      require_once(DIR_FS_ADMIN . DIR_WS_CLASSES . 'class.EP4CeonURIMappingAdminCategoryPages.php');
      require_once(DIR_FS_ADMIN . DIR_WS_CLASSES . 'class.EP4CeonURIMappingAdminEZPagePages.php');
    } //End CEON modification - mc12345678

  }

  //$zco_notifier->notify('EP4_EXPORT_LOOP_FULL_OR_SBASTOCK');
  function updateEP4ExportLoopFullOrSBAStock(&$callingClass, $notifier, $paramsArray) {
    global $prev_uri_mappings, $uri_mappings;
  
      //Start of CEON URI Rewriter Not 100% sure that the following assignment is necessary; however, it works and does not break anything... - mc12345678
    if ($this->ep4CEONURIDoesExist == true) {
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
    global $db, $ep_uses_mysqli, $langcode, $prev_uri_mappings, $uri_mappings, $row, $messageStack, $ceon_uri_mapping_admin;

    //Start of CEON URI Addon - mc12345678
    if ($this->ep4CEONURIDoesExist == true && !(EP4_AUTOCREATE_FROM_BLANK == '0' && EP4_AUTORECREATE_EXISTING == '0')) {
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
0 off no rewrites                             EP4_AUTOCREATE_FROM_BLANK = '0' & EP4_AUTORECREATE_EXISTING = '0'
1 Rewrite unwritten (blank only)        EP4_AUTOCREATE_FROM_BLANK = '1' & EP4_AUTORECREATE_EXISTING = '0'
2 Rewrite Unwritten and written (all - blank & and existing)  EP4_AUTOCREATE_FROM_BLANK = '1' & EP4_AUTORECREATE_EXISTING = '1' || '2'
3 Rewrite only existing (existing only)        EP4_AUTOCREATE_FROM_BLANK = '0' & EP4_AUTORECREATE_EXISTING = '2'

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
        //  blanks from previous copied to current.
        // Update blanks only (recreate == 0 and from blank == 1) then
        //  copy prev existing to current, leaving blanks in prev.
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
    global $ep_dltype, $ceon_uri_EZmapping_admin, $langcode, $row, $thecategory_id, $theparent_id, $ep_uses_mysqli;
  // EZ-Pages - mc12345678
  if ($ep_dltype == 'CEON_EZPages' && !(EP4_AUTOCREATE_EZ_FROM_BLANK == '0' && EP4_AUTORECREATE_EZ_EXISTING == '0')) {
    if ($this->ep4CEONURIDoesExist == true) {
      $EZ_prev_uri_mappings = array();
      $EZ_uri_mappings = array();
    }

//    foreach ($langcode as $key2 => $lang2) {
//      $lid2 = $lang2['id'];
//      $sql2 = 'SELECT * FROM ' . TABLE_PRODUCTS_DESCRIPTION . ' WHERE products_id = ' . $row['v_products_id'] . ' AND language_id = ' . $lid2 . ' LIMIT 1 ';
//      $result2 = ep_4_query($sql2);
//      $row2 = ($ep_uses_mysqli ? mysqli_fetch_array($result2) : mysql_fetch_array($result2));
//      $row['v_products_name_' . $lid2] = $row2['products_name'];
//      $products_name[$lid2] = $row['v_products_name_' . $lid2];
//    } // End modification for CEON URI Rewriter mc12345678

    if ($this->ep4CEONURIDoesExist == true) {
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
//        $EZ_prev_uri_mappings = $ceon_uri_EZmapping_admin->$_prev_uri_mappings;
//        $EZ_uri_mappings = $ceon_uri_EZmapping_admin->$_uri_mappings;
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
        //        $row['v_products_model'] = $row['v_products_model']; */
    } // End of CEON Insert for Export mc12345678
  } //End EZ-Pages - mc12345678

  if ($ep_dltype == 'categorymeta') {
    // names and descriptions require that we loop thru all languages that are turned on in the store
    if ($this->ep4CEONURIDoesExist == true && !(EP4_AUTOCREATE_CAT_FROM_BLANK == '0' && EP4_AUTORECREATE_CAT_EXISTING == '0')) {
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
    global $db, $ep_dltype, $langcode, $thecategory_id, $theparent_id, $row, $ep_uses_mysqli;

    if ($this->ep4CEONURIDoesExist == true && $ep_dltype == 'category' && !(EP4_AUTOCREATE_CAT_FROM_BLANK == '0' && EP4_AUTORECREATE_CAT_EXISTING == '0')) {
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

    if ($notifier == 'EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_START') {
      updateEP4ExtraFunctionsSetFilelayoutFullStart($callingClass, $notifier, $paramsArray);
    }

    //$zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_FILELAYOUT');
    if ($notifier == 'EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_FILELAYOUT') {
      updateEP4ExtraFunctionsSetFilelayoutFullFilelayout($callingClass, $notifier, $paramsArray);
    }
  
  // $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_SQL_SELECT');
    if ($notifier == 'EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_SQL_SELECT') {
      updateEP4ExtraFunctionsSetFilelayoutFullSQLSelect($callingClass, $notifier, $paramsArray);
    }

  // $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_SQL_TABLE');
    if ($notifier == 'EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_FULL_SQL_TABLE') {
      updateEP4ExtraFunctionsSetFilelayoutFullSQLTable($callingClass, $notifier, $paramsArray);
    }
  
  // $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CATEGORY_FILELAYOUT');
    if ($notifier == 'EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CATEGORY_FILELAYOUT') {
      updateEP4ExtraFunctionsSetFilelayoutCategoryFilelayout($callingClass, $notifier, $paramsArray);
    }

  //  $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CATEGORY_SQL_SELECT');
    if ($notifier == 'EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CATEGORY_SQL_SELECT') {
      updateEP4ExtraFunctionsSetFilelayoutCategorySQLSelect($callingClass, $notifier, $paramsArray);
    }

  //  $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CATEGORYMETA_FILELAYOUT');
    if ($notifier == 'EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CATEGORYMETA_FILELAYOUT') {
      updateEP4ExtraFunctionsSetFilelayoutCategorymetaFilelayout($callingClass, $notifier, $paramsArray);
    }

    // $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CASE_DEFAULT');
    if ($notifier == 'EP4_EXTRA_FUNCTIONS_SET_FILELAYOUT_CASE_DEFAULT') {
      updateEP4ExtraFunctionsSetFilelayoutCaseDefault($callingClass, $notifier, $paramsArray);
    }

  //  $zco_notifier->notify('EP4_EXTRA_FUNCTIONS_INSTALL_END');
    if ($notifier == 'EP4_EXTRA_FUNCTIONS_INSTALL_END') {
      updateEP4ExtraFunctionsInstallEnd($callingClass, $notifier, $paramsArray);
    }

    // $zco_notifier->notify('EP4_LINK_SELECTION_END');
    if ($notifier == 'EP4_LINK_SELECTION_END') {
      updateEP4LinkSelectionEnd($callingClass, $notifier, $paramsArray);
    }
  
  // $zco_notifier->notify('EP4_FILENAMES');
    if ($notifier == 'EP4_FILENAMES') {
      updateEP4Filenames($callingClass, $notifier, $paramsArray);
    }

  
  // 'EP4_EXPORT_FILE_ARRAY_START'
    if ($notifier == 'EP4_EXPORT_FILE_ARRAY_START') {
      updateEP4ExportFileArrayStart($callingClass, $notifier, $paramsArray); // mc12345678 doesn't work on ZC 1.5.1 and below
    }

  // 'EP4_EXPORT_CASE_EXPORT_FILE_END'
    if ($notifier == 'EP4_EXPORT_CASE_EXPORT_FILE_END') {
      updateEP4ExportCaseExportFileEnd($callingClass, $notifier, $paramsArray);
    }

// EP4_EXPORT_WHILE_START
    if ($notifier == 'EP4_EXPORT_WHILE_START') {
      updateEP4ExportWhileStart($callingClass, $notifier, $paramsArray);
    }

  //$zco_notifier->notify('EP4_EXPORT_LOOP_FULL_OR_SBASTOCK');
    if ($notifier == 'EP4_EXPORT_LOOP_FULL_OR_SBASTOCK') {
      updateEP4ExportLoopFullOrSBAStock($callingClass, $notifier, $paramsArray);
    }

  //  $zco_notifier->notify('EP4_EXPORT_LOOP_FULL_OR_SBASTOCK_LOOP');
    if ($notifier == 'EP4_EXPORT_LOOP_FULL_OR_SBASTOCK_LOOP') {
      updateEP4ExportLoopFullOrSBAStockLoop($callingClass, $notifier, $paramsArray);
    }

//    $zco_notifier->notify('EP4_EXPORT_LOOP_FULL_OR_SBASTOCK_END');
    if ($notifier == 'EP4_EXPORT_LOOP_FULL_OR_SBASTOCK_END') {
      updateEP4ExportLoopFullOrSBAStockEnd($callingClass, $notifier, $paramsArray);
    }


//  $zco_notifier->notify('EP4_EXPORT_SPECIALS_AFTER');
    if ($notifier == 'EP4_EXPORT_SPECIALS_AFTER') {
      updateEP4ExportSpecialsAfter($callingClass, $notifier, $paramsArray);
    }

//  $zco_notifier->notify('EP4_EXPORT_FULL_OR_CAT_FULL_AFTER');
    if ($notifier == 'EP4_EXPORT_FULL_OR_CAT_FULL_AFTER') {
      updateEP4ExportFullOrCatFullAfter($callingClass, $notifier, $paramsArray);
    }
  } // EOF Update()
}
