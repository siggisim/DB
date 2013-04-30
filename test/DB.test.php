<?php

require_once("../DB.config.php");
  
class DBTest {

  private $db = null; 
  private $break = "<br/>";
  /* Database Functions */
  
  public function __construct($db) {
    $this->db = $db;
    $this->test_deleteRows();
  }

  public function test_close() {
  }
  /* Create Functions */

  public function test_createTable(/*$table*/) {
    echo "TEST createTable()" . $this->break;
    $this->db->createTable("test_table");
    $this->db->selectRow("test_table", array());
    echo "PASS createTable()" . $this->break;
  }
  
  public function test_createColumn(/*$table, $columnName, $type*/) {
    echo "TEST createColumn()" . $this->break;
    $this->db->createColumn("test_table", "test_column", "text");
    $this->db->createColumn("test_table", "other_column", "int");
    $this->db->selectRow("test_table", array("test_column" => "1"));
    echo "PASS createColumn()" . $this->break;
  }
  
  /* Insert Functions */
  
  public function test_insertRow(/*$table, $variables*/) {
    echo "TEST insertRow()" . $this->break;
    $this->db->insertRow("test_table", array("test_column" => "1"));
    $this->db->insertRow("test_table", array("test_column" => "2"));
    $row = $this->db->selectRow("test_table", array("test_column" => "1"));
    if ($row["test_column"] != "1")
      die("FAIL insertRow()");
    echo "PASS insertRow()" . $this->break;
  }
  
  /* Select Functions */
  
  public function test_selectRow(/*$table, $variables, $order = ""*/) {
    echo "TEST selectRow()" . $this->break;
    $this->db->insertRow("test_table", array("test_column" => "3"));
    $this->db->insertRow("test_table", array("test_column" => "4"));
    $row = $this->db->selectRow("test_table", array("test_column" => "3"));
    if ($row["test_column"] != "3")
      die("FAIL selectRow()");
    echo "PASS selectRow()" . $this->break;
  }
  
  public function test_selectRows(/*$table, $variables, $order = "", $limit = ""*/) {
    echo "TEST selectRows()" . $this->break;
    $this->db->insertRow("test_table", array("test_column" => "1"));
    $this->db->insertRow("test_table", array("test_column" => "1"));
    $rows = $this->db->selectRows("test_table", array("test_column" => "1"), "test_column", 2);
    if ($rows[0]["test_column"] != "1" || $rows[1]["test_column"] != "1" || count($rows) != 2)
      die("FAIL selectRows()");
    echo "PASS selectRows()" . $this->break;
  }
  
  /* Miscellaneous Select Functions */
  
  public function test_searchRows(/*$table, $variables, $order, $limit*/) {
    echo "TEST searchRows()" . $this->break;
    $this->db->insertRow("test_table", array("test_column" => "sdlkfjBLAHdlkfjsdf"));
    $this->db->insertRow("test_table", array("test_column" => "BLAH"));
    $rows = $this->db->searchRows("test_table", array("test_column" => "BLAH"), "test_column", 2);
    if (count($rows) != 2)
      die("FAIL searchRows()");
    echo "PASS searchRows()" . $this->break;
  }
  
  public function test_sumRows(/*$table, $field, $variables*/) {
    echo "TEST sumRows()" . $this->break;
    $this->db->insertRow("test_table", array("test_column" => "100", "other_column" => "2"));
    $this->db->insertRow("test_table", array("test_column" => "100", "other_column" => "1"));
    $sum = $this->db->sumRows("test_table", "other_column", array("test_column"=>"100"));
    if ($sum < 3)
      die("FAIL sumRows()");
    echo "PASS sumRows()" . $this->break;
  }

  /* Update Functions */

  public function test_updateRow(/*$table, $variables, $oldVariables*/) {
    echo "TEST updateRow()" . $this->break;
    $this->db->insertRow("test_table", array("test_column" => "74", "other_column" => "2"));
    $this->db->updateRow("test_table", array("other_column" => "99"), array("test_column" => "74"));
    $row = $this->db->selectRow("test_table", array("test_column"=>"74"));
    if ($row["other_column"] != "99")
      die("FAIL updateRow()");
    echo "PASS updateRow()" . $this->break;
  }
  
  public function test_updateRows(/*$table, $variables, $oldVariables*/) {
    echo "TEST updateRows()" . $this->break;
    $this->db->insertRow("test_table", array("test_column" => "76", "other_column" => "2"));
    $this->db->insertRow("test_table", array("test_column" => "76", "other_column" => "3"));
    $this->db->insertRow("test_table", array("test_column" => "76", "other_column" => "4"));
    $this->db->updateRows("test_table", array("other_column" => "88"), array("test_column" => "76"));
    $rows = $this->db->selectRows("test_table", array("other_column"=>"88"));
    if (count($rows) != 3)
      die("FAIL updateRows()");
    echo "PASS updateRows()" . $this->break;
  }
  
  public function test_incrementValue(/*$table, $field, $oldVariables, $amount = 1*/) {
    echo "TEST incrementValue()" . $this->break;
    $this->db->insertRow("test_table", array("test_column" => "101", "other_column" => "1"));
    $sum = $this->db->incrementValue("test_table", "other_column", array("test_column"=>"101"), 5);
    $row = $this->db->selectRow("test_table", array("test_column"=>"101"));
    if ($row["other_column"] != "6")
      die("FAIL incrementValue()");
    echo "PASS incrementValue()" . $this->break;
  
  }
  
  /* Delete Functions */

  public function test_deleteRow(/*$table, $variables*/) {
    echo "TEST deleteRow()" . $this->break;
    $rows = $this->db->selectRows("test_table", array());
    $count = count($rows);
    $this->db->deleteRow("test_table", array());
    $rows = $this->db->selectRows("test_table", array());
    if (count($rows) != $count - 1)
      die("FAIL deleteRow()");
    echo "PASS deleteRow()" . $this->break;
  }
  
  public function test_deleteRows(/*$table, $variables*/) {
    echo "TEST deleteRows()" . $this->break;
    $this->db->deleteRows("test_table", array());
    $rows = $this->db->selectRows("test_table", array());
    if (count($rows) != 0)
      die("FAIL deleteRows()");
    echo "PASS deleteRows()" . $this->break;
  }
}
?>
