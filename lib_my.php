<?php
$dbconn = new PDO('mysql:host=호스트;dbname=디비명', '아이디', '패스워드',
	[PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
$dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function str_replace_nth($search, $replace, $subject, $nth) {
	$found = preg_match_all('/'.preg_quote($search).'/', $subject, $matches, PREG_OFFSET_CAPTURE);
	if( $found === false || $found <= $nth ) return $subject;
	return substr_replace($subject, $replace, $matches[0][$nth][1], strlen($search));
}

// 여러 행 추출 ( N X N )
function query_rows() {
	global $dbconn;
	$raw_params = func_get_args();
	$query = array_shift($raw_params);

	$replaces = array();
	$params = array();
	foreach($raw_params as $i => $raw_param) {
		if(is_array($raw_param)) {
			$replaces[] = array( $i, count($raw_param));
			$params = array_merge($params, $raw_param);
			continue;
		}
		$params[] = $raw_param;
	}
	$replaces = array_reverse($replaces);

	foreach( $replaces as $replace ) {
		list( $position, $cnt ) = $replace;
		$qs = implode(',', array_fill(0, $cnt, '?') );
		$query = str_replace_nth('?', $qs, $query, $position);
	}
	
	try {
		$sth = $dbconn->prepare($query);
		$sth->execute($params);
	} catch (PDOException $e) {
		query_error(__FUNCTION__, $query, $params, $e->getMessage());
	}
	return $sth->fetchAll(PDO::FETCH_ASSOC);
}

// 단일 행 추출 ( 1 X N )
function query_row() {
	global $dbconn;
	$params = func_get_args();
	$query = array_shift($params);
	try {
		$sth = $dbconn->prepare($query);
		$sth->execute($params);
	} catch (PDOException $e) {
		query_error(__FUNCTION__, $query, $params, $e->getMessage());
	}
	$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
	return isset($rows[0])? $rows[0]: false;
}

// 단일 컬럼 여러 행 추출 ( N X 1 )
function query_values() {
	global $dbconn;
	$raw_params = func_get_args();
	$query = array_shift($raw_params);

	$replaces = array();
	$params = array();
	foreach($raw_params as $i => $raw_param) {
		if(is_array($raw_param)) {
			$replaces[] = array( $i, count($raw_param));
			$params = array_merge($params, $raw_param);
			continue;
		}
		$params[] = $raw_param;
	}
	$replaces = array_reverse($replaces);

	foreach( $replaces as $replace ) {
		list( $position, $cnt ) = $replace;
		$qs = implode(',', array_fill(0, $cnt, '?') );
		$query = str_replace_nth('?', $qs, $query, $position);
	}
	
	try {
		$sth = $dbconn->prepare($query);
		$sth->execute($params);
	} catch (PDOException $e) {
		query_error(__FUNCTION__, $query, $params, $e->getMessage());
	}
	$arr = array();
	while($row = $sth->fetch(PDO::FETCH_NUM)) $arr[] = $row[0];
	return $arr;
}

// 단일 값 추출 ( 1 x 1 )
function query_one() {
	global $dbconn;
	$params = func_get_args();
	$query = array_shift($params);
	try {
		$sth = $dbconn->prepare($query);
		$sth->execute($params);
	} catch (PDOException $e) {
		query_error(__FUNCTION__, $query, $params, $e->getMessage());
	}
	$row = $sth->fetch(PDO::FETCH_NUM);
	return isset($row[0])? $row[0]: false;
}

// 쿼리 실행 ( INSERT / UPDATE / DELETE 등 )
function query() {
	global $dbconn;
	$params = func_get_args();
	$query = array_shift($params);
	try {
		$sth = $dbconn->prepare($query);
		$sth->execute($params);
	} catch (PDOException $e) {
		query_error(__FUNCTION__, $query, $params, $e->getMessage());
	}
}

// 마지막으로 생성된 AUTO_INCREMENT ID
function last_insert_id() {
	global $dbconn;
	return $dbconn->lastInsertId();
}

function query_error($function_name, $query, $params, $message) {
	$error_message = "[$function_name] failed: [$message]";
	print_r($error_message);
	print_r($query);
	print_r($params);
	exit;
}

function db_use($db_name) {
	query("use $db_name");
}
