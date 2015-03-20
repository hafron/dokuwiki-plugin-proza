<?php
if(!defined('DOKU_INC')) die();

class helper_plugin_proza extends Dokuwiki_Plugin {

	function groups() {
		global $conf;
		$this->loadConfig();
		$groups = array_filter($conf['plugin']['proza'],
				function($v, $k) { return $v && strpos($k, 'grp') === 0; },
				ARRAY_FILTER_USE_BOTH);
		//wczytaj język
		$lang_code = $conf['lang'];
		$lang = array();
		@include(DOKU_PLUGIN.'proza/lang/en/settings.php');
		if ($lang_code != 'en') @include(DOKU_PLUGIN.'proza/lang/'.$lang_code.'/settings.php');
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

	function username($nick) {
		global $auth;

		$adata = $auth->retrieveUsers();
		return $adata[$nick]['name'];
	}

	function mailto($to, $subject, $body) {
		return 'mailto:'.$to.'?subject='.rawurlencode($subject).'&body='.rawurlencode($body);
	}

	function norm_date($date_str='') {
		if ($date_str == '')
			return date('Y-m-d');

		$date = strtotime($date_str);
		return date('Y-m-d', $date);
	}

	function event_class($ev) {
		if (isset($ev['state']) && $ev['state'] != 0)
			return '';

		$plan_date = strtotime($ev['plan_date']);
		$d = $plan_date - time();
		if ($d <= 0)
			return 'red';
		else if ($d <= 30*24*60*60)
			return 'yellow';

		return 'green';
	}

	function days($date) {
		$d = date_create($date);
		$now = date_create('now');
		$interval = date_diff($now, $d);
		return $interval->format('%R%a '.$this->getLang('days'));
	}

	function user_admin() {
		global $INFO, $auth;

		$userd = $auth->getUserData($INFO['client']); 
		if ($userd && in_array('admin', $userd['grps']))
			return true;

		return false;
	}

	function user_viewer() {
		global $INFO, $auth;

		$userd = $auth->getUserData($INFO['client']); 
		if ($userd)
			return true;

		return false;
	}

}
