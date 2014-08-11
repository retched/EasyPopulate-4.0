<?php

/**
 * Ceon URI Mapping Zen Cart Categories Admin Functionality.
 *
 * This file contains a class which handles the functionality for category pages within the Zen Cart admin.
 *
 * @package     ceon_uri_mapping with EasyPopulate Version 4
 * @author      Conor Kerr <zen-cart.uri-mapping@ceon.net>
 * @modifier	 mc12345678
 * @copyright   Copyright 2008-2012 Ceon
 * @copyright   Copyright 2003-2007 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @copyright	 mc12345678
 * @link        http://ceon.net/software/business/zen-cart/uri-mapping
 * @license     http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version     $Id: class.CeonURIMappingAdminCategoryPages.php 1054 2014-08-04 15:45:15Z mc12345678 EP4.0.24CEON $
 */

if (!defined('IS_ADMIN_FLAG')) {
	die('Illegal Access');
}

/**
 * Load in the parent class if not already loaded
 */
require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.CeonURIMappingAdminCategories.php');


// {{{ CeonURIMappingAdminCategoryPages

/**
 * Handles the functionality for category pages within the Zen Cart admin.
 *
 * @package     ceon_uri_mapping with EasyPopulate Version 4
 * @author      Conor Kerr <zen-cart.uri-mapping@ceon.net>
 * @modifier	 mc12345678
 * @copyright   Copyright 2008-2012 Ceon
 * @copyright   Copyright 2003-2007 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @copyright	 Copyright 2013-2014 mc12345678
 * @link        http://ceon.net/software/business/zen-cart/uri-mapping
 * @link2		 http://mc12345678.weebly.com
 * @license     http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @merged	 This product has been modified to work with EasyPopulate Version 4
 * @merged	 Usage of this requires installation of the standard product.
 * @merged	 Version this was first merged with was EP4 V4.0.24CEON
 */
class EP4CeonURIMappingAdminCategoryPages extends CeonURIMappingAdminCategories
{
	// {{{ Class Constructor
	
	/**
	 * Creates a new instance of the CeonURIMappingAdminCategoryPages class.
	 * 
	 * @access  public
	 */
	function EP4CeonURIMappingAdminCategoryPages()
	{
		// Load the language definition file for the current language
		@include_once(DIR_WS_LANGUAGES . $_SESSION['language'] . '/' . 'ceon_uri_mapping_category_pages.php');
		
		if (!defined('CEON_URI_MAPPING_TEXT_CATEGORY_URI') && $_SESSION['language'] != 'english') {
			// Fall back to english language file
			@include_once(DIR_WS_LANGUAGES . 'english/' . 'ceon_uri_mapping_category_pages.php');
		}
		
		parent::CeonURIMappingAdminCategories();
	}
	
	// }}}
	
	
	// {{{ insertUpdateHandler()
	
