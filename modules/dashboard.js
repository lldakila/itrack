angular.module('dashboard-module', ['ui.bootstrap','bootstrap-modal','block-ui','module-access']).factory('dashboard', function($http,$timeout,$compile,$window,bootstrapModal,bui,access) {
	
	function dashboard() {
		
		var self = this;
		
		self.load = function(scope) {
			
			var d = new Date();				
			
			scope.formHolder = {};
			
			scope.months = [
				{month: "01", text: "January"},
				{month: "02", text: "February"},
				{month: "03", text: "March"},
				{month: "04", text: "April"},
				{month: "05", text: "May"},
				{month: "06", text: "June"},
				{month: "07", text: "July"},
				{month: "08", text: "August"},
				{month: "09", text: "September"},
				{month: "10", text: "October"},
				{month: "11", text: "November"},
				{month: "12", text: "December"}
			];
			scope.periods = [
				{period: "date", text: "Daily"},
				{period: "week", text: "Weekly"},
				{period: "month", text: "Monthly"},
				{period: "year", text: "Annually"},
			];			
			
			scope.filter = {};
			scope.filter.period = {};
			scope.filter.period.selected = {period: "date", text: "Daily"};
			scope.filter.period.week = {};

			var date = (d.getMonth()+1)+'/'+d.getDate()+'/'+d.getFullYear();
			
			scope.filter.period.date = new Date(date);
			scope.views.coverage = scope.months[d.getMonth()].text+' '+d.getDate()+', '+d.getFullYear();
			
			self.filter(scope);		

		};
		
		self.periodChange = function(scope) {

			var d = new Date();
			var df = new Date();
			var dt = new Date();
		
			switch (scope.filter.period.selected.period) {
				
				case 'date':
				
					var date = (d.getMonth()+1)+'/'+d.getDate()+'/'+d.getFullYear();
					scope.filter.period.date = new Date(date);
					
					scope.views.coverage = scope.months[d.getMonth()].text+' '+d.getDate()+', '+d.getFullYear();					
				
				break;
				
				case 'week':
				
					var date = (d.getMonth()+1)+'/'+d.getDate()+'/'+d.getFullYear();
					scope.filter.period.week.from = new Date(date);			
					scope.filter.period.week.to = new Date(date);
					
					if (df.getDay()==0) {
						
						df.setDate(df.getDate()+1);
						scope.filter.period.week.from = df;
						
					} else {

						while (df.getDay()>=1) {
						
							scope.filter.period.week.from = df;
							df.setDate(df.getDate()-1);
							
						};
					
					};
					
					while (dt.getDay()<5) {
					
						scope.filter.period.week.to = dt;
						dt.setDate(dt.getDate()+1);
						
					};
					
					scope.views.coverage = scope.months[df.getMonth()].text+' '+df.getDate()+', '+df.getFullYear() + ' to ' + scope.months[dt.getMonth()].text+' '+dt.getDate()+', '+dt.getFullYear();
				
				break;		
				
				case 'month':
					
					scope.filter.period.month = scope.months[d.getMonth()];
					scope.filter.period.year = d.getFullYear();
					
					scope.views.coverage = scope.months[d.getMonth()].text + ', ' + d.getFullYear();
				
				break;
				
				case 'year':

					scope.filter.period.year = d.getFullYear();

					scope.views.coverage = d.getFullYear();
				
				break;
				
			};
			
		};
		
		self.updateCoverage = function(scope) {		
			
			switch (scope.filter.period.selected.period) {
				
				case 'date':
				
					var d = scope.filter.period.date;
					scope.views.coverage = scope.months[d.getMonth()].text+' '+d.getDate()+', '+d.getFullYear();
				
				break;
				
				case 'week':

					var df = scope.filter.period.week.from;			
					var dt = scope.filter.period.week.to;
					
					scope.views.coverage = scope.months[df.getMonth()].text+' '+df.getDate()+', '+df.getFullYear() + ' to ' + scope.months[dt.getMonth()].text+' '+dt.getDate()+', '+dt.getFullYear();
				
				break;		
				
				case 'month':
					
					scope.views.coverage = scope.filter.period.month.text + ', ' + scope.filter.period.year;
				
				break;
				
				case 'year':

					scope.views.coverage = scope.filter.period.year;
				
				break;
				
			};			
			
		};
		
		self.filter = function(scope) {
			
			$http({
				method: 'POST',				
				url: 'api/dashboard/data',
				data: scope.filter.period
			}).then(function success(response) {
				
				scope.dashboard.data = response.data;
				// incoming(scope);
				
			}, function error(response) {
				
			});
			
		};
		
		function incoming(scope) {
			
			var data = [
				{label: 'Received', color: '#4aadff', data: scope.dashboard.data.incoming_documents.received},
				{label: 'Pick Up', color: '#4a4aff', data: scope.dashboard.data.incoming_documents.pick_up},
			];		

			$.plot('#documents-incoming', data, {
				series: {
					pie: {
						show: true,
						radius: 1,
						label: {
							show: true,
							radius: 3/4,
							formatter: labelFormatter,
							background: {
								opacity: 0.5,
								color: '#000'
							}
						}
					}
				},
				legend: {
					show: false
				}
			});

			function labelFormatter(label, series) {
				return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + series.percent + "</div>";
			};			
			
		};
		
	};
	
	return new dashboard();
	
});