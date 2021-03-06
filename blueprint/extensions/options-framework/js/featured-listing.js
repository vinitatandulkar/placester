jQuery(document).ready(function($) {

	var search_datatable;
	var featured_datatable;
    var max_choices = false;
    init_featured_picker();

    var dialogWidth = 850;
    $('#featured-listing-wrapper').dialog({
      autoOpen: false,
      width: dialogWidth,
      title: "Featured Listings Picker"
    });
	
    $('.featured-listings').live('click', function(event) {
		event.preventDefault();
        $('#featured-listing-wrapper').dialog('open');
		
        var calling_button_id = $(this).attr('id');

        //so the slideshow picker only chooses 1 listing
        listings_container = $('button#'+calling_button_id).closest('.featured-listings-wrapper').find('div.featured-listings');
        max_choices = $(listings_container).attr('data-max');

        //id of the save button in the dialog box
        $('#save-featured-listings').attr('class', calling_button_id);
        $('#save-featured-listings').attr('rel', $(this).attr('rel'));

        var listings_container = $(this).closest('.featured-listings-wrapper').find('div.featured-listings ol li');
        featured_datatable.fnClearTable();
        $(listings_container).each(function(event) {
            // console.log(this);
            var address = $(this).children('div#pls-featured-text').html();
            var listing_id = $(this).children('div#pls-featured-text').attr('ref');
            featured_datatable.fnAddData( [address, '<a id="pls_remove_option_listing" href="#" ref="' + listing_id + '">Remove</a>']);
        });
        // console.log(listings_container);

	});



	function init_featured_picker() {
		//load two datatables
		search_datatable = $('#datatable_search_results').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bFilter" : false,
            "sServerMethod": "POST",
            "sAjaxSource": ajaxurl, 
            "aoColumns" : [
                { sWidth: '200px' },    //address
                { sWidth: '60px' },    //address
                { sWidth: '100px' },    //add
            ], 
            "fnServerParams": function ( aoData ) {
                aoData.push( { "name": "action", "value" : "list_options"} );
                // aoData.push( { "name": "sSearch", "value" : $('input#address_search').val() })
                aoData = options_filters(aoData);
                // console.log(aoData);
            } 
		});
		featured_datatable = $('#datatable_featured_listings').dataTable({
            "bFilter" : false,
			"aoColumns" : [
                { sWidth: '300px' },    //address
                { sWidth: '60px' },    //remove
            ]
		});
	}

	$('#pls_add_option_listing').live('click', function(event) {
		event.preventDefault();
		var listing_id = $(this).attr('ref');
		var cells = $(this).parent().parent().children('td');
		var address = $(cells[0]).html();
        if (max_choices && featured_datatable.fnGetData().length >= max_choices) {
            alert("You can only add one listing to this slide. Remove the listing you have to add another");
        } else {
            featured_datatable.fnAddData( [address, '<a id="pls_remove_option_listing" href="#" ref="' + listing_id + '">Remove</a>']);    
        }
		
	});

	$('#pls_remove_option_listing').live('click', function(event) {
		event.preventDefault();
		featured_datatable.fnDeleteRow($(this).closest("tr").get(0));
	});

	$('#options-filters').live('change submit', function(event) {
        event.preventDefault();
        search_datatable.fnDraw();
    });

    $('#save-featured-listings').live('click', function(event) {
        event.preventDefault();
        var iterator = $(this).attr('rel') || false;
        save_options( iterator );
    });
    
    $('#cancel-featured-listings').live('click', function(event) {
    	event.preventDefault();
    	jQuery('#featured-listing-wrapper').dialog('close');
    });


    function options_filters (aoData) {
        $.each($('#options-filters').serializeArray(), function(i, field) {
            aoData.push({"name" : field.name, "value" : field.value});
        });
        return aoData;
    }

    function save_options ( iterator ) {
        var listings_container;
        var featured_listings = '';
        
        if ( iterator ) {
            var calling_id = 'button#' + $('#save-featured-listings').attr('class') + '[rel="' + iterator + '"]';
        } else {
            var calling_id = 'button#' + $('#save-featured-listings').attr('class');
        }

        listings_container = $(calling_id).closest('.featured-listings-wrapper').find('div.featured-listings').get(0);
        
        // console.log(listings_container);

        var rows = $("#datatable_featured_listings").dataTable().fnGetData();
        
        if (rows.length < 0) {
          $('#featured-listing-wrapper').dialog('close');
          return false;
        }
        
        featured_listings += '<ol>';
        
        // console.log(listings_container);
          // Iterate though all rows
          $(rows).each(function(event) {
          
              var option_name = $(listings_container).attr('id');
              var iterator = $(listings_container).attr('rel') || false;

              if (iterator) {
                  var option_id = $(listings_container).attr('ref') + '][' + iterator;
              } else {
                  var option_id = $(listings_container).attr('ref');
              }
              // console.log(option_id);

              // Set Address
              var address = this[0];
              // console.log(address);

              // Set Ref #
              ref_id = this[1];
              locationStart = ref_id.indexOf(' ref="');
              locationEnd = ref_id.indexOf('">');
              var id = ref_id.substring(locationStart + 6, locationEnd );
              // console.log(id);
              
              if (address && address != 'Sorry, no listings match your search. Please try another.') {
                  featured_listings += '<li>';
                  featured_listings += '<div id="pls-featured-text" ref="' + id + '">' + address + '</div>';
                  featured_listings += '<input type="hidden" name="' + option_name + '[' + option_id + '][' + id + ']=" value="' + address + '">';
                  featured_listings += '</li>';
              }

        });

        featured_listings += '</ol>';
        
        $(listings_container).html(featured_listings);
        $('#featured-listing-wrapper').dialog('close');
    }

    $('#listing_image').live('mouseover', function () {
        $('#image-preview').remove();
        var content = '<div id="image-preview"><img src="' + $(this).attr('href') + '" /></div>';
        $(this).append(content);
    });

    $('#listing_image').live('mouseout', function () {
        $('#image-preview').remove();
    });

});