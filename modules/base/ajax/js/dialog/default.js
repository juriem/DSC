//@depends ajax:js//
/**

 * Special script for centering dialog 
 */


(function($){
	
	
	$.fn.centerDialog = function() {
			
		var holder = $(this).find('.parent').get(0); 
		
		var overlay = $('<div id="id-overlay"></div>');
		$(overlay).css({'height':$(document).height()+'px'});  
		$('body').append(overlay); 
		// Calculate horizontal center for dialog 
		var _width = $(holder).width();
		var _docWidth = $(document).width();
		var _left = 0;  
		if (_width< _docWidth) {
			_left = (_docWidth - _width)/2; 
		}  
		// Calculate vertical center for dialog 
		var _height = $(holder).height(); 
		var _windowHeight = $(window).height();
		var _topAdd = 0; 	 
		if (_height < _windowHeight) {
			_topAdd = (_windowHeight - _height)/2;  
		}
		var _top = $(window).scrollTop() + _topAdd; 
		$(this).css({
			position: 'absolute', 
			left: '30px', top : '30px',
			'z-index' : 101, 
			'left': _left + 'px',
			'top':_top + 'px' 
		});
	}; 
	
})(jQuery); 