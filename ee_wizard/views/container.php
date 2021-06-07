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
			<div class="login__logo">
				<?php echo view('ee-logo'); ?>
			</div>
			<div class="col-group install-wrap">
				<div class="col w-16 last">
					<div class="dialog panel">
						<div class="dialog__header panel-heading text-center">
							<h1 class="dialog__title">
								<?= $heading ?>
								<?php if ($form): ?><?php endif ?>
							</h1>
						</div>
						<?= $content ?>
					</div>
				</div>
			</div>

		</section>

		<section class="product-bar">

			<div class="snap">

				<div class="left bar">
					<p style="float: left;"><a href="https://expressionengine.com/" target="_blank" rel="external"><b>ExpressionEngine</b></a></p>
				</div>
				<div class="right">
					<p>
						<a href="https://github.com/ExpressionEngine/ExpressionEngine/issues/new?assignees=&labels=&template=2-bug-report.md" target="_blank" rel="external">Report Bug</a>
						<b class="sep">&middot;</b>
						<a href="https://github.com/ExpressionEngine/ExpressionEngine/issues/new?assignees=&labels=&template=2-bug-report.md" target="_blank" rel="external">Manual</a>
					</p>
				</div>
			</div>
		</section>
		<section class="footer">
			<div class="snap">
				<div class="left"><p>©<?php echo date('Y') ?> <a href="https://packettide.com/" target="_blank" rel="external">Packet Tide</a>, LLC</p></div>
				<div class="right"><p><a class="scroll" href="#top">scroll to top</a></p></div>
			</div>
		</section>
	</body>
</html>
