<?php


class Drupal_Zend_Db {

  private static $handle;

  /*
   * Preset Object:
   *
   *'host' => string
    'username' => string
    'password' => string
    'dbname' => string
    'port' => string
    'dbtype' => string
   *
   *
   */
  private function __construct($preset)
  {
    $preset = (array) $preset;
    $allowed_values = array('host', 'username', 'password', 'dbname', 'port');

    /* Check all allowed values options */
    foreach ($allowed_values as $required) {
      if (!array_key_exists($required, $preset)) {
        $errors[] = (strtr('There is missing "%s" required value', array('%s' => $required)));
      }
     $options[$required] = $preset[$required];
    }
    if ($errors) {
      throw new Exception(implode("\n", $errors));
    }

    /* Remove port options if it*/
    if (array_key_exists('port', $options) && !$options['port']) unset($options['port']);

    /* Calculate additional options to pass into adapter */
    $additionals = (array_diff($preset, $options));
    unset($additionals['dbtype']);
    $options = $this->mergeAdditional($options, $additionals);

    switch($preset['dbtype']) {
      case 'mysql' :
        $this->createMysql($options, $preset);
        break;
      case 'pgsql' :
        $this->createPgsql($options, $preset);
        break;
      case 'db2' :
        $this->createIbm($options, $preset);
        break;
      case 'oracle' :
        $this->createOracle($options, $preset);
        break;
      case 'sqlite' :
        $this->createSqlite($options, $preset);
        break;
      case 'default' :
        throw new Exception("DBMS $db_type unsupported.");
    }
  }

  private function mergeAdditional($options, $additional = false) {
    if ($additional) {
      $options['options'] = $additional;
    }
    return $options;
  }

  private function createMysql($options, $preset) {
    $this->handle = new Zend_Db_Adapter_Pdo_Mysql($options);
  }

  private function createPgsql($options, $preset = null) {
    $this->handle = new Zend_Db_Adapter_Pdo_Pgsql($options);
  }

  private function createIbm($options, $preset = null) {
    $this->handle = new Zend_Db_Adapter_Pdo_Ibm($options);
  }

  private function createOracle($options, $preset = null) {
    $this->handle = new Zend_Db_Adapter_Pdo_Oci($options);
  }

  private function createSqlite($options, $preset = null) {
    $this->handle = new Zend_Db_Adapter_Pdo_Sqlite($options);
  }

  public function get($preset) {
    static $db = array();
    if ($db[$preset->name] == null) {
       $db[$preset->name] = new Drupal_Zend_Db($preset);
    }
    else {
      if ($db[$preset->name]->handle->isConnected())  {
        return $db[$preset->name];
      }
      /* Object is active but is not more connected to DBMS, connect again */
      else {
        $db[$preset->name] = new Drupal_Zend_Db($preset);
      }
    }
    if ($db[$preset->name]) {
      return $db[$preset->name];
    }
    else {
      throw new Exception('No connections available for this preset.');
    }
  }

  public function raw($query) {
    return $this->handle->getConnection()->exec($query);
  }

  public function handle() {
    return $this->handle;
  }

  public function getTables() {
    return $this->handle->listTables();
  }

  public function describeTable($table) {
    return $this->handle->describeTable($table);
  }

  public function close() {
    return $this->handle->closeConnection();
  }

}


?>
