<?php if (count($this->t['errors']['events']) > 0): ?>
	<?php $this->display_validation_errors($this->t['errors']['events']) ?>
<?php endif ?>

<form action="?id=
<?php 
if ($this->params['action'] == 'edit') 
	echo $this->id('event', 'group', $this->params['group'], 'action', 'update', 'id', $this->params['id']);
else
	echo $this->id('event', 'group', $this->params['group'], 'action', 'add');
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
	<label for="name"><?php echo $this->getLang("h_name") ?></label>
	<span class="proza_cell">
	<select id="name" name="name">
		<?php foreach ($this->t['categories'] as $name): ?>
			<option <?php if ($this->t['value'] == $name) echo 'selected' ?>
			 value="<?php echo $name ?>"><?php echo $name ?></option>
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

<div class="proza_row">
	<label for="plan_date"><?php echo $this->getLang("h_plan_date") ?></label>
	<span class="proza_cell">
	<input class="date" id="plan_date" name="plan_date" value="<?php echo $this->t['values']['plan_date'] ?>" type="text" />
	<span class="normalized_date">
		<?php if ($this->params['action'] == 'edit'): ?>
			<?php echo $this->t['values']['plan_date'] ?>
		<?php endif ?>
	</span>
	</span>
</div>

<div class="proza_row">
	<label for="coordinator"><?php echo $this->getLang("h_coordinator") ?></label>
	<span class="proza_cell">
	<select id="coordinator" name="coordinator">
		<?php foreach ($this->t['coordinators'] as $nick => $name): ?>
			<option <?php if ($this->t['value'] == $nick) echo 'selected' ?>
			 value="<?php echo $nick ?>"><?php echo $name ?></option>
		<?php endforeach ?>
	</select>
	</span>
</div>

<?php if ($this->params['action'] == 'edit'): ?>
	<div class="proza_row">
		<label for="summary"><?php echo $this->getLang("h_summary") ?></label>
		<span class="proza_cell">
		<textarea id="summary" name="summary"><?php echo $this->t['values']['summary'] ?></textarea>
		</span>
	</div>

	<div class="proza_row">
		<label for="finish_date"><?php echo $this->getLang("h_finish_date") ?></label>
		<span class="proza_cell">
		<input class="date" id="finish_date" name="finish_date" value="<?php echo $this->t['values']['finish_date'] ?>" type="text" />
		<span class="normalized_date">
			<?php if ($this->params['action'] == 'edit'): ?>
				<?php echo $this->t['values']['finish_date'] ?>
			<?php endif ?>
		</span>
		</span>
	</div>
<?php endif ?>

</fieldset>
</div>
<input type="submit" value="<?php echo $this->getLang('save') ?>" />

</form>
