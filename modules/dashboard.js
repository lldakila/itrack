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
			
			// var data = []
			var series = Math.floor(Math.random() * 6) + 3;

			/* for (var i = 0; i < series; i++) {
				data[i] = {
					label: "Series" + (i + 1),
					data: Math.floor(Math.random() * 100) + 1
				}
			}; */

			var data = [
				{label: 'Received', color: '#ff5722', data: 75},
				{label: 'Pick Up', color: '#4cff22', data: 25},
			];
			
			$.plot('#placeholder', data, {
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

			/* setCode([
				"$.plot('#placeholder', data, {",
				"    series: {",
				"        pie: {",
				"            show: true,",
				"            radius: 3/4,",
				"            label: {",
				"                show: true,",
				"                radius: 3/4,",
				"                formatter: labelFormatter,",
				"                background: {",
				"                    opacity: 0.5,",
				"                    color: '#000'",
				"                }",
				"            }",
				"        }",
				"    },",
				"    legend: {",
				"        show: false",
				"    }",
				"});"
			]); */

			function labelFormatter(label, series) {
				return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
			};			
			
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
				
				break;		
				
				case 'month':
					
					scope.filter.period.month = scope.months[d.getMonth()];
					scope.filter.period.year = d.getFullYear();
				
				break;		
				
				case 'year':

					scope.filter.period.year = d.getFullYear();				
				
				break;			
				
			};		
			
		};
		
		self.filter = function(scope) {
			
			/* $http({
				
				method: 'POST',				
				url: 'api/dashboard',
				data: scope.filter.period
			}).then(function success(response) {
				
			}, function error(response) {
				
			}); */
			
		};
		
	};
	
	return new dashboard();
	
});