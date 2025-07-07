<?php
/**
 * Template functions for Quote Manager
 */

if (!function_exists('quote_manager_get_faq_section')) {
    /**
     * Display the FAQ section
     */
    function quote_manager_get_faq_section() {
        include plugin_dir_path(__DIR__) . 'template-parts/faq-section.php';
    }
} 