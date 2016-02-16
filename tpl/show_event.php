<div id="proza_event" class="<?php echo $this->t['helper']->event_class($this->t['event']) ?>">
<h1>
	<a href="#">$<?php echo $this->t['event']['id'] ?></a>
	<?php echo $this->t['event']['group_n'] ?>
	(<?php echo $this->getLang('state_'.$this->t['event']['state']) ?>)
</h1>
<div class="timebox">
<span><strong><?php echo $this->getLang('h_plan_date') ?></strong> <?php echo $this->t['event']['plan_date'] ?></span>
<?php if ($this->t['event']['state'] != 0): ?>
	<span>
	<strong><?php echo $this->getLang('h_finish_date') ?></strong>
	<?php echo $this->t['event']['finish_date'] ?>
	</span>
<?php endif ?>
</div>
<table><tr>
<th><?php echo $this->getLang('h_coordinator') ?></th>
<td><?php echo $this->t['helper']->username($this->t['event']['coordinator']) ?></td>
<th><?php echo $this->getLang('h_cost') ?></th>
<td>
<?php if ($this->t['event']['cost']!= ''): ?>
	<?php echo $this->t['event']['cost'] ?>
<?php else: ?>	
	---
<?php endif ?>
</td>
</tr></table>
<h2><?php echo $this->getLang('h_assumptions') ?></h2>
<?php echo $this->t['event']['assumptions_cache'] ?>

<?php if (isset($this->params['action'])): ?>
	<h2><?php echo $this->getLang('h_summary') ?></h2>
	<?php if (count($this->t['errors']['events']) > 0): ?>
		<?php $this->display_validation_errors($this->t['errors']['events']) ?>
	<?php endif ?>
	<form id="proza_form" method="post" action="?id=<?php echo $this->id('show_event', 'group_n', $this->params['group_n'], 'action',
					$this->params['action'].'_save', 'id', $this->t['event']['id']) ?>">
		<textarea id="summary" name="summary"><?php echo $this->t['values']['summary'] ?></textarea>
		<div id="proza_form_buttons">
			<input type="submit" value="<?php echo $this->getLang($this->params['action']) ?>" />
			 <a href="?id=
			<?php echo $this->id('show_event', 'group_n', $this->params['group_n'],'id', $this->t['event']['id']) ?>">
				<?php echo $this->getLang('cancel') ?>
			</a>
		</div>
	</form>
<?php elseif ($this->t['event']['state'] != 0): ?>

<h2><?php echo $this->getLang('h_summary') ?></h2>
<?php echo $this->t['event']['summary_cache'] ?>

<?php endif ?>

<?php if (!isset($this->params['action'])): ?>
	<div class="proza_controls">
		<?php if ($this->t['helper']->user_eventeditor($this->t['event'])): ?>
			<?php if ($this->t['event']['state'] == 0): ?>
				<a href="?id=<?php echo $this->id('show_event', 'group_n', $this->params['group_n'], 'action', 'close',
										'id', $this->t['event']['id']) ?>">
				↬ <?php echo $this->getLang('close') ?></a>
		
				<a href="?id=<?php echo $this->id('show_event', 'group_n', $this->params['group_n'], 'action', 'reject',
										'id', $this->t['event']['id']) ?>">
				↛ <?php echo $this->getLang('reject') ?></a>
			<?php endif ?>
		
			<a href="?id=<?php echo $this->id('event', 'group_n', $this->params['group_n'], 'action', 'edit',
									'id', $this->t['event']['id']) ?>">
			✎ <?php echo $this->getLang('edit') ?></a>
		<?php endif ?>
	
		<a href="
			<?php echo $this->t['helper']->mailto($this->t['event']['coordinator'],
			'$'.$this->t['event']['id'].' '.$this->t['event']['group_n'],
			(isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>">
			✉  <?php echo $this->getLang('send') ?>
		</a>
		<?php if ($this->t['helper']->user_admin()): ?>
			<a href="?id=<?php echo $this->id('event', 'group_n', $this->params['group_n'], 'action', 'duplicate',
									'id', $this->t['event']['id']) ?>">
			⇲ <?php echo $this->getLang('duplicate') ?></a>
		<?php endif ?>	
	</div>
<?php endif ?>

</div>

