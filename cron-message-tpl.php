<!DOCTYPE html>
<html>
<head>
<style type="text/css">
	body {
		font-family: Arial, sans-serif;
	}
	table {
		border-collapse:collapse;
	}
	td, th {
		border: 1px solid #000;
		padding: 2px;
	}
	th {
		text-align: left;
		background-color: #EEE;
	}
	a {
		text-decoration: none;
		color: #2B73B7;
	}
	a:hover {
		text-decoration: underline;
	}
	h1 {
		font-size: 105%;
	}
</style>
</head>
<body>
<h1>Twoje zadania:</h1>
<table>
<tr>
	<th>Nr</th>
	<th>Grupa</th>
	<th>Opis zadania</th>
	<th>Wykonać do</th>
</tr>
<?php foreach ($ev as $event): ?>
<?php
switch($event['class']) {
	case 'red':
		$color = "#F8E8E8";
		break;
	case 'yellow':
		$color = "#ffd";
		break;
}
?>
<tr style="background-color: <?php echo $color ?>">
	<td><a href="<?php echo $http ?>://<?php echo $URI ?>/doku.php?id=proza:show_event:group_n:<?php echo $event['group_n'] ?>:id:<?php echo $event['id'] ?>">
		$<?php echo $event['id'] ?>
	</a></td>
	<td><?php echo $event['pl'] ?></td>
	<td><?php echo $event['assumptions_cache'] ?></td>
	<td><?php echo $event['plan_date'] ?></td>
</tr>
<?php endforeach ?>
</table>
</body>
</html>
