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
     *          f          class.
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


    /**
     *
     *
     *
     *
     */
    public function ep4_metaColumns($tablename)
    {
        $obj = array();
        $tablename = (string)$tablename;
        $sql = "SHOW COLUMNS from :tableName:";
        $sql = $this->db->bindVars($sql, ':tableName:', $tablename, 'noquotestring');
        $res = $this->db->Execute($sql);
        while (!$res->EOF) {
//            if ($res->fields['Field'] == 'products_image') {
//                trigger_error((is_null($res->fields['Default']) ? (array_key_exists('Default', $res->fields) ? 'Yup set NULL' : 'Missing') : 'Value'), E_USER_WARNING);
//            }
            $obj [/*strtoupper(*/$res->fields['Field']/*)*/] = new ep4_queryFactoryMeta($res->fields);
            $res->MoveNext();
        }
        return $obj;
    }
}

// }}}


  /**
   *
   *
   *
   *
   */
class ep4_queryFactoryMeta extends base
{
    public $ep4_type;
    public $ep4_has_default;
    public $ep4_default_value;
    public $ep4_nullable;
    public $ep4_extra;
//    public $max_length;

    public function __construct($field)
    {
        $type = $field['Type'];
        $rgx = preg_match('/^[a-z]*/', $type, $matches);
        $this->ep4_type = $matches[0];

        $this->ep4_has_default = array_key_exists('Default', $field) && (!is_null($field['Default']) || (array_key_exists('Null', $field) && $field['Null'] == 'YES'));
        $this->ep4_nullable = array_key_exists('Null', $field) && $field['Null'] == 'YES' || array_key_exists('Extra', $field) && $field['Extra'] == 'auto_increment';

        if ($this->ep4_has_default) {
//          $this->ep4_default_value = (is_null($field['Default']) ? (array_key_exists('Default', $field) ? null : '') : $field['Default']);
            $this->ep4_default_value = $field['Default'];
        } elseif (array_key_exists('Extra', $field) && $field['Extra'] == 'auto_increment') {
            $this->ep4_has_default = true;
            $this->ep4_nullable = true;
            $this->ep4_default_value = null;
        }
        $this->ep4_extra = isset($field['Extra']) ? $field['Extra'] : '';
//trigger_error(print_r($field, true), E_USER_WARNING);

//        $this->max_length = preg_replace('/[a-z\(\)]/', '', $type);
        if (false && empty($this->max_length)) {
            switch (strtoupper($type)) {
                case 'DATE':
//                    $this->max_length = 10;
                    break;
                case 'DATETIME':
                case 'TIMESTAMP':
//                    $this->max_length = 19; // ignores fractional which would be 26
                    break;
                case 'TINYTEXT':
//                    $this->max_length = 255;
                    break;
                case 'INT':
//                    $this->max_length = 11;
                    break;
                case 'TINYINT':
//                    $this->max_length = 4;
                    break;
                case 'SMALLINT':
//                    $this->max_length = 4;
                    break;
                default:
                    // This is antibugging code to prevent a fatal error
                    // You should not be here unless you have changed the db
//                    $this->max_length = 8;
                    $this->notify('NOTIFY_QUERY_FACTORY_EP4_META_DEFAULT', ['field' => $field, 'type' => $type], $this->max_length);
                    break;
            }
        }
    }

    // Be sure to check that the field has a default before retrieving
    //   Otherwise, the "default" returned will be whatever this function returns
    //   as a "default" (void?).
    public function getDefaultVal()
    {
      if (!$this->ep4_has_default) {
          // Returns a value that is "abnormal" and can be detected
          //   as an issue. Void would be appropriate/possible post php 7.1;
          //   however, am designing for 5.x+
          return array(
              'type' => $this->ep4_type,
          );
      }

      // Has a default, choose specific early return(s).
      if (is_null($this->ep4_default_value)) {
          return null;
      }

      $to_php_type = $this->get_php_type_from_db($this->ep4_type);

      if (empty($this->ep4_type)) {
          return $this->ep4_default_value;
      }

      settype($this->ep4_default_value, $to_php_type);

      return $this->ep4_default_value;

/*        if ($this->ep4_type) {
           switch (strtoupper($this->ep4_type)) {
              case 'DATE':
//                  $this->max_length = 10;
              case 'DATETIME':
              case 'TIMESTAMP':
              case 'TIME':
//                  $this->max_length = 19; // ignores fractional which would be 26
                  settype($this->ep4_default_value, 'string');
                  break;
              case 'YEAR':
                  settype($this->ep4_default_value, 'int');
                  break;
              case 'FLOAT':
              case 'DOUBLE':
              case 'DOUBLE PRECISION':
              case 'REAL':
              case 'DECIMAL':
              case 'NUMERIC':

                  settype($this->ep4_default_value, 'float');
                  break;
              case 'TINYTEXT':
              case 'STRING':
              case 'CHAR':
              case 'VARCHAR':
case 'binary':
case 'varbinary':
              case 'BLOB':
              case 'TINYBLOB':
              case 'MEDIUMBLOB':
              case 'LONGBLOB':
              case 'TEXT':
              case 'TINYTEXT':
              case 'MEDIUMTEXT':
              case 'LONGTEXT':
case 'set':
case 'json':

case 'enum':
                  settype($this->ep4_default_value, 'string');
                  break;
case 'BOOL':
case 'BOOLEAN':
                  settype($this->ep4_default_value, 'bool');
                  break;
case 'bit':
settype($this->ep4_default_value, 'bit');
                  break;
              case 'INT':
              case 'INTEGER':
              case 'TINYINT':
              case 'SMALLINT':
              case 'MEDIUMINT':
              case 'BIGINT':
              case 'SERIAL':
                  settype($this->ep4_default_value, 'int');
                  break;
              default:
                  // This is antibugging code to prevent a fatal error
                  // You should not be here unless you have changed the db
//                  $this->max_length = 8;
                  $this->notify('NOTIFY_QUERY_FACTORY_EP4_META_DEFAULT_VAL', ['field' => $field, 'type' => $type], $this->ep4_default_value);
                  break;
           }
        }

      return $this->ep4_default_value;*/
    }

