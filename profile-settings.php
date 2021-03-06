<?php
	
	require_once 'authentication.php';
	require_once 'updates.php';
	$page = "setting";
	
?>
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
	<link rel="stylesheet" type="text/css" href="app-assets/css/switch.css">
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/menu/menu-types/vertical-overlay-menu.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/colors/palette-gradient.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!-- END Custom CSS-->
  </head>
  <body ng-app="profileSettings" ng-controller="profileSettingsCtrl" data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns  fixed-navbar" account-profile>

    <!-- navbar-fixed-top-->
    <nav class="header-navbar navbar navbar-with-menu navbar-fixed-top navbar-semi-dark navbar-shadow">
      <div class="navbar-wrapper">
        <div class="navbar-header">
          <ul class="nav navbar-nav">
            <li class="nav-item mobile-menu hidden-md-up float-xs-left"><a class="nav-link nav-menu-main menu-toggle hidden-xs"><i class="icon-menu5 font-large-1"></i></a></li>
            <li class="nav-item"><a href="index.html" class="navbar-brand nav-link"><img alt="branding logo" src="images/logo/itrack-logo-large.png" data-expand="images/logo/itrack-logo-large.png" data-collapse="images/logo/itrack-logo-small.png" class="brand-logo"></a></li>
            <li class="nav-item hidden-md-up float-xs-right"><a data-toggle="collapse" data-target="#navbar-mobile" class="nav-link open-navbar-container"><i class="icon-ellipsis pe-2x icon-icon-rotate-right-right"></i></a></li>
          </ul>
        </div>
        <div class="navbar-container content container-fluid">
          <div id="navbar-mobile" class="collapse navbar-toggleable-sm">
            <ul class="nav navbar-nav">
              <li class="nav-item hidden-sm-down"><a class="nav-link nav-menu-main menu-toggle hidden-xs"><i class="icon-menu5">         </i></a></li>
              <li class="nav-item hidden-sm-down"><a href="#" class="nav-link nav-link-expand"><i class="ficon icon-expand2"></i></a></li>
            </ul>
            <ul class="nav navbar-nav float-xs-right">
			  <?php require_once 'notifications.php'; ?>
              <li class="dropdown dropdown-user nav-item"><a href="#" data-toggle="dropdown" class="dropdown-toggle nav-link dropdown-user-link"><span class="avatar avatar-online"><img src="{{profile.picture}}" alt="avatar"><i></i></span><span class="user-name">{{profile.user}}</span></a>
                <div class="dropdown-menu dropdown-menu-right" drop-down></div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>

    <!-- ////////////////////////////////////////////////////////////////////////////-->


    <!-- main menu-->
    <div data-scroll-to-active="true" class="main-menu menu-fixed menu-dark menu-accordion menu-shadow">
      <!-- main menu header-->
      <div class="main-menu-header">
        <!-- <input type="text" placeholder="Search" class="menu-search form-control round"/> -->
      </div>
      <!-- / main menu header-->
      <!-- main menu content-->
      <div class="main-menu-content">
		<?php require_once 'main-menu-navigation.php'; ?>
      </div>
      <!-- /main menu content-->
      <!-- main menu footer-->
      <!-- include includes/menu-footer-->
      <!-- main menu footer-->
    </div>
    <!-- / main menu-->

    <div class="app-content content container-fluid">
      <div class="content-wrapper">
        <div class="content-header row">
        </div>
		<!-- stats -->
        <div class="content-body">
			<div class="row">
				<div class="content-header-left col-md-6 col-xs-12 mb-1">
					<h2 class="content-header-title">Profile Settings</h2>
				</div>
				<div class="content-header-right breadcrumbs-right breadcrumbs-top col-md-6 col-xs-12">
				<div class="breadcrumb-wrapper col-xs-12">
				  <ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="index.html" class="info">iTrack</a>
					</li>
					<li class="breadcrumb-item active">Profile Settings
					</li>
				  </ol>
				</div>
			  </div>
			</div>
			<div class="row">
				<div class="col-lg-6 col-md-12 col-sm-12">
					<div class="card">
						<div class="card-body collapse in">
							<div class="card-block">
								<form class="form" name="formHolder.info" novalidate autocomplete=off>
									<div class="form-body">
										<h4><i class="icon-user"></i> Account Info <button class="btn btn-secondary float-md-right float-xs-right" ng-click="app.info.edit(this)"><i ng-class="{'icon-edit2': settings.btns.info.edit, 'icon-ban': !settings.btns.info.edit}"></i></button></h4><hr>
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
													<label>Username</label>
													<input type="text" class="form-control" ng-class="{'border-danger': formHolder.info.uname.$invalid || settings.info.not_unique || settings.info.alert.show}" name="uname" ng-model="settings.info.uname" ng-disabled="settings.btns.info.edit" required>
													<span class="help-block danger" ng-show="formHolder.info.uname.$invalid">Username is required</span>
													<span class="help-block danger" ng-show="settings.info.not_unique">Username already exists</span>
													<span class="help-block danger" ng-show="settings.info.alert.show">{{settings.info.alert.message}}</span>
												</div>
											</div>
										</div>
									</div>
								</form>
								<div class="row">
									<div class="col-lg-12">
										<button class="btn btn-info float-md-right float-xs-right" ng-disabled="settings.btns.info.edit" ng-click="app.info.update(this)">Update</button>
									</div>
								</div>
							</div>
						</div>
					</div>				
				</div>
				<div class="col-lg-6 col-md-12 col-sm-12">
					<div class="card">
						<div class="card-body collapse in">
							<div class="card-block">
								<form class="form" name="formHolder.security" novalidate>
									<div class="form-body">
										<h4><i class="icon-lock3"></i> Account Security <button class="btn btn-secondary float-md-right float-xs-right" ng-click="app.security.edit(this)"><i ng-class="{'icon-edit2': settings.btns.security.edit, 'icon-ban': !settings.btns.security.edit}"></i></button></h4><hr>
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
													<label>Old Password</label>
													<input type="password" class="form-control" ng-class="{'border-danger': settings.security.alert.opw.required || settings.security.alert.opw.show}" name="opw" ng-model="settings.security.opw" ng-disabled="settings.btns.security.edit" required>
													<span class="help-block danger" ng-show="settings.security.alert.opw.required">Old password is required</span>
													<span class="help-block danger" ng-show="settings.security.alert.opw.show">{{settings.security.alert.opw.message}}</span>													
												</div>
											</div>
											<div class="col-md-12">
												<div class="form-group">
													<label>New Password</label>
													<input type="password" class="form-control" ng-class="{'border-danger': (formHolder.security.pw.$invalid && formHolder.security.pw.$touched) || settings.security.alert.pw.show}" name="pw" ng-model="settings.security.pw" ng-disabled="settings.btns.security.edit" required>
													<span class="help-block danger" ng-show="(formHolder.security.pw.$invalid && formHolder.security.pw.$touched)">New password is required</span>													
												</div>
											</div>
											<div class="col-md-12">
												<div class="form-group">
													<label>Re-Type New Password</label>
													<input type="password" class="form-control" ng-class="{'border-danger': (formHolder.security.rpw.$invalid && formHolder.security.rpw.$touched) || settings.security.alert.pw.show}" name="rpw" ng-model="settings.security.rpw" ng-disabled="settings.btns.security.edit" required>
													<span class="help-block danger" ng-show="(formHolder.security.rpw.$invalid && formHolder.security.rpw.$touched)">Re-type new password</span>													
													<p class="help-block danger" ng-show="settings.security.alert.pw.show">{{settings.security.alert.pw.message}}</p>													
												</div>
											</div>
										</div>
									</div>
								</form>
								<div class="row">
									<div class="col-lg-12">
										<button class="btn btn-info float-md-right float-xs-right" ng-disabled="settings.btns.security.edit" ng-click="app.security.update(this)">Update</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
        </div>
		<!--/ stats -->
      </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->


    <footer class="footer footer-static footer-light navbar-border">
      <p class="clearfix text-muted text-sm-center mb-0 px-2"><span class="float-md-left d-xs-block d-md-inline-block">Copyright &copy; <b><?php echo date("Y"); ?></b> Document Tracking System. All rights reserved. </span></p>
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
	
	<link rel="stylesheet" href="angular/modules/bootbox/bs4-fix.css<?=$ver?>">	
	<script src="angular/modules/bootbox/bootbox.min.js<?=$ver?>"></script>
	<script src="angular/modules/growl/jquery.bootstrap-growl.min.js<?=$ver?>"></script>
	<script src="angular/modules/blockui/jquery.blockUI.js<?=$ver?>"></script>
	<link rel="stylesheet" href="angular/modules/bootbox/bs4-fix.css<?=$ver?>">
	
	<!-- dependencies -->
	<script src="angular/angular.min.js<?=$ver?>"></script>
	<script src="angular/angular-route.min.js<?=$ver?>"></script>
	<script src="angular/angular-sanitize.min.js<?=$ver?>"></script>
	<script src="angular/ui-bootstrap-tpls-3.0.2.min.js<?=$ver?>"></script>
	
	<script src="angular/modules/account/account.js<?=$ver?>"></script>
	<script src="angular/modules/bootbox/bootstrap-modal.js<?=$ver?>"></script>
	<script src="angular/modules/growl/growl.js<?=$ver?>"></script>
	<script src="angular/modules/blockui/blockui.js<?=$ver?>"></script>
	<script src="angular/modules/validation/validate.js<?=$ver?>"></script>
	<script src="angular/modules/post/window-open-post.js<?=$ver?>"></script>
	
	<!-- modules -->
	<script src="modules/notifications.js<?=$ver?>"></script>
	<script src="modules/module-access.js<?=$ver?>"></script>
	<script src="modules/profile-settings.js<?=$ver?>"></script>

	<!-- controller -->
	<script src="controllers/profile-settings.js<?=$ver?>"></script>
  </body>
</html>
