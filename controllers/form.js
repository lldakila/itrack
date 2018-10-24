var app = angular.module('formFd',[]);

app.controller('formFdCtrl',function($scope) {

	$scope.save = function() {

		var json = formToJson();
		
		console.log($scope.formHolder.info.$valid);

	};
	
	function formToJson(formName) {
		
		if (formName==undefined) {
			console.log('Form name is missing');
			return {};
		};
		
		var e = $("[name='"+formName+"']");

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
		var o = {};
		var row_indexes = [];
		
		for (i=0; i<arrs.length; ++i) {			
			
			var str = Object.keys(arrs[i])[0];

			var apn = str.substring(0,str.indexOf("["));

			if (json[apn]==undefined) json[apn] = [];
			
			var property = Object.keys(arrs[i])[0];
			var value = arrs[i][property];			
			
			var row_index = property.substring(property.indexOf("[")+1,property.indexOf("]"));
			
			var re = new RegExp("\\[("+row_index+")\\]");
			var property_row = re.exec(property);
			
			var input_name = property_row.input;
			var pn = input_name.substring(input_name.indexOf("][")+2,input_name.length-1);			
						
			var sameIndex = row_indexes.every(function(index) { return index == parseInt(row_index); });
			
			if (!sameIndex) {
				o = {};
				row_indexes = [];
			};
						
			row_indexes.push(parseInt(row_index));		
			
			o[pn] = value;
			
			json[apn][row_index] = o;
			
		};

		return json;		
		
	};

});