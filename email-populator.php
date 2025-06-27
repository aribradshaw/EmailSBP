<?php
/**
 * Plugin Name: Email Subject and Body Populator Flygon LC
 * Description: Securely handles Guesty API bearer token and exposes it to front-end JavaScript. Allows various Guesty API shortcode calls.
 * Version: 0.1 - Initial Alpha
 * Author: Ari Daniel Bradshaw - Flygon LC
 */

// Register admin menu
add_action('admin_menu', function() {
    add_menu_page(
        'Email SBP',
        'Email SBP',
        'manage_options',
        'email-sbp',
        'email_sbp_admin_page',
        'dashicons-email',
        26
    );
});

// Register settings
add_action('admin_init', function() {
    register_setting('email_sbp_options', 'email_sbp_email');
    register_setting('email_sbp_options', 'email_sbp_fields');
});

// Admin page content
function email_sbp_admin_page() {
    ?>
    <div class="wrap">
        <h1>Email Subject and Body Populator</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('email_sbp_options');
            do_settings_sections('email_sbp_options');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Email to be used</th>
                    <td><input type="email" name="email_sbp_email" value="<?php echo esc_attr(get_option('email_sbp_email', '')); ?>" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Fields to include</th>
                    <td>
                        <?php $fields = (array)get_option('email_sbp_fields', []); ?>
                        <label><input type="checkbox" name="email_sbp_fields[]" value="title" <?php checked(in_array('title', $fields)); ?>> Page/Post Title</label><br>
                        <label><input type="checkbox" name="email_sbp_fields[]" value="category" <?php checked(in_array('category', $fields)); ?>> Post Category</label><br>
                        <label><input type="checkbox" name="email_sbp_fields[]" value="author" <?php checked(in_array('author', $fields)); ?>> Page/Post Author</label><br>
                        <label><input type="checkbox" name="email_sbp_fields[]" value="date" <?php checked(in_array('date', $fields)); ?>> Date</label><br>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <div style="margin-top:2em; font-size:0.9em; color:#888; border-top:1px solid #eee; padding-top:1em;">
            &copy; <?php echo date('Y'); ?> Ari Daniel Bradshaw - Flygon LC
        </div>
    </div>
    <?php
}

// Shortcode handler
add_shortcode('emailpopulator', function($atts) {
    if (!is_singular()) return '';
    $email = get_option('email_sbp_email', '');
    $fields = (array)get_option('email_sbp_fields', []);
    global $post;
    $subject = [];
    $body = [];
    if (in_array('title', $fields)) {
        $subject[] = get_the_title($post);
    }
    if (in_array('category', $fields)) {
        $cats = get_the_category($post->ID);
        if ($cats) $body[] = 'Category: ' . esc_html($cats[0]->name);
    }
    if (in_array('author', $fields)) {
        $body[] = 'Author: ' . get_the_author_meta('display_name', $post->post_author);
    }
    if (in_array('date', $fields)) {
        $body[] = 'Date: ' . get_the_date('', $post);
    }
    $mailto = 'mailto:' . rawurlencode($email)
        . '?subject=' . rawurlencode('Hi, I\'m interested in ' . implode(' ', $subject))
        . '&body=' . rawurlencode("\n" . implode("\n", $body));
    return '<a href="' . esc_url($mailto) . '" class="emailpopulator-btn">Send Email</a>';
});
