<?php
/**
 * Plugin Name:       Hide Featured Image Display
 * Plugin URI:        https://github.com/NzmlHuda/hide-featured-image-display/
 * Description:       Hide featured image display allow you to hide or show featured image in each post separately (in single view).
 * Version:           1.0.0
 * Author:            Md. Nazmul Huda
 * Author URI:        https://github.com/NzmlHuda/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       hide-featured-image-display
 * Domain Path:       /languages
 */

/**
 * Load plugin textdomain
 */
function hide_featured_image_display_load_plugin_textdomain() {
    load_plugin_textdomain( 'hide-featured-image-display', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'hide_featured_image_display_load_plugin_textdomain' );

/**
 * Adds a meta box to the post editing screen
 */
function hide_featured_image_display_meta() {
    add_meta_box( 'hide_featured_image_display_meta', __( 'Hide Featured Image Display', 'hide-featured-image-display' ), 'hide_featured_image_display_meta_callback', null, 'side', 'high' );
}
add_action( 'add_meta_boxes', 'hide_featured_image_display_meta' );

/**
 * Outputs the content of the meta box
 */
function hide_featured_image_display_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'hide_featured_image_display_nonce' );
    $hide_featured_image_display_stored_meta = get_post_meta( $post->ID );
    ?>
    <div class="hide-featured-image-display-row-content .components-panel__row" style="margin-top: 14px;">
        <label for="hide-featured-image-display-checkbox-1">
            <input type="checkbox" name="_hide_featured_image_display_checkbox_1" id="hide-featured-image-display-checkbox-1" value="1" <?php if ( isset ( $hide_featured_image_display_stored_meta['_hide_featured_image_display_checkbox_1'] ) ) checked( $hide_featured_image_display_stored_meta['_hide_featured_image_display_checkbox_1'][0], '1' ); ?> /><?php _e( 'Hide Featured Image Display', 'hide-featured-image-display' )?>
        </label>
    </div>
    <p id="components-form-token-suggestions-howto-0" class="components-form-token-field__help" style="margin-top: 10px;"><?php _e( 'It will hide featured image in single view.', 'hide-featured-image-display' )?></p>
    <?php
}

/**
 * Saves the custom meta input
 */
function hide_featured_image_display_meta_save( $post_id ) {
    // Checks save status - overcome autosave, etc.
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'hide_featured_image_display_nonce' ] ) && wp_verify_nonce( $_POST[ 'hide_featured_image_display_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    // Checks for input and saves - save checked as yes and unchecked at no
    if( isset( $_POST[ '_hide_featured_image_display_checkbox_1' ] ) ) {
        update_post_meta( $post_id, '_hide_featured_image_display_checkbox_1', '1' );
    } else {
        update_post_meta( $post_id, '_hide_featured_image_display_checkbox_1', '0' );
    }
}
add_action( 'save_post', 'hide_featured_image_display_meta_save' );

/**
 * Get the custom meta value from database and add classes to the required places
 */
function hide_featured_image_display_classes() {
    // If is single template or if is page
    if( is_single() || is_page() ){
        // Get the custom meta value from database
        $hide_featured_image_display_checkbox_1 =  get_post_meta( get_the_ID(), '_hide_featured_image_display_checkbox_1', true );/* Hide single post */

        // If hide featured image display checkbox is checked add classes to the required places
        if( '1' === $hide_featured_image_display_checkbox_1  ) {
            // Add classes to the body_class
            function hide_featured_image_display_body_classes( $classes ) {
                $classes[] = 'hide_featured_image_display';
                return $classes;
            }
            add_filter('body_class', 'hide_featured_image_display_body_classes', 100);

            // Add style to head for hide featured image
            ?>
            <style>
                .hide_featured_image_display .post-thumbnail, .hide_featured_image_display .wp-post-image {display: none !important; opacity: 0 !important; visibility: hidden !important;}
            </style>
            <?php
        }
    }
}
add_action( 'wp_head', 'hide_featured_image_display_classes');