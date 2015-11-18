<?php if ( ! defined('SERVER_WIZ')) exit('No direct script access allowed');?>

<form class="settings">
	<?php if (count($errors) > 0): ?>
		<div class="alert inline issue">
			<h3>Oops, there was an error</h3>
			<p>To complete the wizard check, the following error(s) must be addressed:</p>
			<ul>
				<li><?php echo implode("</li>\n\t<li>", $errors);?></li>
			</ul>
		</div>
	<?php else:?>
		<div class="alert inline success">
			<h3><b>Yay!</b> Congratulations! Your server is ready to use ExpressionEngine!</h3>
		</div>
	<?php endif;?>

	<table>
		<thead>
			<tr>
				<th>Requirement</th>
				<th>Importance</th>
				<th>Supported</th>
			</tr>
		</thead>
		<tbody>
		<?php $i = 0; foreach ($requirements as $required): ?>
			<tr class="<?php echo ($i++ % 2) ? 'even' : '';?>">
				<td>
					<strong><?php echo $required['item'];?></strong>
				</td>
				<td>
					 <span class="severity-<?php echo $required['severity'];?>"><?php echo $required['severity'];?></span>
				</td>

				<td class="supported-<?php echo $required['supported'];?>">
					<?php echo ($required['supported'] == 'y') ? 'Yes' : 'No';?>
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
</form>
