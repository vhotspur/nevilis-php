<table border="1">
<tr>
	<th>User</th>
	<?php
	foreach ($assignments as $a) {
		?>
		<th>
			<?php echo h($a->name); ?>
		</th>
		<?php
	} 
	?>
	<th>All</th>
</tr>
<?php
foreach ($users as $u) {
?>
<tr>
	<th>
		<?php echo h($u->name); ?> [<?php echo h($u->uid); ?>]
	</th>
	<?php
	foreach ($assignments as $a) {
		$info = $u->assignments[$a->aid];
		?>
		<td>
			<?php if ($info->uploaded) { ?>
				<a href="<?php echo url_for('admin', 'download', $cid, 'single', $u->uid, $a->aid); ?>">Download</a>
			<?php }else { ?>
				Not uploaded.
			<?php } ?>
		</td>
		<?php
	} 
	?>
	<td>
		<a href="<?php echo url_for('admin', 'download', $cid, 'user', $u->uid); ?>">Download</a>
	</td>
</tr>
<?php
}
?>
<tr>
	<th>All</th>
	<?php
	foreach ($assignments as $a) {
		?>
		<td>
			<a href="<?php echo url_for('admin', 'download', $cid, 'assignment', $a->aid); ?>">Download</a>
		</td>
		<?php
	}
	?>
	<td>
		<a href="<?php echo url_for('admin', 'download', $cid, 'all'); ?>">Download</a>
	</td>
</tr>
</table>
