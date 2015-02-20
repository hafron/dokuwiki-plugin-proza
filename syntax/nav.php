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
	private $params = array();

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
			$this->params[urldecode($ex[$i])] = urldecode($ex[$i+1]);
	}

    function handle($match, $state, $pos, &$handler) {
		return true;
    }

    function render($mode, &$R, $pass) {
		global $conf;

		if ($mode != 'xhtml') return false;

        $R->info['cache'] = false;

		$data = array(
			'proza:start' => array('id' => 'proza:start', 'type' => 'd', 'level' => 1, 'title' => $this->getLang('proza')),
		);

		$helper = $this->loadHelper('proza');
		foreach ($helper->groups() as $g => $lang) {
			$id = 'proza:entities:group:'.$g;
			$data[$id] = array('id' => $id, 'type' => 'f', 'level' => 2, 'title' => $lang);
		}

		if (isset($this->params['proza']))
			$data['proza:start']['open'] = true;
		else {
			$data['proza:start']['open'] = false;
			array_splice($data, 1);
		}

        $R->doc .= html_buildlist($data,'idx',array($this,'_list'),array($this,'_li'));
		return true;
	}

	function _prozalink($id, $title) {
		$uri = DOKU_URL . 'doku.php?id='.$id;
		return '<a href="'.$uri.'">'.($title).'</a>';
	}

    function _list($item){

		$ex = explode(':', $item['id']);

		for ($i = 0; $i < count($ex); $i += 2)
			$item_value[urldecode($ex[$i])] = urldecode($ex[$i+1]);

		//pola brane pod uwagę przy określaniu aktualnej strony
		$fields = array('proza', 'group');

		$actual_page = true;
		foreach ($fields as $field)
			if ($item_value[$field] != $this->params[$field])
				$actual_page = false;



        if(($item['type'] == 'd' && $item['open']) ||  $actual_page) {
            return '<strong>'.$this->_prozalink($this->lang_code.$item['id'], $item['title']).'</strong>';
        }else{
            return $this->_prozalink($this->lang_code.$item['id'], $item['title']);
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
