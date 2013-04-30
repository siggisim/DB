<?php

class DB {

  /* Class Variables */
  private $config = array();
  private $db = null;

  /* Database Functions */
  
  public function __construct($config) {
    $this->config = $config;
    $this->db = new mysqli($this->config['host'], $this->config['username'], $this->config['password'], $this->config['database']) 
        or die("Could not open database");
  }
  
  public function close() {
    $this->db->close();
  }
  
  /* Create Functions */

  public function createTable($table) {
    $query = "CREATE TABLE IF NOT EXISTS $table (id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id))";
    $this->db->query($query)
        or die ("Could not create table $table : " . $this->db->error);
  }
  
  public function createColumn($table, $columnName, $type) {
    $query = "SHOW COLUMNS FROM $table LIKE '%$columnName%'";
    $result = $this->db->query($query)
        or die("Could not show columns in $table: " . $this->db->error);
    if ($result->num_rows == 0) {
      $query = "ALTER TABLE $table ADD COLUMN $columnName $type ";
      $result = $this->db->query($query)
          or die("Could not create table column $table: " .  $this->db->error);      
    }
  }

  /* Insert Functions */
  
  public function insertRow($table, $variables) {
    $variables = $this->sanitize($variables);
    $keys = implode(", ", array_keys($variables));
    $values = implode("', '", array_values($variables));
    $query = "INSERT INTO $table (" . $keys . ") VALUES ('" . $values . "')";
    $result = $this->db->query($query)
        or die("Could not create row: " . $this->db->error);
    return $result->last_id;  
  }
  
  /* Select Functions */
  
  private function select($table, $variables, $order = "", $limit = "", $like = false) {
    $order = $this->orderFactory($order);
    $limit = $this->limitFactory($limit);
    $whereClause = "";
    if ($like)
      $whereClause = $this->likeFactory($variables);
    else
      $whereClause = $this->whereFactory($variables);

    $query = "SELECT * FROM $table" . $whereClause . $order . $limit;
    $result = $this->db->query($query)
        or die("Could not select rows: " . $this->db->error);
    $rows = array();
    while ($row = $result->fetch_assoc())
      array_push($rows, $row);
    return $rows;

  }

  public function selectRow($table, $variables, $order = "") {
    $rows = $this->select($table, $variables, $order, 1);
    return $rows[0];
  }
  
  public function selectRows($table, $variables, $order = "", $limit = "") {
    return $this->select($table, $variables, $order, $limit);
  }
  
  /* Miscellaneous Select Functions */
    
  public function searchRows($table, $variables, $order, $limit) {
    return $this->select($table, $variables, $order, $limit, true);
  }
  
  public function sumRows($table, $field, $variables) {
    $query = "SELECT SUM(" . $field . ") FROM $table " . $this->whereFactory($variables);
    $result = $this->db->query($query)
        or die("Could not get sum: " . $this->db->error);
    $row = $result->fetch_assoc();
    return $row["SUM(" . $field . ")"];
  }

  /* Update Functions */

  private function update($table, $variables, $oldVariables, $limit = "") {
    $limit = $this->limitFactory($limit);
    $setVariables = array();
    $variables = $this->sanitize($variables);
    foreach ($variables as $variable => $value) {
      $setVariables[] = "`" . $variable . "` = '" . $value . "'";
    }
    $setClause = "SET " . implode(", ", $setVariables);
    $query = "UPDATE $table " . $setClause . " " . $this->whereFactory($oldVariables) . $limit;
    $result = $this->db->query($query) 
        or die("Could not update row: " . $this->db->error);
    return $result->affected_rows;
  }

  public function updateRow($table, $variables, $oldVariables) {
    $this->update($table, $variables, $oldVariables, 1);  
  }
  
  public function updateRows($table, $variables, $oldVariables) {
    $this->update($table, $variables, $oldVariables);  
  }
  
  public function incrementValue($table, $field, $oldVariables, $amount = 1) {
    $setClause = "SET " . $field . " = " . $field . " + " . $amount;
    $query = "UPDATE $table ".$setClause." ".$this->whereFactory($oldVariables);
    $this->db->query($query) 
        or die("Could not increment value: " . $this->db->error);
  }
  
  /* Delete Functions */

  private function delete($table, $variables, $limit = "") {
    $limit = $this->limitFactory($limit);
    $query = "DELETE FROM $table " . $this->whereFactory($variables) . $limit;
    $this->db->query($query) 
        or die("Could not delete row: " . $this->db->error);
  }

  public function deleteRow($table, $variables) {
    $this->delete($table, $variables, 1);
  }
  
  public function deleteRows($table, $variables) {
    $this->delete($table, $variables);
  }

  /* Helper Functions */

  private function whereFactory($variables, $operation = " AND ") {
    if (!$variables)
      return "";
    $variables = $this->sanitize($variables);
    $whereVariables = array();
    foreach ($variables as $variable => $value) {
      $whereVariables[] = "`" . $variable . "` = '" . $value . "'";
    }
    $whereClause = " WHERE " . implode($operation, $whereVariables);
    return $whereClause;
  }
  
  private function likeFactory($variables, $operation = " OR ") {
    if (!$variables)
      return "";
    $variables = $this->sanitize($variables);
    $likeVariables = array(); 
    foreach ($variables as $variable => $value) {
      $likeVariables[] = "`" . $variable . "` LIKE('%" . $value . "%')";
    }
    $likeClause = " WHERE " . implode($operation, $likeVariables);
    return $likeClause;
  }
  
  private function limitFactory($limit) {
    if($limit)
      $limit = " LIMIT " . $limit;
    return $limit;
  }
  
  private function orderFactory($order) {
    if($order)
      $order = " ORDER BY " . $order;
    return $order;
  }
  
  private function sanitize($variables) {
    $sanitized = array();
    foreach ($variables as $key => $value) {
      $key = $this->db->real_escape_string($key);
      $value = $this->db->real_escape_string($value);
      $sanitized[$key] = $value;
    }
    return $sanitized;
  }
}

?>
