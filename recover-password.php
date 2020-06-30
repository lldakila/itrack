<!DOCTYPE html>
<html lang="en" data-textdirection="ltr" class="loading">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Robust admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, robust admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>Document Tracking System</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/ico/itrack-icon.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/bootstrap.css">
    <!-- font icons-->
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/icomoon.css">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/extensions/pace.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/app.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/colors.css">
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/menu/menu-types/vertical-overlay-menu.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/colors/palette-gradient.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="assets/css/timeline.css">	
    <!-- END Custom CSS-->
	<style type="text/css">
	
		.footer-single-page {
			padding: 0.4rem;
			position: fixed;			
			bottom: 0;
			width: 100%;
		}
	
	</style>
  </head>
  <body ng-app="recoverPassword" ng-controller="recoverPasswordCtrl" data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns  fixed-navbar">
  
    <!-- navbar-fixed-top-->
    <nav class="header-navbar navbar navbar-with-menu navbar-fixed-top navbar-dark navbar-shadow">
      <div class="navbar-wrapper">
        <div class="navbar-header">
          <ul class="nav navbar-nav">
            <!-- <li class="nav-item mobile-menu hidden-md-up float-xs-left"><a class="nav-link nav-menu-main menu-toggle hidden-xs"><i class="icon-menu5 font-large-1"></i></a></li> -->
            <li class="nav-item"><a href="javascript:;" class="navbar-brand nav-link"><img alt="branding logo" src="images/logo/itrack-logo-large.png" data-expand="images/logo/itrack-logo-large.png" data-collapse="images/logo/itrack-logo-small.png" class="brand-logo" style="margin-top: -6px!important;"></a></li>
            <li class="nav-item hidden-md-up float-xs-right"><a data-toggle="collapse" data-target="#navbar-mobile" class="nav-link open-navbar-container"><i class="icon-ellipsis pe-2x icon-icon-rotate-right-right"></i></a></li>
          </ul>
        </div>
        <div class="navbar-container content container-fluid">
          <div id="navbar-mobile" class="collapse navbar-toggleable-sm">
            <!-- <ul class="nav navbar-nav">
              <li class="nav-item hidden-sm-down"><a class="nav-link nav-menu-main menu-toggle hidden-xs"><i class="icon-menu5"></i></a></li>
              <li class="nav-item hidden-sm-down"><a href="#" class="nav-link nav-link-expand"><i class="ficon icon-expand2"></i></a></li>
            </ul> -->
            <ul class="nav navbar-nav float-xs-right">
			<li class="dropdown dropdown-notification nav-item">
				<a href="/login.html" class="nav-link nav-link-label"><i class="icon-sign-in"></i> Login</a>
            </ul>
          </div>
        </div>
      </div>
    </nav>  
  
    <div class="app-content container-fluid">
      <div class="content-wrapper">
        <div class="content-header row"></div>
        <div class="content-body">
		
		<section class="flexbox-container" style="margin-top: 50px;">
			<div class="col-md-4 offset-md-4 col-xs-10 offset-xs-1 box-shadow-2 p-0">		
				<div class="card border-grey border-lighten-3 px-2 py-2 m-0">
					<div class="card-header no-border pb-0">
						<h6 class="card-subtitle line-on-side text-muted text-xs-center font-small-3 pt-2"><span>We will send you an email with your password.</span></h6>
					</div>
					<div class="card-body collapse in">
						<div class="card-block">
							<form name="formHolder.recover" class="form-horizontal" novalidate autocomplete="off">
								<fieldset class="form-group position-relative has-icon-left">
									<input type="text" class="form-control form-control-lg input-lg" name="username" ng-model="account.username" ng-class="{'border-danger': formHolder.recover.username.$touched && formHolder.recover.username.$invalid}" placeholder="Your username" required>
									<div class="form-control-position">
										<i class="icon-user"></i>
									</div>
									<span class="help-block danger" ng-show="formHolder.recover.username.$touched && formHolder.recover.username.$invalid">Username is required</span>									
								</fieldset>
								<button type="button" class="btn btn-info btn-lg btn-block" ng-click="app.recover(this)"><i class="icon-lock4"></i> Recover Password</button>
							</form>
						</div>
					</div>
					<div class="card-footer no-border">
						<!-- <p class="float-sm-left text-xs-center"><a href="/login.html" class="card-link">Login</a></p> -->
					</div>
				</div>
			</div>
		</section>
		
		</div>
      </div>
    </div>  
  
    <footer class="footer-single-page footer-static footer-light navbar-border">
      <p class="text-muted" style="text-align: center;">Copyright  &copy; <b><?php echo date("Y"); ?></b> Document Tracking System. All rights reserved.</p>
    </footer>  
  
    <!-- BEGIN VENDOR JS-->
    <script src="app-assets/js/core/libraries/jquery.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/ui/tether.min.js" type="text/javascript"></script>
    <script src="app-assets/js/core/libraries/bootstrap.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/ui/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/ui/unison.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/ui/blockUI.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/ui/jquery.matchHeight-min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/ui/screenfull.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/extensions/pace.min.js" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <script src="app-assets/vendors/js/charts/chart.min.js" type="text/javascript"></script>
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN ROBUST JS-->
    <script src="app-assets/js/core/app-menu.js" type="text/javascript"></script>
    <script src="app-assets/js/core/app.js" type="text/javascript"></script>
    <!-- END ROBUST JS-->

    <script src="app-assets/js/jquery-barcode-scanner/jquery.scannerdetection.js" type="text/javascript"></script>	

	<link rel="stylesheet" href="angular/modules/bootbox/bs4-fix.css">	
	<script src="angular/modules/bootbox/bootbox.min.js"></script>
	<script src="angular/modules/growl/jquery.bootstrap-growl.min.js"></script>
	<script src="angular/modules/blockui/jquery.blockUI.js"></script>
	<link rel="stylesheet" href="angular/modules/bootbox/bs4-fix.css">

	<!-- dependencies -->
	<script src="angular/angular.min.js"></script>
	<script src="angular/angular-route.min.js"></script>
	<script src="angular/angular-sanitize.min.js"></script>
	<script src="angular/ui-bootstrap-tpls-3.0.2.min.js"></script>

	<script src="angular/modules/account/account.js"></script>
	<script src="angular/modules/bootbox/bootstrap-modal.js"></script>
	<script src="angular/modules/growl/growl.js"></script>
	<script src="angular/modules/blockui/blockui.js"></script>
	<script src="angular/modules/validation/validate.js"></script>
	<script src="angular/modules/validation/validate-dialog.js"></script>
	<script src="angular/modules/post/window-open-post.js"></script>

	<!-- modules -->
	<script src="modules/recover.js"></script>

	<!-- controller -->
	<script src="controllers/recover.js"></script>  
  
  </body>
  
</html>