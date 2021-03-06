<?php
	
	require_once 'authentication.php';
	require_once 'updates.php';
	$page = "index";
	
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
  <body ng-app="dashboard" ng-controller="dashboardCtrl" data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns  fixed-navbar" account-profile>

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
              <li class="nav-item hidden-sm-down"><a class="nav-link nav-menu-main menu-toggle hidden-xs"><i class="icon-menu5"></i></a></li>
              <li class="nav-item hidden-sm-down"><a href="#" class="nav-link nav-link-expand"><i class="ficon icon-expand2"></i></a></li>
            </ul>
            <ul class="nav navbar-nav float-xs-right">
			  <?php require_once 'notifications.php'; ?>
              <li class="dropdown dropdown-user nav-item"><a href="javascript:;" data-toggle="dropdown" class="dropdown-toggle nav-link dropdown-user-link"><span class="avatar avatar-online"><img src="{{profile.picture}}" alt="avatar"><i></i></span><span class="user-name">{{profile.user}}</span></a>
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
		
			<div class="card">
				<div class="card-header">
					<h4 class="card-title" id="basic-layout-form"><a data-action="collapse">Filter Statistics</a></h4>
					<a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
					<div class="heading-elements">
						<ul class="list-inline mb-0">
							<li><a data-action="collapse"><i class="icon-plus4"></i></a></li>
						</ul>
					</div>
				</div>
				<div class="card-body collapse out">
					<div class="card-block">
						<form class="form" name="formHolder.filter" novalidate>
							<div class="form-body">
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label>Period:</label>
											<select class="form-control" ng-class="{'border-danger': formHolder.filter.period.$touched && formHolder.filter.period.$invalid}" name="period" ng-model="filter.period.selected" ng-options="period.text for period in periods track by period.period" ng-change="dashboard.periodChange(this)" required></select>
											<span class="help-block danger" ng-show="formHolder.filter.period.$touched && formHolder.filter.period.$invalid">Please select period</span>
										</div>
									</div>
									<div class="col-md-3" ng-show="filter.period.selected.period == 'date'">
										<div class="form-group">
											<label>Date:</label>
											<input type="date" class="form-control" ng-class="{'border-danger': formHolder.filter.date.$touched && formHolder.filter.date.$invalid}" name="date" ng-model="filter.period.date" ng-required="filter.period.selected.period == 'date'" ng-change="dashboard.updateCoverage(this)">
											<span class="help-block danger" ng-show="formHolder.filter.date.$touched && formHolder.filter.date.$invalid">Please select date</span>											
										</div>
									</div>									
									<div class="col-md-3" ng-show="filter.period.selected.period == 'week'">
										<div class="form-group">
											<label>From:</label>
											<input type="date" class="form-control" ng-class="{'border-danger': formHolder.filter.from.$touched && formHolder.filter.from.$invalid}" name="from" ng-model="filter.period.week.from" ng-required="filter.period.selected.period == 'week'" ng-change="dashboard.updateCoverage(this)">
											<span class="help-block danger" ng-show="formHolder.filter.from.$touched && formHolder.filter.from.$invalid">Please select from date</span>											
										</div>
									</div>									
									<div class="col-md-3" ng-show="filter.period.selected.period == 'week'">
										<div class="form-group">
											<label>To:</label>
											<input type="date" class="form-control" ng-class="{'border-danger': formHolder.filter.to.$touched && formHolder.filter.to.$invalid}" name="to" ng-model="filter.period.week.to" ng-required="filter.period.selected.period == 'week'" ng-change="dashboard.updateCoverage(this)">
											<span class="help-block danger" ng-show="formHolder.filter.to.$touched && formHolder.filter.to.$invalid">Please select to date</span>											
										</div>
									</div>
									<div class="col-md-3" ng-show="filter.period.selected.period == 'month'">
										<div class="form-group">
											<label>Month:</label>
											<select type="text" class="form-control" ng-class="{'border-danger': formHolder.filter.month.$touched && formHolder.filter.month.$invalid}" name="month" ng-model="filter.period.month" ng-options="month.text for month in months track by month.month" ng-required="filter.period.selected.period == 'month'" ng-change="dashboard.updateCoverage(this)"></select>
											<span class="help-block danger" ng-show="formHolder.filter.month.$touched && formHolder.filter.month.$invalid">Please select month</span>															
										</div>
									</div>
									<div class="col-md-3" ng-show="filter.period.selected.period == 'month' || filter.period.selected.period == 'year'">
										<div class="form-group">
											<label>Year:</label>
											<input type="text" class="form-control" ng-class="{'border-danger': formHolder.filter.year.$touched && formHolder.filter.year.$invalid}" name="year" ng-model="filter.period.year" ng-required="filter.period.selected.period == 'month' || filter.period.selected.period == 'year'" ng-change="dashboard.updateCoverage(this)">
											<span class="help-block danger" ng-show="formHolder.filter.year.$touched && formHolder.filter.year.$invalid">Please enter year</span>											
										</div>
									</div>									
								</div>
							</div>
							<div class="form-actions" style="text-align: right;">
								<button type="submit" class="btn btn-primary" ng-click="dashboard.filter(this);">
									<i class="icon-search"></i> Go
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		
		</div>
        <div class="content-body"><!-- stats -->		
			<div class="row">
				<div class="content-header-left col-md-6 col-xs-12 mb-1">
					<h2 class="content-header-title"><small class="text-muted">Coverage: </small>{{views.coverage}}</h2>
				</div>
				<div class="clearfix"></div>
			<hr>				
			</div>			

			<div ng-show="dashboard.data.opa.show">
				<h4 style="margin-bottom: 25px;">Office of the Provincial Administrator</h4>
				<div class="row">
					<div class="col-xl-3 col-lg-6 col-xs-12">
						<div class="card">
							<div class="card-body">
								<div class="card-block">
									<div class="media">
										<div class="media-body text-xs-left">
											<h3 class="deep-orange">{{dashboard.data.opa.new_documents}}</h3>
											<span>Received Documents</span>
										</div>
										<div class="media-right media-middle">
											<i class="icon-paper deep-orange font-large-2 float-xs-right"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xl-3 col-lg-6 col-xs-12">
						<div class="card">
							<div class="card-body">
								<div class="card-block">
									<div class="media">
										<div class="media-body text-xs-left">
											<h3 class="cyan">{{dashboard.data.opa.for_initial}}</h3>
											<span>For Initial</span>
										</div>
										<div class="media-right media-middle">
											<i class="icon-android-checkbox-outline cyan font-large-2 float-xs-right"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>														
					<div class="col-xl-3 col-lg-6 col-xs-12">
						<div class="card">
							<div class="card-body">
								<div class="card-block">
									<div class="media">
										<div class="media-body text-xs-left">
											<h3 class="teal">{{dashboard.data.opa.initialed_documents}}</h3>
											<span>Initialed</span>
										</div>
										<div class="media-right media-middle">
											<i class="icon-android-checkbox-outline teal font-large-2 float-xs-right"></i>
										</div>
									</div>
								</div>
							</div>
						</div>													
					</div>							
					<div class="col-xl-3 col-lg-6 col-xs-12">
						<div class="card">
							<div class="card-body">
								<div class="card-block">
									<div class="media">
										<div class="media-body text-xs-left">
											<h3 class="cyan">{{dashboard.data.opa.for_approval}}</h3>
											<span>For Signature</span>
										</div>
										<div class="media-right media-middle">
											<i class="icon-thumbsup cyan font-large-2 float-xs-right"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>														
					<div class="col-xl-3 col-lg-6 col-xs-12">
						<div class="card">
							<div class="card-body">
								<div class="card-block">
									<div class="media">
										<div class="media-body text-xs-left">
											<h3 class="teal">{{dashboard.data.opa.approved_documents}}</h3>
											<span>Approved</span>
										</div>
										<div class="media-right media-middle">
											<i class="icon-thumbsup teal font-large-2 float-xs-right"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>				
				</div>
			</div>
			

			<div ng-show="dashboard.data.opg.show">
				<h4 style="margin-bottom: 25px;">Office of the Provincial Governor</h4>
				<div class="row">
					<div class="col-xl-3 col-lg-6 col-xs-12">
						<div class="card">
							<div class="card-body">
								<div class="card-block">
									<div class="media">
										<div class="media-body text-xs-left">
											<h3 class="cyan">{{dashboard.data.opg.for_initial}}</h3>
											<span>For Initial</span>
										</div>
										<div class="media-right media-middle">
											<i class="icon-android-checkbox-outline cyan font-large-2 float-xs-right"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>														
					<div class="col-xl-3 col-lg-6 col-xs-12">
						<div class="card">
							<div class="card-body">
								<div class="card-block">
									<div class="media">
										<div class="media-body text-xs-left">
											<h3 class="teal">{{dashboard.data.opg.initialed_documents}}</h3>
											<span>Initialed</span>
										</div>
										<div class="media-right media-middle">
											<i class="icon-android-checkbox-outline teal font-large-2 float-xs-right"></i>
										</div>
									</div>
								</div>
							</div>
						</div>													
					</div>							
					<div class="col-xl-3 col-lg-6 col-xs-12">
						<div class="card">
							<div class="card-body">
								<div class="card-block">
									<div class="media">
										<div class="media-body text-xs-left">
											<h3 class="cyan">{{dashboard.data.opg.for_approval}}</h3>
											<span>For Signature</span>
										</div>
										<div class="media-right media-middle">
											<i class="icon-thumbsup cyan font-large-2 float-xs-right"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>														
					<div class="col-xl-3 col-lg-6 col-xs-12">
						<div class="card">
							<div class="card-body">
								<div class="card-block">
									<div class="media">
										<div class="media-body text-xs-left">
											<h3 class="teal">{{dashboard.data.opg.approved_documents}}</h3>
											<span>Approved</span>
										</div>
										<div class="media-right media-middle">
											<i class="icon-thumbsup teal font-large-2 float-xs-right"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>				
				</div>
			</div>
			
			<div ng-show="dashboard.data.office.show">
				<h4 style="margin-bottom: 25px;">{{dashboard.data.office.description}}</h4>
				<div class="row">
					<div class="col-xl-3 col-lg-6 col-xs-12">
						<div class="card">
							<div class="card-body">
								<div class="card-block">
									<div class="media">
										<div class="media-body text-xs-left">
											<h3 class="deep-orange">{{dashboard.data.office.outgoing}}</h3>
											<span>Outgoing</span>
										</div>
										<div class="media-right media-middle">
											<i class="icon-upload5 deep-orange font-large-2 float-xs-right"></i>
										</div>
									</div>
								</div>
							</div>
						</div>						
					</div>
					<!--<div class="col-xl-3 col-lg-6 col-xs-12">
						<div class="card">
							<div class="card-body">
								<div class="card-block">
									<div class="media">
										<div class="media-body text-xs-left">
											<h3 class="deep-orange">{{dashboard.data.office.incoming}}</h3>
											<span>Incoming</span>
										</div>
										<div class="media-right media-middle">
											<i class="icon-download5 deep-orange font-large-2 float-xs-right"></i>
										</div>
									</div>
								</div>
							</div>
						</div>						
					</div>-->					
				</div>
			</div>

			</div>
			
			<!-- <div class="row" style="margin-top: 30px;">
			
				<div class="col-xl-6 col-lg-6 col-xs-12">
					<div class="card">
						<div class="card-header">
							<h2>Incoming</h2>
						</div>
						<div class="card-body">
							<div id="documents-incoming" style="width: 200px; height: 200px; margin: 25px auto;"></div>
						</div>
						<div class="card-footer">
							<span class="text-muted">Total: </span><strong>{{dashboard.data.incoming_documents_pie.total}}</strong>
						</div>
					</div>					
				</div>
				
			</div> -->
			<!--/ stats -->

        </div>
      </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->


    <footer class="footer footer-static footer-light navbar-border">
      <p class="clearfix text-muted text-sm-center mb-0 px-2"><span class="float-md-left d-xs-block d-md-inline-block">Copyright  &copy; <b><?php echo date("Y"); ?></b> Document Tracking System. All rights reserved. </span></p>
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

	<!-- flot js -->
    <script src="vendors/flot/jquery.flot.min.js" type="text/javascript"></script>
    <script src="vendors/flot/jquery.colorhelpers.min.js" type="text/javascript"></script>
    <script src="vendors/flot/jquery.flot.canvas.min.js" type="text/javascript"></script>
    <script src="vendors/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
    <script src="vendors/flot/jquery.flot.crosshair.min.js" type="text/javascript"></script>
    <script src="vendors/flot/jquery.flot.errorbars.min.js" type="text/javascript"></script>
    <script src="vendors/flot/jquery.flot.fillbetween.min.js" type="text/javascript"></script>
    <script src="vendors/flot/jquery.flot.image.min.js" type="text/javascript"></script>
    <script src="vendors/flot/jquery.flot.navigate.min.js" type="text/javascript"></script>
    <script src="vendors/flot/jquery.flot.pie.min.js" type="text/javascript"></script>
    <script src="vendors/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
    <script src="vendors/flot/jquery.flot.selection.min.js" type="text/javascript"></script>
    <script src="vendors/flot/jquery.flot.stack.min.js" type="text/javascript"></script>
    <script src="vendors/flot/jquery.flot.symbol.min.js" type="text/javascript"></script>
    <script src="vendors/flot/jquery.flot.threshold.min.js" type="text/javascript"></script>
    <script src="vendors/flot/jquery.flot.time.min.js" type="text/javascript"></script>
	
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
	<script src="modules/notifications.js<?=$ver?>"></script>
	<script src="modules/module-access.js<?=$ver?>"></script>
	<!-- <script src="modules/barcode-listener.js"></script> -->
	<script src="modules/dashboard.js<?=$ver?>"></script>

	<!-- controller -->
	<script src="controllers/dashboard.js<?=$ver?>"></script>
  </body>
</html>
