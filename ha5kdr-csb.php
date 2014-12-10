<?php
/**
 * Plugin Name: HA5KDR Callsign Book
 * Plugin URI: https://github.com/nonoo/ha5kdr-csb-wordpress-plugin
 * Description: Displays a searchable callsign book table.
 * Version: 1.0
 * Author: Nonoo
 * Author URI: http://dp.nonoo.hu/
 * License: MIT
*/

function ha5kdrcsb_generate() {
	$out = '<form id="ha5kdr-csb-search">' . "\n";
	$out .= '	<input type="text" id="ha5kdr-csb-search-string" />' . "\n";
	$out .= '	<input type="submit" id="ha5kdr-csb-search-button" value="Keresés" />' . "\n";
	$out .= '</form>' . "\n";
	$out .= '<div id="ha5kdr-csb-container"></div>' . "\n";
	$out .= '<script type="text/javascript">' . "\n";
	$out .= '	$(document).ready(function () {' . "\n";
	$out .= '		$("#ha5kdr-csb-container").jtable({' . "\n";
	$out .= '			paging: true,' . "\n";
	$out .= '			sorting: true,' . "\n";
	$out .= '			defaultSorting: "validity asc",' . "\n";
	$out .= '			actions: {' . "\n";
	$out .= '				listAction: "' . plugins_url('ha5kdr-csb-getdata.php', __FILE__) . '",' . "\n";
	$out .= '			},' . "\n";
	$out .= '			fields: {' . "\n";
	$out .= '				callsign: { title: "Hívójel" },' . "\n";
	$out .= '				partnercode: { title: "PK", key: true, visibility: "hidden" },' . "\n";
	$out .= '				name: { title: "Név", width: "15%" },' . "\n";
	$out .= '				country: { title: "Ország", visibility: "hidden" },' . "\n";
	$out .= '				zip: { title: "ISZ", visibility: "hidden" },' . "\n";
	$out .= '				city: { title: "Város" },' . "\n";
	$out .= '				streethouse: { title: "Cím", width: "15%" },' . "\n";
	$out .= '				licensenumber: { title: "ESZ", visibility: "hidden" },' . "\n";
	$out .= '				communityorprivate: { title: "Típus" },' . "\n";
	$out .= '				state: { title: "Státusz", visibility: "hidden" },' . "\n";
	$out .= '				levelofexam: { title: "Szint" },' . "\n";
	$out .= '				morse: { title: "Morze" },' . "\n";
	$out .= '				licensedate: { title: "Dátum", type: "date", width: "15%", visibility: "hidden" },' . "\n";
	$out .= '				validity: { title: "Érvényes", type: "date", width: "15%" },' . "\n";
	$out .= '				chiefoperator: { title: "Kezelő", visibility: "hidden" }' . "\n";
	$out .= '			}' . "\n";
	$out .= '		});' . "\n";
	$out .= '		$("#ha5kdr-csb-search-button").click(function (e) {' . "\n";
	$out .= '			e.preventDefault();' . "\n";
	$out .= '			$("#ha5kdr-csb-container").jtable("load", {' . "\n";
	$out .= '				searchfor: $("#ha5kdr-csb-search-string").val()' . "\n";
	$out .= '			});' . "\n";
	$out .= '		});' . "\n";
	$out .= '		$("#ha5kdr-csb-search-button").click();' . "\n";
	$out .= '	});' . "\n";
	$out .= '</script>' . "\n";

	return $out;
}

function ha5kdrcsb_filter($content) {
    $startpos = strpos($content, '<ha5kdr-csb');
    if ($startpos === false)
		return $content;

    for ($j=0; ($startpos = strpos($content, '<ha5kdr-csb', $j)) !== false;) {
		$endpos = strpos($content, '>', $startpos);
		$block = substr($content, $startpos, $endpos - $startpos + 1);

		$out = ha5kdrcsb_generate();

		$content = str_replace($block, $out, $content);
		$j = $endpos;
    }
    return $content;
}
load_plugin_textdomain('ha5kdr-csb-wordpress-plugin', false, basename(dirname(__FILE__)) . '/languages');
add_filter('the_content', 'ha5kdrcsb_filter');
add_filter('the_content_rss', 'ha5kdrcsb_filter');
add_filter('the_excerpt', 'ha5kdrcsb_filter');
add_filter('the_excerpt_rss', 'ha5kdrcsb_filter');

function ha5kdrcsb_jscss() {
	echo '<link rel="stylesheet" type="text/css" media="screen" href="' . plugins_url('jtable-theme/jtable_basic.css', __FILE__) . '" />';
	echo '<link rel="stylesheet" type="text/css" media="screen" href="' . plugins_url('ha5kdr-csb.css', __FILE__) . '" />';
}
add_action('wp_head', 'ha5kdrcsb_jscss');
?>
