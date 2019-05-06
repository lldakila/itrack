<ul id="main-menu-navigation" data-menu="menu-navigation" class="navigation navigation-main">
	<li class="nav-item" ng-show="profile.pages_access.dashboard.value"><a href="<?=$url?>index.html" class="menu-item"><i class="icon-dashboard"></i><span class="menu-title">Dashboard</span></a></li>
	<li class="nav-item" ng-show="profile.pages_access.receive_document.value || profile.pages_access.receive.value || profile.pages_access.update_tracks.value || profile.pages_access.track_document.value || profile.pages_access.documents.value"><a href="#"><i class="icon-copy2"></i><span class="menu-title">Documents</span></a>
		<ul class="menu-content">
		  <li ng-show="profile.pages_access.receive_document.value"><a href="<?=$url?>receive-document.html" class="menu-item"></i>Add Document</a></li>
		  <li ng-show="profile.pages_access.receive.value"><a href="<?=$url?>receive.html" class="menu-item">Incoming</a></li>		  
		  <li><a href="<?=$url?>file.html" class="menu-item">File Document</a></li>		  
		  <li class="<?=($page=="update-tracks")?'active':''?>" ng-show="profile.pages_access.update_tracks.value"><a href="<?=$url?>update-tracks.html" class="menu-item">Update Tracks</a></li>
		  <li class="<?=($page=="track-document")?'active':''?>" ng-show="profile.pages_access.track_document.value"><a href="<?=$url?>track-document.html" class="menu-item">Track Document</a></li>		  
		  <li><a href="<?=$url?>documents.html" class="menu-item" ng-show="profile.pages_access.documents.value">Documents</a></li>
		</ul>
	</li>
	<li class=" nav-item" ng-show="profile.pages_access.accounts.value"><a href="#"><i class="icon-group"></i><span class="menu-title">Accounts</span></a>
		<ul class="menu-content">
		  <li ng-show="profile.pages_access.accounts.value"><a href="<?=$url?>account-add.html" class="menu-item">Add Account</a></li>
		  <li ng-show="profile.pages_access.accounts.value"><a href="<?=$url?>account-list.html" class="menu-item">List</a></li>
		</ul>
	</li>
	<li class="nav-item" ng-show="profile.pages_access.groups.value"><a href="#"><i class="icon-sitemap"></i><span class="menu-title">Groups</span></a>
		<ul class="menu-content">
		  <li ng-show="profile.pages_access.groups.value"><a href="<?=$url?>groups-add.html" class="menu-item">Add Group</a></li>
		  <li ng-show="profile.pages_access.groups.value"><a href="<?=$url?>groups-list.html" class="menu-item">List</a></li>
		</ul>
	</li>
	<li class="nav-item" ng-show="profile.pages_access.maintenance.value"><a href="#"><i class="icon-gear"></i><span data-i18n="nav.advance_cards.main" class="menu-title">Maintenance</span></a>
		<ul class="menu-content">
		  <li ng-show="profile.pages_access.maintenance.value"><a href="<?=$url?>offices.html" class="menu-item"></i>Offices</a></li>
		  <li ng-show="profile.pages_access.maintenance.value"><a href="<?=$url?>document-types.html" class="menu-item">Document Types</a></li>
		  <li ng-show="profile.pages_access.maintenance.value"><a href="<?=$url?>transactions.html" class="menu-item">Transactions</a></li>
		  <li ng-show="profile.pages_access.maintenance.value"><a href="<?=$url?>options.html" class="menu-item">Options</a></li>
		</ul>
	</li>
</ul>