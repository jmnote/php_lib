<?php
// http://jmnote.com/wiki/Lib_my.php
$mysqli = new mysqli('localhost', '계정명', '패스워드', '디비명');
if($mysqli->connect_errno) die('Connect failed: '.$mysqli->connect_error);
if(!$mysqli->set_charset('utf8')) die('Error loading character set utf8: '.$mysqli->error);

function single_quotes($value) { return "'$value'"; }

function query($qry, $mode='APPLY') {
  if($mode == 'TEST') { echo "<font color=red>$qry;</font><br />"; return; }
  global $mysqli;
  $result = $mysqli->query($qry);
  if($result === false) die("Query: [[ $qry ]] → Error: ".$mysqli->error);
  return $result ;
}

function query_rows($qry) {
  global $mysqli;
  $result = $mysqli->query($qry);
  if($result === false) die("Query: [[ $qry ]] → Error: ".$mysqli->error);
  $rows = array();
  eval('while(@$row = $result->fetch_assoc()) array_push($rows, $row);');
  return $rows;
}

function query_arr($qry) {
  global $mysqli;
  $result = $mysqli->query($qry);
  if($result === false) die("Query: [[ $qry ]] → Error: ".$mysqli->error);
  $arr = array();
  while($row = $result->fetch_row()) $arr[] = $row[0];
  return $arr;
}

function query_one($qry) {
  global $mysqli;
  $result = $mysqli->query($qry);
  if($result === false) die("Query: [[ $qry ]] → Error: ".$mysqli->error);
  $row = $result->fetch_row();
  return $row[0];
}


function insert_rows($arr, $table_name, $mode='APPLY') {
  foreach($arr as $row) {
 
    $keys = array_keys($row);
    $keys = array_map('mysql_real_escape_string', $keys);
 
    $values = array_values($row);
    $values = array_map('mysql_real_escape_string', $values);
    $values = array_map('single_quotes', $values);
 
    $keys_str = implode(',', $keys);
    $values_str = implode(',', $values);
 
    if($mode == 'TEST') echo "INSERT INTO $table_name ($keys_str) VALUES ($values_str);<br/>";
    else query("INSERT INTO $table_name ($keys_str) VALUES ($values_str)");
  }
}

function print_rows($rows) {
  echo "<table><tr>";
  foreach($rows[0] as $key => $value) echo "<th>$key</th>";
  echo "</tr>";
  foreach($rows as $row) {
    echo "<tr>";
    foreach($row as $key => $value) echo "<td>$value</td>";
    echo "</tr>";
  }
  echo "</table>";
}
?>
