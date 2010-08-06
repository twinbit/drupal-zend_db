<?php

function zend_db_get_instance($preset_name, $test = false) {
  if (!$test) {
    $preset = _zend_db_get_connection_by_name($preset_name);
  }
  else {
    $preset = $preset_name;
  }
  if ($preset) {
   try {
      $conn = Drupal_Zend_Db::get($preset);
      $conn->handle()->getServerVersion();
      return $conn;
   }
   catch (Exception $e) {
     drupal_set_message(t('Zend DB Connection Error:'). $e->getMessage(), 'error');
     return null;
   }
  }
}


function _zend_db_get_connection_by_name($name) {
  return db_fetch_object(db_query('SELECT * FROM {zend_db} WHERE name = "%s"', $name));
}

function _zend_db_humanize_keys($elem) {
  return ucfirst(strtolower(str_replace('_', ' ', $elem)));
}

function _zend_db_humanize_empty($elem) {
  if (!$elem) {
    return t('N/A');
  }
  else {
    return $elem;
  }
}

?>
