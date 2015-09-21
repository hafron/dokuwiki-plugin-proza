<?php if (count($this->t['errors']['events']) > 0): ?>
	<?php $this->display_validation_errors($this->t['errors']['events']) ?>
<?php endif ?>

<form action="?id=
<?php 
if ($this->params['action'] == 'edit') 
	echo $this->id('event', 'group_n', $this->params['group_n'], 'action', 'update', 'id', $this->params['id']);
else
	echo $this->id('event', 'group_n', $this->params['group_n'], 'action', 'add');
?>" method="post" id="proza_form">

<div id="proza_event" class="
<?php if ($this->params['action'] == 'edit') 
	echo $this->t['helper']->event_class($this->t['values']);
else
	echo 'green';
?>">

<fieldset>
<?php if ($this->params['action'] == 'edit'): ?>
<div class="proza_row">
	<label><?php echo $this->getLang('h_id') ?></label>
	<span class="proza_cell"><strong>$<?php echo $this->params['id'] ?></strong></span>
</div>
<?php endif ?>

<div class="proza_row">
	<label for="name"><?php echo $this->getLang("h_group_n") ?></label>
	<span class="proza_cell">
	<select id="group_n" name="group_n">
		<?php foreach ($this->t['groups'] as $id => $group_n): ?>
			<option <?php if ($this->t['values']['group_n'] == $id) echo 'selected' ?>
			 value="<?php echo $id ?>"><?php echo $group_n ?></option>
		<?php endforeach ?>
	</select>
	</span>
</div>

<div class="proza_row">
	<label for="assumptions"><?php echo $this->getLang("h_assumptions") ?></label>
	<span class="proza_cell">
	<textarea id="assumptions" name="assumptions"><?php echo $this->t['values']['assumptions'] ?></textarea>
	</span>
</div>

<?php /*admini mogą edytować daty wykonania*/ ?>
<?php if ($this->params['action'] == 'edit' && !$this->t['helper']->user_admin()): ?>
<div class="proza_row">
	<label><?php echo $this->getLang('h_plan_date') ?></label>
	<span class="proza_cell"><strong><?php echo $this->t['values']['plan_date'] ?></strong></span>
</div>
<?php else: ?>
<div class="proza_row">
	<label for="plan_date"><?php echo $this->getLang("h_plan_date") ?></label>
	<span class="proza_cell">
		<input class="date" id="plan_date" name="plan_date"
				value="<?php echo $this->t['values']['plan_date'] ?>" type="text" />
		<span class="normalized_date">
			<?php if ($this->t['values']['plan_date'] != ''): ?>
				<?php echo $this->t['helper']->norm_date($this->t['values']['plan_date']) ?>
			<?php endif ?>
		</span>
	</span>
</div>
<?php endif ?>

<div class="proza_row">
	<label for="coordinator"><?php echo $this->getLang("h_coordinator") ?></label>
	<span class="proza_cell">
	<select id="coordinator" name="coordinator">
		<?php foreach ($this->t['coordinators'] as $nick => $name): ?>
			<option <?php if ($this->t['values']['coordinator'] == $nick) echo 'selected' ?>
			 value="<?php echo $nick ?>"><?php echo $name ?></option>
		<?php endforeach ?>
	</select>
	</span>
</div>

<?php if ($this->params['action'] == 'edit'): ?>
	<div class="proza_row">
		<label for="state"><?php echo $this->getLang("h_state") ?></label>
		<span class="proza_cell">
		<select id="state" name="state">
			<?php foreach (array(0, 1, 2) as $state): ?>
				<option <?php if ($this->t['values']['state'] == $state) echo 'selected' ?>
				 value="<?php echo $state ?>"><?php echo $this->getLang('state_'.$state) ?></option>
			<?php endforeach ?>
		</select>
		</span>
	</div>

	<div class="proza_row">
		<label for="summary"><?php echo $this->getLang("h_summary") ?></label>
		<span class="proza_cell">
		<textarea id="summary" name="summary"><?php echo $this->t['values']['summary'] ?></textarea>
		</span>
	</div>
<?php endif ?>

</fieldset>
</div>
<div id="proza_form_buttons">
	<input type="submit" value="<?php echo $this->getLang('save') ?>" />
	 <a href="?id=
		<?php 
		if ($this->params['action'] == 'edit') 
			echo $this->id('show_event', 'group_n', $this->params['group_n'], 'id', $this->params['id']); 
		else
			echo $this->id('events', 'group_n', $this->params['group_n']);
	?>">
		<?php echo $this->getLang('cancel') ?>
	</a>
</div>

</form>
