<div id="proza_event" class="<?php echo $this->t['helper']->event_class($this->t['event']) ?>">
<h1><a href="#">$<?php echo $this->t['event']['id'] ?></a> <?php echo $this->t['event']['name'] ?></h1>
<div class="timebox">
<span><strong><?php echo $this->getLang('h_plan_date') ?></strong> <?php echo $this->t['event']['plan_date'] ?></span>
<?php if ($this->t['event']['finish_date'] != ''): ?>
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

<?php if ($this->t['event']['summary'] != ''): ?>
	<h2><?php echo $this->getLang('h_summary') ?></h2>
	<?php echo $this->t['event']['summary_cache'] ?>
<?php endif ?>

<a class="proza_inline_button proza_send_button" href="
	<?php echo $this->t['helper']->mailto($template['issue']['coordinator_email'],
	$bezlang['issue'].': #'.$template['issue']['id'].' ['.$template['issue']['entity'].'] '.$template['issue']['title'],
	$template['uri']) ?>">
	✉ <?php echo $this->getLang('send') ?>
</a>

<a href="?id=<?php echo $this->id('event', 'group', $this->params['group'], 'id', $this->t['event']['id']) ?>"
class="proza_inline_button proza_edit_button"><?php echo $this->getLang('edit') ?></a>

</div>
