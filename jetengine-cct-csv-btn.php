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

    $leads_table = $_POST['leads_table'] ? $_POST['leads_table'] : 'wp_jet_cct_leads';
    $school_id = $_POST['school_id'] ? $_POST['school_id'] : '0';

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $query = "
    SET @cols := (SELECT GROUP_CONCAT(column_name SEPARATOR ', ') AS columns FROM information_schema.columns WHERE table_name = '" . $leads_table . "' AND table_schema = '" . DB_NAME . "');
    SET @colnames := (SELECT CONCAT('\'', GROUP_CONCAT(column_name SEPARATOR '\', \''),'\'') AS columns FROM information_schema.columns WHERE table_name = '" . $leads_table . "' AND table_schema = '" . DB_NAME . "');
    SET @getheaders := CONCAT('SELECT ', @colnames);
    SET @getdata := CONCAT('(', @getheaders, ') UNION (SELECT ', @cols, ' FROM " . $leads_table . " WHERE lead_school_id = " . $school_id . ")');
    PREPARE leadsquery FROM @getdata;
    EXECUTE leadsquery;
    DEALLOCATE PREPARE leadsquery;
    ";

    $cct_elements_to_csv = array();
    $mysqli->multi_query($query);
    do {
        /* store the result set in PHP */
        if ($result = $mysqli->store_result()) {
            while ($row = $result->fetch_row()) {
                array_push($cct_elements_to_csv, $row);
            }
        }
    } while ($mysqli->next_result());


    if ($mysqli->connect_error) {
        die('Connection error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
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
        'school_id' => '',
        'leads_table' => '',
    );
    $a = shortcode_atts($default, $atts);

    return '<button class="download-csv" school-id="' . $a['school_id'] . '" leads-t="' . $a['leads_table'] . '">Download CSV</button>';
}
add_shortcode('cct_csv', 'cct_csv_link_att');
