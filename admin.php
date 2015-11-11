<?php
/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
$errors = array();
include_once DOKU_PLUGIN."proza/mdl/events.php";
class admin_plugin_proza extends DokuWiki_Admin_Plugin {

	private $imported = false;
 
	function getMenuText() {
		return 'ProZa - zaimportuj dane historyczne';
	}
 
	/**
	 * handle user request
	 */
	function handle() {
		global $errors;
 
		if (!isset($_REQUEST['run'])) return;   // first time - nothing to do
		if (!checkSecurityToken()) return;

		//importuj
		$csv = $_POST['proza_data'];

		$lines = explode("\n", $csv);
		$data = array();
		foreach($lines as $line) $data[] = str_getcsv($line); //parse the items in rows 
		/*usuń nagłówek*/
		array_shift($data);


		$db = new DB();
		$events = $db->spawn('events');
		foreach ($data as $row) {
			$info = array();
			$ins = $row;
			$ins[8] = p_render('xhtml',p_get_instructions($row[2]), $info);
			$ins[9] = p_render('xhtml',p_get_instructions($row[5]), $info);

			$toins = array();
			foreach ($ins as $v)
				$toins[] = $events->db->escape($v);

			$events->db->query("
			INSERT INTO events
				(group_n, state, assumptions, coordinator, plan_date, summary, finish_date,
				assumptions_cache, summary_cache)
				VALUES (".implode(',', $toins).")");
		}
		if (count($errors) == 0)
			$this->imported = true;
	}
 
	/**
	 * output appropriate html
	 */
	function html() {
		global $errors;
		ptln('<h1>'.$this->getMenuText().'</h1>');
		if ($this->imported == true) {
		    ptln('<div class="success">Dane zostały zaimportowane pomyślnie.</div>');
		} else {
		  	if (is_array($errors))
		  		foreach ($errors as $error) {
		  			echo '<div class="error">';
		  			echo $error;
		  			echo '</div>';
		  		}
		}
	 
		ptln('<form action="'.wl($ID).'" method="post">');
		// output hidden values to ensure dokuwiki will return back to this plugin
		ptln('<input type="hidden" name="do"   value="admin" />');
		ptln('<input type="hidden" name="page" value="'.$this->getPluginName().'" />');
		formSecurityToken();
		ptln('<label for="proza_data">Kolejność pól: <i>Grupa,Status,Opis zadania,Koordynator,Wykonać do,Wynik,Data wykonania</i><br />');
		ptln('Przy czym: <i>Grupa = id grupy</i><br />');
		ptln('<i>status ∊ {0,1,2}</i>, gdzie 0 - otwarte, 1 - zamknięte, 2 - odrzucone, <br />');
		ptln('a <i>kordynator</i> musi być poprawnym nickiem użytkownika wiki.<br />');
		ptln('Dane w wormacie CSV do zaimportowania:<br>Separator: <b>,</b><br />Separator tekstu: <b>"</b><br /></label>');
		ptln('<textarea id="proza_data" name="proza_data" cols="50" rows="20"></textarea><br />');
		ptln('<input type="submit" name="run"  value="Importuj" />');
		ptln('</form>');
	}
}

