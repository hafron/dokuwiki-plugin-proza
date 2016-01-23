<div id="proza_filter">
<form action="?id=<?php echo $this->id('start') ?>" method="POST">
<fieldset>
<div>
	<label><?php echo $this->getLang('h_coordinator') ?>:
		<select name="coordinator">
			<option <?php if (!isset($this->params['coordinator'])) echo 'selected' ?>
				value="-all">--- <?php echo $this->getLang('all') ?> ---</option>
		<?php foreach ($this->t['coordinators'] as $key => $name): ?>
			<option <?php if (isset($this->params['coordinator']) && $this->params['coordinator'] == $key) echo 'selected' ?>
				value="<?php echo $key ?>"><?php echo $name ?></option>
		<?php endforeach ?>
		</select>
	</label>
	<input type="submit" value="<?php echo $this->getLang('filter') ?>" />
</div>

</fieldset>
</form>
</div>
<table id="proza_table">
	<tr>
		<th><?php echo $this->getLang('h_id') ?></th>
		<th><?php echo $this->getLang('h_group_n') ?></th>
		<th><?php echo $this->getLang('h_assumptions') ?></th>
		<th><?php echo $this->getLang('h_plan_date') ?></th>
		<th><?php echo $this->getLang('h_coordinator') ?></th>
	</tr>
	<?php $rows_no = 0 ?>
	<?php while ($row = $this->t['events']->fetchArray()): ?>
		<tr class="<?php echo $this->t['helper']->event_class($row) ?>">
			<td>
				<a href="?id=<?php echo $this->id('show_event', 'group_n', $row['group_n'], 'id', $row['id']) ?>">
					$<?php echo $row['id'] ?>
				</a>
			</td>
			<td><?php echo $row['group_name'] ?></td>
			<td><?php echo $row['assumptions_cache'] ?></td>
			<td><?php echo $row['plan_date'] ?><br>
				<?php if ($row['state'] == 0): ?>
				<?php echo $this->t['helper']->days($row['plan_date']) ?>
				<?php endif ?>
			</td>
			<td><?php echo $this->t['helper']->username($row['coordinator']) ?></td>
		</tr>
	<?php $rows_no++ ?>
	<?php endwhile ?>
	<tr>
		<th><?php echo $this->getLang('total') ?></th>
		<td colspan="9"><?php echo $rows_no ?></td>
	</tr>
</table>
