<?php
// must be run within DokuWiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_proza_nav extends DokuWiki_Syntax_Plugin {
	private $lang_code = '';
	private $page_params = array();

    function getPType() { return 'block'; }
    function getType() { return 'substition'; }
    function getSort() { return 99; }


    function connectTo($mode) {
		$this->Lexer->addSpecialPattern('~~PROZANAV~~',$mode,'plugin_proza_nav');
    }

	function __construct() {
		$ex = explode(':', $_GET['id']);
		//wielojęzyczność
		if ($ex[1] == 'proza') {
			$this->lang_code = $ex[0];
			$ex = array_slice($ex, 1);
		}

		for ($i = 0; $i < count($ex); $i += 2)
			$this->page_params[urldecode($ex[$i])] = urldecode($ex[$i+1]);
	}

    function handle($match, $state, $pos, &$handler) {
		return true;
    }

    function render($mode, &$R, $pass) {

		if ($mode != 'xhtml') return false;

        $R->info['cache'] = false;

		$data = array(
			'proza:start' => array('id' => 'proza:start', 'type' => 'd', 'level' => 1, 'title' => $this->getLang('proza')),
		);

        $R->doc .= html_buildlist($data,'idx',array($this,'_list'),array($this,'_li'));

		return true;
	}

	function _bezlink($id, $title) {
		//$uri = wl($id);
		$uri = DOKU_URL . 'doku.php?id='.$id;
		return '<a href="'.$uri.'">'.($title).'</a>';
	}

    function _list($item){

		$ex = explode(':', $item['id']);

		for ($i = 0; $i < count($ex); $i += 2)
			$item_value[urldecode($ex[$i])] = urldecode($ex[$i+1]);

		//pola brane pod uwagę przy określaniu aktualnej strony
		$fields = array('bez');
		if ($item_value['bez'] == 'report') {
			$fields[] = 'month';
			$fields[] = 'year';
		}

		$actual_page = true;
		foreach ($fields as $field)
			if ($item_value[$field] != $this->value[$field])
				$actual_page = false;



        if(($item['type'] == 'd' && $item['open']) ||  $actual_page) {
            return '<strong>'.$this->_bezlink($this->lang_code.$item['id'], $item['title']).'</strong>';
        }else{
            return $this->_bezlink($this->lang_code.$item['id'], $item['title']);
        }

    }

    function _li($item){
        if($item['type'] == "f"){
            return '<li class="level'.$item['level'].'">';
        }elseif($item['open']){
            return '<li class="open">';
        }else{
            return '<li class="closed">';
        }
    }
}
