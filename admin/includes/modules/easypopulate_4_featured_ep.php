<?php

/*
 * This is the file processed if the import file is a featured-ep file.
 * $Id: includes\modules\easypopulate_4_featured_ep.php, v4.0.35.ZC.2 10-03-2016 mc12345678 $
 */

      // check products table to see if product_model exists
      while ($items = fgetcsv($handle, 0, $csv_delimiter, $csv_enclosure)) { // read 1 line of data

        @set_time_limit($ep_execution);

        $sql = "SELECT * FROM " . TABLE_PRODUCTS;

        $sql .= $chosen_key_sql_limit;

        ${$chosen_key} = NULL;

        $sql = $db->bindVars($sql, ':products_model:', $items[$filelayout['v_products_model']], $zc_support_ignore_null);
        $sql = $db->bindVars($sql, ':products_id:', $items[$filelayout['v_products_id']], $zc_support_ignore_null);
        $sql = ep_4_chosen_key_sub($sql, $items[$filelayout[$chosen_key]]);
        $result = ep_4_query($sql);
        if ($row = $ep_4_fetch_array($result)) {
          $v_products_id = $row['products_id'];
          // Add or Update the table Featured
          $sql2 = "SELECT * FROM " . TABLE_FEATURED . " WHERE ( products_id = :products_id: ) LIMIT 1";
          $sql2 = $db->bindVars($sql2, ':products_id:', $v_products_id, 'integer');
          $result2 = ep_4_query($sql2);
          if ($row2 = $ep_4_fetch_array($result2)) { // update featured product
            $v_featured_id = $row2['featured_id'];
            $v_today = strtotime(date("Y-m-d"));
            if (isset($filelayout['v_expires_date']) && $items[$filelayout['v_expires_date']] > '0001-01-01') {
              $v_expires_date = $items[$filelayout['v_expires_date']];
            } else {
              $v_expires_date = '0001-01-01';
            }
            if (isset($filelayout['v_featured_date_available']) && $items[$filelayout['v_featured_date_available']] > '0001-01-01') {
              $v_featured_date_available = $items[$filelayout['v_featured_date_available']];
            } else {
              $v_featured_date_available = '0001-01-01';
            }
            if (($v_today >= strtotime($v_featured_date_available)) && ($v_today < strtotime($v_expires_date)) || ($v_today >= strtotime($v_featured_date_available) && $v_featured_date_available != '0001-01-01' && $v_expires_date == '0001-01-01') || ($v_featured_date_available == '0001-01-01' && $v_expires_date == '0001-01-01' && (defined('EP4_ACTIVATE_BLANK_FEATURED') ? EP4_ACTIVATE_BLANK_FEATURED : true))) {
              $v_status = 1;
            } else {
              $v_status = 0;
            }
            $v_date_status_change = date("Y-m-d");
            $sql = "UPDATE " . TABLE_FEATURED . " SET
              featured_last_modified  = CURRENT_TIMESTAMP,
              expires_date            = :expires_date:,
              date_status_change      = :date_status_change:,
              status                  = :status:,
              featured_date_available = :featured_date_available:
              WHERE (
              featured_id = :featured_id:)";
            $sql = $db->bindVars($sql, ':expires_date:', $v_expires_date, 'string');
            $sql = $db->bindVars($sql, ':date_status_change:', $v_date_status_change, 'string');
            $sql = $db->bindVars($sql, ':status:', $v_status , 'integer');
            $sql = $db->bindVars($sql, ':featured_date_available:', $v_featured_date_available, 'string');
            $sql = $db->bindVars($sql, ':featured_id:', $v_featured_id, 'string');
            $result = ep_4_query($sql);
            if ($result) {
              zen_record_admin_activity('Updated featured product with featured_id ' . (int) $v_featured_id . ' via EP4.', 'info');
              $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_FEATURED_UPDATE, $items[$filelayout[$chosen_key]], substr($chosen_key, 2), $items[$filelayout['v_products_id']], (int) $v_featured_id);
              $ep_update_count++;
            } else {
              $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_FEATURED_UPDATE_SKIPPED, $items[$filelayout[$chosen_key]], substr($chosen_key, 2), (int)$v_featured_id);
              $ep_error_count++;
            }
          } else {
            // add featured product
            $sql_max = "SELECT MAX(featured_id) max FROM " . TABLE_FEATURED;
            $result_max = ep_4_query($sql_max);
            $row_max = $ep_4_fetch_array($result_max);
            $max_featured_id = $row_max['max'] + 1;
            // if database is empty, start at 1
            if (!is_numeric($max_featured_id)) {
              $max_featured_id = 1;
            }
            $v_today = strtotime(date("Y-m-d"));
            if (isset($filelayout['v_expires_date']) && $items[$filelayout['v_expires_date']] > '0001-01-01') {
            $v_expires_date = $items[$filelayout['v_expires_date']];
            } else {
              $v_expires_date = '0001-01-01';
            }
            if (isset($filelayout['v_featured_date_available']) && $items[$filelayout['v_featured_date_available']] > '0001-01-01') {
            $v_featured_date_available = $items[$filelayout['v_featured_date_available']];
            } else {
              $v_featured_date_available = '0001-01-01';
            }
            if (($v_today >= strtotime($v_featured_date_available)) && ($v_today < strtotime($v_expires_date)) || ($v_today >= strtotime($v_featured_date_available) && $v_featured_date_available != '0001-01-01' && $v_expires_date == '0001-01-01') || ($v_featured_date_available == '0001-01-01' && $v_expires_date == '0001-01-01' && (!defined('EP4_ACTIVATE_BLANK_FEATURED') || EP4_ACTIVATE_BLANK_FEATURED))) {
              $v_status = 1;
            } else {
              $v_status = 0;
            }
            $v_date_status_change = date("Y-m-d");
            $sql = "INSERT INTO " . TABLE_FEATURED . " SET
              featured_id             = :max_featured_id:,
              products_id             = :products_id:,
              featured_date_added     = CURRENT_TIMESTAMP,
              featured_last_modified  = '',
              expires_date            = :expires_date:,
              date_status_change      = :date_status_change:,
              status                  = :status:,
              featured_date_available = :featured_date_available:";
            $sql = $db->bindVars($sql, ':max_featured_id:', $max_featured_id, 'string');
            $sql = $db->bindVars($sql, ':products_id:', $v_products_id, 'integer');
            $sql = $db->bindVars($sql, ':expires_date:', $v_expires_date, 'date');
            $sql = $db->bindVars($sql, ':date_status_change:', $v_date_status_change, 'string');
            $sql = $db->bindVars($sql, ':status:', $v_status , 'integer');
            $sql = $db->bindVars($sql, ':featured_date_available:', $v_featured_date_available, 'date');
            $result = ep_4_query($sql);
            if ($result) {
              zen_record_admin_activity('Inserted product ' . (int) $v_products_id . ' via EP4 into featured table.', 'info');
              $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_FEATURED_INSERT, $items[$filelayout[$chosen_key]], substr($chosen_key, 2), (int)$v_featured_id);
              $ep_import_count++;
            } else {
              $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_FEATURED_INSERT_SKIPPED, $items[$filelayout[$chosen_key]], substr($chosen_key, 2), (int)$v_featured_id);
              $ep_error_count++;
            }
          }
        } else { // ERROR: This products_model doesn't exist!
          $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_FEATURED_RECORD_MISSING, $items[$filelayout[$chosen_key]], substr($chosen_key, 2));
          $ep_error_count++;
        }
        ep4_flush();
      }
