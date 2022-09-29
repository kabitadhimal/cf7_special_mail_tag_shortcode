<?php
/**
 * step 1. create a custom tag for post_title as cf7 special tag wont work if the cf7 shortcode is placed outside the `the_content`
 * step 2. place the custom tag in the form (mandatory) and the body (optional)
 * step 3. the cf7 to webhook plugin ignore the custom cf7 tags, so create a cf7 special mail tags pair for each custom tag
 * step 4. the suggested naming convension for custom tag is [vendor_custom_tag_name]
 * and for special mail tag, add _ (underscore) to the custom tag eg [_vendor_custom_tag_name]
 *
 * In Contact Form and Email Body use [app_post_title] and [app_post_id]
 * In webhook use [_app_post_title] and [_app_post_id]
 */
add_action( 'wpcf7_init', 'app_custom_post_tags' );
function app_custom_post_tags() {
    wpcf7_add_form_tag( 'app_post_title', 'app_custom_post_title_tag_handler' );
    wpcf7_add_form_tag( 'app_post_id', 'app_custom_post_id_tag_handler' );
}

function app_custom_post_title_tag_handler( $tag ) {
    global $post;
    return <<<EOF
<input type="hidden" name="app_post_title" value="{$post->post_title}">
EOF;
}

function app_custom_post_id_tag_handler( $tag ) {
    global $post;
	$postID = (int) $post->ID;
    return <<<EOF
<input type="hidden" name="app_post_id" value="{$postID}">
EOF;
}

/**
 * contactform7 special tag to retrieve _post_* meta does not work
 * if the contactform7 shortcode is placed outside the post content container
 * so need to create a custom special_mail_tag
 */
add_filter( 'wpcf7_special_mail_tags', function ($output, $name, $html){
    // For backwards compatibility
    $name = preg_replace( '/^wpcf7\./', '_', $name );

    if($name == '_app_post_title'){
        $output = (isset($_POST['app_post_title']))
            ? sanitize_title($_POST['app_post_title']) : null;
    }

    if($name == '_app_post_id'){
        $output = (isset($_POST['app_post_id']))
            ? sanitize_title($_POST['app_post_id']) : null;
    }
    return $output;

}, 20, 3 );
