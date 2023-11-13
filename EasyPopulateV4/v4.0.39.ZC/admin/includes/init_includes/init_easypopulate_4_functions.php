<?php
/**
 * @package Easypopulate V4
 * @copyright Copyright 2003-2019 Zen Cart Development Team
 * @author mc12345678
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * $Id: init_easypopulate_4_functions.php xxxx 2019-01-02 20:31:10Z $
 */


$ep4_file_load_array = array(
    'FILENAME_CONFIGURATION',
    'FILENAME_EASYPOPULATE_4',
    'FILENAME_EP4_CRON',
);
$ep4_eject = true;
foreach ($ep4_file_load_array as $ep4_file_load) {
    if ((defined($ep4_file_load) && $_SERVER['SCRIPT_NAME'] == DIR_WS_ADMIN . constant($ep4_file_load) . (!strstr(constant($ep4_file_load), '.php') ?  '.php' : ''))) {
        $ep4_eject = false;
        break;
    }
}
if ($ep4_eject) {
    return;
}
unset($ep4_eject);

require_once realpath(__DIR__ . "/../../") . "/" . DIR_WS_FUNCTIONS . 'extra_functions/reg_easypopulate_4.php';

if (function_exists('ep_4_curly_quotes')) {
    return;
}

require_once realpath(__DIR__ . "/../../") . "/" . DIR_WS_FUNCTIONS . 'extra_functions/easypopulate_4_functions.php';

