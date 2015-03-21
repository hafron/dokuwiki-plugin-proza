<?php
// must be run within DokuWiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */

require_once DOKU_PLUGIN."proza/mdl/events.php";

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
		global $conf, $INFO;

		if ($mode != 'xhtml') return false;

        $R->info['cache'] = false;

		$data = array(
		'proza:start' => array('id' => 'proza:start:coordinator:'.$INFO['client'], 'type' => 'd', 'level' => 1, 'title' => $this->getLang('proza')),
		);

		if (isset($this->params['proza'])) { 

			$data['proza:start']['open'] = true;

			$helper = $this->loadHelper('proza');
			foreach ($helper->groups() as $g => $lang) {
				$id = 'proza:events:group:'.$g.':year:'.date('Y');
				$data[$id] = array('id' => $id, 'type' => 'd', 'level' => 2, 'title' => $lang);

				if ($this->params['group'] == $g) {
					$data[$id]['open'] = true;

					$id = 'proza:event:group:'.$g;
					$data[$id] = array('id' => $id, 'type' => 'f', 'level' => 3, 'title' => $this->getLang('add_event'));
					if ($helper->user_admin()) {
						$id = 'proza:categories:group:'.$g;
						$data[$id] = array('id' => $id, 'type' => 'f', 'level' => 3, 'title' => $this->getLang('t_categories'));
					}
					$id = 'proza:report:group:'.$g;
					$data[$id] = array('id' => $id, 'type' => 'd', 'level' => 3, 'title' => $this->getLang('t_report'));

					if ($this->params['proza'] == 'report' && $this->params['group'] == $g) {
						$data[$id]['open'] = true;
						$db = new DB();
						$events = $db->spawn('events');
						$years = $events->years($this->params['group']);
						foreach ($years as $year) {
							$id = 'proza:report:group:'.$g.':year:'.$year;
							$data[$id] = array('id' => $id, 'type' => 'f', 'level' => 4, 'title' => $year);
						}
					}
				}
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
		$fields = array('proza', 'group', 'year');

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
