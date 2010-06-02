<?php
/**
 * DokuWiki Plugin mellelexport (Renderer Component)
 *
 * @author  Simon Br�chner <powtac at gmx dot de>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
require_once DOKU_INC.'inc/parser/renderer.php';

class renderer_plugin_mellelexport extends Doku_Renderer {

    function getFormat(){
        return 'mellelexport';
    }

    function isSingleton(){
        return true;
    }

    function document_start() {
        parent::document_start();
		
        // If older or equal to 2007-06-26, we need to disable caching
        $dw_version = preg_replace('/[^\d]/', '', getversion());
        if (version_compare($dw_version, "20070626", "<=")) {
            $this->info["cache"] = false;
        }

        // send the content type header, new method after 2007-06-26 (handles caching)
        if (version_compare($dw_version, "20070626")) {
            // store the content type headers in metadata
            $headers = array(
                'Content-Type' => 'text/xml',
                'Content-Disposition' => 'attachment; filename="main.xml";',
            );
            p_set_metadata($ID,array('format' => array('mellelexport' => $headers) ));
        } else { // older method
            header('Content-Type: text/xml');
            header('Content-Disposition: attachment; filename="main.xml";');
        }
    }

    function document_end(){
		global $ID;
		
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'mellelconvert.php';
		
		$this->doc = mellelconvert(rawWiki($ID));
    }
}