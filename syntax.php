<?php
if(!defined('DOKU_INC')) die();

class syntax_plugin_abwesenheitsmatrixpopup extends DokuWiki_Syntax_Plugin {

    public function getType(){ return 'substition'; }
    public function getPType(){ return 'block'; }
    public function getSort(){ return 999; }

    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern("{{abwesenheitsmatrixpopup}}", $mode, 'plugin_abwesenheitsmatrixpopup');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler){
        return [];
    }

    public function render($mode, Doku_Renderer $renderer, $data) {
        if($mode != 'xhtml') return false;

        $url = DOKU_BASE . 'lib/plugins/abwesenheitsmatrixpopup/matrix.html';
        $renderer->doc .= "<button onclick="window.open('$url','_blank','width=1200,height=800');">📝 Bearbeiten</button>";
        return true;
    }
}
