<?php
if(!defined('DOKU_INC')) die();

class helper_plugin_proza extends dokuwiki_plugin {

	function groups() {
		global $conf;
		$this->loadConfig();
		$groups = array_filter($conf['plugin']['proza'],
				function($v, $k) { return $v && strpos($k, 'grp') === 0; },
				ARRAY_FILTER_USE_BOTH);
		//wczytaj język
		$lang = array();
		@include(DOKU_PLUGIN.'proza/lang/en/settings.php');
		if ($this->lang_code != 'en') @include(DOKU_PLUGIN.'proza/lang/'.$this->lang_code.'/settings.php');
		$grp = array();
		foreach ($groups as $g => $v) {
			$grp[$g] = $lang[$g];
		}
		return $grp;
	}

	function users() {
		global $auth;
		$adata = $auth->retrieveUsers();

		$anames = array();
		foreach ($adata as $nick => $data)
			$anames[$nick] = $data['name'];
		return $anames;
	}
}
