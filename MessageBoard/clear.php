<?php

error_reporting(E_ALL);
ini_set('display_errors','On');

try {
  $dbname = dirname($_SERVER["SCRIPT_FILENAME"]) . "/mydb.sqlite";
  $dbh = new PDO("sqlite:$dbname");
  $dbh->exec('delete from posts')
        or die(print_r($dbh->errorInfo(), true));
} catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}
?>