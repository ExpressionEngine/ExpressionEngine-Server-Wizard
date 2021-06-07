<?php if ( ! defined("SERVER_WIZ")) exit("No direct script access allowed");?>

<form class="settings form-standard" method="post" action="./index.php?wizard=run">
	<div class="panel-body">
		<h2>Database Settings</h2>
		<fieldset class="col-group required">
			<div class="setting-txt">
				<h3>Server Address</h3>
				<em>Commonly <b>localhost</b>, but your host may require something else.</em>
			</div>
			<div class="setting-field last">
				<input class="required" type="text" name="db_hostname" value="<?php echo $db_hostname; ?>"  />
			</div>
		</fieldset>
		<fieldset class="col-group required">
			<div class="setting-txt">
				<h3>Name</h3>
				<em><mark>Make sure the database exists, the installer will <b>not</b> create it.</mark></em>
			</div>
			<div class="setting-field last">
				<input type="text" name="db_name" value="<?php echo $db_name; ?>" class="required" />
			</div>
		</fieldset>
		<fieldset class="col-group required">
			<div class="setting-txt">
				<h3>Username</h3>
			</div>
			<div class="setting-field last">
				<input type="text" name="db_username" value="<?php echo $db_username; ?>" class="required" />
			</div>
		</fieldset>
		<fieldset class="col-group last">
			<div class="setting-txt ">
				<h3>Password</h3>
			</div>
			<div class="setting-field last">
				<input type="password" name="db_password" value="<?php echo $db_password; ?>" />
			</div>
		</fieldset>
	</div>
	<div class="panel-footer">
		<fieldset>
			<input class="btn btn-large btn-block" type="submit" value="Check Server">
		</fieldset>
	</div>
</form>
