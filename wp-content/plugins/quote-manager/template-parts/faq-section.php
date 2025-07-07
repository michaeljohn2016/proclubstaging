<?php
/**
 * Template part for displaying FAQ section
 */

// Get the current page template/file name to conditionally apply styles
$current_file = basename(get_page_template());
?>
<style>
@media (min-width: 960px) {
    .clubreview .faq-wrapper {
        margin-top: 110px;
    }
}
</style>
<div class="faq-wrapper">
    <h2 class="faq-title">FAQ</h2>
    <div class="faq-columns">
        <div class="faq-column">
            <div class="faq-list-items">
                <div class="faq-box">
                    <div class="faq-content">
                        <div class="faq-question">
                            <h3 class="faq-question__title">Does ProClubs have a retail location?</h3>
                            <button class="faq-question__btn">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                        <div class="faq-answer">
                            <p>While ProClubs does not operate a traditional retail storefront, we
                            do have an office located in North Phoenix where customers can sell 
                            their equipment in person. The process is quick and efficient—
                            typically taking only 5 to 10 minutes. Before visiting, please make 
                            sure to check current values on sell.proclubs.com to ensure a 
                            smooth experience.</p>
                        </div>
                    </div>
                </div>
                <div class="faq-box">
                    <div class="faq-content">
                        <div class="faq-question">
                            <h3 class="faq-question__title">Can international customers use your program to sell equipment?</h3>
                            <button class="faq-question__btn">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                        <div class="faq-answer">
                            <p>At this time, we are only able to purchase equipment from customers 
                                within the United States. Unfortunately, due to duties, tariffs, and 
                                international shipping complexities, we do not accept equipment from 
                                international customers. We appreciate your understanding and hope to 
                                offer more options in the future.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="faq-column">
            <div class="faq-list-items">
                <div class="faq-box">
                    <div class="faq-content">
                        <div class="faq-question">
                            <h3 class="faq-question__title">How will I receive payment for my order?</h3>
                            <button class="faq-question__btn">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                        <div class="faq-answer">
                            <p>Once we receive your equipment, our team will check in your order 
                            within 1 business day. After that, your payment will be processed shortly. 
                            You can choose to receive your payment either via PayPal or by check-
                            whichever method you prefer.</p>
                        </div>
                    </div>
                </div>
                <div class="faq-box">
                    <div class="faq-content">
                        <div class="faq-question">
                            <h3 class="faq-question__title">Who pays for shipping to get my clubs to you?</h3>
                            <button class="faq-question__btn">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                        <div class="faq-answer">
                            <p>Yes, customers are responsible for properly boxing and shipping their 
                            clubs to us. Please make sure your equipment is securely packaged to 
                            prevent any damage during transit. We advise you to use a courier that 
                            offers tracking with proof of delivery as well as insurance. ProClubs is not 
                            responsible for any damage caused to any item(s) sent for trade-in 
                            transactions.</p>
                        </div>
                    </div>
                </div>
                <div class="faq-box">
                    <div class="faq-content">
                        <div class="faq-question">
                            <h3 class="faq-question__title">What are the different condition categories?</h3>
                            <button class="faq-question__btn">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                        <div class="faq-answer">
                            <p>New – Brand new; the club has never been used or hit. Average – Shows typical signs of use on the clubhead, but all original 
                            design features remain intact. No skymarks, dents, dings, or rattles. Fair – Displays more noticeable wear than average but still maintains the 
                            overall structural integrity and performance of the club.
                            Please note: ProClubs.com reserves the right to refuse any club that does 
                            not meet the condition guidelines outlined above.
                            Additional Conditions:
                            WE WILL ONLY ACCEPT 10 LEFT-HANDED CLUBS PER CUSTOMER 
                            PER 12 MONTH PERIOD.
                            MAXIMUM OF 10 ITEMS OF THE SAME SKU PER 12 MONTH 
                            PERIOD</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 