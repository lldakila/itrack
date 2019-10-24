angular.module('app-module',['bootstrap-modal','ui.bootstrap','notifications-module','block-ui','bootstrap-growl','module-access','barcode-listener-track']).factory('app',function($http,$timeout,$compile,$window,bootstrapModal,bui,access,growl) {

	function app() {
		
		var self = this;
		
		self.data = function(scope) {

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
			
			scope.views = {};

			scope.criteria = {};
			scope.filter = {};
			scope.filter.meta = {};
			
			scope.filter.period = {};
			scope.filter.period.selected = {period: "date", text: "Daily"};
			scope.filter.period.week = {};

			var d = new Date();	
			var date = (d.getMonth()+1)+'/'+d.getDate()+'/'+d.getFullYear();

			scope.filter.period.date = new Date(date);
			scope.views.coverage = scope.months[d.getMonth()].text+' '+d.getDate()+', '+d.getFullYear();						
						
			scope.documents = [];			
			scope.document = {};
			
			popFilter(scope);
			
			scope.reports = {};			
			
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

		function popFilter(scope) {
			
			$http({
				method: 'GET',
				url: 'document/filters/reports'
			}).then(function mySuccess(response) {
				
				scope.criteria = angular.copy(response.data);
				
				scope.filter.meta.origin = {"id":0,"office":"All","shortname":"All"};
				scope.filter.meta.communication = {"id":0,"communication":"All","shortname":"All"};
				scope.filter.meta.document_transaction_type = {"id":0,"transaction":"All","days":"All"};
				scope.filter.meta.doc_type = {"id":0,"document_type":"All"};
				scope.filter.meta.action = {"id":0,"description":"All","key":"","value":""};
					
			}, function myError(response) {
				
		
			});
			
		};

		self.filter = function(scope) {

			$http({
				method: 'POST',
				url: '/api/reports/documents',
				data: scope.filter
			}).then(function mySuccess(response) {
				
				generateReport(scope,response.data)
					
			}, function myError(response) {
				
		
			});

		};

		function generateReport(scope,data) {
		
			$.getJSON("/jsreports/designs/documents.json", function(report_def) {			
												
				scope.reports.report_def = report_def;				
				
				scope.reports.dataSources = [
					{
						"id": "documents",
						"name": "Documents Report",
						// "url": "/api/reports/documents"
						"data": data
					}
				];		
				
				jsreports.render({
					report_def: scope.reports.report_def,
					target: $("#reports-results"),
					datasets: scope.reports.dataSources,
					showToolbar: false,
					scaleFonts: true
				});
				
			});

		};
		
		self.print = function(scope) {
			
            jsreports.export({
                report_def: scope.reports.report_def,
                format: 'pdf',
                datasets: scope.reports.dataSources
            });
			
		};
		
	};	
	
	return new app();
	
});