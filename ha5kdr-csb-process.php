#!/usr/bin/php5
<?php
	ini_set('display_errors','On');
	error_reporting(E_ALL);

	include('ha5kdr-csb-config.inc.php');

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

	function sendfailemail() {
		$header = 'From: ' . PROCESSFAIL_MAIL_FROM . "\nReply-To: " . PROCESSFAIL_MAIL_FROM . "\nMIME-Version: 1.0\n";
		$header .= "Content-type: text/plain; charset=UTF-8";

		$subject = 'Callsign book processing error';
		$msg = 'Error processing downloaded callsign book data!';

		mail(PROCESSFAIL_MAIL_TO, '=?UTF-8?B?' . base64_encode($subject) .'?=', $msg, $header);
	}

	if ($argc < 2) {
		echo "usage: ${argv[0]} [xml file]\n";
		return 1;
	}

	$reader = new XMLReader;
	@$reader->open($argv[1]);

	if (!$reader) {
		echo "can't open ${argv[1]}!\n";
		sendfailemail();
		return 1;
	}

	// Moving to the first <Row /> node which contain user data
	while ($reader->read() && $reader->name !== 'Row')
		;

	if ($reader->name !== 'Row') {
		echo "can't find beginning of data!\n";
		sendfailemail();
		return 1;
	}

	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if (!$conn) {
		echo "can't connect to mysql database!\n";
		sendfailemail();
		return 1;
	}

	$conn->query("set names 'utf8'");
	$conn->query("set charset 'utf8'");

	if (!$conn->query('truncate table `' . DB_TABLE . '`')) {
		echo "can't truncate table\n";
		sendfailemail();
		return 1;
	}

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

		$res = $conn->query('insert into `' . DB_TABLE . '` (' .
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
			'"' . $conn->escape_string($partnercode) . '", ' .
			'"' . $conn->escape_string($name) . '", ' .
			'"' . $conn->escape_string($country) . '", ' .
			'"' . $conn->escape_string($zip) . '", ' .
			'"' . $conn->escape_string($city) . '", ' .
			'"' . $conn->escape_string($streethouse) . '", ' .
			'"' . $conn->escape_string($licensenumber) . '", ' .
			'"' . $conn->escape_string($callsign) . '", ' .
			'"' . $conn->escape_string($communityorprivate) . '", ' .
			'"' . $conn->escape_string($state) . '", ' .
			'"' . $conn->escape_string($levelofexam) . '", ' .
			'"' . $conn->escape_string($morse) . '", ' .
			'"' . $conn->escape_string($licensedate) . '", ' .
			'"' . $conn->escape_string($validity) . '", ' .
			'"' . $conn->escape_string($chiefoperator) . '")');

		if (!$res)
			echo "error adding row, partnercode: $partnercode callsign: $callsign\n";
	}

	$result = $conn->query('select count(*) as `recordcount` from `' . DB_TABLE . '`');
	$row = $result->fetch_array();
	$recordcount = $row['recordcount'];

	if ($recordcount < 1000)
		sendfailemail();

	$conn->close();
?>
