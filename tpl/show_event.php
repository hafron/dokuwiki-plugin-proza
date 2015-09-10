<div id="proza_event" class="<?php echo $this->t['helper']->event_class($this->t['event']) ?>">
<h1><a href="#">$<?php echo $this->t['event']['id'] ?></a> <?php echo $this->t['event']['group_n'] ?></h1>
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
</tr></table>
<h2><?php echo $this->getLang('h_assumptions') ?></h2>
<?php echo $this->t['event']['assumptions_cache'] ?>

<?php if ($this->t['event']['summary_cache'] != ''): ?>
	<h2><?php echo $this->getLang('h_summary') ?></h2>
	<?php echo $this->t['event']['summary_cache'] ?>
<?php endif ?>

<div class="proza_controls">
	<a href="
		<?php echo $this->t['helper']->mailto($this->t['event']['coordinator'],
		'$'.$this->t['event']['id'].' '.$this->t['event']['group_n'],
		(isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>">
		✉  <?php echo $this->getLang('send') ?>
	</a>

	<?php if ($this->t['helper']->user_eventeditor($this->t['event'])): ?>
		<a href="?id=<?php echo $this->id('event', 'group_n', $this->params['group_n'], 'action', 'edit',
								'id', $this->t['event']['id']) ?>">
		✎ <?php echo $this->getLang('edit') ?></a>
	<?php endif ?>

	<a href="?id=<?php echo $this->id('event', 'group', $this->params['group'], 'action', 'duplicate',
							'id', $this->t['event']['id']) ?>">
	⇲ <?php echo $this->getLang('duplicate') ?></a>
</div>

</div>

