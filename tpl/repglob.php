<h1 id="proza_report_header">
	<?php echo $this->getLang('t_report') ?>
	<?php if (isset($this->params['year'])): ?>
		<?php echo $this->params['year'] ?>
	<?php endif ?>
</h1>

<table id="proza_table">
	<tr>
		<th><?php echo $this->getLang('h_group_n') ?></th>
		<th><?php echo $this->getLang('h_events_open') ?></th>
		<th><?php echo $this->getLang('h_events_ontime') ?></th>
		<th><?php echo $this->getLang('h_events_outdated') ?></th>
		<th><?php echo $this->getLang('h_events_rejected') ?></th>
		<th><?php echo $this->getLang('h_events_all') ?></th>
	</tr>
	<?php $sums = array('nopen' => 0, 'nclosed_ontime' => 0, 'nclosed_outdated' => 0, 'nrejected' => 0, 'nall' => 0) ?>
	<?php while ($row = $this->t['repglob']->fetchArray()): ?>
		<tr>
			<td><?php echo $row['group_n'] ?></td>
			<?php foreach ($sums as $h => $v): ?>
				<?php $sums[$h] += (int)$row[$h] ?>
				<td><?php echo $row[$h] ?></td>
			<?php endforeach ?>
		</tr>
	<?php endwhile ?>
	<tr>
		<th><?php echo $this->getLang('total') ?></th>
		<?php foreach ($sums as $v): ?>
			<td><?php echo $v ?></td>
		<?php endforeach ?>
	</tr>
</table>
