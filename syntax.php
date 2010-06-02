<?php
/**
 * MELLEL Plugin: Exports to MELLEL
 *
 * @author Simon Bruechner 
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_mellelexport extends DokuWiki_Syntax_Plugin {

    /**
     * return some info
     */
    function getInfo(){
        return confToHash(dirname(__FILE__).'/plugin.info.txt');
    }

    /**
     * What kind of syntax are we?
     */
    function getType(){
        return 'substition';
    }

    /**
     * What about paragraphs?
     */
    function getPType(){
        return 'normal';
    }

    /**
     * Where to sort in?
     */
    function getSort(){
        return 318; // Before image detection, which uses {{...}} and is 320
    }

    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('~~MELLEL~~',$mode,'plugin_mellelexport');
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, &$handler){
        // Export button
        if ($match == '~~MELLEL~~') { return array(); }
    }

    /**
     * Create output
     */
    function render($format, &$renderer, $data) {
        global $ID, $REV;
        if (!$data) { // Export button
            if($format != 'xhtml') return false;
            $renderer->doc .= '<a href="'.exportlink($ID, 'mellelexport', ($REV != '' ? 'rev='.$REV : '')).'" title="'.'Export page to Redit Mellel format'.'">';
            $renderer->doc .= '<img src="'.DOKU_BASE.'lib/plugins/mellelexport/MellelDocument.png" align="right" alt="'.'Export page to Redit Mellel format'.'" width="48" height="48" />';
            $renderer->doc .= '</a>';
            return true;
        } else { // Extended info
            list($info_type, $info_value) = $data;
            if ($info_type == "template") { // Template-based export
                $renderer->template = $info_value;
            }
        }
        return false;
    }

}