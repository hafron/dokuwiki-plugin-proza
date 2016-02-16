<?php
 
if(!defined('DOKU_INC')) die();
 
class action_plugin_proza extends DokuWiki_Action_Plugin {

	private $action = '';
	private $params = array();
	private $t = array();
	private $preventDefault;
	private $errors=array();

	private $default_lang = 'pl';

	/**
	 * Register its handlers with the DokuWiki's event controller
	 */
	function register(Doku_Event_Handler $controller) {
		$controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'action_act_preprocess');
		$controller->register_hook('TPL_ACT_RENDER', 'BEFORE', $this, 'tpl_act_render');
		$controller->register_hook('TEMPLATE_PAGETOOLS_DISPLAY', 'BEFORE', $this, 'tpl_pagetools_display');
		$controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this, '_ajax_call');
		/*$controller->register_hook('WIKI_RESOLVE_PAGEID', 'BEFORE', $this, 'proza_internallinks');*/
	}

	function __construct() {
		global $ACT, $conf;

		$id = $_GET['id'];
		/*usuń : z początku id - link bezwzględny*/
		if ($id[0] == ':')
			$id = substr($id, 1);

		$ex = explode(':', $id);
		if ($ex[0] == 'proza' && $ACT == 'show') {
			$this->action = $ex[1];
			$ex = array_slice($ex, 2);
			$this->lang_code = $this->default_lang;
		/*proza w innym języku*/
		} else if ($ex[1] == 'proza' && $ACT == 'show') {
			$this->lang_code = $ex[0];

			$old_lang = $conf['lang'];
			$conf['lang'] = $this->lang_code;
			$this->setupLocale();
			$conf['lang'] = $old_lang;

			$this->action = $ex[2];
			$ex = array_slice($ex, 3);
		}
		for ($i = 0; $i < count($ex); $i += 2)
			$this->params[urldecode($ex[$i])] = urldecode($ex[$i+1]);

	}

	function _ajax_call(&$event, $param) {
		if ($event->data !== 'plugin_proza') return;
		$event->stopPropagation();
		$event->preventDefault();

		$date = $_POST['date'];

		require_once DOKU_INC . 'inc/JSON.php';
		$json = new JSON();

		$data = array();
		if (strtotime($date)) {
			$ev = array('plan_date' => $date);
			$helper = $this->loadHelper('proza');
			$data['status'] = 'success';
			$data['class'] = $helper->event_class($ev);
			$data['date'] = $helper->norm_date($date);
		} else {
			$data['status'] = 'error';
			$data['msg'] = $this->getLang('e_date');
		}

		header('Content-Type: application/json');
		echo $json->encode($data);
	}
	
	/*function proza_internallinks(&$event, $param) {
	
		if (strpos($event->data['page'], 'proza') !== 0)  return false;
		$event->preventDefault();
		$event->data['exists'] = true;
		return true;
	}*/

	function preventDefault() {
		throw new Exception('preventDefault');
	}

	function id() {
		$args = func_get_args();

		if (is_array($args[0]))
			$a = $args[0];
		else
			$a = $args;

		array_unshift($a, 'proza');
		if ($this->lang_code != $this->default_lang)
			array_unshift($a, $this->lang_code);

		return implode(':', $a);
	}

	function display_error($error) {
		echo '<div class="error">';
		echo $error;
		echo '</div>';
	}

	function display_validation_errors($errors) {
		if ( ! is_array($errors)) return;

		foreach ($errors as $e) {
			if ($e[0] == '')
				$this->display_error($e[1]);
			else
				$this->display_error($this->getLang('h_'.$e[0]).': '.$this->getLang('e_'.$e[1]));
		}
	}

	function tpl_pagetools_display($event, $param) {
		if ($this->action != '')  
			$event->preventDefault();
	}

	function action_act_preprocess($event, $param) {
		global $conf;	

		if ($this->action == '') return;

		$ctl = DOKU_PLUGIN."proza/ctl/".str_replace('/', '', $this->action).".php";
		if (file_exists($ctl)) {
			//wczytaj konfigurację
			$this->loadConfig();
			try {
				require $ctl;
			} catch(Proza_DBException $e) {
				$this->errors[] = array('', $e->getMessage());
			} catch(Exception $e) {
				//preventDefault
				$this->preventDefault = true;
			}
		}
	}

	function tpl_act_render($event, $param) {
		if ($this->action == '') return;
		
		if (count($this->errors) > 0) {
			$this->display_validation_errors($this->errors); 
		} else if ( ! $this->preventDefault && $this->action != '') {
			$tpl = DOKU_PLUGIN."proza/tpl/".str_replace('/', '', $this->action).".php";
			if (file_exists($tpl))
				require $tpl;
		}
		$event->preventDefault();
	}
}
