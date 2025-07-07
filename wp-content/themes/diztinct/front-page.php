<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Pro Clubs Theme
 */

get_header(); ?>

<div id="mb-container">
  <div id="main-banner">
    <img class="mb-clubs mbc-left" src="<?php bloginfo('template_url'); ?>/src/images/clubs-left-hero.png" alt="clubs">
    <img class="mb-clubs mbc-right" src="<?php bloginfo('template_url'); ?>/src/images/clubs-right-hero.png" alt="clubs">
    <div id="mb-text">
      <h2>Sell Your Clubs, Get Paid Fast</h2>
      <p>Find current cash values by using our club finder.</p>

            <?php echo the_widget('quote_manager_widget'); ?>
            <!--
            <div id="clubselector">
               <div class="cfinder">
                  <div class="cs-select">
                    <div id="cs-manu" class="stepitem" data-option="manufacturer">
                      <span class="stepnum"><span>1</span><i class="far fa-check"></i></span> <span class="cs-name">Select Manufacturer</span> <i class="fas fa-chevron-down"></i>
                    </div>
                    <div id="manu-list" class="optionlist"></div>
                  </div>
                  <div class="cs-select">
                    <div id="cs-type" class="stepitem" data-option="type">
                      <span class="stepnum"><span>2</span><i class="far fa-check"></i></span> <span class="cs-name">Select Club Type</span> <i class="fas fa-chevron-down"></i>
                    </div>
                    <div id="type-list" class="optionlist"></div>
                  </div>
                  <div class="cs-select">
                    <div id="cs-model" class="stepitem" data-option="model">
                      <span class="stepnum"><span>3</span><i class="far fa-check"></i></span> <span class="cs-name">Select Model</span> <i class="fas fa-chevron-down"></i>
                    </div>
                    <div id="model-list" class="optionlist"></div>
                  </div>
                </div>
            </div>-->
      <div id="hiddenclubwidget" style="display: none;"></div>
    </div>
  </div>

  <div id="how-it-works">
    <h2 class="page-heading">How it works</h2>
    <ul id="hiw-carousel">
      <li class="hiw-slide">
        <div class="hiw-wrap">
          <div class="blue-circle sbc">
            <i class="fas fa-ballot-check"></i>
          </div>
          <div class="hiw-text">
            <strong>Get A Quick Quote</strong>
            <p>Use our Club Finder to find your club’s current cash value.</p>
          </div>
        </div>
      </li>
      <li class="hiw-slide">
        <div class="hiw-wrap">
          <div class="blue-circle">
            <i class="fas fa-box-check"></i>
          </div>
          <div class="hiw-text">
            <strong>Ship Your Clubs</strong>
            <p>Set up an account and submit your order to ship your clubs.</p>
          </div>
        </div>
      </li>
      <li class="hiw-slide">
        <div class="hiw-wrap">
          <div class="blue-circle">
            <i class="fas fa-dollar-sign"></i>
          </div>
          <div class="hiw-text">
            <strong>Get Paid Fast</strong>
            <p>Payment is processed by Check or Paypal within 1 business day of arrival.</p>
          </div>
        </div>
      </li>
    </ul>
  </div>

  <div id="sub-banner-wrap">
    <div class="sub-banner">
      <img src="<?php bloginfo('template_url'); ?>/src/images/sub-1.jpg" alt="">
      <div class="sub-text">
        <h3>Sell Golf Shafts</h3>
        <a href="/contact-us/" class="button button-primary">Contact Us <i class="fas fa-chevron-right"></i></a>
      </div>
    </div>
    <div class="sub-banner">
      <img src="<?php bloginfo('template_url'); ?>/src/images/sub-2.jpg" alt="">
      <div class="sub-text">
        <h3>Sell Club Heads</h3>
        <a href="/contact-us/" class="button button-primary">Contact Us <i class="fas fa-chevron-right"></i></a>
      </div>
    </div>
    <div id="black-banner">
      <div class="sub-text">
        <h3>Visit Our Store</h3>
        <strong>Buy New & Used Golf Equipment</strong>
        <a href="https://proclubs.com/" class="button button-primary">Shop Now <i class="fas fa-chevron-right"></i></a>
      </div>
    </div>
  </div>

  <div id="rec-rev">
    <h2 class="page-heading">Recent Reviews</h2>

    <ul id="rr-carousel">
      <li class="rr-slide">
        <div class="rr-wrap">
          <div class="rr-text">
            <strong>Highly Recommended</strong>
            <p class="rr-rev">ProClubs is the standard for trade-ins and demo liquidation in the industry.  We have been using them for a decade and can’t recommend their service more.</p>
            <div class="five-star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <p class="rr-date">Assistant Director of Golf,<br> Desert Mountain Golf Club</p>
            <p class="rr-name">Dan Sewalk</p>
          </div>
        </div>
      </li>
      <li class="rr-slide">
        <div class="rr-wrap">
          <div class="rr-text">
            <strong>Trusted</strong>
            <p class="rr-rev">We have been using ProClubs for years and they are a great service for rentals, demo’s and member trade-ins.  A trusted name in the industry!</p>
            <div class="five-star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <p class="rr-date">Director of Golf, Glen Oaks Country Club</p>
            <p class="rr-name">Steve Watt</p>
          </div>
        </div>
      </li>
      <li class="rr-slide">
        <div class="rr-wrap">
          <div class="rr-text">
            <strong>Love It!</strong>
            <p class="rr-rev">With the ProClub’s Club Finder it was so quick and easy to find the club I was looking for. I would recommend this site to anyone looking to sell their clubs.</p>
            <div class="five-star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <p class="rr-name">Kevin</p>
          </div>
        </div>
      </li>
      <li class="rr-slide">
        <div class="rr-wrap">
          <div class="rr-text">
            <strong>Online Leader</strong>
            <p class="rr-rev">There’s a reason ProClubs.com is the online leader in buying golf. Their commitment to quality and service is second to none. And don’t forget to check out their eBay store!</p>
            <div class="five-star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <p class="rr-name">Matt</p>
          </div>
        </div>
      </li>
      <li class="rr-slide">
        <div class="rr-wrap">
          <div class="rr-text">
            <strong>Reliable</strong>
            <p class="rr-rev">Select and submit your order, ship the clubs, then get paid. It really is that easy! Thank you ProClubs for offering a fast, reliable place to sell my boyfriend’s clubs.</p>
            <div class="five-star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <p class="rr-name">Angie</p>
          </div>
        </div>
      </li>
      <li class="rr-slide">
        <div class="rr-wrap">
          <div class="rr-text">
            <strong>Great Customer Service</strong>
            <p class="rr-rev">The easiest way to sell your clubs on the NET.  Great values and great customer service.  I wish I would have found this site years ago.</p>
            <div class="five-star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <p class="rr-name">Kristopher</p>
          </div>
        </div>
      </li>
      <li class="rr-slide">
        <div class="rr-wrap">
          <div class="rr-text">
            <strong>Happy</strong>
            <p class="rr-rev">After my last arrangement a few months ago, I have recommended you guys to quite a few friends and colleagues. I buy way too many clubs, and have traded with most places (usually for credit), but you were quick with no hassles. I will likely not trade anywhere else, and I really hope you guys are doing well. I could not be happier with your service!</p>
            <div class="five-star">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <p class="rr-name">Jason</p>
          </div>
        </div>
      </li>
    </ul>
  </div>

  <div id="value-is-our-trade">
    <!-- <h2 class="page-heading">Value Is Our Trade</h2> -->
    <ul id="viot-carousel">
      <li class="viot-slide">
        <div class="viot-wrap">
          <div class="blue-circle">
            <i class="fas fa-analytics"></i>
          </div>
          <div class="viot-text">
            <strong>View Live Pricing</strong>
            <p>Use our club finder to find real time cash values for your clubs.</p>
          </div>
        </div>
      </li>
      <li class="viot-slide">
        <div class="viot-wrap">
          <div class="blue-circle">
            <i class="fas fa-boxes"></i>
          </div>
          <div class="viot-text">
            <strong>Sell Excess Shop Inventory</strong>
            <p>We are the standard for trade-ins and demo liquidation.</p>
          </div>
        </div>
      </li>
      <li class="viot-slide">
        <div class="viot-wrap">
          <div class="blue-circle">
            <i class="fas fa-headset"></i>
          </div>
          <div class="viot-text">
            <strong>Sales Rep Selling Inventory</strong>
            <p>A name you can trust for car stock or demo liquidation.</p>
          </div>
        </div>
      </li>
    </ul>
  </div>
</div>

<?php get_footer(); ?>
