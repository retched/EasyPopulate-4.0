<?php
/**
 * Autoloader array for Easy Populate V4 ADMIN functionality. Makes sure that EP4 functions are instantiated at the
 * right point of the Zen Cart initsystem.
 *
 * @package     Easy Populate V4
 * @author      McNumbers Ware <mc12345678@mc12345678.com>
 * @copyright   Copyright 2021 mc12345678
 * @copyright   Copyright 2003-2007 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        https://mc12345678.com
 * @license     http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version     2021 - 4.0.37.14
 */

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

//added to support processing when working with configurations as navigation occurs.

$autoLoadConfig[199][] = array(
    'autoType'=>'init_script',
    'loadFile'=>'init_easypopulate_4_functions.php'
  );
