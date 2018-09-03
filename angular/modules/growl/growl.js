angular.module('bootstrap-growl', []).factory('growl',function() {
	
	function growl() {
		
		var self = this;
		
		self.show = function(type = 'success',offset,content,delay = null) {
			
			if (delay == null) delay = 4000;
			else delay = delay*1000;
			
			$.bootstrapGrowl(content, {
				type: type,
				offset: offset,
				align: 'right',
				width: 'auto',
				delay: delay,
				allow_dismiss: true,
                stackup_spacing: 30
			});			
			
		};
		
	}
	
	return new growl();
	
});