;
(function($, window, undefined) {
    $(function() {
        $('#ps_plugin_template_settings_tabs_header a').on('click', function(e) {
            e.preventDefault();
            if ($(this).hasClass('bpu-tab-active')) {
                return;
            }
            $(this).addClass('bpu-tab-active').siblings('a').removeClass('bpu-tab-active');
            $($(this).attr('href')).addClass('bpu-tab-active').siblings('.bpu-tab-content').removeClass('bpu-tab-active');
        });
        // Put General Admin Scripts Here
        $('.bpu-multi-select').multiselect({enableClickableOptGroups: true, enableCollapsibleOptGroups: true, enableFiltering: true,includeSelectAllOption: true });
        jQuery('#bpu_method_' + jQuery('input[name="bpu[bpu_price_change_method]"]').val()).show();
        jQuery('input[name="bpu[bpu_price_change_method]"]').change(function(e) {
            jQuery('.bpu_method_aria_tc').hide();
            jQuery('#bpu_method_' + jQuery(this).val()).show();
        });
        jQuery("select.bpu-multi-select-product").select2({
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        s: params.term,
                        action: 'bpu_get_products',
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: (params.page * 50) < data.count_filtered
                        }
                    };
                },
                cache: true
            },
            placeholder: "Select Products...",
            width: "90%",
            minimumInputLength: 0,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });
        // Datepicker fields
		jQuery( '.bpu_sale_price_dates_fields' )
		.find( 'input' )
		.datepicker( {
			defaultDate: '',
			dateFormat: 'yy-mm-dd',
			numberOfMonths: 1,
			showButtonPanel: true,
			onSelect: function () {
				var option = jQuery( this ).is( '#bpu_sale_price_dates_from' )
						? 'minDate'
						: 'maxDate',
					dates = jQuery( this )
						.closest( '.bpu_sale_price_dates_fields' )
						.find( 'input' ),
					date = jQuery( this ).datepicker( 'getDate' );

				dates.not( this ).datepicker( 'option', option, date );
				jQuery( this ).trigger( 'change' );
			},
		} );
        jQuery("#bpu_percentage").keypress(function(e) 
	  	{
			if (e.keyCode === 46 && this.value.split('.').length === 2)
			{
				return false;
			}
	   	});

        function formatRepo(repo) {
            if (repo.loading) {
                return repo.text;
            }
            var $container = jQuery("<div class='select2-result-repository clearfix'><div class='select2-result-repository__meta'><div class='select2-result-repository__title'>" + repo.text + "</div></div></div>");
            return $container;
        }

        function formatRepoSelection(repo) {
            return repo.name || repo.text;
        }

        if ($('.bpu-subscription-callout-wrapper').length > 0) {
            // Show the popup after 5 seconds
            window.setTimeout(function() {
                $('.bpu-subscription-callout-wrapper').addClass('open');
            }, 5000);
            // Overtake the form submission request
            $('.bpu-subscription-form').on('submit', function(e) {
                e.preventDefault();
                if ($('.bpu-subscription-callout').hasClass('bpu-ajaxing')) {
                    return; // request is already in progress
                }
                $('.bpu-subscription-callout').addClass('bpu-ajaxing');
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'bpu_handle_subscription_request',
                        email: $('.bpu-subscription-form input').val(),
                        security: $('.bpu-subscription-form input[name="_wpnonce"]').val(),
                        from_callout: 1,
                    },
                    success: function(data) {
                        $('.bpu-subscription-callout-thanks').show().delay(2000).fadeOut();
                    }
                }).fail(function() {
                    $('.bpu-subscription-error').show();
                }).always(function() {
                    $('.bpu-subscription-callout').removeClass('bpu-ajaxing');
                });
            });

            function store_popup_shown_status() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'bpu_subscription_popup_shown'
                    },
                });
            }
            $('.bpu-subscription-skip').on('click', function(e) {
                e.preventDefault();
                $('.bpu-subscription-callout-wrapper').removeClass('open');
                store_popup_shown_status();
            });
        }

        function bpu_tc_start_over() 
		{					
			jQuery('#bpu_save_settings').css({'opacity':0.5});
			jQuery('#bpu_save_settings').attr('disable',true);
			jQuery('#bpu_update_product_results_body').html('');
			jQuery('#bpu_loader').show();				
		}

		var wp_product_update_ids = { action: 'bpu_change_price_percentge'};		
		var wp_product_get_ids = { action: 'bpu_change_price_product_ids'};		
		var arr = [];
	   	var opration_type='';
	   	var price_type_by_change='';
	   	var percentage='';
	   	var bpu_dry_run = '';
	   	var price_rounds_point='';
	   	var bpu_sale_price_dates_from='';
	   	var bpu_sale_price_dates_to='';

		jQuery(document).on("submit", "#bpu_setting_form", function(e){
			e.preventDefault();

			Array.prototype.chunk = function(n) {
				return (!this.length) ? [] : [this.slice(0, n)].concat(this.slice(n).chunk(n));
			};
			jQuery('.bpu-progress').attr('value',0);
			if(arr.length == 0)
			{
				percentage=jQuery("#bpu_percentage").val();	
				if(percentage > 0)
				{	
					opration_type = jQuery("input[name='bpu[bpu_price_change_type]']:checked").val();	
					var bpu_nonce = jQuery("input[name='bpu_product_ids_nonce']").val();	
					bpu_sale_price_dates_from = jQuery("input[name='bpu[bpu_sale_price_dates_from]']").val();	
					bpu_sale_price_dates_to = jQuery("input[name='bpu[bpu_sale_price_dates_to]']").val();	
					price_type_by_change = jQuery("input[name='bpu[bpu_price_type_by_change]']:checked").val();	
					price_rounds_point = (jQuery("#bpu_price_rounds_point").is(":checked")) ? 'true' : 'false';
					bpu_dry_run = (jQuery("#bpu_dry_run").is(":checked")) ? 'true' : 'false';
					if(jQuery("input[name='bpu[bpu_price_change_method]']:checked").val()=='by_categories')
					{
				        if(jQuery('#bpu_categories').val() != ''){
				        	bpu_tc_start_over();
				        	wp_product_get_ids['cat_ids'] = jQuery('#bpu_categories').val();	
					        wp_product_get_ids['nonce'] = bpu_nonce;			
							jQuery.post( ajaxurl, wp_product_get_ids, function(res_cat) 
						   	{
						   		arr = JSON.parse(res_cat);
						   		arr = arr.chunk(5);
								bpu_recur_loop();
								jQuery('.bpu-progress').attr('max',arr.length);
							});
				        }
				        else{
							alert('Please select a Category...!!');
				        }			
					}
					else{
						if(jQuery('#bpu_products').val() != ''){
							arr = jQuery('#bpu_products').val();
						   	arr = arr.chunk(5);
							bpu_tc_start_over();
							bpu_recur_loop(); 
							jQuery('.bpu-progress').attr('max',arr.length);
						}
						else{
							alert('Please select a Product...!!');								
						}
					}
				}			
				else
				{
					alert('Please provide a Amount more-than Zero...!!');
				}
			}

		});

		var bpu_recur_loop = function(i) 
		{
		    var num = i || 0; 
		    if(num < arr.length) 
		    {
		    	var bpu_nonce = jQuery("input[name='bpu_product_update_nonce']").val();	
		        wp_product_update_ids['product_id'] = arr[num];
		        wp_product_update_ids['opration_type'] = opration_type;
		        wp_product_update_ids['price_type_by_change'] = price_type_by_change;
		        wp_product_update_ids['percentage'] = percentage;
		        wp_product_update_ids['price_rounds_point'] = price_rounds_point;
		        wp_product_update_ids['bpu_dry_run'] = bpu_dry_run;
		        wp_product_update_ids['bpu_req_count'] = num;
		        wp_product_update_ids['nonce'] = bpu_nonce;
		        wp_product_update_ids['bpu_sale_price_dates_from'] = bpu_sale_price_dates_from;
		        wp_product_update_ids['bpu_sale_price_dates_to'] = bpu_sale_price_dates_to;
			   	jQuery.post( ajaxurl, wp_product_update_ids, function(response) 
			   	{
			   		jQuery('#bpu_update_product_results').show();
			   		var count=num+1;
		        	bpu_recur_loop(num+1);
			   		jQuery('.bpu-progress').attr('value',count);
			   		jQuery('#bpu_update_product_results_body').append(response);
				});  
		    }
		    else
		    {
		    	arr = [];
				jQuery('#bpu_loader').hide();
				if(bpu_dry_run=='true'){
					alert('Dry Run Complete...!!');
				}
				else{
					jQuery('#bpu_products').val('');
					jQuery("#bpu_percentage").val('');
					jQuery("#bpu_sale_price_dates_from").val('');
					jQuery("#bpu_sale_price_dates_to").val('');
					jQuery("#bpu-multi-select").multiselect('refresh');
					if(jQuery('.bpu-multi-select-product').length > 0){
						jQuery('.search-choice-close').trigger('click');
					}
					alert('Operation Complete...!!');
				}
				jQuery('#bpu_save_settings').css({'opacity':''});
				jQuery('#bpu_save_settings').removeAttr('disable');
		    }
		};
    });
}(jQuery, window));