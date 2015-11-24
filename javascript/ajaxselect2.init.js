(function($) {
	$.entwine('select2', function($){
		$('select.ajaxselect2').entwine({
			applySelect2: function() {
				var self = this,
					$select = $(this);

				// There is a race condition where Select2 might not
				// be bound to jQuery yet. So here we make sure Select2
				// // is defined before trying to invoke it.
				// if ($.fn.select2 === void 0) {
				// 	console.log('something');
				// 	return setTimeout(function () {
				// 		console.log('something else');

				// 		self.applySelect2();
				// 	}, 0);
				// }

				$select.select2({
				    placeholder: self.data('placeholder'),
				    minimumInputLength: self.data('minimuminputlength'),
				    page_limit: self.data('resultslimit'),
				    quietMillis: 100,
				    ajax: {
				        url: $select.data('searchurl'),
				        dataType: 'json',
				        type: 'GET',
				        delay: 250,
				        data: function (params) {
				        	// console.log(params);
				        	return {
				        		term: params.term,
				        		page: params.page
				        	};
				        },
				        processResults: function (data, params) {

				        	console.log(data, params);

				        	params.page = params.page || 1;

				            return {
				            	results: data.list
				           	};
				        },
    				    cache: true
				    },
				    templateResult: function(item) {
				    	console.log(item);
				        return item.resultsContent;
				    },
    				templateSelection: function(data) {
    					console.log(data);
    					return data.label || data.text;
    					// console.log(item);
    					// if(item.selectionContent){
    					// 	return item.selectionContent;
    					// }else{
    					// 	return item;
    					// }

				    },
				    allowClear: true,
				    escapeMarkup: function (markup) { return markup; }
				});
			},
			onmatch: function() {
				this.applySelect2();
				// console.log(this);
			}
		});
	});
})(jQuery);


// (function($) {
// 	$.entwine("select2", function($) {
// 		$("input.ajaxselect2").entwine({
// 			onmatch: function() {
// 				var self = this;
// 				self.select2({
// 				    placeholder: self.data('placeholder'),
// 				    minimumInputLength: self.data('minimuminputlength'),
// 				    page_limit: self.data('resultslimit'),
// 				    quietMillis: 100,
// 				    ajax: {
// 				        url: self.data('searchurl'),
// 				        dataType: 'json',
// 				        data: function (term, page) {
// 				            return {
// 				                term: term,
// 				                page: page
// 				            };
// 				        },
// 				        results: function (data, page) {
// 				        	var more = (page * self.data('resultslimit')) < data.total
// 				            return {
// 				            	results: data.list,
// 				            	more: more
// 				           	};
// 				        }
// 				    },
// 				    initSelection: function(element, callback) {
// 				    	console.log('INISEL')
// 				        callback($(element).data('selectioncontent'));
// 				    },
// 				    formatResult: function(item) {
// 				        return item.resultsContent;
// 				    },
//     				formatSelection: function(item) {
//     					if(item.selectionContent){
//     						return item.selectionContent;
//     					}else{
//     						return item;
//     					}

// 				    },
// 				    dropdownCssClass: "bigdrop",
// 				    escapeMarkup: function (m) { return m; }
// 				});
// 			},
// 			onchange: function() {
// 				if($(this).val()) {
// 					$(this).next('a.ajaxselect2Remove').show();
// 				} else {
// 					$(this).next('a.ajaxselect2Remove').hide();
// 				}
// 			}
// 		});

// 		// ability to remove set option -
// 		// only for Ajax version
// 		$("a.ajaxselect2Remove").entwine({
// 			onclick: function() {
// 				var input = $(this).siblings('input');
// 				input.select2('data', {id: null, text: null});
// 				input.trigger('change')
// 			}
// 		});

// 	});
// }(jQuery));