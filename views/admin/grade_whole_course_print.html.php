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
			<?php echo h($info->grade); ?><br />
			<small><?php echo h($info->comment); ?></small>
		</td>
		<?php
	} 
	?>
</tr>
<?php 
}
?>
</table>
