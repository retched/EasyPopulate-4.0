<?php

use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;
use Zencart\FileSystem\FileSystem;

class ScriptedInstaller extends ScriptedInstallBase
{

    protected $file_name = 'zcAjaxEasyPopulateV4.php';

    protected function executeInstall()
    {
        global $db;
//        zen_deregister_admin_pages(['toolsUpdgradeTemplate']);
//        $next_sort = $db->Execute('SELECT MAX(sort_order) + 1 as max_sort FROM ' . TABLE_ADMIN_PAGES . " WHERE menu_key='tools'");
//        zen_register_admin_page(
//            'toolsUpdgradeTemplate', 'BOX_TOOLS_UPGRADE_TEMPLATE', 'FILENAME_UPGRADE_TEMPLATE', '', 'tools', 'Y', $next_sort->fields['max_sort']);

        if (class_exists('FileSystem') && method_exists('FileSystem', 'getInstance')) { // Existed in 1.5.7 then removed in 1.5.8
            $files = FileSystem::getInstance();
        } else {
            $files = new FileSystem;
        }

        $orig_ws_path = DIR_WS_CLASSES . 'ajax/';
        $dir['rel'] = realpath(dirname(__FILE__) . '/../');
        // Ensure ends with a directory divider to simplify further handling
        if ($dir['rel'] !== '/') {
            $dir['rel'] .= '/';
        }
/*
Is there catalog side code even needed?
Ajax.php requires ajax file(s) to be in the actual catalog fileset.
*/
        $dir['plugin'] = $dir['rel'] . 'catalog/' . $orig_ws_path;
        
        // Prepare for install.
        global $curver_detail;
        require $dir['rel'] . 'admin/' . DIR_WS_MODULES . 'easypopulate_4_version.php';

        require $dir['rel'] . 'admin/' . DIR_WS_FUNCTIONS . 'extra_functions/reg_easypopulate_4.php';
        require $dir['rel'] . 'admin/' . DIR_WS_FUNCTIONS . 'extra_functions/easypopulate_4_functions.php';

        require_once $dir['rel'] . 'admin/' . DIR_WS_CLASSES . 'class.query_factory_ep4.php';

        global $ep4;

        if (class_exists('queryFactoryEP4')) {
            $ep4['db'] = new queryFactoryEP4($db);
            $ep4['link'] = $ep4['db']->getLink();
        } else {
            $ep4['db'] = $db;
            $ep4['link'] = $ep4['db']->getLink();
        }
        install_easypopulate_4();


        // Check if the file exists in the catalog side.
        //   If it does not exist in the catalog, then check to see if it is in the plugin directory.
        //     If it is in the plugin directory, then copy it to the catalog.
        if ($files->fileExistsInDirectory(DIR_FS_CATALOG . $orig_ws_path, $this->file_name)) {
            return;
        }
        
        if (!is_dir(DIR_FS_CATALOG . 'zc_plugins/' . $_GET['colKey']) || !is_dir(DIR_FS_CATALOG . 'zc_plugins/' . $_GET['colKey'] . '/' . $_POST['version']) || ((DIR_FS_CATALOG . 'zc_plugins/' . $_GET['colKey'] . '/' . $_POST['version'] . '/') !== $dir['rel'])) {
            trigger_error('invalid path', E_USER_WARNING);
            return;
        }
        
        if (!$files->fileExistsInDirectory($dir['plugin'], $this->file_name)) {
            return;
        }
        // copy file to the catalog side.
        // Dest: DIR_FS_CATALOG . $orig_ws_path
        // Src:  $dir['rel'] . 'catalog/' . $orig_ws_path
        // file: $file_name;
        if (is_file($dir['plugin'] . $this->file_name)) {
            $result = @copy($dir['plugin'] . $this->file_name, DIR_FS_CATALOG . $orig_ws_path . $this->file_name);
        }
        if (empty($result) || !is_file($dir['plugin'] . $this->file_name)) {
            // Need Message that did not succeed.
            trigger_error('Unable to establish ajax file.', E_USER_WARNING);
        } else {
            // Notify that successfully placed.
//            trigger_error('Ajax file successfully in place.', E_USER_WARNING);
        }
        unset($result);
/*
        $this->executeInstallerSql($sql);*/
    }

    protected function executeUninstall()
    {
        global $db, $ep4;

        $orig_ws_path = DIR_WS_CLASSES . 'ajax/';
        $dir['rel'] = realpath(dirname(__FILE__) . '/../');

        // Ensure ends with a directory divider to simplify further handling
        if ($dir['rel'] !== '/') {
            $dir['rel'] .= '/';
        }
        $dir['plugin'] = $dir['rel'] . 'catalog/' . $orig_ws_path;

        if (!class_exists('queryFactoryEP4')) {
            require_once $dir['rel'] . 'admin/' . DIR_WS_CLASSES . 'class.query_factory_ep4.php';
        }

        $ep4['db'] = new queryFactoryEP4($db);
        $ep4['link'] = $ep4['db']->getLink();

        // If require is used, then log file created that duplicate definition.
        // If remove line, then log that missing the file with the function in it.
        require_once $dir['rel'] . 'admin/' . DIR_WS_FUNCTIONS . 'extra_functions/easypopulate_4_functions.php';
// $ep4 needed to support the function file operations.
        remove_easypopulate_4();
        zen_deregister_admin_pages(['easypopulate_4']);

  // NEED TO REMOVE THE AJAX FILE
// DIR_FS_CATALOG . $orig_ws_path . $this->file_name // Base catalog
// $dir['plugin'] . $this->file_name // Plugin directory.
        unlink(DIR_FS_CATALOG . $orig_ws_path . $this->file_name);
    }
}
