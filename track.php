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
  <body ng-app="track" ng-controller="trackCtrl" data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns  fixed-navbar" track-document>
  
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
              <li class="dropdown dropdown-user nav-item"><a href="javascript:;" data-toggle="dropdown" class="dropdown-toggle nav-link dropdown-user-link"><span class="avatar avatar-online"><img src="pictures/avatar.png" alt="avatar"><i></i></span><span class="user-name">Guest</span></a>
                <div class="dropdown-menu dropdown-menu-right">					
					<a href="login.html" class="dropdown-item"><i class="icon-sign-in"></i> Login</a>
				</div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>  
  
    <div class="app-content container-fluid">
      <div class="content-wrapper">
        <div class="content-header row">
			<div class="col-lg-8 offset-lg-2">
				<div style="margin-top: 30px;">
					<div class="alert alert-info mb-2" role="alert">
						Use a barcode scanner and scan document's barcode or just enter barcode to <strong>track</strong> document
					</div>
				</div>
			</div>	
		</div>
        <div class="content-body">
			<div class="row">
				<div class="col-lg-8 offset-lg-2">
					<div class="card">
						<div class="card-header">
							<h4 class="card-title" id="basic-layout-form">Track Document</h4>
						</div>
						<div class="card-body collapse in">
							<div class="card-block">
								<div class="card-text">
									<form name="formHolder.track" novalidate autocomplete="off">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon"><i class="icon-barcode"></i></span>
												<input type="text" class="form-control square" name="barcode" ng-model="doc.barcode" ng-class="{'border-danger': formHolder.track.barcode.$touched && formHolder.track.barcode.$invalid}" required>
												<span class="input-group-addon" style="cursor: pointer;" ng-click="app.track(this)">Track</span>
											</div>
											<span class="help-block danger" ng-show="formHolder.track.barcode.$touched && formHolder.track.barcode.$invalid">Barcode is required</span>
										</div>
									</form>
								</div>
								<div id="tracks"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
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
	<script src="angular/modules/validation/validate-dialog.js<?=$ver?>"></script>	
	<script src="angular/modules/post/window-open-post.js<?=$ver?>"></script>

	<!-- modules -->
	<script src="modules/module-access.js<?=$ver?>"></script>	
	<script src="common/track-document.js<?=$ver?>"></script>	
	<script src="modules/track.js<?=$ver?>"></script>

	<!-- controller -->
	<script src="controllers/track.js<?=$ver?>"></script>  
  
  </body>
  
</html>