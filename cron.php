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
require_once DOKU_PLUGIN.'proza/mdl/groups.php';
$auth = new auth_plugin_authplain();
require_once DOKU_PLUGIN.'proza/helper.php';
$helper = new helper_plugin_proza();


$msg = array();
$bycolor = array('yellow' => array(), 'red' => array());
//$today = strtotime('2015-03-21');
$today = time();

//wysyłamy tylko w poniedziałek
if (date('N', $today) != '1')
	exit(0);

$db = new DB();
$events = $db->spawn('events');
$res = $events->select(array('events.id', 'coordinator', 'plan_date', 'assumptions_cache', 'finish_date', 'groups.pl', 'group_n'),
			array('state' => 0), 'plan_date, group_n, coordinator', 'DESC');

while ($row = $res->fetchArray()) {
	$cord = $row['coordinator'];
	
	$class = $helper->event_class($row);
	if ($class != 'yellow' && $class != 'red')
		continue;
		
	if (!isset($msg[$cord])) 
		$msg[$cord] = array();
	
	$row['class'] = $class;
	$msg[$cord][] = $row; 
}

foreach ($msg as $cord => $ev) {

	/*wyślij powiadomienie*/
	$to = $cord;
	$title = trim($conf['title']);
	if ($title == '')
		$title = $URI;
	$subject = "[PROZA][$conf[title]] Termin realizacji programu";

	ob_start();
	include "cron-message-tpl.php";
	$body = ob_get_clean();

	$body .= $http.'://'.$URI . "/doku.php?id=proza:start:coordinator:".$cord;
	$helper->mail($to, $subject, $body, $URI, "text/html");
}

