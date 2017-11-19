<?php

          if (isset($v_artists_name) && ($v_artists_name != '') && ((function_exists('mb_strlen') && mb_strlen($v_artists_name) <= $max_len['artists_name']) || (!function_exists('mb_strlen') && strlen($v_artists_name) <= $max_len['artists_name']))) {
            $sql = "SELECT artists_id AS artistsID FROM " . TABLE_RECORD_ARTISTS . " WHERE artists_name = :artists_name: LIMIT 1";
            $sql = $db->bindVars($sql, ':artists_name:', ep_4_curly_quotes($v_artists_name), 'string');
            $result = ep_4_query($sql);
            unset($sql);
            if ($row = ($ep_uses_mysqli ? mysqli_fetch_array($result) : mysql_fetch_array($result) )) {
              unset($result);
              $v_artists_id = $row['artistsID']; // this id goes into the product_music_extra table, the other information is collected from the assignment of ${$key} = $items[$value]
              $sql = "UPDATE " . TABLE_RECORD_ARTISTS . " SET
                artists_image = :artists_image:,
                last_modified = CURRENT_TIMESTAMP
                WHERE artists_id = :artists_id:";
              $sql = $db->bindVars($sql, ':artists_image:', $v_artists_image, 'string');
              $sql = $db->bindVars($sql, ':artists_id:', $v_artists_id, 'integer');
              $result = ep_4_query($sql);
              unset($sql);
              if ($result) {
                zen_record_admin_activity('Updated record artist ' . (int) $v_artists_id . ' via EP4.', 'info');
              }
              unset($result);
              foreach ($langcode as $lang) {
                $l_id = $lang['id'];
                // if the column is not in the import file, then don't modify
                //  or update that particular language's value.  This way
                //  only the columns desired to be updated are modified, not
                //  all columns and thus require on any update to have all
                //  columns present even those not being updated.
                if (!isset($filelayout['v_artists_url_' . $l_id])) {
                  unset($l_id);
                  unset($lang);
                  continue;
                }
                $sql = "UPDATE " . TABLE_RECORD_ARTISTS_INFO . " SET
                  artists_url = :artists_url:
                  WHERE artists_id = :artists_id: AND languages_id = :languages_id:";
                $sql = $db->bindVars($sql, ':artists_id:', $v_artists_id, 'integer');
                $sql = $db->bindVars($sql, ':artists_url:', $items[$filelayout['v_artists_url_' . $l_id]], 'string');
                $sql = $db->bindVars($sql, ':languages_id:', $l_id, 'integer');
                $result = ep_4_query($sql);
                unset($sql);
                if ($result) {
                  zen_record_admin_activity('Updated record artist info ' . (int) $v_artists_id . ' via EP4.', 'info');
                }
                unset($l_id);
                unset($result);
              }
              unset($lang);
            } else { // It is set to autoincrement, do not need to fetch max id
              unset($result);
              $sql = "INSERT INTO " . TABLE_RECORD_ARTISTS . " (artists_name, artists_image, date_added, last_modified)
                VALUES (:artists_name:, :artists_image:, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
              $sql = $db->bindVars($sql, ':artists_name:', ep_4_curly_quotes($v_artists_name), 'string');
              $sql = $db->bindVars($sql, ':artists_image:', $v_artists_image, 'string');
              $result = ep_4_query($sql);
              unset($sql);

              $v_artists_id = ($ep_uses_mysqli ? mysqli_insert_id($db->link) : mysql_insert_id()); // id is auto_increment, so can use this function

              if ($result) {
                zen_record_admin_activity('Inserted record artist ' . zen_db_input(ep_4_curly_quotes($v_artists_name)) . ' via EP4.', 'info');
              }
              unset($result);

              foreach ($langcode as $lang) {
                $l_id = $lang['id'];
                // If the artists_url column for this language was not in the file,
                //  then do not modify the setting... But, also make sure
                //  using the correct "check" mc12345678 2015-12-30
                //  $filelayout chosen as it is to return an array represeting
                //  the position in the file that is translated to the data
                //  at that position.  For an insert (ie. new record), if
                //  all of the data is not provided, then will populate with
                //  the data of the "first" language (which should be included)
                //  if the particular artists_url is provided then that is used.
                $sql = "INSERT INTO " . TABLE_RECORD_ARTISTS_INFO . " (artists_id, languages_id, artists_url)
                  VALUES (:artists_id:, :languages_id:, :artists_url:)"; // seems we are skipping manufacturers url
                $sql = $db->bindVars($sql, ':artists_id:', $v_artists_id, 'integer');
                $sql = $db->bindVars($sql, ':languages_id:', $l_id, 'integer');
                $sql = $db->bindVars($sql, ':artists_url:', (isset($filelayout['v_artists_url_' . $l_id]) ? $items[$filelayout['v_artists_url_' . $l_id]] : $items[$filelayout['v_artists_url_' . $lid]]), 'string');
                $result = ep_4_query($sql);
                unset($sql);
                if ($result) {
                  zen_record_admin_activity('Inserted record artists info ' . (int) $v_artists_id . ' via EP4.', 'info');
                }
                unset($l_id);
                unset($result);
              }
              unset($lang);
            }
          } else { // $v_artists_name == '' or name length violation
            if ((function_exists('mb_strlen') && mb_strlen($v_artists_name) > $max_len['artists_name']) || (!function_exists('mb_strlen') && strlen($v_artists_name) > $max_len['artists_name'])) {
              $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_ARTISTS_NAME_LONG, $v_artists_name, $max_len['artists_name']);
              $ep_error_count++;
              continue;
            }
            $v_artists_id = 0; // chadd - zencart uses artists_id = '0' for no assisgned artists
          }

