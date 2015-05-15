<?php
$inc = realpath(__DIR__.'/../../..');

define('DOKU_INC', $inc.'/');
define('DOKU_PLUGIN', $inc.'/lib/plugins/');
define('DOKU_CONF', $inc.'/conf/');

if (count($argv) < 2)
	die("podaj URI wiki dla którego odpalasz tego crona\n");
$URI = $argv[1];

if (isset($argv[2]) && $argv[2] == 'https')
	$http = 'https';
else
	$http = 'http';

$config_cascade = array();
require_once DOKU_INC.'inc/config_cascade.php';

$conf = array();
// load the global config file(s)
foreach (array('default','local','protected') as $config_group) {
	if (empty($config_cascade['main'][$config_group])) continue;
	foreach ($config_cascade['main'][$config_group] as $config_file) {
		if (file_exists($config_file)) {
		include($config_file);
		}
	}
}

require_once DOKU_INC.'inc/plugin.php';
require_once DOKU_INC.'inc/plugincontroller.class.php';
$plugin_controller = new Doku_Plugin_Controller();
require_once DOKU_INC.'inc/pluginutils.php';
require_once DOKU_PLUGIN.'auth.php';
require_once DOKU_PLUGIN.'authplain/auth.php';

require_once DOKU_PLUGIN.'proza/mdl/events.php';
$auth = new auth_plugin_authplain();
require_once DOKU_PLUGIN.'proza/helper.php';
$helper = new helper_plugin_proza();

//email => array('user' => array('yellow' => array('wiadomość o żółtych'), 'red' => array('wiadomość o czerwonych)))
//wiadomość o żółtych wysyłamy w poniedziałek
$msg = array();
$bygroup = array();
//$today = strtotime('2015-03-21');
$today = time();

$db = new DB();
$events = $db->spawn('events');
$res = $events->select(array('id', 'coordinator', 'group_n', 'plan_date', 'finish_date'), array('state' => 0));

while ($row = $res->fetchArray()) {
	$cord = $row['coordinator'];
	if (!isset($msg[$cord])) 
		$msg[$cord] = array('yellow' => array(), 'red' => array());

	/*Opiekunowie*/
	$group_n = $row['group_n'];
	if (!isset($bygroup[$group_n]))
		$bygroup[$group_n] = array('yellow' => array(), 'red' => array());

	switch ($helper->event_class($row)) {
		case 'yellow':
			$msg[$cord]['yellow'][] = $row;
		$bygroup[$group_n]['yellow'][] = $row;
			break;
		case 'red':
			$msg[$cord]['red'][] = $row;
			$bygroup[$group_n]['red'][] = $row;
			break;
	}


}

foreach ($msg as $cord => $ev) {
	/*jeżeli same żółte wysyłamy wiadomość tylko w poniedziałek*/
	if (count($ev['red']) > 0 || (count($ev['yellow']) > 0 && date('N', $today) == '1')) {
		
		/*wyślij powiadomienie*/
		$to = $cord;
		$subject = "[PROZA][$conf[title]] Termin realizacji programu";

		$body = '';
		$no = count($ev['red']);
		if ($no > 0)
			$body .= "Masz ".$no." przeterminowanych zadań!\n";
		$no = count($ev['yellow']);
		if ($no > 0)
			$body .= "Masz ".$no." zadań do zrobienia.\n";

		$body .= $http.'://'.$URI . "/doku.php?id=proza:start:coordinator:".$cord;
		$helper->mail($to, $subject, $body, $URI);
	}
}

/*Opiekunowie*/
foreach ($bygroup as $group_n => $ev)
	if (count($ev['red']) > 0 || (count($ev['yellow']) > 0 && date('N', $today) == '1')) {

		$grps = $helper->groups('pl');
		$subject = "[PROZA][$conf[title]][".$grps[$group_n]."] Zadania do wykonania";
		$body = '';
		$no = count($ev['red']);
		if ($no > 0)
			$body .= "Na wykonanie czeka ".$no." przeterminowanych zadań!\n";
		$no = count($ev['yellow']);
		if ($no > 0)
			$body .= "Na wykonanie czeka ".$no." zadań do zrobienia.\n";
		$body .= $http.'://'.$URI . "/doku.php?id=proza:start";

		$maint = $conf['plugin']['proza']['notify_'.$group_n];
		$ms = preg_split('/\s+/', $maint);
		foreach ($ms as $to) {
			/*wyślij powiadomienie*/
			if ($to != '')
				$helper->mail($to, $subject, $body, $URI);
		}
	}
