<?php

/**
 * query_factory class extended for EP4.
 *
 * @author      mc12345678
 * @copyright   Copyright 2010-2023 
 * @copyright   Copyright 2003-2023 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        
 * @license     http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version     $Id: class.query_factory_ep4.php 1054 2023-01-22 15:45:15Z mc12345678 $
 */

/**
 * Load in the query_factory class so it can be extended
 */
require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'db/' .DB_TYPE . '/query_factory.php');


// {{{ queryFactoryEP4

/**
 * Specifies the essential version properties for the module and implements the installed version's version number
 * (if any). Allows the extending class to access the version information for the module easily.
 *
 * @abstract
 * @author      mc12345678 <mc12345678@mc12345678.com>
 * @copyright   Copyright 2012-2023 McNumbers Ware
 * @copyright   Copyright 2003-2023 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @license     http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */
class queryFactoryEP4 extends queryFactory
{
  protected $db;
  
  // {{{ Class Constructor
  
  /**
   * Creates a new instance of the class.
   * 
   * @param   object $db_var Whether or not the autogeneration configuration should be loaded when instantiating the
   *                    class.
   * @access  public
   */
  public function __construct()
  {
    global $db;
    $this->db = $db;
  }
  // {{{ getLink()
  
  /**
   * Looks up the database link.
   *
   * @access  public
   * @return  mysqli object which may include the value false
   */
  public function getLink()
  {
      return $this->db->link;
  }
  
  
  // }}}
  
  
}

// }}}
