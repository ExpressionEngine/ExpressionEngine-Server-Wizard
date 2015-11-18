<!doctype html>
<html>
	<head>
		<title><?= $title ?></title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" lang="en-us" dir="ltr">
		<meta content="width=device-width, initial-scale=1.0" name="viewport">

		<link href="./asset/css/common.min.css" rel="stylesheet">
		<link href="./asset/css/wizard.css" rel="stylesheet">
		<!-- <link href="touch-icon-iphone.png" rel="apple-touch-icon-precomposed" sizes="114x114">
		<link href="touch-icon-ipad.png" rel="apple-touch-icon-precomposed" sizes="144x144"> -->
	</head>
	<body id="top">
		<section class="wrap">
			<div class="col-group install-wrap">
				<div class="col w-16 last">
					<div class="box">
						<h1>
							<?= $heading ?>
							<?php if ($form): ?><span class="req-title">Required Fields</span><?php endif ?>
						</h1>
						<?= $content ?>
					</div>
				</div>
			</div>
		</section>
		<section class="product-bar">
			<div class="snap">
				<div class="left">
					<p><b><?= $heading ?></b></p>
				</div>
				<div class="right">
					<p>
						<a href="https://support.ellislab.com/bugs/submit" rel="external">Report Bug</a>
						<b class="sep">&middot;</b>
						<a href="https://ellislab.com/expressionengine/user-guide/installation/requirements.html#server-compatibility-wizard" rel="external">Manual</a>
					</p>
				</div>
			</div>
		</section>
		<section class="footer">
			<div class="snap">
				<div class="left"><p>&copy;2003&mdash;<?php echo date('Y') ?> <a href="http://ellislab.com/expressionengine" rel="external">EllisLab</a>, Inc.</p></div>
				<div class="right"><p><a class="scroll" href="#top">scroll to top</a></p></div>
			</div>
		</section>
	</body>
</html>
