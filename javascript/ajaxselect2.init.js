(function($) {
	$.entwine('select2', function($){
		$('select.ajaxselect2').entwine({

			applySelect2: function() {
				var self = this,
					$select = $(this);

				$select.select2({
		    		placeholder: $select.data('placeholder'),
				    minimumInputLength: $select.data('minimuminputlength'),
				    maximumSelectionLenght: 1,
				    page_limit: $select.data('resultslimit'),
				    ajax: {
				        url: $select.data('searchurl'),
				        dataType: 'json',
				        type: 'GET',
				        delay: 250,
				        data: function (params) {
				        	console.log(params);
				        	return {
				        		term: params.term,
				        		page: params.page
				        	};
				        },
				        processResults: function (data, params) {

				        	console.log(data);

				        	params.page = params.page || 1;

				            return {
				            	results: data.list,
				            	pagintion : {
				            		more: (params.page * $select.data('resultslimit')) < data.total
				            	}
				           	};
				        },
    				    cache: true
				    },
				    templateResult: function(item) {
				        return item.templateResult;
				    },
    				templateSelection: function(item) {
    					if(item.templateSelection){
							$(this).find('.select2').addClass('hasSelection')
    						return item.templateSelection;
    					}else{
    						return item.title || item.id;
    					}
				    },
				    allowClear: true,
				    escapeMarkup: function (markup) { return markup; }
				});
			},
			getTitle: function() {
				console.log("hello");
				var val = $(this).siblings(':input:hidden').val();
				console.log(val);
				return val;
			},
			initSelection: function() {
				// var val = self.getTitle();
				// console.log(val);

				// var $request = $.ajax({
				// 	url: $(this).data('searchurl') + '&id=' + val,
			 //        dataType: 'json'
				// });

				// $request.then(function(data){
				// 	console.log(data);

				// 	for (var d = 0; d < data.list.length; d++) {

				// 		// console.log(d);

				// 		var item = data.list[d];

				// 		console.log(item);

				// 		var option = new Option(item.text, item.id, true, true);

				// 		$(this).append(option);
				// 	}
				// });

				// $(this).trigger('change');

				if ($(this).data('selectioncontent')){
					var $option = '<option selected>' + $(this).data('selectioncontent') + '</option>';
					$(this).append($option).trigger('change');
					$(this).find('.select2').addClass('hasSelection')
				} else {
					// var $option = '<option value="1" selected>' + $(this).data('placeholder') + '</option>';
					// $(this).append($option).trigger('change');
				}
			},
			onmatch: function() {
				this.applySelect2();
				this.initSelection();
			}
		});
	});
})(jQuery);