<?php
/**
 * Plugin Name: HA5KDR Callsign Book
 * Plugin URI: https://github.com/nonoo/ha5kdr-csb
 * Description: Displays a searchable callsign book table.
 * Version: 1.0
 * Author: Nonoo
 * Author URI: http://dp.nonoo.hu/
 * License: MIT
*/

function ha5kdrcsb_generate() {
	$out = '<img id="ha5kdr-csb-loader" src="' . plugins_url('loader.gif', __FILE__) . '" />' . "\n";
	$out .= '<form id="ha5kdr-csb-search">' . "\n";
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
	$out .= '				callsign: { title: "' . __('Callsign', 'ha5kdr-csb') . '" },' . "\n";
	$out .= '				partnercode: { title: "' . __('Partnercode', 'ha5kdr-csb') . '", key: true, visibility: "hidden" },' . "\n";
	$out .= '				name: { title: "' . __('Name', 'ha5kdr-csb') . '", width: "15%" },' . "\n";
	$out .= '				country: { title: "' . __('Country', 'ha5kdr-csb') . '", visibility: "hidden" },' . "\n";
	$out .= '				zip: { title: "' . __('ZIP', 'ha5kdr-csb') . '", visibility: "hidden" },' . "\n";
	$out .= '				city: { title: "' . __('City', 'ha5kdr-csb') . '" },' . "\n";
	$out .= '				streethouse: { title: "' . __('Address', 'ha5kdr-csb') . '", width: "15%" },' . "\n";
	$out .= '				licensenumber: { title: "' . __('Licensenum', 'ha5kdr-csb') . '", visibility: "hidden" },' . "\n";
	$out .= '				communityorprivate: { title: "' . __('Type', 'ha5kdr-csb') . '" },' . "\n";
	$out .= '				state: { title: "' . __('State', 'ha5kdr-csb') . '", visibility: "hidden" },' . "\n";
	$out .= '				levelofexam: { title: "' . __('Level', 'ha5kdr-csb') . '" },' . "\n";
	$out .= '				morse: { title: "' . __('Morse', 'ha5kdr-csb') . '" },' . "\n";
	$out .= '				licensedate: { title: "' . __('Date', 'ha5kdr-csb') . '", type: "date", width: "15%", visibility: "hidden" },' . "\n";
	$out .= '				validity: { title: "' . __('Valid', 'ha5kdr-csb') . '", type: "date", width: "15%" },' . "\n";
	$out .= '				chiefoperator: { title: "' . __('Operator', 'ha5kdr-csb') . '", visibility: "hidden" }' . "\n";
	$out .= '			}' . "\n";
	$out .= '		});' . "\n";
	$out .= '		function csb_update_showloader() {' . "\n";
	$out .= '			$("#ha5kdr-csb-loader").fadeIn();' . "\n";
	$out .= '		}' . "\n";
	$out .= '		function csb_update_hideloader() {' . "\n";
	$out .= '			$("#ha5kdr-csb-loader").fadeOut();' . "\n";
	$out .= '		}' . "\n";
	$out .= '		$("#ha5kdr-csb-search-button").click(function (e) {' . "\n";
	$out .= '			e.preventDefault();' . "\n";
	$out .= '			csb_update_showloader();' . "\n";
	$out .= '			$("#ha5kdr-csb-container").jtable("load", {' . "\n";
	$out .= '				searchfor: $("#ha5kdr-csb-search-string").val()' . "\n";
	$out .= '			}, csb_update_hideloader);' . "\n";
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
load_plugin_textdomain('ha5kdr-csb', false, basename(dirname(__FILE__)) . '/languages');
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
