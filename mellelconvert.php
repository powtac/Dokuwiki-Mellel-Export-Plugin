<?php
/*
 * wikitomellel
 * Simon Brüchner, 19.11.2007, 2010, 24.01.2013
 */
function mellelconvert($wikiMarkup,  $zip = true) {
	define(MELLEL_UEBERSCHRIFT,     '--UEBERSCHRIFT--');
	define(MELLEL_KLAMMER,          '--KLAMMER--');
	define(MELLEL_HIGHLIGHT,        '--HIGHLIGHT--');
	define(MELLEL_TEMPLATE_CONTENT, '{{CONTENT}}');

	/**
	 * Callback to cleanup wiki markup 
	 * Remove unnecessary stuff (headings, empty rows)
	 */
	function cleanup($data) {
		if (strlen(trim($data)) > 0) {
			return $data;
		}
	}

	$template   = dirname(__FILE__).DIRECTORY_SEPARATOR.'template.txt';
	$wikiMarkup = $wikiMarkup;
	$template   = file_get_contents($template);
	$rowbreak  = "\n";


	$wikiMarkup = explode($rowbreak, $wikiMarkup);
	$wikiMarkup = array_filter($wikiMarkup, 'cleanup');


	$patternNote    = "~\(\((.+)\)\)~U";
	$patternBold    = "~\*\*(.+)\*\*~U";
	$patternItalic  = "~//(.+)//~U";

	$mellelMarkup   = '';
	foreach ($wikiMarkup as $row) {
		$row = trim(utf8_encode($row));

		$row = str_replace("\n", '', $row);
		$row = str_replace(' & ', ' &amp; ', $row);
		$row = str_replace('<>', MELLEL_KLAMMER, $row);
		$row = str_replace('</hi>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #ffff00>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #ff0000>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #ffa500>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #fa8072>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #ffc0cb>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #dda0dd>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #800080>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #ff00ff>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #c0c0c0>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #00ffff>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #008080>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #6495ed>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #87ceeb>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #7fffd4>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #98fb98>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #00ff00>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #008000>', MELLEL_HIGHLIGHT, $row);
		$row = str_replace('<hi #808000>', MELLEL_HIGHLIGHT, $row);
		$row = strip_tags($row);  

		if (!strstr($row, '**') AND !strstr($row, '//') AND !strstr($row, '((')) {
			if (substr($row, 0, 2) !== '==') {
				$row = '<p style="ps-0" dir="ltr"><c style="cs-0">'.$row.'</c></p>';
			} else {
				// Headline   
				if (substr($row, 0, 6) === '======') {
					$row = '<p style="ps-2" dir="ltr"><c style="cs-5">'.trim(str_replace('=', '', $row)).'</c></p>';
				} else {
					$row = '<p style="ps-3" dir="ltr"><c style="cs-1">'.trim(str_replace('=', '', $row)).'</c></p>';
				}
			}
		} else {
			$pregResultNote     = null;
			$pregResultBold     = null;
			$pregResultItalic   = null;
			
			// Bold
			preg_match_all($patternBold, $row, $pregResultBold);
			if ($pregResultBold[1]) {
				$row = preg_replace($patternBold, "</c><c style=\"cs-1\">$1</c><c style=\"cs-0\">", $row);
			}
			
			// Italic
			preg_match_all($patternItalic, $row, $pregResultItalic);
			if ($pregResultItalic[1]) {
				$row = preg_replace($patternItalic, "</c><c style=\"cs-4\">$1</c><c style=\"cs-0\">", $row);
			}
			
			// Footnote
			preg_match_all($patternNote, $row, $pregResultNote);
			if ($pregResultNote[1]) {//                <c style='cs-1'><note stream='nsm-0'><p style='ps-1' dir='ltr'><c style='cs-2'>Footnote content</c></p></note></c>
				$row = preg_replace($patternNote, "</c><c style='cs-1'><note stream='nsm-0'><p style='ps-1' dir='ltr'><c style='cs-2'>$1</c></p></note></c><c style='cs-0'>", $row);
			}
			
			$row = '<p style="ps-0" dir="ltr"><c style="cs-0">'.$row.'</c></p>';
		}
		$mellelMarkup .= $row;
	}
	$mellelMarkup = str_replace(MELLEL_TEMPLATE_CONTENT, $mellelMarkup, $template);
	
	if ($zip AND class_exists('ZipArchive')) {
		
		$zip = new ZipArchive();
		
		$tmpZipFile = tempnam(sys_get_temp_dir().'/', 'aaa_inge_wiki_2_mellel_render_');
		$res = $zip->open($tmpZipFile, ZipArchive::CREATE);
		if ($res === TRUE) {
		    $zip->addFromString('main.xml', $mellelMarkup);
		    $zip->addFromString('.redlex', '');
		    $zip->close();
		    
		    $mellelMarkup = file_get_contents($tmpZipFile);
		    @unlink($tmpZipFile);
		}
	}

	return $mellelMarkup;
}
