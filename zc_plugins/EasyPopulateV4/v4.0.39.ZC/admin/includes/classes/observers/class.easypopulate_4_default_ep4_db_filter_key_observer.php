<?php
/* This is an observer class to take action when the ep4_db_filter_key is processed/determined.
It is expected to set the variables needed within EP4 to support an alternate primary key.
*/

class ep4_db_filter_key extends base
{
    public function __construct()
    {
        $attachThis = array();
        $attachThis[] = 'EP4_MODULES_FILELAYOUT_CHOSEN_KEY';
        $attachThis[] = 'EP4_IMPORT_DEFAULT_EP4_DB_FILTER_KEY_DATA';
        $attachThis[] = 'EP4_FUNCTION_REMOVE_PRODUCT';
        $this->attach($this, $attachThis);
    }

    public function updateEp4ImportDefaultEp4DbFilterKeyData(&$callingClass, $notifier, $ep4_db_filter_key, &$chosen_key, &$chosen_key_sql, &$chosen_key_sql_limit)
    {
        if ($ep4_db_filter_key == 'products_upc') {
            $chosen_key = 'v_products_upc';
            $chosen_key_sql = "
                p.products_upc = :products_upc:";
            $chosen_key_sql_limit = " WHERE (products_upc = :products_upc:) LIMIT 1";
        }
    }

    public function updateEp4ModulesFilelayoutChosenKey(&$callingClass, $notifier, $emptyArray, &$chosen_key)
    {
        if (EP4_DB_FILTER_KEY == 'products_upc') {
            $chosen_key = 'v_products_upc';
        }
    }

    public function updateEp4FunctionRemoveProduct(&$callingClass, $notifier, $key_value, &$sql_add)
    {
        if (EP4_DB_FILTER_KEY == 'products_upc') {
            global $chosen_key, $chosen_key_sql, $zc_support_ignore_null;
            
            if (in_array($chosen_key, array('v_products_id', 'v_products_model'))) {
                return;
            }
            $sql_add = $chosen_key_sql;

            $chosen_key_sub = $chosen_key;
            if (strpos($chosen_key_sub, 'v_') === 0) {
                $chosen_key_sub = substr($chosen_key_sub, 2);
            }
            $sql_add = $db->bindVars($sql_add, ':' . $chosen_key_sub . ':', $key_value, $zc_support_ignore_null);
        }
    }
}
