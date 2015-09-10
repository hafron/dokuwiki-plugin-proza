<?php if (count($this->t['errors']['groups']) > 0): ?>
	<?php $this->display_validation_errors($this->t['errors']['groups']) ?>
<?php endif ?>
<table>
	<tr>
		<th><?php echo $this->getLang('h_pl') ?></th>
		<th><?php echo $this->getLang('h_en') ?></th>
		<th colspan="3"><?php echo $this->getLang('h_refs') ?></th>
	</tr>
	<?php while ($row = $this->t['groups']->fetchArray()): ?>
		<tr>
			<td><?php echo $row['pl'] ?></td>
			<td><?php echo $row['en'] ?></td>
			<td>
				<a href="?id=
				<?php echo $this->id('events', 'group_n', $row['id']) ?>">
					<?php echo $row['refs'] ?>
				</a>
			</td>
			<td>
				<a href="?id=
				<?php echo $this->id('groups', 'edit', $row['id']) ?>">
					<?php echo $this->getLang('edit') ?>
				</a>
			</td>
			<td>
			<?php if ($this->params['confirm_delete'] != $row['id']): ?>
				<a href="?id=
				<?php echo $this->id('groups', 'confirm_delete', $row['id']) ?>">
					<?php echo $this->getLang('delete') ?>
				</a>
			<?php else: ?>
				<a href="?id=
				<?php echo $this->id('groups', 'delete', $row['id']) ?>">
					<?php echo $this->getLang('approve_delete') ?>
				</a>
			<?php endif ?>
			</td>
		</tr>
	<?php endwhile ?>
	<tr>
		<form action="?id=
		<?php echo $this->id('groups', 'action', $this->t['action']) ?>"
		method="post">
			<td><input type="text" name="pl" value="<?php echo $this->t['values']['pl'] ?>"/></td>
			<td><input type="text" name="en" value="<?php echo $this->t['values']['en'] ?>"/></td>
			<td colspan="3"><input type="submit" value="<?php echo $this->getLang('save') ?>" /></td>
		</form>
	</tr>
</table>
