<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />
	<title><?=lang('login_title')?> - HR System</title>
	<link rel="shortcut icon" href="<?=base_url("assets/img/favicon-child.ico")?>" />

	<meta name="description" content="HR System login page" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

	<!-- bootstrap & fontawesome -->
	<link rel="stylesheet" href="<?=base_url()?>assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?=base_url()?>assets/font-awesome/4.2.0/css/font-awesome.min.css" />

	<!-- text fonts -->
	<link rel="stylesheet" href="<?=base_url()?>assets/fonts/fonts.googleapis.com.css" />

	<!-- ace styles -->
	<link rel="stylesheet" href="<?=base_url()?>assets/css/ace.min.css" />

	<!--[if lte IE 9]>
	<link rel="stylesheet" href="<?=base_url()?>assets/css/ace-part2.min.css" />
	<![endif]-->
	<link rel="stylesheet" href="<?=base_url()?>assets/css/ace-rtl.min.css" />

	<!--[if lte IE 9]>
	<link rel="stylesheet" href="<?=base_url()?>assets/css/ace-ie.min.css" />
	<![endif]-->

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->

	<!--[if lt IE 9]>
	<script src="<?=base_url()?>assets/js/html5shiv.js"></script>
	<script src="<?=base_url()?>assets/js/respond.min.js"></script>
	<![endif]-->
</head>

<body class="login-layout light-login">
<div class="main-container">
<div class="main-content">
<div class="row">
<div class="col-sm-10 col-sm-offset-1">
<div class="login-container">

<div class="space-32"></div>

<div class="center">
	<h1>
		<span class="red2">H</span>
		<span class="red2">R</span>
		<i class="ace-icon fa fa-child red2"></i>
		<span class="red2" id="id-text2">S</span>
	</h1>
	<h5 class="grey" id="id-company-text">Human Resource Information System</h5>
</div>

<div class="space-16"></div>