	/**
	 * Handles the Ceon URI Mapping functionality when a category is being inserted or updated.
	 *
	 * @access  public
	 * @param   integer   $category_id   The ID of the category.
	 * @param   integer   $current_category_id   The ID of the category's parent category.
	 * @param	  array(string) $categories_name	The name of the category being added.
	 * @return  none
	 * Called by update_category in categories.php
	 */
	function insertUpdateHandler($category_id, $current_category_id, $prev_uri_mappings, $uri_mappings, $categories_name, $uri_mapping_autogen = NULL)
	{
		global $languages, $messageStack;
		
		$uri_mapping_autogen = (isset($uri_mapping_autogen) ? true : false);
      
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$prev_uri_mapping = trim($prev_uri_mappings[$languages[$i]['id']]);
			
			// Auto-generate the URI if requested
			if (true /*$uri_mapping_autogen*/) {
				$uri_mapping = $this->autogenCategoryURIMapping((int) $category_id, $current_category_id,
					$categories_name[$languages[$i]['id']], $languages[$i]['code'], $languages[$i]['id']);
				
				//Need to test autogen rules against database switches.  And reassign $uri_mapping as applicable.
				$uri_mapping_autogen = ((!zen_not_null($prev_uri_mapping) && EP4_AUTOCREATE_CAT_FROM_BLANK == '1') || EP4_AUTORECREATE_CAT_EXISTING == '1' || (EP4_AUTORECREATE_CAT_EXISTING == '2' && (EP4_AUTOCREATE_CAT_FROM_BLANK == '1' || (EP4_AUTOCREATE_CAT_FROM_BLANK == '0' && zen_not_null($prev_uri_mapping)))));

				if (EP4_AUTOCREATE_CAT_FROM_BLANK == '0' && (EP4_AUTORECREATE_CAT_EXISTING == '2') && !zen_not_null($prev_uri_mapping)) {
					//Cycle through languages, where previous is not blank, current = previous
					$uri_mapping = $prev_uri_mapping;
				}

				if (EP4_AUTOCREATE_CAT_FROM_BLANK == '1' && (EP4_AUTORECREATE_CAT_EXISTING == '0') && zen_not_null($prev_uri_mapping)) {
					//Cycle through languages, where previous is not blank, current = previous
					$uri_mapping = $prev_uri_mapping;
				}
				
				if ($uri_mapping == CEON_URI_MAPPING_GENERATION_ATTEMPT_FOR_CATEGORY_WITH_NO_NAME ||
						$uri_mapping == CEON_URI_MAPPING_GENERATION_ATTEMPT_FOR_CATEGORY_PATH_PART_WITH_NO_NAME) {
					// Can't generate the URI because of missing "uniqueness" data or invalid data
					if ($uri_mapping == CEON_URI_MAPPING_GENERATION_ATTEMPT_FOR_CATEGORY_WITH_NO_NAME) {
						$message = CEON_URI_MAPPING_TEXT_ERROR_AUTOGENERATION_CATEGORY_HAS_NO_NAME;
					} else {
						$message = CEON_URI_MAPPING_TEXT_ERROR_AUTOGENERATION_A_PARENT_CATEGORY_HAS_NO_NAME;
					}
					
					$failure_message = sprintf($message, ucwords($languages[$i]['name']));
					
					$messageStack->add_session($failure_message, 'error');
					
					continue;
				}
			} else {
				$uri_mapping = $uri_mappings[$languages[$i]['id']];
			}
			
			if (strlen($uri_mapping) > 1) {
				// Make sure URI mapping is relative to root of site and does not have a trailing slash or any
				// illegal characters
				$uri_mapping = $this->_cleanUpURIMapping($uri_mapping);
			}
			
			$insert_uri_mapping = false;
			$update_uri_mapping = false;
			
			if ($uri_mapping != '') {
				// Check if the URI mapping is being updated or does not yet exist
				if ($prev_uri_mapping == '') {
					$insert_uri_mapping = true;
				} else if ($prev_uri_mapping != $uri_mapping) {
					$update_uri_mapping = true;
				}
			}
			
			if ($insert_uri_mapping || $update_uri_mapping) {
				if ($update_uri_mapping) {
					// Consign previous mapping to the history, so old URI mapping isn't broken
					$this->makeURIMappingHistorical($prev_uri_mapping, $languages[$i]['id']);
				}
				
				// Add the new URI mapping
				$uri = $uri_mapping;
				
				$main_page = FILENAME_DEFAULT;
				
				$mapping_added = $this->addURIMapping($uri, $languages[$i]['id'], $main_page, null, $category_id);
				
				if ($mapping_added == CEON_URI_MAPPING_ADD_MAPPING_SUCCESS) {
					if ($insert_uri_mapping) {
						$success_message = sprintf(CEON_URI_MAPPING_TEXT_CATEGORY_MAPPING_ADDED,
							ucwords($languages[$i]['name']),
							'<a href="' . HTTP_SERVER . $uri . '" target="_blank">' . $uri . '</a>');
					} else {
						$success_message = sprintf(CEON_URI_MAPPING_TEXT_CATEGORY_MAPPING_UPDATED,
							ucwords($languages[$i]['name']),
							'<a href="' . HTTP_SERVER . $uri . '" target="_blank">' . $uri . '</a>');
					}
					
					$messageStack->add_session($success_message, 'success');
					
				} else {
					if ($mapping_added == CEON_URI_MAPPING_ADD_MAPPING_ERROR_MAPPING_EXISTS) {
						$failure_message = sprintf(CEON_URI_MAPPING_TEXT_ERROR_ADD_MAPPING_EXISTS,
							ucwords($languages[$i]['name']),
							'<a href="' . HTTP_SERVER . $uri . '" target="_blank">' . $uri . '</a>');
						
					} else if ($mapping_added == CEON_URI_MAPPING_ADD_MAPPING_ERROR_DATA_ERROR) {
						$failure_message = sprintf(CEON_URI_MAPPING_TEXT_ERROR_ADD_MAPPING_DATA,
							ucwords($languages[$i]['name']), $uri);
					} else {
						$failure_message = sprintf(CEON_URI_MAPPING_TEXT_ERROR_ADD_MAPPING_DB,
							ucwords($languages[$i]['name']), $uri);
					}
					
					$messageStack->add_session($failure_message, 'error');
				}
			} else if ($prev_uri_mapping != '' && $uri_mapping == '') {
				// No URI mapping, consign existing mapping to the history, so old URI mapping isn't broken
				$this->makeURIMappingHistorical($prev_uri_mapping, $languages[$i]['id']);
				
				$success_message = sprintf(CEON_URI_MAPPING_TEXT_CATEGORY_MAPPING_MADE_HISTORICAL,
					ucwords($languages[$i]['name']));
				
				$messageStack->add_session($success_message, 'caution');
			}
		}
	}
	
	// }}}
	
	
	// {{{ addURIMappingFieldsToAddCategoryFieldsArray()
	
	/**
	 * Adds the fields necessary for the Ceon URI Mapping options to the list of add category fields, accessing the
	 * list of add category fields directly, through a global variable.
	 *
	 * @access  public
	 * @return  none
	 * new_category calls this in categories.php
	 */
	function addURIMappingFieldsToAddCategoryFieldsArray()
	{
		global $contents;
		
		// New category doesn't have any previous URI mappings
		$prev_uri_mappings = array();
		
		$uri_mapping_input_fields = $this->buildCategoryURIMappingFields($prev_uri_mappings);
		
		$contents[] = array('text' => $uri_mapping_input_fields);
	}
	
	// }}}
	
	
	// {{{ addURIMappingFieldsToEditCategoryFieldsArray()
	
	/**
	 * Adds the fields necessary for the Ceon URI Mapping options to the list of edit category fields, accessing
	 * the list of edit category fields directly, through a global variable.
	 *
	 * @access  public
	 * @param   integer   $category_id   The ID of the category.
	 * @return  none
	 * 
	 * edit_category from categories.php calls this.
	 */
	function addURIMappingFieldsToEditCategoryFieldsArray($category_id)
	{
		global $contents;
		
		// Get any current category mappings from the database, up to one for each language
		$prev_uri_mappings = array();
		
		$columns_to_retrieve = array(
			'language_id',
			'uri'
			);
		
		$selections = array(
			'main_page' => FILENAME_DEFAULT,
			'associated_db_id' => $category_id,
			'current_uri' => 1
			);
		
		$prev_uri_mappings_result = $this->getURIMappingsResultset($columns_to_retrieve, $selections);
		
		while (!$prev_uri_mappings_result->EOF) {
			$prev_uri_mappings[$prev_uri_mappings_result->fields['language_id']] =
				$prev_uri_mappings_result->fields['uri'];
			
			$prev_uri_mappings_result->MoveNext();
		}
		
		$uri_mapping_input_fields = $this->buildCategoryURIMappingFields($prev_uri_mappings); //Should be able to copy this, or perhaps it needs to be returned? At this point $uri_mapping_input_fields will equal $prev_uri_mappings, either all blank, or will have the previous mappings.
		
		return $uri_mapping_input_fields;
		//$contents[] = array('text' => $uri_mapping_input_fields);
	}
	
	// }}}
	
	
	// {{{ buildCategoryURIMappingFields()
	
	/**
	 * Builds the input fields for adding/editing URI mappings for categories.
	 *
	 * @access  public
	 * @param   array     $prev_uri_mappings   An array of the current values for the URI mappings, if any.
	 * @return  string    The html source for the input fields. (Need to revise to be the arrays of strings for the data.
	 * 
	 * this function in the categories.php file is called by this file only. 
	 */
	function buildCategoryURIMappingFields($prev_uri_mappings /* The following fields need to be added to the array being returned:  $uri_mappings, $autogen_selected, $num_prev_uri_mappings*/ )
	{
		global $languages;
		
		$num_prev_uri_mappings = sizeof($prev_uri_mappings);
		
		$num_languages = sizeof($languages);
		
		
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			
			if (!isset($prev_uri_mappings[$languages[$i]['id']])) {
				$prev_uri_mappings[$languages[$i]['id']] = '';
			}
			
			$uri_mappings[$languages[$i]['id']] = $prev_uri_mappings[$languages[$i]['id']];
		}
		
		// Not sure that this section really does anything necessary..
		/*if ($this->_autogenEnabled()) {
			if ($num_languages == 1) {
				$autogen_message = CEON_URI_MAPPING_TEXT_CATEGORY_URI_AUTOGEN;
			} else {
				$autogen_message = CEON_URI_MAPPING_TEXT_CATEGORY_URIS_AUTOGEN;
			}
			
			if ($num_prev_uri_mappings == 0) {
				$autogen_selected = true;
			} else {
				$autogen_selected = false;
				
				*/ /*if ($num_prev_uri_mappings == 1) {
					$autogen_message[] .= '<br />' . CEON_URI_MAPPING_TEXT_URI_AUTOGEN_ONE_EXISTING_MAPPING;
				} else if ($num_prev_uri_mappings == $num_languages) {
					$autogen_message[] .= '<br />' . CEON_URI_MAPPING_TEXT_URI_AUTOGEN_ALL_EXISTING_MAPPINGS;
				} else {
					$autogen_message[] .= '<br />' . CEON_URI_MAPPING_TEXT_URI_AUTOGEN_SOME_EXISTING_MAPPINGS;
				}*/ // Not interested in knowing if one, or more mappings previously existed at least not via text.  Will determine this based on other actions/information.
//			}
			
			//$uri_mapping_input_fields[] .= zen_draw_checkbox_field('uri-mapping-autogen', '1', $autogen_selected) .
			//	' ' . $autogen_message;
//		} else {
			//$uri_mapping_input_fields[] .= CEON_URI_MAPPING_TEXT_URI_AUTOGEN_DISABLED;
//		}
		
//		$uri_mapping_input_fields .= "</p>";
		
//		$uri_mapping_input_fields .= "\n\t\t</td>\n\t</tr>\n</table>\n";
		
//		$uri_mapping_input_fields .= zen_draw_separator('pixel_black.gif', '100%', '2');
		
		return $uri_mapping_input_fields;
	}
	
	/// }}}
}

// }}}
