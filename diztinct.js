$diz( document ).ready(function() {
    console.log( "ready!" );

    $diz('#cs-manu').click( function() {
    	console.log('test');
    	$diz('#manu-list').slideToggle();
    });
    $diz('#widget_manufacturer option').each( function() {
    	var optionname = $diz(this).val();
    	$diz('<div class="cluboption">'+optionname+'</div>').appendTo( $diz('#manu-list') );
    });
    $diz('#manu-list .cluboption').on("click",function() {
    	var optionname = $diz(this).text();
    	console.log(optionname);
    	$diz('#manu-list').slideToggle();
    	$diz('#widget_manufacturer').val(optionname).trigger("change");
    });

    $diz('#conditionhead').on("click", function() {
      $diz('#condition-levels').slideToggle();
      $diz('#conditionhead').toggleClass('active');
    });

    $diz('#hctoggle').on("click", function() {
      $diz('#Club_Finder').show();
      $diz(this).hide();
    });

    
});
