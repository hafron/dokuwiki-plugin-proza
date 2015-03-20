<div id="proza_filter">
<form action="?id=<?php echo $this->id('events', 'group', $this->params['group']) ?>" method="POST">
<fieldset>
<div>
	<label><?php echo $this->getLang('h_name') ?>:
		<select name="name">
			<option <?php if (!isset($this->params['name'])) echo 'selected' ?>
				value="-all">--- <?php echo $this->getLang('all') ?> ---</option>
		<?php while ($row = $this->t['categories']->fetchArray()): ?>
			<option <?php if (isset($this->params['name']) && $this->params['name'] == $row['name']) echo 'selected' ?>
				value="<?php echo $row['name'] ?>"><?php echo $row['name'] ?></option>
		<?php endwhile ?>
		</select>
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
</div>

<div>
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
</div>

</fieldset>
</form>
</div>
<table id="proza_table">
	<tr>
		<th><?php echo $this->getLang('h_id') ?></th>
		<th><?php echo $this->getLang('h_name') ?></th>
		<th><?php echo $this->getLang('h_state') ?></th>
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
			<td><?php echo $this->getLang('state_'.$row['state']) ?></td>
			<td><?php echo $row['assumptions_cache'] ?></td>
			<td><?php echo $row['plan_date'] ?></td>
			<td><?php echo $this->t['helper']->username($row['coordinator']) ?></td>
			<td><?php echo $row['summary_cache'] ?></td>
			<td><?php echo $row['finish_date'] ?></td>
		</tr>
	<?php endwhile ?>
</table>
