<?php

// Register shortcode
add_shortcode('tstshortcode', 'tstshortcode_function');

// Shortcode function
function tstshortcode_function() {
    // Display form
    $output = '<form method="post">';
    $output .= '<label for="title">Title:</label><br>';
    $output .= '<input type="text" id="title" name="title"><br>';
    $output .= '<label for="text">Text:</label><br>';
    $output .= '<textarea id="text" name="text"></textarea><br>';
    $output .= '<input type="submit" value="Submit">';
    $output .= '</form>';

    return $output;
}

// Form submission handler
add_action('init', 'tstshortcode_handle_form_submission');
function tstshortcode_handle_form_submission() {
    if(isset($_POST['title']) && isset($_POST['text'])) {
        $title = sanitize_text_field($_POST['title']);
        $text = sanitize_text_field($_POST['text']);

        // Check if post with same title already exists
        $post = get_page_by_title($title, OBJECT, 'post');
        if($post) {
            wp_die('A post with this title already exists.');
        }

        // Insert new post
        $postarr = array(
            'post_title' => $title,
            'post_content' => $text,
            'post_status' => 'draft',
            'post_type' => 'post'
        );
        $post_id = wp_insert_post($postarr);

        // Send email
        $to = get_option('admin_email');
        $subject = 'New post submitted';
        $message = "Title: $title\n\nText: $text";
        wp_mail($to, $subject, $message);
    }
}
