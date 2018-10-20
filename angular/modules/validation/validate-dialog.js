angular.module('form-validator-dialog', ['bootstrap-modal']).factory('validateDialog', function() {
	
	function validateDialog() {
	
		var self = this;
	
		self.form = function(scope,form) {
			
			var controls = scope.formHolder[form].$$controls;
			
			angular.forEach(controls,function(elem,i) {

				if (elem.$$attr.$attr.required) scope.$apply(function() { elem.$touched = elem.$invalid; });
									
			});

			return scope.formHolder[form].$invalid;
			
		};
		
		self.cancel = function(scope,form) {
			
			var controls = scope.formHolder[form].$$controls;
			
			angular.forEach(controls,function(elem,i) {
				
				if (elem.$$attr.$attr.required) elem.$touched = false;
									
			});			
			
		};
		
	};
	
	return new validateDialog();
	
});