    public function get_php_type_from_db($db_type = null){

        $return_type = 'string';

        if (is_null($this->ep4_type)) {
            return $return_type;
        }

        if (is_null($db_type)) {
            $db_type = $this->ep4_type;
        }

        switch (strtoupper($db_type)) {
            case 'DATE':
//                  $this->max_length = 10;
            case 'DATETIME':
            case 'TIMESTAMP':
            case 'TIME':
//                $this->max_length = 19; // ignores fractional which would be 26
                $return_type = 'string';
//                settype($this->ep4_default_value, 'string');
                break;
            case 'YEAR':
                $return_type = 'int';
//                settype($this->ep4_default_value, 'int');
                break;
            case 'FLOAT':
            case 'DOUBLE':
            case 'DOUBLE PRECISION':
            case 'REAL':
            case 'DECIMAL':
            case 'NUMERIC':

                $return_type = 'float';
//                settype($this->ep4_default_value, 'float');
                break;
            case 'TINYTEXT':
            case 'STRING':
            case 'CHAR':
            case 'VARCHAR':
case 'binary':
case 'varbinary':
            case 'BLOB':
            case 'TINYBLOB':
            case 'MEDIUMBLOB':
            case 'LONGBLOB':
            case 'TEXT':
            case 'TINYTEXT':
            case 'MEDIUMTEXT':
            case 'LONGTEXT':
case 'set':
case 'json':

case 'enum':
                $return_type = 'string';
//                settype($this->ep4_default_value, 'string');
                break;
case 'BOOL':
case 'BOOLEAN':
                $return_type = 'bool';
//                settype($this->ep4_default_value, 'bool');
                break;
case 'bit':
    settype($this->ep4_default_value, 'bit');
                break;
            case 'INT':
            case 'INTEGER':
            case 'TINYINT':
            case 'SMALLINT':
            case 'MEDIUMINT':
            case 'BIGINT':
            case 'SERIAL':
                $return_type = 'int';
//                settype($this->ep4_default_value, 'int');
                break;
            default:
                // This is antibugging code to prevent a fatal error
                // You should not be here unless you have changed the db
//                $this->max_length = 8;
                $this->notify('NOTIFY_QUERY_FACTORY_EP4_META_DEFAULT_VAL', ['field' => $field, 'type' => $type], $return_type);
                $return_type = 'string';

                break;
        }
        return $return_type;
    }


    // Be sure to check that the field has a default before retrieving
    //   Otherwise, the "default" returned will be whatever this function returns
    //   as a "default" (void?).
    public function getData()
    {
        $data = array();

        if ($this->ep4_has_default)

        if (!$this->ep4_has_default) {
            // Returns a value that is "abnormal" and can be detected
            //   as an issue. Void would be appropriate/possible post php 7.1;
            //   however, am designing for 5.x+
            return array(
                'type' => $this->ep4_type,
            );
        }

        if ($this->ep4_type) {
            switch (strtoupper($this->ep4_type)) {
                case 'DATE':
//                    $this->max_length = 10;
                    break;
                case 'DATETIME':
                case 'TIMESTAMP':
//                    $this->max_length = 19; // ignores fractional which would be 26
                    break;
                case 'TINYTEXT':
//                    $this->max_length = 255;
                    break;
                case 'INT':
//                    $this->max_length = 11;
//                    break;
                case 'TINYINT':
//                    $this->max_length = 4;
//                    break;
                case 'SMALLINT':
//                    $this->max_length = 4;
                    settype($this->ep4_default_value, $this->ep4_type);
                    break;
                default:
                    // This is antibugging code to prevent a fatal error
                    // You should not be here unless you have changed the db
//                    $this->max_length = 8;
                    $this->notify('NOTIFY_QUERY_FACTORY_EP4_META_DEFAULT', ['field' => $field, 'type' => $type], $this->max_length);
                    break;
            }
        }

        return $this->ep4_default_value;
    }
}

