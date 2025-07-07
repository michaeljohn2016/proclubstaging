<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Pro Clubs Theme
 */

?>

	<footer class="site-footer">


		<div class="container">
	    <section class="footer-info">
	      <article class="footer-info-col footer-info-col-small">
	        <div class="footer-box">
	          <div class="blue-circle"><i class="fas fa-tag"></i></div>
	          <div class="fb-text">
	            <strong><a href="https://proclubs.com/">Visit Our New Store</a></strong>
	            <span>Buy New & Used Golf Equipment</span>
	          </div>
	        </div>
	        <div class="footer-box">
	          <div class="blue-circle"><i class="fas fa-phone"></i></div>
	          <div class="fb-text">
	            <strong><a href="tel:6234346570">(623) 434-6570</a></strong>
	            <span>Mon–Fri, 8am–4pm MST</span>
	          </div>
	        </div>
	        <div class="footer-box">
	          <div class="blue-circle"><i class="fas fa-comment-alt-dots"></i></div>
	          <div class="fb-text">
	            <strong><a href="/contact-us/">Contact Us</a></strong>
	            <span>Send an email</span>
	          </div>
	        </div>
			<div class="footer-box">
				<ul class="sociallinks sociallinks-alt">
					<li class="sociallinks-item">
						<a class="icon-twitter" href="https://twitter.com/proclubs" target="_blank" rel="noopener"><i class="fab fa-twitter"></i></a>
					</li>
					<li class="sociallinks-item">
						<a class="icon-facebook" href="https://www.facebook.com/proclubsgolf/" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>
					</li>
					<li class="sociallinks-item">
						<a class="icon-instagram" href="https://www.instagram.com/proclubsgolf/" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>
					</li>
				</ul>
			</div>
			
	      </article>

				<article class="footer-info-col footer-info-col-small">
	        <h3 class="footer-info-heading">Sell Your Clubs <i class="far fa-chevron-down"></i></h3>
	        <ul class="footer-info-list">
	          <li><a href="/condition-guide/">Condition Guide</a></li>
	          <li><a href="/program-details/">How it Works</a></li>
	          <li><a href="/testimonials/">Testimonials</a></li>
	          <li><a href="/faq/">FAQ</a></li>
	        </ul>
	      </article>

	      <article class="footer-info-col footer-info-col-small">
	        <h3 class="footer-info-heading">Company <i class="far fa-chevron-down"></i></h3>
	        <ul class="footer-info-list">
	          <li><a href="/about-us/">About Us</a></li>
	          <li><a href="/contact-us/">Contact Us</a></li>
	          <li><a href="/shipping-returns/">Shipping & Returns</a></li>
	          <li><a href="https://proclubs.com/policies/privacy-policy" target="_blank">Privacy Policies</a></li>
	          <li><a href="https://proclubs.com/policies/terms-of-service" target="_blank">Terms & Conditions</a></li>
	        </ul>
	      </article>

	      <article class="footer-info-col footer-info-col-small" data-section-type="footer-categories">
	        <h3 class="footer-info-heading">Account <i class="far fa-chevron-down"></i></h3>
	        <ul class="footer-info-list">
	          <li><a href="/login">My Account</a></li>
	          <li><a href="/registeraccount">Create Account</a></li>
	          <!-- <li><a href="/account?action=order_status">Order Status</a></li> -->
	          <li><a href="/?accounthistory">Order History</a></li>
	        </ul>
	      </article>
	    </section>

			<div class="footer-copyright">
        <p class="powered-by">&copy; <span id="copyright_year"></span> Proclubs.com Design by <a href="https://www.diztinct.com/bigcommerce-design.html" target="_blank" rel="nofollow">Diztinct</a></p>
				<script type="text/javascript">
            document.getElementById("copyright_year").innerHTML = new Date().getFullYear();
        </script>
      </div>
	  </div>
	</footer><!-- .site-footer container-->

	<?php wds_proclubs_display_mobile_menu(); ?>
	<?php wp_footer(); ?>
	<script type="text/javascript" src="/jquery-1.12.4.min.js"></script>
	<script>
 		var $diz = jQuery.noConflict();
 	</script>
	<script type="text/javascript" src="/diztinct.js"></script>
</body>
</html>
