(function( $ ) {
	'use strict';

	$(document).ready(function () {
        $(".ajax-load.ehri-item-container").each(function(i) {
            var $elem = $(this);
            $elem.addClass("loading");
            var data = {
                action: "load_ehri_data",
                id: $elem.data("id")
            };
            $.get(Ajax.ajaxurl, data, function(html) {
                $elem.replaceWith(html);
            }).always(function() {
                $elem.removeClass("loading");
            }).fail(function (e, t, err) {
                $elem.find(".ehri-item-placeholder")
                    .text("Unable to load EHRI portal data: " + err);
            });
        });

    });

})( jQuery );
