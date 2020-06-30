<?php

	require_once 'authentication.php';
	require_once 'updates.php';

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
    <link rel="stylesheet" type="text/css" href="assets/css/files.css">
    <!-- END Custom CSS-->
  </head>
  <body ng-app="formFd" ng-controller="formFdCtrl" data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns  fixed-navbar" account-profile>

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
              <li class="dropdown dropdown-notification nav-item">
				<a href="#" data-toggle="dropdown" class="nav-link nav-link-label"><i class="ficon icon-bell4"></i><span class="tag tag-pill tag-default tag-danger tag-default tag-up">5</span></a>
                <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right"></ul>
              </li>
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
        <input type="text" placeholder="Search" class="menu-search form-control round"/>
      </div>
      <!-- / main menu header-->
      <!-- main menu content-->
      <div class="main-menu-content">
        <ul id="main-menu-navigation" data-menu="menu-navigation" class="navigation navigation-main">
			<li class="nav-item"><a href="index.html"><i class="icon-dashboard"></i><span class="menu-title">Dashboard</span></a></li>
			<li class="nav-item"><a href="#"><i class="icon-copy2"></i><span class="menu-title">Documents</span></a>
				<ul class="menu-content">
				  <li class="active"><a href="receive-document.html" class="menu-item">Receive Document</a></li>
				  <li><a href="documents.html" class="menu-item">Documents</a></li>
				</ul>
			</li>
			<li class="nav-item"><a href="#"><i class="icon-group"></i><span class="menu-title">Accounts</span></a>
				<ul class="menu-content">
				  <li><a href="account-add.html" class="menu-item">Add Account</a></li>
				  <li><a href="account-list.html" class="menu-item">List</a></li>
				</ul>
			</li>
			<li class="nav-item"><a href="#"><i class="icon-sitemap"></i><span class="menu-title">Groups</span></a>
				<ul class="menu-content">
				  <li><a href="groups-add.html" class="menu-item">Add Group</a></li>
				  <li><a href="groups-list.html" class="menu-item">List</a></li>
				</ul>
			</li>
			<li class="nav-item"><a href="#"><i class="icon-gear"></i><span class="menu-title">Maintenance</span></a>
				<ul class="menu-content">
				  <li><a href="offices.html" class="menu-item">Offices</a></li>
				  <li><a href="document-types.html" class="menu-item">Document Types</a></li>
				  <li><a href="transactions.html" class="menu-item">Transactions</a></li>
				  <li><a href="options.html" class="menu-item">Options</a></li>
				</ul>
			</li>
		</ul>
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
				<div class="col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4 class="card-title" id="basic-layout-form">Form</h4>
						</div>
						<div class="card-body collapse in" aria-expanded="true" style="">
							<div class="card-block">
								<form class="form" id="info" name="formHolder.info" novalidate autocomplete="off">
									<div class="form-body">

										<div class="row">
											<div class="col-lg-6">
												<div class="form-group">
													<label>First Name</label>
													<input type="text" class="form-control" name="firstname" value="" required>
												</div>
											</div>
											<div class="col-lg-6">
												<div class="form-group">
													<label>Last Name</label>
													<input type="text" class="form-control" name="lastname" value="" required>
												</div>											
											</div>
										</div>

										<div class="row">
											<div class="col-lg-6">										
												<table>
													<thead>
														<tr><th>#</th><th>Name</th><th>Contact</th><th>Remarks</th></tr>
													</thead>
													<tbody>
														<tr><td>1</td><td><input type="text" class="form-control" name="contacts[0][name]" value=""></td><td><input type="text" class="form-control" name="contacts[0][contact_no]" value=""></td><td><input type="text" class="form-control" name="contacts[0][remarks]" value=""></td></tr>
														<tr><td>2</td><td><input type="text" class="form-control" name="contacts[1][name]" value=""></td><td><input type="text" class="form-control" name="contacts[1][contact_no]" value=""></td><td><input type="text" class="form-control" name="contacts[1][remarks]" value=""></td></tr>
														<tr><td>2</td><td><input type="text" class="form-control" name="contacts[2][name]" value=""></td><td><input type="text" class="form-control" name="contacts[2][contact_no]" value=""></td><td><input type="text" class="form-control" name="contacts[2][remarks]" value=""></td></tr>
													</tbody>
												</table>
											</div>
										</div>										
										
										<div class="row">
											<div class="col-lg-12">
												<button class="btn btn-info float-lg-right" ng-click="save(this)" style="margin-right: 10px;">Save</button>
												<div class="clearfix"></div>
											</div>
										</div>										
										
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		<!--/ stats -->
      </div>
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
	
    <script src="app-assets/js/jquery-barcode-scanner/jquery.scannerdetection.js" type="text/javascript"></script>
	
	<!-- jspdf and barcode -->
	<script src="app-assets/js/jspdf/jspdf.min.js"></script>
	<script src="app-assets/js/jspdf/jspdf.plugin.autotable.js"></script>
	<script src="app-assets/js/jsbarcode/JsBarcode.all.min.js"></script>
	
	<link rel="stylesheet" href="angular/modules/bootbox/bs4-fix.css">	
	<script src="angular/modules/bootbox/bootbox.min.js"></script>
	<script src="angular/modules/growl/jquery.bootstrap-growl.min.js"></script>
	<script src="angular/modules/blockui/jquery.blockUI.js"></script>
	<link rel="stylesheet" href="angular/modules/bootbox/bs4-fix.css">
	
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
	<script src="modules/jspdf.js<?=$ver?>"></script>
	<script src="modules/upload-files.js<?=$ver?>"></script>
	<script src="modules/form.js<?=$ver?>"></script>
	
	<!-- controller -->
	<script src="controllers/form.js<?=$ver?>"></script>
  </body>
</html>
