<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Pro Clubs Theme
 */ 

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<script src="https://kit.fontawesome.com/2d7a0f05f0.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://use.typekit.net/xey3kvb.css">

	<?php wp_head(); ?>

	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
	<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/diztinct.css" />
</head>

<body <?php body_class( 'site-wrapper' ); ?>>

	<?php wp_body_open(); ?>

	<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'proclubs' ); ?></a>

	<div id="blackout">
    <div id="nav-close"><i class="far fa-times"></i></div>
  </div>

	<header class="site-header">
		<div id="header-topbar">
		<div class="navpages-item npi-desk">
	        <a id="menu-button" class="mobileMenu-toggle navpages-action" data-mobile-menu-toggle="menu">
	          <i class="fas fa-bars"></i> Menu
	        </a>

	        <div class="navpages-container" id="menu" data-menu>
	          <div id="nav-header">
	          </div>
	          <nav class="navpages">
	            <ul class="navpages-list menu">
								<nav class="navpages">
									<ul class="navpages-list navpages-list-user menu">
								    <li class="navpages-item">
								      <a id="na-trigger" class="navpages-action has-submenu" aria-label="My Account">
								        <i class="far fa-user"></i>
								        Hello, Sign In
								        <i class="navpages-action-moreicon far fa-chevron-right"></i>
								      </a>
								      <div class="navpage-submenu" id="navpages-account">
								        <ul class="navpage-submenu-list">


								          <?php if (is_user_logged_in()) : ?>
								            <li class="navpage-submenu-item">
								              <a class="navpage-submenu-action navpages-action" href="<?php echo wp_login_url(get_permalink()); ?>" aria-label="Sign In">Sign In</a>
								            </li>
								            <li class="navpage-submenu-item">
								              <a class="navpage-submenu-action navpages-action" href="/wp-login.php?action=register&redirect_to=https%3A%2F%2Fproclubs.com%2F" aria-label="Register">Register</a>
								            </li>
								          <?php endif;?>
								          <li class="navpage-submenu-item">
								            <a class="navpage-submenu-action navpages-action" href="/?accounthistory" aria-label="My Account">My Account</a>
								          </li>
								          <?php if (is_user_logged_in()) : ?>
								            <li class="navpages-item">
								              <a class="navpages-action" href="<?php echo wp_logout_url(get_permalink()); ?>" aria-label="Sign Out">Sign Out</a>
								            </li>
								          <?php endif;?>
								        </ul>
								      </div>
								    </li>
								  </ul>

									<ul class="navpages-list menu">
							      <li class="navpages-item">
											<a class="navpages-action" href="/sell-your-clubs/" aria-label="Sell Your Clubs">Sell Your Clubs <i class="navpages-action-moreicon far fa-chevron-right" aria-hidden="true"></i>
											</a>
							      </li>
										<li class="navpages-item">
											<a class="navpages-action" href="/program-details/" aria-label="Program Details">Program Details <i class="navpages-action-moreicon far fa-chevron-right" aria-hidden="true"></i>
											</a>
							      </li>
										<li class="navpages-item">
											<a class="navpages-action" href="/blog/" aria-label="Tips & News">Tips & News <i class="navpages-action-moreicon far fa-chevron-right" aria-hidden="true"></i>
											</a>
							      </li>
								  </ul>

									<ul class="navpages-list menu" style="padding: 24px 0; background: #F7F8FA;">
								    <li class="navpages-blurb">
								      <a href="https://www.proclubs.com">
								        <div class="blue-circle"><i class="far fa-tag"></i></div>
								        <div class="hs-text">
								          <strong>Visit Our New Store</strong>
								          <span>Buy New & Used Golf Equipment</span>
								        </div>
								      </a>
								    </li>
								    <li class="navpages-blurb">
								      <a href="tel:6234346570">
								        <div class="blue-circle"><i class="far fa-phone"></i></div>
								        <div class="hs-text">
								          <strong>(623) 434-6570</strong>
								          <span>Mon–Fri, 8am–4pm MST</span>
								        </div>
								      </a>
								    </li>
								    <li class="navpages-blurb">
								      <a href="/contact-us/">
								        <div class="blue-circle"><i class="far fa-comments"></i></div>
								        <div class="hs-text">
								          <strong>Contact Us</strong>
								          <span>Send an email</span>
								        </div>
								      </a>
								    </li>
								  </ul>
								</nav>
	            </ul>
	          </nav>
	        </div>
	      </div>
	      <a class="topbar-phone" href="tel:6234346570" aria-label="Talk to a Pro">Talk to a Pro <i class="far fa-phone"></i> (623) 434-6570</a>

		<nav class="navuser">
		  <div class="container">
		  <ul class="navuser-section menu">
		    <li class="navuser-item talktoapro">
		      <a class="navuser-action" href="tel:6234346570" aria-label="Talk to a Pro"><span class="hidem">Talk to a Pro</span> <i class="far fa-phone"></i> (623) 434-6570</a>
		    </li>
		  </ul>
		  <ul class="navuser-section navuser-section-alt menu">
		    <li class="navuser-item navuser-item-account">
		      <a class="navuser-action" aria-label="My Account">
		        <i class="far fa-user"></i>
		        <div id="acc-text">
		          <?php global $current_user; wp_get_current_user(); ?>
				  <?php if ( is_user_logged_in() ) { 
 					echo 'Hello, ' . $current_user->user_login . "\n";} 
					else { 
						echo ' Sign In';
					} ?>		          
		        </div>
		      </a>
		      <div id="acc-dd">
		        <p>
		        	<?php if (is_user_logged_in()) : ?>
					    <a class="button button-primary" href="/logout/" aria-label="Sign Out">Sign Out</a>
					<?php else : ?>
					    <a class="button button-primary" href="/login/" aria-label="Sign In">Sign In</a>
					<?php endif;?>
		        </p>
		        <?php if (is_user_logged_in()) : ?>
		          <a href="/?accounthistory" aria-label="My Account">My Account</a>
		        <?php else : ?>
		          <a href="/register/" aria-label="Register">Register</a>		          
		        <?php endif;?>
		      </div>
		    </li>
		    <li class="navuser-item navuser-item-cart">
		      <a class="navuser-action" href="/index.php?quotesummary" aria-label="Cart">
		        <i class="far fa-shopping-cart"></i>
		        <span class="navuser-item-cartlabel">Cart</span>
		        <span class="countpill cart-quantity"></span>
		      </a>
		    </li>
		  </ul>
		  </div>
		</nav>
		</div>

		<div class="container">
			<div class="header-logo">
				<a href="/" class="header-logo__link">
				  <picture>
				    <source media="(min-width: 768px)" srcset="<?php bloginfo('template_url'); ?>/src/images/proclubs-logo.png">
				    <img src="<?php bloginfo('template_url'); ?>/src/images/proclubs-logo-mob.png" alt="Proclubs Logo">
				  </picture>
				</a>
	    </div>
		</div>
		<div class="container hs-container">
	    <div id="header-sell">
	      <a href="https://www.proclubs.com">
	        <div id="blue-circle"><i class="fas fa-tag"></i></div>
	        <div id="hs-text">
	          <strong>Visit Our New Store</strong>
	          <span>Buy New & Used Golf Equipment</span>
						<span id="blue-line">Shop Now</span>
	        </div>
	      </a>
	    </div>
	  </div>
		<div id="headercf" class="cf-nh">
			<div id="hctoggle">SELL YOUR CLUBS</div>

			<div id="clubfheader">
				CLUB <br />
				FINDER
			</div>
			<?php 
			if ( is_front_page() ) {
				// Do Nothing
			} else { ?> 
				<div id="Club_Finder" class="widgets_on_page wop_tiny1  wop_small1  wop_medium1  wop_large1  wop_wide1"> 
					<?php echo the_widget('quote_manager_widget') ;?> 
				</div> 
			<?php } ?>
				
		</div>

	</header><!-- .site-header-->
