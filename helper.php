<?php
if(!defined('DOKU_INC')) die();

class helper_plugin_proza extends Dokuwiki_Plugin {

	function groups($lang_code) {
		global $conf;
		$this->loadConfig();

		$groups = array();
		foreach ($conf['plugin']['proza'] as $k => $v)
			if (strpos($k, 'grp') === 0 && $this->getConf($k) == 1)
				$groups[$k] = $v;

		//wczytaj język
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
		global $auth;
		$adata = $auth->retrieveUsers();

		return 'mailto:'.$adata[$to]['mail'].'?subject='.rawurlencode($subject).'&body='.rawurlencode($body);
	}

	function mail($to, $subject, $body, $URI='', $debug = false) {
		global $auth;
		$adata = $auth->retrieveUsers();

		$headers = 	"From: noreply@$URI\n";
		$headers .= "Content-Type: text/plain; charset=UTF-8\n"; 
		$headers .= "Content-Transfer-Encoding: 8bit\n";

		$rec = $adata[$to]['name']." <".$adata[$to]['mail'].">";
		if ($debug) {
			echo $rec."\n";
			echo $subject."\n";
			echo $body."\n";
			echo $headers."\n";
			echo "\n\n";
			return;
		}

		if ($URI == '')
			$URI = $_SERVER['SERVER_NAME'];
		mail($rec, $subject, $body, $headers);
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

	function user_eventeditor($ev) {
		global $INFO;

		if (self::user_viewer())
			if ($ev['coordinator'] == $INFO['client'] || self::user_admin())
				return true;

		return false;
	}
}
