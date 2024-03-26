<?php
/**
 * zcAjaxEasyPopulateV4
 *
 * @copyright Copyright 2023 mc12345678 of McNumbers Ware
 * @copyright Copyright 2003-2018 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Thu Sep 10 2023 +0000 New in v1.5.7 $
 */

$ep4_extra_funcs_path = DIR_WS_FUNCTIONS . 'extra_functions/easypopulate_4_functions.php';
$ep4_lang_path = DIR_WS_LANGUAGES . $_SESSION['language'] . '/' . 'easypopulate_4.php';
if (!is_file($ep4_lang_path)) {
    $ep4_lang_path = DIR_WS_LANGUAGES . 'english' . '/' . 'easypopulate_4.php';
}

if (!defined('DIR_FS_ADMIN')) {
    return false;
}

if (!isset($installedPlugins['EasyPopulateV4'])){
    // Software not installed as a plugin.
    if (!is_file(DIR_FS_ADMIN . $ep4_extra_funcs_path)) {
      // Software not installed in the expected location, presume that not installed at all.
        return false;
    }
    // Support versions of Zen Cart before 1.5.7 that also support Zen Cart implemented ajax.
    $ep4_prefix = DIR_FS_ADMIN;
} else {
    // Supports Zen Cart 1.5.7+ and/or with software installed via Zen Cart Plugin Manager.
    $ep4_prefix = $pluginManager->getPluginVersionDirectory('EasyPopulateV4', $installedPlugins) . 'admin/';
    if (!is_file($ep4_prefix . $ep4_lang_path)) {
        // Plugin was installed; however, files are missing, assume that have downgraded
        //   back to standard admin.
        $ep4_prefix = DIR_FS_ADMIN;
    }
}

require $ep4_prefix . $ep4_lang_path;

class zcAjaxEasyPopulateV4 extends base
{

    public function updateDrop()
    {
        global $db;

        $ep_category_filter = '0';
        if (isset($_POST['ep_category_filter'])) {
            $ep_category_filter = $db->prepare_input($_POST['ep_category_filter']);
        }

        $ep_status_filter = '1';
        if (isset($_POST['ep_status_filter'])) {
            $ep_status_filter = $db->prepare_input($_POST['ep_status_filter']);
        }

        if (!isset($_POST['ep_export_type']) || !in_array($_POST['ep_export_type'], array('4', '5'))) {
            $result = zen_draw_pull_down_menu('ep_category_filter', ep4_get_category_tree('0', '', '0', array(array('id' => '', 'text' => EASYPOPULATE_4_DD_FILTER_CATEGORIES), array('id' => '0', 'text' => TEXT_TOP)), false, true, false, true, false, $ep_status_filter), $ep_category_filter);

            return (array(
                'down_filter' => $result,
            ));
        }

        
        $result = zen_draw_pull_down_menu('ep_category_filter', ep4_get_category_tree('0', '', '0', array(array('id' => '', 'text' => EASYPOPULATE_4_DD_FILTER_CATEGORIES), array('id' => '0', 'text' => TEXT_TOP)), false, true, false, true, true, $ep_status_filter), $ep_category_filter);

        return (array(
            'down_filter' => $result,
        ));
    }
}

