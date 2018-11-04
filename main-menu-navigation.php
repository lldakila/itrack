<ul id="main-menu-navigation" data-menu="menu-navigation" class="navigation navigation-main">
	<li class="nav-item<?=($page=="index")?' active':''?>"><a href="index.html"><i class="icon-dashboard"></i><span class="menu-title">Dashboard</span></a></li>
	<li class="nav-item"><a href="#"><i class="icon-copy2"></i><span class="menu-title">Documents</span></a>
		<ul class="menu-content">
		  <li class="<?=($page=="receive-document")?'active':''?>"><a href="receive-document.html" class="menu-item">Add Document</a></li>
		  <li class="<?=($page=="receive")?'active':''?>"><a href="receive.html" class="menu-item">Receive</a></li>
		  <li class="<?=($page=="update-tracks")?'active':''?>"><a href="update-tracks.html" class="menu-item">Update Tracks</a></li>
		  <li class="<?=($page=="track-document")?'active':''?>"><a href="track-document.html" class="menu-item">Track Document</a></li>
		  <li class="<?=($page=="documents")?'active':''?>"><a href="documents.html" class="menu-item">Documents</a></li>
		</ul>
	</li>
	<li class="nav-item"><a href="#"><i class="icon-group"></i><span class="menu-title">Accounts</span></a>
		<ul class="menu-content">
		  <li class="<?=($page=="account-add")?'active':''?>"><a href="account-add.html" class="menu-item">Add Account</a></li>
		  <li class="<?=($page=="account-list")?'active':''?>"><a href="account-list.html" class="menu-item">List</a></li>
		</ul>
	</li>
	<li class="nav-item"><a href="#"><i class="icon-sitemap"></i><span class="menu-title">Groups</span></a>
		<ul class="menu-content">
		  <li class="<?=($page=="groups-add")?'active':''?>"><a href="groups-add.html" class="menu-item">Add Group</a></li>
		  <li class="<?=($page=="groups-list")?'active':''?>"><a href="groups-list.html" class="menu-item">List</a></li>
		</ul>
	</li>
	<li class="nav-item"><a href="#"><i class="icon-gear"></i><span class="menu-title">Maintenance</span></a>
		<ul class="menu-content">
		  <li class="<?=($page=="offices")?'active':''?>"><a href="offices.html" class="menu-item">Offices</a></li>
		  <li class="<?=($page=="document-types")?'active':''?>"><a href="document-types.html" class="menu-item">Document Types</a></li>
		  <li class="<?=($page=="transactions")?'active':''?>"><a href="transactions.html" class="menu-item">Transactions</a></li>
		  <li class="<?=($page=="options")?'active':''?>"><a href="options.html" class="menu-item">Options</a></li>
		</ul>
	</li>
</ul>