<?php

/**
 * Plugin Name: Platty Jetengine cct csv button
 * Description: Shortcode to create a button to download a CSV file containing a CCT data. usage = [cct_csv]
 * Version: 0.1
 * Author: Harvey Botero
 **/

add_action('wp_enqueue_scripts', 'dcms_insertar_js');

function dcms_insertar_js()
{
    wp_register_script('dcms_miscript', plugin_dir_url(__FILE__) . '/js/script.js', array('jquery'), '1', true);
    wp_enqueue_script('dcms_miscript');
    wp_localize_script('dcms_miscript', 'dcms_vars', ['ajaxurl' => admin_url('admin-ajax.php')]);
}


add_action('wp_ajax_nopriv_dcms_ajax_readmore', 'dcms_enviar_contenido');
add_action('wp_ajax_dcms_ajax_readmore', 'dcms_enviar_contenido');

function dcms_enviar_contenido()
{
    global $wpdb;
    $cct_elements = $wpdb->get_results("SELECT * FROM wp_jet_cct_leads LIMIT 200");
    $cct_elements_to_csv = array();
    foreach ($cct_elements as $line) {
        array_push($cct_elements_to_csv, (array) $line);
    }

    $f = fopen('php://output', 'w');

    foreach ($cct_elements_to_csv as $line) {
        fputcsv($f, $line, ",");
    }

    wp_die();
}


function cct_csv_link_att($atts)
{

    $default = array(
        'link' => '#',
    );
    $a = shortcode_atts($default, $atts);

    return '<button class="download-csv">Download CSV</button>';
}
add_shortcode('cct_csv', 'cct_csv_link_att');
