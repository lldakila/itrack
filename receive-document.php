<?php
	
	require_once 'authentication.php';
	require_once 'updates.php';
	$page = "receive-document";
	
?>
<!DOCTYPE html>
<html lang="en" data-textdirection="ltr" class="loading">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="PGLU iTrack - Document Tracking System">
    <meta name="keywords" content="iTrack, PGLU">
    <meta name="author" content="slyflores">
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
  <body ng-app="document" ng-controller="documentCtrl" data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns  fixed-navbar" account-profile>

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
				<div class="col-lg-6">
					<button class="btn btn-info" ng-click="app.add(this)" ng-show="controls.add" ng-disabled="!controls.btns.ok"><i class="icon-plus3"></i> Add</button>
					<button class="btn btn-info" ng-click="app.edit(this)" ng-show="controls.edit" ng-disabled="!controls.btns.ok"><i class="icon-edit2"></i> Edit</button>
				</div>
				<div class="content-header-right breadcrumbs-right breadcrumbs-top col-lg-6">
					<div class="breadcrumb-wrapper col-xs-12">
					  <ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.html" class="info">iTrack</a>
						</li>
						<li class="breadcrumb-item"><a href="documents.html" class="info">Documents</a>
						</li>
						<li class="breadcrumb-item active">Add Document
						</li>
					  </ol>
					</div>
				</div>
			</div><hr>
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4 class="card-title" id="basic-layout-form">Add document for tracking</h4>
							<a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
							<div class="heading-elements">
								<ul class="list-inline mb-0">
									<li><a href="javascript:;" ng-click="app.receipt(this)" data-toggle="tooltip" data-placement="top" title="Print"><i class="icon-print"></i></a></li>
								</ul>
							</div>
						</div>
						<div class="card-body collapse in" aria-expanded="true" style="">
							<div class="card-block">
								<form class="form" novalidate autocomplete="off" name="formHolder.doc">
									<div class="form-body">
										<div class="row">
											<div class="col-lg-6">
												<div class="alert alert-warning mb-2" role="alert">
													<strong>Enter</strong> the information of the document to be tracked and monitored.<br><strong>Print</strong> the receipt and provide the originating office a copy.<br>The <strong>barcode</strong> will served as the tracking number.
												</div>					
											</div>							
										</div>
										<hr>
										<div class="row">
											<div class="col-lg-6">
												<label>Is this document urgent?</label>
												<div class="input-group">
													<label class="display-inline-block custom-control custom-radio">
														<input type="radio" name="is_rush" ng-model="doc.is_rush" ng-value="false" class="custom-control-input" ng-disabled="controls.btns.ok">
														<span class="custom-control-indicator"></span>
														<span class="custom-control-description ml-0">No</span>
													</label>												
													<label class="display-inline-block custom-control custom-radio ml-1">
														<input type="radio" name="is_rush" ng-model="doc.is_rush" ng-value="true" class="custom-control-input" ng-disabled="controls.btns.ok">
														<span class="custom-control-indicator"></span>
														<span class="custom-control-description ml-0">Yes</span>
													</label>
												</div>												
											</div>
											<div class="col-lg-6">
												<canvas id="barcode" style="display: none;"></canvas>
												<div class="form-group">
													<label>Barcode</label>
													<div class="position-relative has-icon-left">
														<input type="text" class="form-control" name="barcode" ng-model="doc.barcode" ng-disabled="true">
														<div class="form-control-position">
															<i class="icon-barcode"></i>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-4">
												<div class="form-group">
													<label>Originating Office</label>
													<!-- <select class="form-control" name="origin" ng-class="{'border-danger': formHolder.doc.origin.$touched && formHolder.doc.origin.$invalid}" ng-model="doc.origin" ng-options="o.office for o in offices track by o.id" ng-disabled="controls.btns.ok" required></select> -->
													<input type="text" class="form-control" name="origin" ng-class="{'border-danger': formHolder.doc.origin.$touched && formHolder.doc.origin.$invalid}" ng-model="doc.origin" uib-typeahead="o.shortname for o in offices | filter:{shortname:$viewValue}" typeahead-on-select="app.originSelected(this, $item, $model, $label, $event)" ng-disabled="controls.btns.ok" required>
													<span class="help-block danger" ng-show="formHolder.doc.origin.$touched && formHolder.doc.origin.$invalid">Office is required</span>
												</div>
											</div>
											<div class="col-lg-4">
												<div class="form-group">
													<label>Document Type</label>
													<select class="form-control" name="doc_type" ng-class="{'border-danger': formHolder.doc.doc_type.$touched && formHolder.doc.doc_type.$invalid}" ng-model="doc.doc_type" ng-options="d.document_type for d in document_types track by d.id" ng-change="app.dtParams(this,doc.doc_type)" ng-disabled="controls.btns.ok" required></select>
													<span class="help-block danger" ng-show="formHolder.doc.doc_type.$touched && formHolder.doc.doc_type.$invalid">Document Type is required</span>
												</div>
												<div class="form-group" ng-repeat="param in dt_add_params">
													<label>{{param.description}}</label>
													<select ng-show="param.type=='select'" class="form-control" name="{{param.model}}" ng-model="param.value" ng-options="p.description for p in param.options track by p.id" ng-disabled="controls.btns.ok"></select>
													<input ng-show="param.type=='input'" type="text" class="form-control" name="{{param.model}}" ng-model="param.value" ng-disabled="controls.btns.ok">
												</div>												
											</div>
											<div class="col-lg-4">
												<div class="form-group">
													<label>Transaction <span ng-show="doc.document_transaction_type!=undefined"><strong>({{doc.document_transaction_type.days+' days'}})</strong></span></label>
													<select class="form-control" name="document_transaction_type" ng-class="{'border-danger': formHolder.doc.document_transaction_type.$touched && formHolder.doc.document_transaction_type.$invalid}" ng-model="doc.document_transaction_type" ng-options="t.transaction for t in transactions track by t.id" ng-disabled="controls.btns.ok" required></select>
													<span class="help-block danger" ng-show="formHolder.doc.document_transaction_type.$touched && formHolder.doc.document_transaction_type.$invalid">Transaction is required</span>
												</div>
											</div>											
										</div>
										
										<div class="row" ng-show="doc.origin.office == 'Others'">
											<div class="col-lg-4">
												<div class="form-group">
													<label>If Other Office</label>
													<input type="text" class="form-control" name="other_origin" ng-class="{'border-danger': formHolder.doc.other_origin.$touched && formHolder.doc.other_origin.$invalid}" ng-model="doc.other_origin" ng-disabled="controls.btns.ok" ng-required="doc.origin.office == 'Others'">
													<span class="help-block danger" ng-show="formHolder.doc.other_origin.$touched && formHolder.doc.other_origin.$invalid">Office is required</span>
												</div>
											</div>
										</div>
										
										<div class="row">
											<div class="col-lg-4">
												<div class="form-group">
													<label>Communication</label>
													<select class="form-control" name="communication" ng-class="{'border-danger': formHolder.doc.communication.$touched && formHolder.doc.communication.$invalid}" ng-model="doc.communication" ng-options="c.communication for c in communications track by c.id" ng-disabled="controls.btns.ok" required></select>
													<span class="help-block danger" ng-show="formHolder.doc.communication.$touched && formHolder.doc.communication.$invalid">Communication is required</span>
												</div>
											</div>
											<div class="col-lg-8">
												<div class="form-group">
													<label>Subject</label>
													<!-- <input type="text" class="form-control" name="doc_name" ng-class="{'border-danger': formHolder.doc.doc_name.$touched && formHolder.doc.doc_name.$invalid}" ng-model="doc.doc_name" ng-disabled="controls.btns.ok" required> -->
													<textarea rows="5" class="form-control" name="doc_name" ng-class="{'border-danger': formHolder.doc.doc_name.$touched && formHolder.doc.doc_name.$invalid}" ng-model="doc.doc_name" ng-disabled="controls.btns.ok" required></textarea>
													<span class="help-block danger" ng-show="formHolder.doc.doc_name.$touched && formHolder.doc.doc_name.$invalid">Subject is required</span>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-6">
												<div class="form-group">
													<label>Remarks</label>
													<textarea rows="5" class="form-control" name="remarks" ng-model="doc.remarks" ng-disabled="controls.btns.ok"></textarea>
												</div>	
											</div>
											<div class="col-lg-6">
												<label>Actions</label>
												<div id="headingForInitial" class="card-header">
													<div class="row">
														<div class="col-lg-3">
															<a data-toggle="collapse" href="#{{(!controls.btns.ok)?'for_initial':''}}" aria-expanded="false" aria-controls="for_initial" class="card-title collapsed" ng-click="app.headerActionParam(this,'for_initial')"><span class="text-muted">{{doc.actions.for_initial.description}}</span></a>
														</div>
														<div class="col-lg-8">
															<div class="checkbox">
																<label class="switch">
																  <input type="checkbox" hidden="hidden" name="for_initial" ng-model="doc.actions.for_initial.value" ng-checked="doc.actions.for_initial.value" ng-click="app.checkboxActionParam(this,'for_initial')" ng-disabled="controls.btns.ok">
																  <span class="slider round"></span>
																</label>
															</div>
														</div>
													</div>
												</div>
												<div id="for_initial" role="tabpanel" aria-labelledby="headingForInitial" class="card-collapse collapse" aria-expanded="false" style="">
													<div class="card-body">
														<div class="card-block">
															<div class="form-group" ng-repeat="param in doc.actions.for_initial.params">
																<label>{{param.description}}</label>
																<select ng-show="param.type=='select'" class="form-control" name="{{param.model}}" ng-model="param.value" ng-options="p.description for p in param.options track by p.id" ng-disabled="controls.btns.ok"></select>
																<input ng-show="param.type=='input'" type="text" class="form-control" name="{{param.model}}" ng-model="param.value" ng-disabled="controls.btns.ok">
																<div ng-show="param.type=='checkbox'" style="margin-left: 20px;" class="form-check" ng-repeat="po in param.options">
																	<input type="checkbox" class="form-check-input" ng-model="po.value" ng-checked="po.value" id="fi{{$index}}" ng-disabled="controls.btns.ok">
																	<label class="form-check-label" for="fi{{$index}}">{{po.description}}</label>
																</div>															
															</div>														
														</div>
													</div>
												</div>
												<div id="headingForSignature" class="card-header">
													<div class="row">
														<div class="col-lg-3">												
															<a data-toggle="collapse" href="#{{(!controls.btns.ok)?'for_signature':''}}" aria-expanded="false" aria-controls="for_signature" class="card-title collapsed" ng-click="app.headerActionParam(this,'for_signature')"><span class="text-muted">{{doc.actions.for_signature.description}}</span></a>
														</div>
														<div class="col-lg-8">
															<div class="checkbox">
																<label class="switch">
																  <input type="checkbox" hidden="hidden" name="for_signature" ng-model="doc.actions.for_signature.value" ng-checked="doc.actions.for_signature.value" ng-click="app.checkboxActionParam(this,'for_signature')" ng-disabled="controls.btns.ok">
																  <span class="slider round"></span>
																</label>
															</div>
														</div>														
													</div>
												</div>
												<div id="for_signature" role="tabpanel" aria-labelledby="headingForSignature" class="card-collapse collapse" aria-expanded="false" style="">
													<div class="card-body">
														<div class="card-block">
															<div class="form-group" ng-repeat="param in doc.actions.for_signature.params">
																<label>{{param.description}}</label>
																<select ng-show="param.type=='select'" class="form-control" name="{{param.model}}" ng-model="param.value" ng-options="p.description for p in param.options track by p.id" ng-disabled="controls.btns.ok"></select>
																<input ng-show="param.type=='input'" type="text" class="form-control" name="{{param.model}}" ng-model="param.value" ng-disabled="controls.btns.ok">
																<div ng-show="param.type=='checkbox'" style="margin-left: 20px;" class="form-check" ng-repeat="po in param.options">
																	<input type="checkbox" class="form-check-input" ng-model="po.value" ng-checked="po.value" id="fs{{$index}}" ng-disabled="controls.btns.ok">
																	<label class="form-check-label" for="fs{{$index}}">{{po.description}}</label>
																</div>															
															</div>
														</div>
													</div>
												</div>
												<div ng-show="for_routing.params.length>0" id="headingForRouting" class="card-header">
													<div class="row">
														<div class="col-lg-3">													
															<a data-toggle="collapse" href="#{{(!controls.btns.ok)?'for_routing':''}}" aria-expanded="false" aria-controls="for_routing" class="card-title collapsed" ng-click="app.headerActionParam(this,'for_routing')"><span class="text-muted" style="">{{doc.actions.for_routing.description}}</span></a>
														</div>
														<div class="col-lg-8">
															<div class="checkbox">
																<label class="switch">
																  <input type="checkbox" hidden="hidden" name="for_routing" ng-model="doc.actions.for_routing.value" ng-checked="doc.actions.for_routing.value" ng-click="app.checkboxActionParam(this,'for_routing')" ng-disabled="controls.btns.ok">
																  <span class="slider round"></span>
																</label>
															</div>
														</div>
													</div>
												</div>
												<div ng-show="for_routing.params.length>0" id="for_routing" role="tabpanel" aria-labelledby="headingForRouting" class="card-collapse collapse" aria-expanded="false" style="">
													<div class="card-body">
														<div class="card-block">
															<div class="form-group" ng-repeat="param in doc.actions.for_routing.params">
																<label>{{param.description}}</label>
																<select ng-show="param.type=='select'" class="form-control" name="{{param.model}}" ng-model="param.value" ng-options="p.description for p in param.options track by p.id" ng-disabled="controls.btns.ok"></select>
																<input ng-show="param.type=='input'" type="text" class="form-control" name="{{param.model}}" ng-model="param.value" ng-disabled="controls.btns.ok">
																<div ng-show="param.type=='checkbox'" style="margin-left: 20px;" class="form-check" ng-repeat="po in param.options">
																	<input type="checkbox" class="form-check-input" ng-model="po.value" ng-checked="po.value" id="fr{{$index}}" ng-disabled="controls.btns.ok">
																	<label class="form-check-label" for="fr{{$index}}">{{po.description}}</label>
																</div>															
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-12">
												<div class="form-group">
													<label>Files:</label>
													<div class="images-container">
														<div class="image-container" ng-repeat="df in documentFiles">
															<div class="controls">
																<a href="javascript:;" class="control-btn remove" remove-file="{{$index}}">
																	<i class="icon-trash4"></i>
																</a>
															</div>
															<object id="dfpdf{{$index}}" class="object" data="" ng-show="df.type == 'pdf'"></object>
															<img id="dfimg{{$index}}" class="image" src="" ng-show="df.type == 'jpeg' || df.type == 'png'">															
														</div>
														<input type="file" style="display: none;" id="upload-files" add-files multiple>														
														<a href="javascript:;" class="add-image" ng-click="app.addFile(this)">
															<div class="image-container new">
																<div class="image">
																	<i class="icon-plus4"></i>
																</div>
															</div>
														</a>														
													</div>
												</div>
											</div>										
										</div>
										<hr>
										<div class="row">
											<div class="col-lg-12">
												<button class="btn btn-secondary float-lg-right" ng-disabled="controls.btns.cancel" ng-click="app.cancel(this)" ng-show="controls.cancel">{{controls.labels.cancel}}</button>											
												<button class="btn btn-info float-lg-right" ng-click="app.save(this)" ng-disabled="controls.btns.ok" ng-show="controls.ok" style="margin-right: 10px;">Save</button>
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
	
	<link rel="stylesheet" href="angular/modules/bootbox/bs4-fix.css<?=$ver?>">	
	<script src="angular/modules/bootbox/bootbox.min.js<?=$ver?>"></script>
	<script src="angular/modules/growl/jquery.bootstrap-growl.min.js<?=$ver?>"></script>
	<script src="angular/modules/blockui/jquery.blockUI.js<?=$ver?>"></script>
	<link rel="stylesheet" href="angular/modules/bootbox/bs4-fix.css<?=$ver?>">
	
	<!-- dependencies -->
	<script src="angular/angular.min.js<?=$ver?>"></script>
	<script src="angular/angular-route.min.js<?=$ver?>"></script>
	<script src="angular/angular-sanitize.min.js<?=$ver?>"></script>
	<!-- <script src="angular/ui-bootstrap-tpls-3.0.2.min.js"></script> -->
	<script src="angular/ui-bootstrap-tpls-3.0.6.min.js<?=$ver?>"></script>
	
	<script src="angular/modules/account/account.js<?=$ver?>"></script>
	<script src="angular/modules/bootbox/bootstrap-modal.js<?=$ver?>"></script>
	<script src="angular/modules/growl/growl.js<?=$ver?>"></script>
	<script src="angular/modules/blockui/blockui.js<?=$ver?>"></script>
	<script src="angular/modules/validation/validate.js<?=$ver?>"></script>
	<script src="angular/modules/post/window-open-post.js<?=$ver?>"></script>
	
	<!-- modules -->
	<script src="modules/notifications.js<?=$ver?>"></script>
	<script src="modules/module-access.js<?=$ver?>"></script>
	<script src="modules/jspdf.js<?=$ver?>"></script>
	<script src="modules/upload-files.js<?=$ver?>"></script>
	<script src="/common/prints.js<?=$ver?>"></script>	
	<script src="modules/receive-document.js<?=$ver?>"></script>
	
	<!-- controller -->
	<script src="controllers/receive-document.js<?=$ver?>"></script>
  </body>
</html>
