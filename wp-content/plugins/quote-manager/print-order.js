/*
	print-order.js
	Description: Opens a priner-friendly order card 
	Version: 0.1
	Author: JS - Luminary WS
*/



jQuery(function(){
	
	

	//uploading icon
	jQuery('#print-rate-card').bind('click', function() {
		
		console.log("PRINT");
		
		jQuery("body").append("<div class='print-lightbox'><div class='print-content'>Print Content</div></div>");
	});
	
});