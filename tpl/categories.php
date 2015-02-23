<?php if (count($this->t['errors']['categories']) > 0): ?>
	<?php $this->display_validation_errors($this->t['errors']['categories']) ?>
<?php endif ?>
<table>
	<tr>
		<th colspan="2"><?php echo $this->getLang('h_name') ?></th>
	</tr>
	<?php while ($row = $this->t['categories']->fetchArray()): ?>
		<tr>
			<td><?php echo $row['name'] ?></td>
			<td>
			<?php if ($this->params['confirm_delete'] != $row['name']): ?>
				<a href="?id=
				<?php echo $this->id('categories', 'group',
				$this->params['group'], 'confirm_delete', $row['name']) ?>">
					<?php echo $this->getLang('delete') ?>
				</a>
			<?php else: ?>
				<a href="?id=
				<?php echo $this->id('categories', 'group',
				$this->params['group'], 'delete', $row['name']) ?>">
					<?php echo $this->getLang('approve_delete') ?>
				</a>
			<?php endif ?>
			</td>
		</tr>
	<?php endwhile ?>
	<tr>
		<form action="?id=
		<?php echo $this->id('categories', 'group', $this->params['group'], 'action', 'add') ?>"
		method="post">
			<td><input type="text" name="name" /></td>
			<td><input type="submit" value="<?php echo $this->getLang('save') ?>" /></td>
		</form>
	</tr>
</table>
