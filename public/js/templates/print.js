(function($){ 
	"use strict"; 
	function auto_grow(element) {
	  	element.style.height = "5px";
	  	element.style.height = (element.scrollHeight)+"px";
	}

	if (document.getElementById("comment")) {
		auto_grow(document.getElementById("comment"));
	}

	$( document ).ready(function() {
		jQuery(document).on("click", "#print_button", function() {
		  	window.print();
		  	setInterval(function() {
		      	window.close();
		  	}, 500);
		});
	  
	   	setTimeout(function() {
	       	window.print();
	   	}, 3000);

	   	
	   	window.onafterprint = function(){
	       	setTimeout(function() {
	           	window.close();
	       	}, 10000);
	   	}
	});
})(jQuery);