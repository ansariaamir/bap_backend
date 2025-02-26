<div class="container content">
<h3>Incentive Result</h3>

<?php if($grid_data){  ?>
<table class="table table-bordered">
	<tr>
		<?php  foreach ($grid_data['header'] as $key => $value) { ?>
			<th><?= $value ?></th>
		<?php } ?>
	</tr>
	<?php  foreach ($grid_data['grid_data'] as $key => $value) { ?>
		<tr>
			<?php  foreach ($value as $k => $val) { ?>
			<td><?= $val['field_value'] ?></td>
			<?php } ?>
		</tr>
	<?php } ?>
</table>
<?php } ?>
</div>