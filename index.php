<?php

/**
 * Plugin Name: DSContractsGeneration
 * Description: Automatically generates contracts (PDF format) based on meta field text and sends an email with the pdf link
 * Version: 3.0
 * Author: David Ardelean
 * Text Domain: desprespa-contracts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define('CONTRACTS_TABLE', "desprespa_contracts") ;
define('ADMIN_EMAIL', 'ioana.marian@desprespa.ro');
class ContractsHandler{

    public static ?object $contracts = null;
    private static $listing;

    public static function get_instance(): ContractsHandler {
        if ( self::$contracts === null ) self::$contracts = new ContractsHandler();

        return self::$contracts;
    }
    public function init(){

        register_activation_hook(__FILE__, [$this, 'contracts_activation']);
        $this->load_dependencies();
        self::$listing = new ContractsListing();
        $this->hook_loader();
    }
    public function load_dependencies(){
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        require_once(__DIR__ . '/TCPDF-main/tcpdf.php');
        require_once(__DIR__ . '/src/PDFGeneration.php');
        require_once(__DIR__ . '/src/ContractsListing.php');
        require_once(__DIR__ . '/src/EmailSend.php'); 
        require_once(__DIR__ . '/src/Auxiliary.php');
    }

    public function hook_loader(){
        add_action( 'woocommerce_order_status_processing', 'pdf_contract', 99, 1 );
        add_action('admin_menu', [$this, 'contracts_menu_page']);

        add_filter( 'woocommerce_account_menu_items', 'add_user_endpoint', 99, 1 );
        add_action( 'init', 'rewrite_user_endpoint' );
        flush_rewrite_rules();
        add_action( 'woocommerce_account_contracte_endpoint', [self::$listing,'user_dashboard_content']);

        add_action('woocommerce_billing_fields', 'add_custom_checkout_fields_awards');
        add_action('woocommerce_checkout_create_order', 'save_custom_checkout_fields');
        add_action('wp_enqueue_scripts', [$this, 'enqueue_plugin_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);

    
    }

    public function contracts_menu_page(){
    
            add_menu_page(
                'Contracte despreSpa',         
                'Contracte despreSpa',         
                'manage_options',         
                'contracte-despre-spa',         
                [self::$listing, 'contracts_menu_page_content'], 
                'dashicons-media-document'  
            );
        
    }
    public function enqueue_plugin_styles() {
        wp_enqueue_style('contracts-styles', plugins_url('css/contract_styles.css', __FILE__),array(), '6.2', 'all');
    }

    function enqueue_admin_styles() {
        wp_enqueue_style('admin-styles', plugins_url('css/admin_styles.css', __FILE__), array(), '1.5', 'all');

}
    
    
    
    public function contracts_activation() {
        
        global $wpdb;

        $charset_collate =$wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . CONTRACTS_TABLE;

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            
            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                id_comanda varchar(255) NOT NULL,
                id_client varchar(255) NOT NULL,
                nume_firma varchar(255) NOT NULL,
                status_contract varchar(255) NOT NULL,
                link_contract text NOT NULL,
                numar_contract mediumint(9) NOT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";

            dbDelta($sql);

            $option_name = 'numar_contract';
            $option_value = 656;

            $autoload = 'yes'; 

            add_option($option_name, $option_value, $autoload);
        }
    }

}

$contracts = ContractsHandler::get_instance();
$contracts->init();










