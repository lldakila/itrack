<?php
	
	require_once 'authentication.php';
	require_once 'updates.php';	
	$page = "documents";
	
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
  <body ng-app="documents" ng-controller="documentsCtrl" data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns  fixed-navbar" account-profile listen-barcode>

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
					<h2 class="content-header-title">Documents</h2>
				</div>
				<div class="content-header-right breadcrumbs-right breadcrumbs-top col-md-6 col-xs-12">
				<div class="breadcrumb-wrapper col-xs-12">
				  <ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="index.html" class="info">iTrack</a>
					</li>
					<li class="breadcrumb-item active">Documents
					</li>
				  </ol>
				</div>
			  </div>
			</div>
			
			<div class="row">
				<div class="col-lg-12">
					<div class="card" style="margin-top: 15px;">
						<div class="card-header">
							<h4 class="card-title" id="basic-layout-form">Filter Documents</h4>						
						</div>
						<div class="card-body collapse in">
							<div class="card-block">
								<div class="card-text"></div>
								<form class="form" name="formHolder.filter">
									<div class="form-body">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label>Office</label>
													<select class="form-control" ng-model="filter.origin" ng-options="office.shortname for office in criteria.offices track by office.id" name="origin"></select>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>Communication</label>
													<select class="form-control" ng-model="filter.communication" ng-options="communication.communication for communication in criteria.communications track by communication.id" name="communication"></select>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label>Transaction</label>
													<select class="form-control" ng-model="filter.document_transaction_type" ng-options="transaction.transaction for transaction in criteria.transactions track by transaction.id" name="document_transaction_type"></select>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>Document Type</label>
													<select class="form-control" ng-model="filter.doc_type" ng-options="doc_type.document_type for doc_type in criteria.doc_types track by doc_type.id" name="doc_type"></select>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label>Barcode</label>
													<input class="form-control" ng-model="filter.barcode">
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
				<div class="col-xs-12">
					<div class="card">
						<div class="card-header">
							<h4 class="card-title">List</h4>
							<a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
							<div class="heading-elements">
								<ul class="list-inline mb-0">
									<li><a data-action="collapse"><i class="icon-minus4"></i></a></li>
									<li><a data-action="reload" ng-click="app.list(this)"><i class="icon-reload"></i></a></li>
									<li><a data-action="expand"><i class="icon-expand2"></i></a></li>
								</ul>
							</div>
						</div>
						<div class="card-body collapse in">
						
							<div class="card-block">
							<div class="row">
								<div class="position-relative has-icon-left col-lg-4 offset-lg-8">
									<input type="text" ng-model="search" class="form-control" placeholder="Search. . . " style="height: 35px;">
									<div class="form-control-position">
										<i class="icon-search"></i>
									</div>
								</div>
							</div>						
						
							<div class="table-hover" style="margin-top: 25px;">
								<table class="table table-bordered" style="font-size: 12px;">
									<thead>
										<tr>
											<th>#</th>
											<th>Barcode</th>
											<th>Subject</th>
											<th>Document Type</th>
											<th>Originating Office</th>
											<th>Communication</th>
											<th>Transaction</th>
											<th>Recent Status</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="d in documents | filter:search">
											<td>{{d.id}}</td>
											<td>{{d.barcode}}</td>
											<td>{{d.doc_name}}</td>
											<td>{{d.doc_type.document_type}}</td>
											<td>{{d.origin.office}}</td>
											<td>{{d.communication.communication}}</td>
											<td>{{d.document_transaction_type.transaction}}</td>
											<td>{{d.recent_status}}</td>
											<td width="130px;">
												<button class="btn btn-sm btn-info" ng-click="app.view(this,d)"><i class="icon-search7"></i></button>
												<button class="btn btn-sm btn-secondary" ng-click="app.delete(this,d)" ng-show="profile.pages_access.documents.delete.delete_document"><i class="icon-trash-o"></i></button>
											</td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="9" style="text-align: right;">
												<div class="float-right"><ul uib-pagination direction-links="false" boundary-links="true" total-items="pagination.count" items-per-page="pagination.entryLimit" ng-model="pagination.currentPage" max-size="pagination.noOfPages" template-url="angular/modules/my-pagination/my-pagination.html" ng-click="pageChanged()"></ul></div>
											</td>
										</tr>											
									</tfoot>
								</table>
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
	<script src="angular/angular.min.js<?=$ver?>"></script>
	<script src="angular/angular-route.min.js<?=$ver?>"></script>
	<script src="angular/angular-sanitize.min.js<?=$ver?>"></script>
	<script src="angular/ui-bootstrap-tpls-3.0.6.min.js<?=$ver?>"></script>	

	<script src="angular/modules/account/account.js<?=$ver?>"></script>
	<script src="angular/modules/bootbox/bootstrap-modal.js<?=$ver?>"></script>
	<script src="angular/modules/growl/growl.js<?=$ver?>"></script>
	<script src="angular/modules/blockui/blockui.js<?=$ver?>"></script>
	<script src="angular/modules/validation/validate.js<?=$ver?>"></script>
	<script src="angular/modules/validation/validate-dialog.js<?=$ver?>"></script>
	<script src="angular/modules/post/window-open-post.js<?=$ver?>"></script>
	<script src="angular/modules/my-pagination/my-pagination.js<?=$ver?>"></script>

	<!-- modules -->
	<script src="modules/notifications.js<?=$ver?>"></script>
	<script src="modules/module-access.js<?=$ver?>"></script>
	<script src="modules/barcode-listener-document.js<?=$ver?>"></script>
	<script src="modules/documents.js<?=$ver?>"></script>

	<!-- controller -->
	<script src="controllers/documents.js<?=$ver?>"></script>
  </body>
</html>
