<?php

/**
 * Autoloader array for golf notification functionality. Makes sure that golf is instantiated at the
 * right point of the Zen Cart initsystem.
 * 
 * @package     golf notifications
 * @author      Chad M. 
 * @copyright   Copyright 2008-2013 Chad M.
 * @copyright   Copyright 2003-2007 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://www.zen-cart.com/
 * @license     http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version     $Id: config.golf.php xxxx 2013-06-28 20:31:10Z mc12345678 $
 */

//if (!defined('IS_ADMIN_FLAG')) {
//	die('Illegal Access');
//} 


 $autoLoadConfig[0][] = array(
	'autoType' => 'class',
	'loadFile' => 'observers/class.ep4ceonuri.php',
	'classPath'=> DIR_WS_CLASSES
	);
 $autoLoadConfig[200][] = array(
	'autoType' => 'classInstantiate',
	'className' => 'ep4ceonuri',
	'objectName' => 'ep4ceonuri_observe'
	); 
?>
