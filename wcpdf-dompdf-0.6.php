<?php
/**
 * Plugin Name: WooCommerce PDF Invoices & Packing Slips dompdf 0.6
 * Plugin URI: http://www.wpovernight.com
 * Description: Uses dompdf 0.6 instead of 0.8
 * Version: 1.0
 * Author: Ewout Fernhout
 * Author URI: http://www.wpovernight.com
 * License: GPLv2 or later
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 * Text Domain: woocommerce-pdf-invoices-packing-slips
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'WCPDF_Custom_PDF_Maker_DOMPDF_0_6' ) ) :

class WCPDF_Custom_PDF_Maker_DOMPDF_0_6 {
	public $html;
	public $settings;

	public function __construct( $html, $settings = array() ) {
		$this->html = $html;

		$default_settings = array(
			'paper_size'		=> 'A4',
			'paper_orientation'	=> 'portrait',
		);
		$this->settings = $settings + $default_settings;
	}

	public function output() {
		if ( empty( $this->html ) ) {
			return;
		}
		
		if ( !class_exists('\DOMPDF') ) {
			// extra check to avoid clashes with other plugins using DOMPDF
			// This could have unwanted side-effects when the version that's already
			// loaded is different, and it could also miss fonts etc, but it's better
			// than not checking...
			require_once( plugin_dir_path( __FILE__ ) . "dompdf/dompdf_config.inc.php" );
		}

		try {
			$dompdf = new \DOMPDF();
			$dompdf->load_html( $this->html );
			$dompdf->set_paper( $this->settings['paper_size'], $this->settings['paper_orientation'] );
			$dompdf = apply_filters( 'wpo_wcpdf_before_dompdf_render', $dompdf, $this->html );
			$dompdf->render();
			$dompdf = apply_filters( 'wpo_wcpdf_after_dompdf_render', $dompdf, $this->html );

			return $dompdf->output();
		} catch (Exception $e) {
			return;
		}
	}
}

endif; // class_exists

add_filter( 'wpo_wcpdf_pdf_maker', 'wpo_wcpdf_pdf_maker_custom' );
function wpo_wcpdf_pdf_maker_custom( $class ) {
	$class = 'WCPDF_Custom_PDF_Maker_DOMPDF_0_6';
	return $class;
}