<div class="position-relative">
	<div id="login-box" class="login-box visible widget-box no-border">
		<div class="widget-body">
			<div class="widget-main">
				<h4 class="header blue lighter bigger">
					<i class="ace-icon fa fa-coffee blue"></i>
					<?=lang('login_enter_info_label')?>
				</h4>

				<div class="space-6"></div>
				
				<?php if($message): ?>
				<div class="alert alert-success">

					<?php echo $message;?>
				</div>
				<?php endif; ?>
				
				<?php if($error): ?>
				<div class="alert alert-danger">

					<?php echo $error;?>
				</div>
				<?php endif; ?>

				<?php echo form_open("auth/login"); ?>
					<fieldset>
						<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="text" class="form-control" name="identity" id="identity" placeholder="<?=lang('login_username_placeholder')?>" value="<?=$_identity?>" />
															<i class="ace-icon fa fa-user"></i>
														</span>
						</label>

						<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="password" class="form-control" name="password" id="password" placeholder="<?=lang('login_password_placeholder')?>" />
															<i class="ace-icon fa fa-lock"></i>
														</span>
						</label>

						<div class="space"></div>

						<div class="clearfix">
							<label class="inline">
								<input type="checkbox" class="ace" name="remember" id="remember" value="1" checked="checked" />
								<span class="lbl"> <?=lang('login_remember_label')?></span>
							</label>

							<button type="submit" class="width-35 pull-right btn btn-sm btn-primary">
								<i class="ace-icon fa fa-key"></i>
								<span class="bigger-110"><?=lang('login_login_button')?></span>
							</button>
						</div>

						<div class="space-4"></div>
					</fieldset>
				</form>

				<!--
				<div class="social-or-login center">
					<span class="bigger-110">Or Login Using</span>
				</div>

				<div class="space-6"></div>

				<div class="social-login center">
					<a class="btn btn-primary">
						<i class="ace-icon fa fa-facebook"></i>
					</a>

					<a class="btn btn-info">
						<i class="ace-icon fa fa-twitter"></i>
					</a>

					<a class="btn btn-danger">
						<i class="ace-icon fa fa-google-plus"></i>
					</a>
				</div>
				-->
			</div><!-- /.widget-main -->

			<div class="toolbar clearfix">
				<div>
					<a href="#" data-target="#forgot-box" class="forgot-password-link">
						<i class="ace-icon fa fa-question-circle"></i>
						<?=lang('login_help_link')?>
					</a>
				</div>

				<!--
				<div>
					<a href="#" data-target="#signup-box" class="user-signup-link">
						I want to register
						<i class="ace-icon fa fa-arrow-right"></i>
					</a>
				</div>
				-->
			</div>
		</div><!-- /.widget-body -->
	</div><!-- /.login-box -->

	<div id="forgot-box" class="forgot-box widget-box no-border">
		<div class="widget-body">
			<div class="widget-main">
				<h4 class="header red lighter bigger">
					<i class="ace-icon fa fa-key"></i>
					Retrieve Password
				</h4>

				<div class="space-6"></div>
				<p>
					Enter your email and to receive instructions
				</p>

				<form>
					<fieldset>
						<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="email" class="form-control" placeholder="Email" />
															<i class="ace-icon fa fa-envelope"></i>
														</span>
						</label>

						<div class="clearfix">
							<button type="button" class="width-35 pull-right btn btn-sm btn-danger">
								<i class="ace-icon fa fa-lightbulb-o"></i>
								<span class="bigger-110">Send Me!</span>
							</button>
						</div>
					</fieldset>
				</form>
			</div><!-- /.widget-main -->

			<div class="toolbar center">
				<a href="#" data-target="#login-box" class="back-to-login-link">
					Back to login
					<i class="ace-icon fa fa-arrow-right"></i>
				</a>
			</div>
		</div><!-- /.widget-body -->
	</div><!-- /.forgot-box -->

	<div id="signup-box" class="signup-box widget-box no-border">
		<div class="widget-body">
			<div class="widget-main">
				<h4 class="header green lighter bigger">
					<i class="ace-icon fa fa-users blue"></i>
					New User Registration
				</h4>

				<div class="space-6"></div>
				<p> Enter your details to begin: </p>

				<form>
					<fieldset>
						<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="email" class="form-control" placeholder="Email" />
															<i class="ace-icon fa fa-envelope"></i>
														</span>
						</label>

						<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="text" class="form-control" placeholder="Username" />
															<i class="ace-icon fa fa-user"></i>
														</span>
						</label>

						<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="password" class="form-control" placeholder="Password" />
															<i class="ace-icon fa fa-lock"></i>
														</span>
						</label>

						<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="password" class="form-control" placeholder="Repeat password" />
															<i class="ace-icon fa fa-retweet"></i>
														</span>
						</label>

						<label class="block">
							<input type="checkbox" class="ace" />
														<span class="lbl">
															I accept the
															<a href="#">User Agreement</a>
														</span>
						</label>

						<div class="space-24"></div>

						<div class="clearfix">
							<button type="reset" class="width-30 pull-left btn btn-sm">
								<i class="ace-icon fa fa-refresh"></i>
								<span class="bigger-110">Reset</span>
							</button>

							<button type="button" class="width-65 pull-right btn btn-sm btn-success">
								<span class="bigger-110">Register</span>

								<i class="ace-icon fa fa-arrow-right icon-on-right"></i>
							</button>
						</div>
					</fieldset>
				</form>
			</div>

			<div class="toolbar center">
				<a href="#" data-target="#login-box" class="back-to-login-link">
					<i class="ace-icon fa fa-arrow-left"></i>
					Back to login
				</a>
			</div>
		</div><!-- /.widget-body -->
	</div><!-- /.signup-box -->
</div><!-- /.position-relative -->

</div>
</div><!-- /.col -->
</div><!-- /.row -->
</div><!-- /.main-content -->
</div><!-- /.main-container -->

<!-- basic scripts -->

<!--[if !IE]> -->
<script src="<?=base_url()?>assets/js/jquery.2.1.1.min.js"></script>

<!-- <![endif]-->

<!--[if IE]>
<script src="<?=base_url()?>assets/js/jquery.1.11.1.min.js"></script>
<![endif]-->

<!--[if IE]>
<script type="text/javascript">
	window.jQuery || document.write("<script src='<?=base_url()?>assets/js/jquery1x.min.js'>"+"<"+"/script>");
</script>
<![endif]-->
<script type="text/javascript">
	if('ontouchstart' in document.documentElement) document.write("<script src='<?=base_url()?>assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
</script>

<script type="text/javascript" src="<?=base_url()?>assets/js/jstorage.min.js"></script>
<!-- inline scripts related to this page -->
<script type="text/javascript">
	
	$(function () {
		
		setTimeout(function () {
			
			$('input#identity').focus();
			
		}, 1);
		
		$.jStorage.deleteKey('online');
		
	});

</script>
</body>
</html>