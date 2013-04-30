<?
require_once("DB.test.php");

$test = new DBTest($db);
$test->test_createTable();
$test->test_createColumn();
$test->test_insertRow();
$test->test_selectRow();
$test->test_selectRows();
$test->test_searchRows();
$test->test_getSum();
$test->test_updateRow();
$test->test_updateRows();
$test->test_incrementValue();
$test->test_deleteRow();
$test->test_deleteRows();
?>