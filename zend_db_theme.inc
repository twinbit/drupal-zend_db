<?php

function zend_db_theme() {
    return array(
    'zend_db_list_tables' => array(
      'arguments' => array('preset' => NULL, 'tables' => NULL)),
    'zend_db_describe_table' => array(
      'arguments' => array('preset' => NULL, 'tables' => NULL)),
    'zend_db_query_result' => array(
      'arguments' => array('result' => NULL))
    );
}

function theme_zend_db_describe_table($preset, $table, $fields) {
  if (!$fields) {
    return t('No description available for this table.');
  }

  /* Extract keys and use them as table headers */
  $header = array_map('_zend_db_humanize_keys', array_keys(array_pop($fields)));
  foreach ($fields as $key => $field) {
    $rows[] = array_map('_zend_db_humanize_empty', array_values($field));
  }
  return theme('table', $header, $rows);
}

function theme_zend_db_list_tables($preset, $tables) {
  $output = '<br /> <h3>'.count($tables).' '.t('Tables available').'</h3></div>';
  $header = array(t('Table'), t('Actions'));
  foreach ($tables as $table) {
    $rows[] = array(
      $table, l(t('Show'), 'admin/build/zend/db/tables/describe/'.$preset->name.'/'.$table)
    );
  }
  $output .= theme('table', $header, $rows);
  return $output;
}

function theme_zend_db_query_result($result) {
  $header = array_keys($result[0]);
  foreach ($result as $res) {
    $row = array();
    foreach ($header as $key) {
      $row[] = $res[$key];
    }
    $rows[] = $row;
  }
  return theme('table', $header, $rows);
}

?>
