<ul id="main-menu-navigation" data-menu="menu-navigation" class="navigation navigation-main">
	<li class="nav-item"><a href="<?=$url?>index.html"><i class="icon-dashboard"></i><span class="menu-title">Dashboard</span></a></li>
	<li class=" nav-item"><a href="#"><i class="icon-copy2"></i><span class="menu-title">Documents</span></a>
		<ul class="menu-content">
		  <li><a href="<?=$url?>receive-document.html" class="menu-item"></i>Receive Document</a></li>
		  <li class="<?=($page=="action")?'active':''?>"><a href="<?=$url?>update-tracks.html" class="menu-item">Update Tracks</a></li>
		  <li><a href="<?=$url?>documents.html" class="menu-item">Documents</a></li>
		</ul>
	</li>
	<li class=" nav-item"><a href="#"><i class="icon-group"></i><span class="menu-title">Accounts</span></a>
		<ul class="menu-content">
		  <li><a href="<?=$url?>account-add.html" class="menu-item">Add Account</a></li>
		  <li><a href="<?=$url?>account-list.html" class="menu-item">List</a></li>
		</ul>
	</li>
	<li class="nav-item"><a href="#"><i class="icon-sitemap"></i><span class="menu-title">Groups</span></a>
		<ul class="menu-content">
		  <li><a href="<?=$url?>groups-add.html" class="menu-item">Add Group</a></li>
		  <li><a href="<?=$url?>groups-list.html" class="menu-item">List</a></li>
		</ul>
	</li>
	<li class="nav-item"><a href="#"><i class="icon-gear"></i><span data-i18n="nav.advance_cards.main" class="menu-title">Maintenance</span></a>
		<ul class="menu-content">
		  <li><a href="<?=$url?>offices.html" class="menu-item"></i>Offices</a></li>
		  <li><a href="<?=$url?>document-types.html" class="menu-item">Document Types</a></li>
		  <li><a href="<?=$url?>transactions.html" class="menu-item">Transactions</a></li>
		  <li><a href="<?=$url?>options.html" class="menu-item">Options</a></li>
		</ul>
	</li>
</ul>