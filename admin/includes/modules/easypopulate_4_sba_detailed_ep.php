﻿<?php

/*
 * This is the file processed if the import file is a attrib-detailed-ep file.
 * includes\modules\easypopulate_4_sba_detailed_ep.php, v4.0.35.ZC.2 10-03-2016 mc12345678 $
 */

      while ($items = fgetcsv($handle, 0, $csv_delimiter, $csv_enclosure)) { // read 1 line of data

        @set_time_limit($ep_execution);

        $sql = 'SELECT * FROM ' . TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK . '
          WHERE (
          stock_id = :stock_id: ' . /* AND
                  products_id = '.$items[$filelayout['v_products_id']].' AND
                  options_id = '.$items[$filelayout['v_options_id']].' AND
                  options_values_id = '.$items[$filelayout['v_options_values_id']].'
                 */') LIMIT 1';
        $sql = $db->bindVars($sql, ':stock_id:', $items[$filelayout['v_stock_id']], 'integer');
        $sql = $db->bindVars($sql, ':products_attributes_id:', $items[$filelayout['v_products_attributes_id']], 'integer');

        $result = ep_4_query($sql);
        unset($sql);
        if (!($row = $ep_4_fetch_array($result))) { // error Attribute entry not found - needs work!
          $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_SBA_DETAIL_NOT_FOUND, $items[$filelayout[$chosen_key]], $chosen_key);
          unset($result);
          $ep_error_count++;
          ep4_flush();
          continue;
        }
        
        // UPDATE
        $sql = "UPDATE " . TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK . " SET
          products_id                 = :products_id:,
          stock_attributes                  = :stock_attributes:,
          quantity              = :quantity:,
          sort                = :sort:" . ( $ep_4_SBAEnabled == '2' ? ",
          customid            = :customid: " : " ") .
                "
          WHERE (
          stock_id = :stock_id: )";

        $sql = $db->bindVars($sql, ':products_id:', $items[$filelayout['v_products_id']], 'integer');
        $sql = $db->bindVars($sql, ':stock_attributes:', $items[$filelayout['v_stock_attributes']], 'string');
        $sql = $db->bindVars($sql, ':quantity:', $items[$filelayout['v_quantity']], 'float');
        $sql = $db->bindVars($sql, ':sort:', $items[$filelayout['v_sort']], 'integer');
        $sql = $db->bindVars($sql, ':customid:', (zen_not_null($items[$filelayout['v_customid']]) ? $items[$filelayout['v_customid']] : 'NULL'), (zen_not_null($items[$filelayout['v_customid']]) ? 'string' : 'passthru'));
        $sql = $db->bindVars($sql, ':stock_id:', $items[$filelayout['v_stock_id']], 'integer');

        $result = ep_4_query($sql);
        unset($sql);
        if ($result) {
          zen_record_admin_activity('Updated products with attributes stock ' . (int) $items[$filelayout['v_stock_id']] . ' via EP4.', 'info');
        }
        unset($result);

        if ($items[$filelayout['v_products_attributes_filename']] <> '') { // download file name
          $sql = 'SELECT * FROM ' . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . '
            WHERE (products_attributes_id = :products_attributes_id:) LIMIT 1';
          $sql = $db->bindVars($sql, ':products_attributes_id:', $items[$filelayout['v_products_attributes_id']], 'integer');

          $result = ep_4_query($sql);
          unset($sql);
          $sql_text = "INSERT INTO ";
          $admin_act_text = "inserted";
          if ($row = $ep_4_fetch_array($result)) { // update
            $sql_text = "UPDATE ";
            $admin_act_text = "updated";
          }
          unset($result);

          $sql = $sql_text . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " SET
            products_attributes_filename = :products_attributes_filename:,
            products_attributes_maxdays  = :products_attributes_maxdays:,
            products_attributes_maxcount = :products_attributes_maxcount:" . 
            ($sql_text === "UPDATE " ? 
            " WHERE (
            products_attributes_id = :products_attributes_id:) " :
            " products_attributes_id      = :products_attributes_id: ");
          unset($sql_text);
          $sql = $db->bindVars($sql, ':products_attributes_filename:', $items[$filelayout['v_products_attributes_filename']], 'string');
          $sql = $db->bindVars($sql, ':products_attributes_maxdays:', $items[$filelayout['v_products_attributes_maxdays']], 'integer');
          $sql = $db->bindVars($sql, ':products_attributes_maxcount:', $items[$filelayout['v_products_attributes_maxcount']], 'integer');
          $sql = $db->bindVars($sql, ':products_attributes_id:', $items[$filelayout['v_products_attributes_id']], 'integer');

          $result = ep_4_query($sql);
          if ($result) {
            zen_record_admin_activity('Downloads-manager details " . $admin_act_text . " by EP4 for ' . $items[$filelayout['v_products_attributes_id']], 'info');
          }
          unset($admin_act_text, $result);
        }
        $ep_update_count++;
        ep4_flush();
      } // while
