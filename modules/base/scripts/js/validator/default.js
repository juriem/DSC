//@depends: scripts:js//
/**
 * Form validator
 */

(function($) {

	$.fn.modScripts = {

		validator : function() {

			return this.each(function(options) {

				if (options == undefined)
					options = {};

				$(this).find('.data').each(function() {
					var name = $(this).attr('name');
					var value = $(this).val();
					var pattern;
					$.each(options, function(_name, type) {
						if (_name == name) {
							switch (value) {
							case 'number':
								pattern = /[0-1]*/i;
								break;
							case 'email':
								pattern = '/([a-z0-9]+)@([a-z0-9.\-]+)/i';
								break;
							}
						}
					});
					if (pattern != '') {
						var regExp = new RegExp(pattern);
						if (regExp.test(value)) {
							$(this).removeClass('is-bad');
						} else {
							$(this).addClass('is-bad');
						}
					}
				});
			});
		}
	}

})(jQuery);