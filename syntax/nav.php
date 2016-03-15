<?php
// must be run within DokuWiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */

require_once DOKU_PLUGIN."proza/mdl/events.php";
require_once DOKU_PLUGIN."proza/mdl/groups.php";

class syntax_plugin_proza_nav extends DokuWiki_Syntax_Plugin {
	private $default_lang = 'pl';
	private $params = array();

    function getPType() { return 'block'; }
    function getType() { return 'substition'; }
    function getSort() { return 99; }


    function connectTo($mode) {
		$this->Lexer->addSpecialPattern('~~PROZANAV~~',$mode,'plugin_proza_nav');
    }

	function __construct() {
		global $conf;

		$id = $_GET['id'];

		/*usuń : z początku id - link bezwzględny*/
		if ($id[0] == ':')
			$id = substr($id, 1);

		$ex = explode(':', $_GET['id']);

		//wielojęzyczność
		if ($ex[1] == 'proza') {
			$this->lang_code = $ex[0];
			$ex = array_slice($ex, 1);

			$old_lang = $conf['lang'];
			$conf['lang'] = $this->lang_code;
			$this->setupLocale();
			$conf['lang'] = $old_lang;

		} else {
			$this->lang_code = $conf['lang'];
		}
		for ($i = 0; $i < count($ex); $i += 2)
			$this->params[urldecode($ex[$i])] = urldecode($ex[$i+1]);
	}

    function handle($match, $state, $pos, Doku_Handler $handler) {
		return true;
    }

    function render($mode, Doku_Renderer $R, $pass) {
		global $conf, $INFO;

		if ($mode != 'xhtml') return false;
		$helper = $this->loadHelper('proza');
		
		if (!$helper->user_viewer()) return false;
		
		$R->info['cache'] = false;

		$data = array(
		'proza:start' => array('id' => 'proza:events:state:0:coordinator:'.$INFO['client'], 'type' => 'd', 'level' => 1, 'title' => $this->getLang('proza')),
		);

		if (isset($this->params['proza'])) { 

			$data['proza:start']['open'] = true;

			
			$db = new DB();
			$groups = $db->spawn('groups');
			foreach ($groups->groups($this->lang_code) as $g => $lang) {
				$id = 'proza:events:group_n:'.$g.':year:'.date('Y');
				$data[$id] = array('id' => $id, 'type' => 'd', 'level' => 2, 'title' => $lang);
				if ($this->params['group_n'] == $g) {
					$data[$id]['open'] = true;

					$id = 'proza:event:group_n:'.$g;
					$data[$id] = array('id' => $id, 'type' => 'f', 'level' => 3,
						'title' => $this->getLang('add_event'));
				}
			}

			$id = 'proza:repglob';
			$data[$id] = array('id' => $id, 'type' => 'd', 'level' => 2, 'title' => $this->getLang('repglob'));
			if ($this->params['proza'] == 'repglob') {
				$data[$id]['open'] = true;
				$db = new DB();
				$events = $db->spawn('events');
				$years = $events->years();
				foreach ($years as $year) {
					$id = 'proza:repglob:year:'.$year;
					$data[$id] = array('id' => $id, 'type' => 'f', 'level' => 3, 'title' => $year);
				}
			}
			if ($helper->user_admin()) {
				$id = 'proza:groups';
				$data[$id] = array('id' => $id, 'type' => 'f', 'level' => 2, 'title' => $this->getLang('groups'));
				if ($this->params['proza'] == 'groups')
					$data[$id]['open'] = true;
			}
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
		$fields = array('proza', 'group_n', 'year');

		$actual_page = true;
		foreach ($fields as $field)
			if ($item_value[$field] != $this->params[$field])
				$actual_page = false;



        if(($item['type'] == 'd' && $item['open']) ||  $actual_page) {
			$id = $item['id'];
			if ($this->lang_code != $this->default_lang)
				$id = $this->lang_code.':'.$id;
            return '<strong>'.$this->_prozalink($id, $item['title']).'</strong>';
        }else{
			$id = $item['id'];
			if ($this->lang_code != $this->default_lang)
				$id = $this->lang_code.':'.$id;
            return $this->_prozalink($id, $item['title']);
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
