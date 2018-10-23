var app = angular.module('formFd',[]);

app.controller('formFdCtrl',function($scope) {

	$scope.save = function() {

		var e = $("[name='formHolder.info']");

		var sa = e.serializeArray();

		var json = {};

		var arrs = [];

		sa.forEach(function(item,i) {

			if (/\[(.+)\]/.test(item.name)) {
				var o = {};
				o[item.name] = item.value || '';
				arrs.push(o);
			} else {
				json[item.name] = item.value || '';
			};

		});

		var i;

		for (i=0; i<arrs.length; ++i) {			
			
			var str = Object.keys(arrs[i])[0];

			var apn = str.substring(0,str.indexOf("["));

			json[apn] = [];			

			var property = Object.keys(arrs[i])[0];
			var value = arrs[i][property];			
			
			var row_index = property.substring(property.indexOf("[")+1,property.indexOf("]"));
			
			var re = new RegExp("\\[("+row_index+")\\]");
			var property_row = re.exec(property);
			
			var input_name = property_row.input;
			var pn = input_name.substring(input_name.indexOf("][")+2,input_name.length-1);			
			
			json[apn][row_index] = {};
			console.log(row_index);
			var o = {};
			
		};
		
		console.log(json);	
		
	};

});