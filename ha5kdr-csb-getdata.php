<?php
//	ini_set('display_errors','On');
//	error_reporting(E_ALL);

	function sanitize($s) {
		return strip_tags(stripslashes(trim($s)));
	}

	include('ha5kdr-csb-config.inc.php');

	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if (!$conn) {
		echo "can't connect to mysql database!\n";
		return;
	}

	$conn->query("set names 'utf8'");
	$conn->query("set charset 'utf8'");

	$searchfor = sanitize($_POST['searchfor']);
	$searchtoks = explode(' ', $searchfor);
	$search = '';
	for ($i = 0; $i < count($searchtoks); $i++) {
		if ($i == 0)
			$search = 'where ';
		else
			$search .= 'and ';

		$searchtok = $conn->escape_string($searchtoks[$i]);
		$search .= "(`partnercode` like '%$searchtok%' or " .
			"`name` like '%$searchtok%' or " .
			"`country` like '%$searchtok%' or " .
			"`zip` like '%$searchtok%' or " .
			"`city` like '%$searchtok%' or " .
			"`streethouse` like '%$searchtok%' or " .
			"`licensenumber` like '%$searchtok%' or " .
			"`callsign` like '%$searchtok%' or " .
			"`communityorprivate` like '%$searchtok%' or " .
			"`state` like '%$searchtok%' or " .
			"`levelofexam` like '%$searchtok%' or " .
			"`morse` like '%$searchtok%' or " .
			"`chiefoperator` like '%$searchtok%') ";
	}

	$sorting = sanitize($_GET['jtSorting']);
	$startindex = sanitize($_GET['jtStartIndex']);
	if (!ctype_digit($startindex))
		return;
	$pagesize = sanitize($_GET['jtPageSize']);
	if (!ctype_digit($pagesize))
		return;

	// Getting record count
	$result = $conn->query('select count(*) as `recordcount` from `' . DB_TABLE . '` ' . $search);
	$row = $result->fetch_array();
	$recordcount = $row['recordcount'];

	$result = $conn->query('select * from `' . DB_TABLE . '` ' . $search . 'order by ' . $conn->escape_string($sorting) .
		' limit ' . $conn->escape_string($startindex) . ',' . $conn->escape_string($pagesize));
	$rows = array();
	while ($row = $result->fetch_array(MYSQLI_ASSOC))
	    $rows[] = $row;

	$jtableresult = array();
	$jtableresult['Result'] = "OK";
	$jtableresult['TotalRecordCount'] = $recordcount;
	$jtableresult['Records'] = $rows;
	echo json_encode($jtableresult);

	$conn->close();
?>
