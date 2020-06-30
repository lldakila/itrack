<ul id="main-menu-navigation" data-menu="menu-navigation" class="navigation navigation-main">
	<li ng-show="profile.pages_access.dashboard.value" class="nav-item<?=($page=="index")?' active':''?>"><a href="index.php"><i class="icon-dashboard"></i><span class="menu-title">Dashboard</span></a></li>
	<li ng-show="profile.pages_access.receive_document.value || profile.pages_access.receive.value || profile.pages_access.update_tracks.value || profile.pages_access.track_document.value || profile.pages_access.documents.value" class="nav-item"><a href="#"><i class="icon-location4"></i><span class="menu-title">Tracks</span></a>
		<ul class="menu-content">
		  <li ng-show="profile.pages_access.receive_document.value" class="<?=($page=="receive-document")?'active':''?>"><a href="receive-document.php" class="menu-item">New</a></li>
		  <li ng-show="profile.pages_access.receive.value" class="<?=($page=="receive")?'active':''?>"><a href="receive.php" class="menu-item">Receive</a></li>
		  <li class="<?=($page=="file")?'active':''?>"><a href="file.php" class="menu-item">File Document</a></li>
		  <li ng-show="profile.pages_access.update_tracks.value" class="<?=($page=="update-tracks")?'active':''?>"><a href="update-tracks.php" class="menu-item">Update</a></li>
		  <li ng-show="profile.pages_access.track_document.value" class="<?=($page=="track-document")?'active':''?>"><a href="track-document.php" class="menu-item">Track</a></li>
		</ul>
	</li>
	<li ng-show="profile.pages_access.documents.value" class="nav-item<?=($page=="documents")?' active':''?>"><a href="documents.php"><i class="icon-android-document"></i><span class="menu-title">Documents</span></a></li>	
	<li class="nav-item"><a href="#"><i class="icon-android-clipboard"></i><span class="menu-title">Reports</span></a>
		<ul class="menu-content">
		  <li class="<?=($page=="reports_transactions")?'active':''?>"><a href="reports-tracks.php" class="menu-item">Tracks</a></li>
		</ul>
	</li>
	<li ng-show="profile.pages_access.accounts.value" class="nav-item"><a href="#"><i class="icon-group"></i><span class="menu-title">Accounts</span></a>
		<ul class="menu-content">
		  <li ng-show="profile.pages_access.accounts.value" class="<?=($page=="account-add")?'active':''?>"><a href="account-add.php" class="menu-item">Add Account</a></li>
		  <li ng-show="profile.pages_access.accounts.value" class="<?=($page=="account-list")?'active':''?>"><a href="account-list.php" class="menu-item">List</a></li>
		</ul>
	</li>
	<li ng-show="profile.pages_access.groups.value" class="nav-item"><a href="#"><i class="icon-sitemap"></i><span class="menu-title">Groups</span></a>
		<ul class="menu-content">
		  <li ng-show="profile.pages_access.groups.value" class="<?=($page=="groups-add")?'active':''?>"><a href="groups-add.php" class="menu-item">Add Group</a></li>
		  <li ng-show="profile.pages_access.groups.value" class="<?=($page=="groups-list")?'active':''?>"><a href="groups-list.php" class="menu-item">List</a></li>
		</ul>
	</li>
	<li ng-show="profile.pages_access.maintenance.value" class="nav-item"><a href="#"><i class="icon-gear"></i><span class="menu-title">Maintenance</span></a>
		<ul class="menu-content">
		  <li ng-show="profile.pages_access.maintenance.value" class="<?=($page=="offices")?'active':''?>"><a href="offices.php" class="menu-item">Offices</a></li>
		  <li ng-show="profile.pages_access.maintenance.value" class="<?=($page=="document-types")?'active':''?>"><a href="document-types.php" class="menu-item">Document Types</a></li>
		  <li ng-show="profile.pages_access.maintenance.value" class="<?=($page=="transactions")?'active':''?>"><a href="transactions.php" class="menu-item">Transactions</a></li>
		  <li ng-show="profile.pages_access.maintenance.value" class="<?=($page=="options")?'active':''?>"><a href="options.php" class="menu-item">Options</a></li>
		</ul>
	</li>
</ul>