#!/usr/bin/php5
<?php
	ini_set('display_errors','On');
	error_reporting(E_ALL);

	include('ha5kdr-csb-process-config.php');

	// This defines the order in which the data can be found in the xml file.
	define('ROWCODE_PARTNERCODE',			0);
	define('ROWCODE_NAME',					1);
	define('ROWCODE_COUNTRY',				2);
	define('ROWCODE_ZIP',					3);
	define('ROWCODE_CITY',					4);
	define('ROWCODE_STREETHOUSE',			5);
	define('ROWCODE_LICENSENUMBER',			6);
	define('ROWCODE_CALLSIGN',				7);
	define('ROWCODE_COMMUNITYORPRIVATE',	8);
	define('ROWCODE_STATE',					9);
	define('ROWCODE_LEVELOFEXAM',			10);
	define('ROWCODE_MORSE',					11);
	define('ROWCODE_LICENSEDATE',			12);
	define('ROWCODE_VALIDITY',				13);
	define('ROWCODE_CHIEFOPERATOR',			14);

	if ($argc < 2) {
		echo "usage: ${argv[0]} [xml file]\n";
		return 1;
	}

	$reader = new XMLReader;
	@$reader->open($argv[1]);

	if (!$reader) {
		echo "can't open ${argv[1]}!\n";
		return 1;
	}

	$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if (!$conn) {
		echo "can't connect to mysql database!\n";
		return 1;
	}

	$db = mysql_select_db(DB_NAME, $conn);
	if (!$db) {
		mysql_close($conn);
		echo "can't connect to mysql database!\n";
		return 1;
	}

	mysql_query("set names 'utf8'");
	mysql_query("set charset 'utf8'");

	if (!mysql_query('truncate table `' . DB_TABLE . '`')) {
		echo "can't truncate table\n";
		return 1;
	}

	// Moving to the first <Row /> node which contain user data
	while ($reader->read() && $reader->name !== 'Row')
		;

	while ($reader->read()) {
		// Moving to the next row.
		if ($reader->name !== 'Row')
			continue;

		// Moving to the node which contains the data.
		$reader->read();
		$reader->read();
		$node = $reader->expand();

		$field = 0;

		$partnercode = '';
		$name = '';
		$country = '';
		$zip = '';
		$city = '';
		$streethouse = '';
		$licensenumber = '';
		$callsign = '';
		$communityorprivate = '';
		$state = '';
		$levelofexam = '';
		$morse = '';
		$licensedate = '';
		$validity = '';
		$chiefoperator = '';

		foreach ($node->childNodes as $childnode) {
			if ($childnode->nodeType == XML_ELEMENT_NODE) {
				$textcontent = trim($childnode->firstChild->textContent);
				switch ($field) {
					case ROWCODE_PARTNERCODE:
						$partnercode = $textcontent;
						break;
					case ROWCODE_NAME:
						$name = $textcontent;
						break;
					case ROWCODE_COUNTRY:
						$country = $textcontent;
						break;
					case ROWCODE_ZIP:
						$zip = $textcontent;
						break;
					case ROWCODE_CITY:
						$city = $textcontent;
						break;
					case ROWCODE_STREETHOUSE:
						$streethouse = $textcontent;
						break;
					case ROWCODE_LICENSENUMBER:
						$licensenumber = $textcontent;
						break;
					case ROWCODE_CALLSIGN:
						$callsign = $textcontent;
						break;
					case ROWCODE_COMMUNITYORPRIVATE:
						$communityorprivate = $textcontent;
						break;
					case ROWCODE_STATE:
						$state = $textcontent;
						break;
					case ROWCODE_LEVELOFEXAM:
						$levelofexam = $textcontent;
						break;
					case ROWCODE_MORSE:
						$morse = $textcontent;
						break;
					case ROWCODE_LICENSEDATE:
						$licensedate = $textcontent;
						break;
					case ROWCODE_VALIDITY:
						$validity = $textcontent;
						break;
					case ROWCODE_CHIEFOPERATOR:
						$chiefoperator = $textcontent;
						break;
					default:
						break;
				}

				$field++;
			}
		}

		if ($partnercode == '')
			continue;

		$res = mysql_query('insert into `' . DB_TABLE . '` (' .
			'`partnercode`, ' .
			'`name`, ' .
			'`country`, ' .
			'`zip`, ' .
			'`city`, ' .
			'`streethouse`, ' .
			'`licensenumber`, ' .
			'`callsign`, ' .
			'`communityorprivate`, ' .
			'`state`, ' .
			'`levelofexam`, ' .
			'`morse`, ' .
			'`licensedate`, ' .
			'`validity`, ' .
			'`chiefoperator`) values (' .
			'"' . mysql_real_escape_string($partnercode) . '", ' .
			'"' . mysql_real_escape_string($name) . '", ' .
			'"' . mysql_real_escape_string($country) . '", ' .
			'"' . mysql_real_escape_string($zip) . '", ' .
			'"' . mysql_real_escape_string($city) . '", ' .
			'"' . mysql_real_escape_string($streethouse) . '", ' .
			'"' . mysql_real_escape_string($licensenumber) . '", ' .
			'"' . mysql_real_escape_string($callsign) . '", ' .
			'"' . mysql_real_escape_string($communityorprivate) . '", ' .
			'"' . mysql_real_escape_string($state) . '", ' .
			'"' . mysql_real_escape_string($levelofexam) . '", ' .
			'"' . mysql_real_escape_string($morse) . '", ' .
			'"' . mysql_real_escape_string($licensedate) . '", ' .
			'"' . mysql_real_escape_string($validity) . '", ' .
			'"' . mysql_real_escape_string($chiefoperator) . '")');

		if (!$res)
			echo "error adding row, partnercode: $partnercode callsign: $callsign\n";
	}

	mysql_close($conn);
?>
