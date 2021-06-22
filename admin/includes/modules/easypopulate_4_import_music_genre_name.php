<?php

if (isset($v_music_genre_name) && ($v_music_genre_name != '')) {
  $post_array = array(
    'music_genre_name' => array(
      'v_music_genre_name' => compact('v_music_genre_name'),
    ),
  );
  
  $data_array = ep4_post_sanitize($post_array);
  extract($data_array, EXTR_OVERWRITE);

}

$v_music_genre_id = 0; // chadd - zencart uses artists_id = '0' for no assisgned artists

$music_genre_name_str_len = isset($v_music_genre_name) && ($v_music_genre_name != '') ? $ep_4_strlen($v_music_genre_name) : false;

if ($music_genre_name_str_len === false) {
  $display_output .= sprintf(EASYPOPULATE_4_DISPLAY_RESULT_MUSIC_GENRE_NAME_EMPTY, $items[$filelayout[$chosen_key]], $chosen_key);
  unset($music_genre_name_str_len);
  return;
}

unset($music_genre_name_str_len);

if (ep_4_extend_field($v_music_genre_name, $max_len, 'music_genre_name', null, array(TABLE_MUSIC_GENRE)) == 'continue') {
  $ep_error_count++;
  return;
}

$sql = "SELECT music_genre_id AS music_genreID FROM " . TABLE_MUSIC_GENRE . " WHERE music_genre_name = :music_genre_name: LIMIT 1";
$sql = $db->bindVars($sql, ':music_genre_name:', $v_music_genre_name, $zc_support_ignore_null);
$result = ep_4_query($sql);
if ($row = $ep_4_fetch_array($result)) {
  $v_music_genre_id = $row['music_genreID']; // this id goes into the product_music_extra table
} else { // It is set to autoincrement, do not need to fetch max id
  $sql = "INSERT INTO " . TABLE_MUSIC_GENRE . " (music_genre_name, date_added)
    VALUES (:music_genre_name:, CURRENT_TIMESTAMP)";
  $sql = $db->bindVars($sql, ':music_genre_name:', $v_music_genre_name, $zc_support_ignore_null);
  $result = ep_4_query($sql);

  $v_music_genre_id = $ep_4_insert_id(($ep_uses_mysqli ? $db->link : null)); // id is auto_increment

  if ($result) {
    zen_record_admin_activity('Inserted music genre ' . zen_db_input($v_music_genre_name) . ' via EP4.', 'info');
  }
}

unset($music_genre_name_str_len);
