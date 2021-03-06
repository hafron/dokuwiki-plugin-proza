<div id="proza_filter">
<form action="?id=<?php echo $this->id('events') ?>" method="POST">
	<label><?php echo $this->getLang('h_group_n') ?>:
		<select name="group_n">
			<option <?php if (!isset($this->params['group_n'])) echo 'selected' ?>
				value="-all">--- <?php echo $this->getLang('all') ?> ---</option>
		<?php foreach ($this->t['groups'] as $id => $group_n): ?>
			<option <?php if (isset($this->params['group_n']) && $this->params['group_n'] == $id)
							echo 'selected' ?>
				value="<?php echo $id ?>"><?php echo $group_n ?></option>
		<?php endforeach ?>
		</select>
	</label>

	<label><?php echo $this->getLang('h_state') ?>:
		<select name="state">
			<option <?php if (!isset($this->params['state'])) echo 'selected' ?>
				value="-all">--- <?php echo $this->getLang('all') ?> ---</option>
		<?php foreach (array(0,1,2) as $state): ?>
			<option <?php if (isset($this->params['state']) && $this->params['state'] == $state) echo 'selected' ?>
				value="<?php echo $state ?>"><?php echo $this->getLang('state_'.$state) ?></option>
		<?php endforeach ?>
		</select>
	</label>

	<label><?php echo $this->getLang('h_assumptions') ?>:
		<input name="assumptions" value="<?php echo $this->params['assumptions'] ?>">
	</label>

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
	
	<label><?php echo $this->getLang('h_summary') ?>:
		<input name="summary" value="<?php echo $this->params['summary'] ?>">
	</label>

	<label><?php echo $this->getLang('h_year') ?>:
		<select name="year">
			<option <?php if (!isset($this->params['year'])) echo 'selected' ?>
				value="-all">--- <?php echo $this->getLang('all') ?> ---</option>
		<?php foreach ($this->t['years'] as $year): ?>
			<option <?php if (isset($this->params['year']) && $this->params['year'] == $year) echo 'selected' ?>
				value="<?php echo $year ?>"><?php echo $year ?></option>
		<?php endforeach ?>
		</select>
	</label>
	<input type="submit" value="<?php echo $this->getLang('filter') ?>" />
	<label>[<a class="" href="
		<?php echo $this->t['helper']->mailto('',
		'[PROZA] '.ucfirst($this->t['groups'][$this->params['group_n']]),
		DOKU_URL . 'doku.php?id='.$_GET['id']) ?>">
		✉ <?php echo $this->getLang('send') ?>
	</a>]</label>
</form>
</div>
<table id="proza_table">
	<tr>
		<th><?php echo $this->getLang('h_id') ?></th>
		<th><?php echo $this->getLang('h_group_n') ?></th>
		<th><?php echo $this->getLang('h_state') ?></th>
		<th><?php echo $this->getLang('h_assumptions') ?></th>
		<th><?php echo $this->getLang('h_plan_date') ?></th>
		<th><?php echo $this->getLang('h_coordinator') ?></th>
		<th><?php echo $this->getLang('h_summary') ?></th>
		<th><?php echo $this->getLang('h_cost') ?></th>
		<th><?php echo $this->getLang('h_finish_date') ?></th>
	</tr>
	<?php $rows_no = 0 ?>
	<?php $cost_total = 0 ?>
	<?php while ($row = $this->t['events']->fetchArray()): ?>
		<tr class="<?php echo $this->t['helper']->event_class($row) ?>">
			<td>
				<a href="?id=<?php echo $this->id('show_event', 'group_n', $row['raw_group_n'], 'id', $row['id']) ?>">
					$<?php echo $row['id'] ?>
				</a>
			</td>
			<td><?php echo $row['group_n'] ?></td>
			<td><?php echo $this->getLang('state_'.$row['state']) ?></td>
			<td><?php echo $row['assumptions_cache'] ?></td>
			<td><?php echo $row['plan_date'] ?><br>
				<?php if ($row['state'] == 0): ?>
				<?php echo $this->t['helper']->days($row['plan_date']) ?>
				<?php endif ?>
			</td>
			<td><?php echo $this->t['helper']->username($row['coordinator']) ?></td>
			<td><?php echo $row['summary_cache'] ?></td>
			<td><?php echo $row['cost'] ?></td>
			<td><?php echo $row['finish_date'] ?></td>
		</tr>
	<?php $rows_no++ ?>
	<?php $cost_total += (int)$row['cost'] ?>
	<?php endwhile ?>
	<tr>
		<th><?php echo $this->getLang('total') ?></th>
		<td colspan="6"><?php echo $rows_no ?></td>
		<td colspan="2"><?php echo $cost_total ?></td>
	</tr>
</table>
