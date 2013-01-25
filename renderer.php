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
require_once DOKU_INC.'inc/parser/renderer.php';

class renderer_plugin_mellelexport extends Doku_Renderer {

    function getInfo(){
        return confToHash(dirname(__FILE__).'/plugin.info.txt');
    }
	
    function getFormat(){
        return 'mellelexport';
    }

    function isSingleton(){
        return true;
    }
    
    function loadConfig() {
    	require_once dirname(__FILE__).'/mapping.php';
    	
    	$this->conf['m'] = $m;
    	
    	parent::loadConfig();
    }

    function document_start() {
        global $ID;
        // parent::document_start();
		
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
            p_set_metadata($ID, array('format' => array('mellelexport' => $headers) ));
        } else { // older method
            header('Content-Type: '.$contentType);
            header('Content-Disposition: attachment; filename="'.$contentFileName.'";');
        }
    }

    function document_end(){
		global $ID;
		
//		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'mellelconvert.php';
//		
//		$this->doc = mellelconvert(rawWiki($ID));
    }
    
    public function __call($name, $arguments) {
       	$m 		= $this->conf['m'];
		$args 	= func_get_args();
       	
       	if (substr($name, -6) === '_open') {
       		$type 	= 'OPEN';
       		$multi 	= true;
       	} elseif (substr($name, -6) === '_close') {
       		$type = 'OPEN';
       		$multi 	= true;
       	} else {
       		$type = 'SINGLE';
       		$multi 	= false;
       	}
       	
       	$tag = rtrim($name, '_open', '_close');
       	
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
       	
       	if (!strlen($mapping)) {
       		echo 'No mapping found for function "'.$name.'()" and tag "'.$tag.'"';
       	}
       	
       	// Get the corresponding part of the template
		$templateParts = explode($mapping['replacement'], $mapping['template']);
       	switch ($type) {
       		case 'OPEN':
       			$doc = $templateParts[0];
   			break;
   			
       		case 'CLOSE':       			
       			$doc = $templateParts[1];
       		break;
       		
       		case 'SINGLE':
       			$doc = str_replace($mapping['replacement'], $args[0]);
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
       	
       	$this->doc .= $doc;
    }
}