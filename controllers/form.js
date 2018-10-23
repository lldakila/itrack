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
			
			console.log(arrs[i]);
			
			var str = Object.keys(arrs[i])[0];

			var apn = str.substring(0,str.indexOf("["));

			if (json[apn]==undefined) json[apn] = [];			

			var o = {};	
			arrs.forEach(function(item,ii) {
				
				var key = Object.keys(item)[0];
				var value = item[key];
				
				var re = new RegExp("\\[("+i+")\\]");
				
				var key_o = re.exec(key);

				if (key_o!=null) {
					
					var input_name = key_o.input;
					var pn = input_name.substring(input_name.indexOf("][")+2,input_name.length-1);

					if (parseInt(key_o[1]) == i) {

						o[pn] = value;
						
						json[apn].push(o);
						
					};
					
				};
				
			});			
			
		};
		
		console.log(json);	
		
	};

});