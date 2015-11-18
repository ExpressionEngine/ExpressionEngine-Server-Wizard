<?php if ( ! defined('SERVER_WIZ')) exit('No direct script access allowed');?>
<form class="settings">
	<div class="alert inline issue">
		<h3>Oops, there was an error</h3>
		<p><?= $message ?></p>
	</div>
	<fieldset class="install-btn">
		<a class="btn disable" href="javascript:history.go(-1);">Go Back</a>
	</fieldset>
</form>

