( function ( $ ) {
	const menuToggle = () => {
		$( '#menu-button' ).on( 'click', function () {
			$( this ).toggleClass( 'is-open' );
			$( '.navpages-container' ).toggleClass( 'is-open' );
			$( '.site-header' ).toggleClass( 'is-open' );
			$( '#blackout' ).toggleClass( 'on' );
			$( 'body' ).toggleClass( 'has-activenavpages' );
		} );
	};
	menuToggle();

	// Close Menu
	const blackoutToggle = () => {
		$( '#blackout' ).click( () => {
			$( '#blackout' ).removeClass( 'on' );
			$( '#menu-button' ).removeClass( 'is-open' );
			$( 'body' ).removeClass( 'has-activenavpages' );
			$( '.site-header' ).removeClass( 'is-open' );
			$( '.navpages-container' ).removeClass( 'is-open' );
		} );
	};
	blackoutToggle();

	let lastScrollTop = 0;
	$( window ).scroll( function () {
		const st = $( this ).scrollTop();
		if ( $( window ).width() < 768 ) {
			if ( $( this ).scrollTop() > 150 ) {
				if ( st > lastScrollTop ) {
					$( '.site-header' ).addClass( 'showscroll' );
					$( 'body' ).addClass( 'nxt-scroll' );
				}
				lastScrollTop = st;
			} else {
				$( '.site-header' ).removeClass( 'showscroll' );
				$( 'body' ).removeClass( 'nxt-scroll' );
			}
		}
	} );

	$( '#na-trigger' ).click( function () {
		$( this ).toggleClass( 'active' );
		$( '#navpages-account' ).slideToggle();
	} );

	$( '.navuser-item-account .navuser-action' ).click( function () {
		$( this ).toggleClass( 'active' );
		$( '#acc-dd' ).slideToggle();
	} );

	const hiwSlider = $( '#hiw-carousel' );
	const hiwSettings = {
		lazyLoad: 'ondemand',
		mobileFirst: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		dots: true,
		arrows: false,
		infinite: false,
		responsive: [
			{
				breakpoint: 959,
				settings: {
					slidesToShow: 4,
					dots: false,
				},
			},
		],
	};

	if ( hiwSlider.length ) {
		hiwSlider.slick( hiwSettings );

		// reslick only if it's not slick()
		$( window ).on( 'resize', () => {
			if ( $( window ).width() > 959 ) {
				if ( hiwSlider.hasClass( 'slick-initialized' ) ) {
					hiwSlider.slick( 'unslick' );
				}
				return;
			}

			if ( ! hiwSlider.hasClass( 'slick-initialized' ) ) {
				return hiwSlider.slick( hiwSettings );
			}
		} );
	}

	const rrSlider = $( '#rr-carousel' );
	const rrSettings = {
		lazyLoad: 'ondemand',
		mobileFirst: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		dots: true,
		arrows: false,
		infinite: false,
		responsive: [
			{
				breakpoint: 959,
				settings: {
					slidesToShow: 3,
					arrows: true,
				},
			},
			{
				breakpoint: 767,
				settings: {
					slidesToShow: 2,
					dots: true,
					arrows: true,
				},
			},
		],
	};

	if ( rrSlider.length ) {
		rrSlider.slick( rrSettings );
	}

	const viotSlider = $( '#viot-carousel' );
	const viotSettings = {
		lazyLoad: 'ondemand',
		mobileFirst: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		dots: true,
		arrows: false,
		infinite: false,
		responsive: [
			{
				breakpoint: 959,
				settings: {
					slidesToShow: 4,
					dots: false,
				},
			},
		],
	};

	if ( viotSlider.length ) {
		viotSlider.slick( viotSettings );

		// reslick only if it's not slick()
		$( window ).on( 'resize', () => {
			if ( $( window ).width() > 959 ) {
				if ( viotSlider.hasClass( 'slick-initialized' ) ) {
					viotSlider.slick( 'unslick' );
				}
				return;
			}

			if ( ! viotSlider.hasClass( 'slick-initialized' ) ) {
				return viotSlider.slick( viotSettings );
			}
		} );
	}

	// Footer Accordions
	const accResize = () => {
		$( '.footer-info-heading' ).on( 'click', function () {
			if ( $( window ).width() < 960 ) {
				if ( $( this ).is( '.active' ) ) {
					$( this ).removeClass( 'active' );
					$( this )
						.next( '.footer-info-list' )
						.removeClass( 'is-active' );
					$( this ).next( '.footer-info-list' ).slideToggle();
				} else {
					$( '.footer-info-heading.active' ).removeClass( 'active' );
					$( this ).addClass( 'active' );
					$( '.footer-info-list.is-active' ).slideToggle();
					$( '.footer-info-list.is-active' ).removeClass(
						'is-active'
					);
					$( this )
						.next( '.footer-info-list' )
						.addClass( 'is-active' );
					$( this ).next( '.footer-info-list' ).slideToggle();
				}
			}
		} );
	};
	accResize();

	// Footer Subscription Form Placeholder Functionality
	( function () {
		const id = document.getElementById( 'sub-form' );
		if ( id && id.nl_email ) {
			const name = id.nl_email;
			const unclicked = function () {
				if ( name.value === '' ) {
					name.style.background =
						'#FFFFFF url(https://cdn11.bigcommerce.com/s-jgixxa04zj/product_images/uploaded_images/envelope.png) 20px no-repeat';
					name.style.textIndent = '36px';
				}
			};
			const clicked = function () {
				name.style.background = '#ffffff';
				name.style.textIndent = '0';
			};
			name.onfocus = clicked;
			name.onblur = unclicked;
			unclicked();
		}
	} )();

	// Condition Guide Accordion
	const conditonGuide = () => {
		$( '#cg-header' ).on( 'click', function () {
			$( this ).toggleClass( 'active' );
			$( '#cg-list' ).slideToggle();
		} );
	};
	conditonGuide();
} )( jQuery );
