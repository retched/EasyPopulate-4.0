<?php

          if (isset($v_record_company_name) && ($v_record_company_name != '') && ((function_exists('mb_strlen') && mb_strlen($v_record_company_name) <= $max_len['record_company_name']) || (!function_exists('mb_strlen') && strlen($v_record_company_name) <= $max_len['record_company_name']))) {
            $sql = "SELECT record_company_id AS record_companyID FROM " . TABLE_RECORD_COMPANY . " WHERE record_company_name = :record_company_name: LIMIT 1";
            $sql = $db->bindVars($sql, ':record_company_name:', ep_4_curly_quotes($v_record_company_name), 'string');
            $result = ep_4_query($sql);
            if ($row = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result) )) {
              $v_record_company_id = $row['record_companyID']; // this id goes into the product_music_extra table
              $sql = "UPDATE " . TABLE_RECORD_COMPANY . " SET
                record_company_image = :record_company_image:,
                last_modified = CURRENT_TIMESTAMP
                WHERE record_company_id = :record_company_id:";
              $sql = $db->bindVars($sql, ':record_company_image:', $v_record_company_image, 'string');
              $sql = $db->bindVars($sql, ':record_company_id:', $v_record_company_id, 'integer');
              $result = ep_4_query($sql);
              if ($result) {
                zen_record_admin_activity('Updated record company ' . (int) $v_record_company_id . ' via EP4.', 'info');
              }
              foreach ($langcode as $lang) {
                $l_id = $lang['id'];
                if (!isset($filelayout['v_record_company_url_' . $l_id])) {
                  continue;
                }
                $sql = "UPDATE " . TABLE_RECORD_COMPANY_INFO . " SET
                  record_company_url = :record_company_url:
                  WHERE record_company_id = :record_company_id: AND languages_id = :languages_id:";
                $sql = $db->bindVars($sql, ':record_company_url:', $items[$filelayout['v_record_company_url_' . $l_id]], 'string');
                $sql = $db->bindVars($sql, ':record_company_id:', $v_record_company_id, 'integer');
                $sql = $db->bindVars($sql, ':languages_id:', $l_id, 'integer');
                $result = ep_4_query($sql);
                if ($result) {
                  zen_record_admin_activity('Updated record company info ' . (int) $v_record_company_id . ' via EP4.', 'info');
                }
              }
              unset($lang);
            } else { // It is set to autoincrement, do not need to fetch max id
              $sql = "INSERT INTO " . TABLE_RECORD_COMPANY . " (record_company_name, record_company_image, date_added, last_modified)
                VALUES (:record_company_name:, :record_company_image:, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
              $sql = $db->bindVars($sql, ':record_company_name:', ep_4_curly_quotes($v_record_company_name), 'string');
              $sql = $db->bindVars($sql, ':record_company_image:', $v_record_company_image, 'string');
              $result = ep_4_query($sql);

              $v_record_company_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment, so can use this function

              if ($result) {
                zen_record_admin_activity('Inserted record company ' . zen_db_input(ep_4_curly_quotes($v_record_company_name)) . ' via EP4.', 'info');
              }

              foreach ($langcode as $lang) {
                $l_id = $lang['id'];
                $sql = "INSERT INTO " . TABLE_RECORD_COMPANY_INFO . " (record_company_id, languages_id, record_company_url)
                  VALUES (:record_company_id:, :languages_id:, :record_company_url:)"; // seems we are skipping manufacturers url
                $sql = $db->bindVars($sql, ':record_company_id:', $v_record_company_id, 'integer');
                $sql = $db->bindVars($sql, ':languages_id:', $l_id, 'integer');
                $sql = $db->bindVars($sql, ':record_company_url:',  (isset($filelayout['v_record_company_url_' . $l_id]) ? $items[$filelayout['v_record_company_url_' . $l_id]] : $items[$filelayout['v_record_company_url_' . $lid]]), 'string');
                $result = ep_4_query($sql);
                if ($result) {
                  zen_record_admin_activity('Inserted record company info ' . (int) $v_record_company_id . ' via EP4.', 'info');
                }
              }
              unset($lang);
            }
          } else { // $v_record_company_name == '' or name length violation
            if ((function_exists('mb_strlen') && mb_strlen($v_record_company_name) > $max_len['record_company_name']) || (!function_exists('mb_strlen') && strlen($v_record_company_name) > $max_len['record_company_name'])) {
              $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_RECORD_COMPANY_NAME_LONG, $v_record_company_name, $max_len['record_company_name']);
              $ep_error_count++;
              continue;
            }
            $v_record_company_id = 0; // record_company_id = '0' for no assisgned artists
          }
