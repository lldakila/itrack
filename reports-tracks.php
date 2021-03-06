<?php
	
	require_once 'authentication.php';
	require_once 'updates.php';
	$page = "reports_transactions";
	
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
	<link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,700,700italic,400italic,300italic' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Roboto+Mono:300' rel='stylesheet' type='text/css'>	
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
	<!-- BEGIN jsreports-->	
	<link href="jsreports/css/tomorrow-night-eighties.css" rel="stylesheet" type='text/css' />	
	<link href="jsreports/css/style.css" rel="stylesheet" type='text/css' />
	<!-- END jsreports-->	
  </head>
  <body ng-app="reportsDocuments" ng-controller="reportsDocumentsCtrl" data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns  fixed-navbar" account-profile listen-barcode>

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
        </div>
		<!-- stats -->
        <div class="content-body">
			<div class="row">
				<div class="content-header-left col-md-6 col-xs-12 mb-1">
					<h2 class="content-header-title">Tracks</h2>
				</div>
				<div class="content-header-right breadcrumbs-right breadcrumbs-top col-md-6 col-xs-12">
					<div class="breadcrumb-wrapper col-xs-12">
					  <ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.html" class="info">iTrack</a></li>
						<li class="breadcrumb-item active">Reports</li>
					  </ol>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="card" style="margin-top: 15px;">
						<div class="card-header">
							<h4 class="card-title" id="basic-layout-form">Filter Reports</h4>						
						</div>
						<div class="card-body collapse in">
							<div class="card-block">
								<div class="card-text"></div>
								<form class="form" name="formHolder.filter">
									<div class="form-body">
										<div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label>Period:</label>
													<select class="form-control" ng-class="{'border-danger': formHolder.filter.period.$touched && formHolder.filter.period.$invalid}" name="period" ng-model="filter.period.selected" ng-options="period.text for period in periods track by period.period" ng-change="app.periodChange(this)" required></select>
													<span class="help-block danger" ng-show="formHolder.filter.period.$touched && formHolder.filter.period.$invalid">Please select period</span>
												</div>
											</div>
											<div class="col-md-3" ng-show="filter.period.selected.period == 'date'">
												<div class="form-group">
													<label>Date:</label>
													<input type="date" class="form-control" ng-class="{'border-danger': formHolder.filter.date.$touched && formHolder.filter.date.$invalid}" name="date" ng-model="filter.period.date" ng-required="filter.period.selected.period == 'date'" ng-change="app.updateCoverage(this)">
													<span class="help-block danger" ng-show="formHolder.filter.date.$touched && formHolder.filter.date.$invalid">Please select date</span>											
												</div>
											</div>									
											<div class="col-md-3" ng-show="filter.period.selected.period == 'week'">
												<div class="form-group">
													<label>From:</label>
													<input type="date" class="form-control" ng-class="{'border-danger': formHolder.filter.from.$touched && formHolder.filter.from.$invalid}" name="from" ng-model="filter.period.week.from" ng-required="filter.period.selected.period == 'week'" ng-change="app.updateCoverage(this)">
													<span class="help-block danger" ng-show="formHolder.filter.from.$touched && formHolder.filter.from.$invalid">Please select from date</span>											
												</div>
											</div>									
											<div class="col-md-3" ng-show="filter.period.selected.period == 'week'">
												<div class="form-group">
													<label>To:</label>
													<input type="date" class="form-control" ng-class="{'border-danger': formHolder.filter.to.$touched && formHolder.filter.to.$invalid}" name="to" ng-model="filter.period.week.to" ng-required="filter.period.selected.period == 'week'" ng-change="app.updateCoverage(this)">
													<span class="help-block danger" ng-show="formHolder.filter.to.$touched && formHolder.filter.to.$invalid">Please select to date</span>											
												</div>
											</div>
											<div class="col-md-3" ng-show="filter.period.selected.period == 'month'">
												<div class="form-group">
													<label>Month:</label>
													<select type="text" class="form-control" ng-class="{'border-danger': formHolder.filter.month.$touched && formHolder.filter.month.$invalid}" name="month" ng-model="filter.period.month" ng-options="month.text for month in months track by month.month" ng-required="filter.period.selected.period == 'month'" ng-change="app.updateCoverage(this)"></select>
													<span class="help-block danger" ng-show="formHolder.filter.month.$touched && formHolder.filter.month.$invalid">Please select month</span>															
												</div>
											</div>
											<div class="col-md-3" ng-show="filter.period.selected.period == 'month' || filter.period.selected.period == 'year'">
												<div class="form-group">
													<label>Year:</label>
													<input type="text" class="form-control" ng-class="{'border-danger': formHolder.filter.year.$touched && formHolder.filter.year.$invalid}" name="year" ng-model="filter.period.year" ng-required="filter.period.selected.period == 'month' || filter.period.selected.period == 'year'" ng-change="app.updateCoverage(this)">
													<span class="help-block danger" ng-show="formHolder.filter.year.$touched && formHolder.filter.year.$invalid">Please enter year</span>											
												</div>
											</div>									
										</div>									
										<div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label>Origin</label>
													<select class="form-control" ng-model="filter.meta.origin" ng-options="origin.shortname for origin in criteria.origins track by origin.id" name="origin"></select>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label>Document Type</label>
													<select class="form-control" ng-model="filter.meta.doc_type" ng-options="doc_type.document_type for doc_type in criteria.doc_types track by doc_type.id" name="doc_type"></select>
												</div>
											</div>											
											<div class="col-md-3">
												<div class="form-group">
													<label>Communication</label>
													<select class="form-control" ng-model="filter.meta.communication" ng-options="communication.communication for communication in criteria.communications track by communication.id" name="communication"></select>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label>Transaction</label>
													<select class="form-control" ng-model="filter.meta.document_transaction_type" ng-options="transaction.transaction for transaction in criteria.transactions track by transaction.id" name="document_transaction_type"></select>
												</div>
											</div>											
										</div>
										<div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label>Office</label>
													<select class="form-control" ng-model="filter.meta.office" ng-options="office.shortname for office in criteria.offices track by office.id" name="office"></select>
												</div>
											</div>										
											<div class="col-md-3">
												<div class="form-group">
													<label>Action</label>
													<select class="form-control" ng-model="filter.meta.action" ng-options="action.description for action in criteria.actions track by action.id" name="action"></select>
												</div>
											</div>										
										</div>
									</div>
									<div class="form-actions">
										<button type="button" class="btn btn-primary float-lg-right float-xs-right" ng-click="app.filter(this)">Go!</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<!-- <div class="card-header">
							<h4 class="card-title">Results</h4>
							<a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
							<div class="heading-elements">
								<ul class="list-inline mb-0">
									<li><a href="javascript:;" ng-click="app.print(this)" data-toggle="tooltip" data-placement="top" title="Print"><i class="icon-print"></i></a></li>
								</ul>
							</div>							
						</div> -->
						<div class="card-body collapse in">
							<div class="card-block report-output" style="width: 100%;"></div>
						</div>
					</div>					
				</div>
			</div>
			<br>
        </div>
		<!--/ stats -->
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
	<script src="angular/angular.min.js"></script>
	<script src="angular/angular-route.min.js"></script>
	<script src="angular/angular-sanitize.min.js"></script>
	<script src="angular/ui-bootstrap-tpls-3.0.2.min.js"></script>	

	<script src="angular/modules/account/account.js<?=$ver?>"></script>
	<script src="angular/modules/bootbox/bootstrap-modal.js<?=$ver?>"></script>
	<script src="angular/modules/growl/growl.js<?=$ver?>"></script>
	<script src="angular/modules/blockui/blockui.js<?=$ver?>"></script>
	<script src="angular/modules/validation/validate.js<?=$ver?>"></script>
	<script src="angular/modules/validation/validate-dialog.js<?=$ver?>"></script>
	<script src="angular/modules/post/window-open-post.js<?=$ver?>"></script>

	<!-- jsreports -->
	<script type="text/javascript" src="jsreports/js/highlight.pack.js"></script>
	<script type="text/javascript" src="jsreports/js/jsreports-all.min.js?1.4.91"></script>

	<!-- modules -->
	<script src="modules/notifications.js<?=$ver?>"></script>
	<script src="modules/module-access.js<?=$ver?>"></script>
	<script src="modules/barcode-listener-track.js<?=$ver?>"></script>
	<script src="common/track-document.js<?=$ver?>"></script>	
	<script src="modules/reports-documents.js<?=$ver?>"></script>

	<!-- controller -->
	<script src="controllers/reports-documents.js<?=$ver?>"></script>
  </body>
</html>