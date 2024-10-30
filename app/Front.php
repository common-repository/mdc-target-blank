<?php
namespace Codexpert\Target_Blank\App;

use Codexpert\Plugin\Base;
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Front
 * @author Codexpert <hi@codexpert.io>
 */
class Front extends Base {

	public $plugin;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->version	= $this->plugin['Version'];
	}

	public function head() {}
	
	/**
	 * Enqueue JavaScripts and stylesheets
	 */
	public function enqueue_scripts() {
		$min = defined( 'TBLANK_DEBUG' ) && TBLANK_DEBUG ? '' : '.min';

		wp_enqueue_style( $this->slug, plugins_url( "/assets/css/front{$min}.css", TBLANK ), '', $this->version, 'all' );

		wp_enqueue_script( $this->slug, plugins_url( "/assets/js/front{$min}.js", TBLANK ), [ 'jquery' ], $this->version, true );
		
		$localized = [
			'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
			'_wpnonce'	=> wp_create_nonce(),
		];
		wp_localize_script( $this->slug, 'TBLANK', apply_filters( "{$this->slug}-localized", $localized ) );
	}
    
    /**
     * Add the `target="_blank"` attribute
     * 
     * @uses regex
     * @since 3.0
     */
    public function the_content( $content ) {

        $result = preg_replace_callback(
            '/<a(.*?)>/i',
            function ($matches) {
                
                // Check if the 'target' attribute already exists
                if ( strpos( $matches[1], 'target=' ) !== false) {
                    return $matches[0]; // Return the original match unchanged
                }

                // Add the 'target="_blank"' attribute
                else {
                    return '<a' . $matches[1] . ' target="_blank">';
                }
            },

            $content
        );

        return $result;
    }

	public function modal() {
		echo '
		<div id="mdc-target-blank-modal" style="display: none">
			<img id="mdc-target-blank-modal-loader" src="' . esc_attr( TBLANK_ASSET . '/img/loader.gif' ) . '" />
		</div>';
	}
}