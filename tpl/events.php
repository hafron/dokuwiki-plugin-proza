<table id="proza_table">
	<tr>
		<th><?php echo $this->getLang('h_id') ?></th>
		<th><?php echo $this->getLang('h_name') ?></th>
		<th><?php echo $this->getLang('h_assumptions') ?></th>
		<th><?php echo $this->getLang('h_plan_date') ?></th>
		<th><?php echo $this->getLang('h_coordinator') ?></th>
		<th><?php echo $this->getLang('h_summary') ?></th>
		<th colspan="2"><?php echo $this->getLang('h_finish_date') ?></th>
	</tr>
	<?php while ($row = $this->t['events']->fetchArray()): ?>
		<tr class="<?php echo $this->t['helper']->event_class($row) ?>">
			<td>
				<a href="?id=<?php echo $this->id('show_event', 'group', $this->params['group'], 'id', $row['id']) ?>">
					$<?php echo $row['id'] ?>
				</a>
			</td>
			<td><?php echo $row['name'] ?></td>
			<td><?php echo $row['assumptions_cache'] ?></td>
			<td><?php echo $row['plan_date'] ?></td>
			<td><?php echo $this->t['helper']->username($row['coordinator']) ?></td>
			<td><?php echo $row['summary_cache'] ?></td>
			<td><?php echo $row['finish_date'] ?></td>
		</tr>
	<?php endwhile ?>
</table>
