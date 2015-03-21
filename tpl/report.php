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
	<input type="submit" value="<?php echo $this->getLang('filter') ?>" />
</div>

</fieldset>
</form>
</div>
