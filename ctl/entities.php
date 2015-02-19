<?php

require_once DOKU_PLUGIN."proza/mdl/entities.php";

$db = new DB();
$entities = $db->spawn('entities');
