<?php
/**
 * @package Easypopulate V4
 * @copyright Copyright 2003-2019 Zen Cart Development Team
 * @author mc12345678
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * $Id: init_easypopulate_4_lang_defines.php xxxx 2023-06-24 20:31:10Z $
 */


// Load language file(s) for main screen menu(s).
if(file_exists(realpath(__DIR__ . '/../../') . '/' . DIR_WS_LANGUAGES . $_SESSION['language'] . '/extra_definitions/easypopulate_4.php'))
{
  require_once realpath(__DIR__ . '/../../') . '/' . DIR_WS_LANGUAGES . $_SESSION['language'] . '/extra_definitions/easypopulate_4.php';
} else {
  require_once realpath(__DIR__ . '/../../') . '/' . DIR_WS_LANGUAGES . 'english' . '/extra_definitions/easypopulate_4.php';
}
