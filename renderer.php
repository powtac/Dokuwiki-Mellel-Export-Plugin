<?php
/**
 * DokuWiki Plugin mellelexport (Renderer Component)
 *
 * @author  Simon Brüchner <powtac at gmx dot de>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");

require_once DOKU_INC . 'inc/parser/renderer.php';
require_once DOKU_INC . 'inc/html.php';


class renderer_plugin_mellelexport extends Doku_Renderer {
    var $info = array(
        'cache' => false, // may the rendered result cached?
        'toc'   => false, // render the TOC?
    );
    
    function getInfo(){
        return confToHash(dirname(__FILE__).'/plugin.info.txt');
    }
	
    function getFormat(){
        return 'mellelexport';
    }

    function isSingleton(){
        return false;
    }
    
    function renderer_plugin_mellelexport() {
    	require_once dirname(__FILE__).'/mapping.php';
    	$this->conf['m'] = $m;
    }

    function document_start() {
        global $ID;

        parent::document_start();
		#echo '.';
        // If older or equal to 2007-06-26, we need to disable caching
        $dw_version = preg_replace('/[^\d]/', '', getversion());
        if (version_compare($dw_version, "20070626", "<=")) {
            $this->info["cache"] = false;
        }
		
		$contentType 		= class_exists('ZipArchive') ? 'application/zip' 	: 'text/xml';
		$contentFileName 	= class_exists('ZipArchive') ? noNS($ID).'.mellel' 	: 'main.xml';
		
		
        // send the content type header, new method after 2007-06-26 (handles caching)
        if (version_compare($dw_version, "20070626")) {
            // store the content type headers in metadata
            $headers = array(
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="'.$contentFileName.'";',
            );
           # p_set_metadata($ID, array('format' => array('mellelexport' => $headers) ));
        } else { // older method
            header('Content-Type: '.$contentType);
            #header('Content-Disposition: attachment; filename="'.$contentFileName.'";');
        }
    }

    function document_end(){
		global $ID;
		
//		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'mellelconvert.php';
//		
//		$this->doc = mellelconvert(rawWiki($ID));

		$template = file_get_contents(dirname(__FILE__).'/template.txt');
		
		$this->doc = str_replace('{{CONTENT}}', $this->doc, $template);
		
		$zip = true;
		#$zip = false;
		
		if ($zip AND class_exists('ZipArchive')) {
			
			$zip = new ZipArchive();
			
			$tmpZipFile = tempnam(sys_get_temp_dir().'/', 'aaa_inge_wiki_2_mellel_render_');
			$res = $zip->open($tmpZipFile, ZipArchive::CREATE);
			if ($res === TRUE) {
			    $zip->addFromString('main.xml', $this->doc);
			    $zip->addFromString('.redlex', '');
			    $zip->close();
			    
			    $this->doc = file_get_contents($tmpZipFile);
			    @unlink($tmpZipFile);
			}
		}
		
//		$this->doc = htmlentities($this->doc);
		#echo '<pre>';
		#header('Content-Type: text/xml');
		#echo $this->doc;
    }
    
    function __call($name, $arguments) {
       	$m 		= $this->conf['m'];
		$args 	= func_get_args();
		
		#echo '.';
		#var_dump($args);
		
		array_shift($args);
		$args	= $args[0];
       	
       	if (substr($name, -5) === '_open') {
       		$type 	= 'OPEN';
       		$multi 	= true;
       	} elseif (substr($name, -6) === '_close') {
       		$type 	= 'CLOSE';
       		$multi 	= true;
       	} else {
       		$type = 'SINGLE';
       		$multi 	= false;
       	}
       	
       	
       	$tag = str_replace(array('_open', '_close'), '', $name); // not nice but short
       	
       	// Debug
       	#echo '&nbsp;';
       	#echo 'Tag:'.$tag.' Type:'.$type.' Args:'.var_export($args, 1).'<br />';  
       	
       	
       	if (isset($m[$tag])) {
       		$mapping = $m[$tag];
       	} else {
       		foreach ($m as $key => $value) {
       			if (in_array($tag, $value['alias'])) {
       				$mapping = $m[$key];
       				break;
       			}
       		}
       	}
       	
       	if (!is_array($mapping)) {
       		// echo 'No mapping found for function "'.$name.'()" and tag "'.$tag.'"';
       		#$this->doc .= '';
       		#echo 'Tag:'.$tag.' Type:'.$type.' Args:'.var_export($args, 1).'<br />';  
       		return;
       	} else {
       		#echo 'tag '.$tag.' found'.PHP_EOL;
       	}
       	
       	// Get the corresponding part of the template
		$templateParts = explode($mapping['replacement'], $mapping['template']);
		
		
       	switch ($type) {
       		case 'OPEN':
       			$doc = self::cleanTemplate($templateParts[0]);
   			break;
   			
       		case 'CLOSE':
       			$doc = self::cleanTemplate($templateParts[1]);
       		break;
       		
       		case 'SINGLE':
       			$doc = str_replace($mapping['replacement'], $args[0], self::cleanTemplate($mapping['template']));
       		break;
       		
       		default:
       			die('No type set');
       	}
       	
       	
       	// Check the given arguments and parse additional information
       	if (!$multi) {
   			array_shift($args); // remove the first entry
       	}
       	
       	if (isset($args)) {
       		$doc = str_replace($mapping['subpattern'], $args, $doc);
       	}
       	
       	if ($tag === 'externallink') {
       		$doc = urlencode($doc);
       	}
       	
       	
       	$this->doc .= $doc;
    }
    
    static function cleanTemplate($xml) {
    	return preg_replace('~[\r|\n]\s*<~', '<', trim($xml));
    }
	
	
	
	
	// Dummy mappings of method to __call()
    function render_TOC() { return ''; }

    function toc_additem($id, $text, $level) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function header($text, $level, $pos) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function section_open($level) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function section_close() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function cdata($text) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function p_open() {
    	#var_dump(func_get_args());
    	
    	call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));
    	
    	
	}

    function p_close() {#var_dump(func_get_args());
    	call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function linebreak() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function hr() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function strong_open() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function strong_close() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function emphasis_open() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function emphasis_close() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function underline_open() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function underline_close() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function monospace_open() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function monospace_close() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function subscript_open() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function subscript_close() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function superscript_open() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function superscript_close() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function deleted_open() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function deleted_close() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function footnote_open() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function footnote_close() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function listu_open() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function listu_close() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function listo_open() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function listo_close() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function listitem_open($level) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function listitem_close() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function listcontent_open() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function listcontent_close() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function unformatted($text) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function php($text) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function phpblock($text) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function html($text) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function htmlblock($text) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function preformatted($text) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function quote_open() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function quote_close() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function file($text, $lang = null, $file = null ) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function code($text, $lang = null, $file = null ) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function acronym($acronym) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function smiley($smiley) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function wordblock($word) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function entity($entity) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    // 640x480 ($x=640, $y=480)
    function multiplyentity($x, $y) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function singlequoteopening() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function singlequoteclosing() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function apostrophe() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function doublequoteopening() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function doublequoteclosing() {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    // $link like 'SomePage'
    function camelcaselink($link) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function locallink($hash, $name = NULL) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    // $link like 'wiki:syntax', $title could be an array (media)
    function internallink($link, $title = NULL) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    // $link is full URL with scheme, $title could be an array (media)
    function externallink($link, $title = NULL) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function rss ($url,$params) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    // $link is the original link - probably not much use
    // $wikiName is an indentifier for the wiki
    // $wikiUri is the URL fragment to append to some known URL
    function interwikilink($link, $title = NULL, $wikiName, $wikiUri) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    // Link to file on users OS, $title could be an array (media)
    function filelink($link, $title = NULL) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    // Link to a Windows share, , $title could be an array (media)
    function windowssharelink($link, $title = NULL) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

//  function email($address, $title = NULL) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}
    function emaillink($address, $name = NULL) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function internalmedia ($src, $title=NULL, $align=NULL, $width=NULL,
                            $height=NULL, $cache=NULL, $linking=NULL) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function externalmedia ($src, $title=NULL, $align=NULL, $width=NULL,
                            $height=NULL, $cache=NULL, $linking=NULL) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function internalmedialink (
        $src,$title=NULL,$align=NULL,$width=NULL,$height=NULL,$cache=NULL
        ) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function externalmedialink(
        $src,$title=NULL,$align=NULL,$width=NULL,$height=NULL,$cache=NULL
        ) {call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function table_open($maxcols = null, $numrows = null, $pos = null){call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function table_close($pos = null){call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function tablerow_open(){call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function tablerow_close(){call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function tableheader_open($colspan = 1, $align = NULL, $rowspan = 1){call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function tableheader_close(){call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function tablecell_open($colspan = 1, $align = NULL, $rowspan = 1){call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

    function tablecell_close(){call_user_func_array(array($this, '__call'), array(__FUNCTION__, func_get_args()));}

}