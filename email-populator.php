<?php
/**
 * Plugin Name: Email Subject and Body Populator by Flygon LC
 * Description: A lightweight WordPress plugin to generate mailto links with customizable subject and body content.
 * Version: 1.1
 * Author: Ari Daniel Bradshaw - Flygon LC
 * Author URI: https://flygonlc.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
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
    register_setting('email_sbp_options', 'email_sbp_email', [
        'sanitize_callback' => 'sanitize_email',
    ]);
    register_setting('email_sbp_options', 'email_sbp_subject_template', [
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    register_setting('email_sbp_options', 'email_sbp_body_template', [
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    register_setting('email_sbp_options', 'email_sbp_color', [
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    register_setting('email_sbp_options', 'email_sbp_hover_color', [
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    register_setting('email_sbp_options', 'email_sbp_underline', [
        'sanitize_callback' => 'absint',
    ]);
    register_setting('email_sbp_options', 'email_sbp_bold', [
        'sanitize_callback' => 'absint',
    ]);
    register_setting('email_sbp_options', 'email_sbp_hover_underline', [
        'sanitize_callback' => 'absint',
    ]);
    register_setting('email_sbp_options', 'email_sbp_hover_bold', [
        'sanitize_callback' => 'absint',
    ]);
    register_setting('email_sbp_options', 'email_sbp_excluded_pages', [
        'sanitize_callback' => function($value) {
            return array_map('absint', (array)$value);
        },
    ]);
});

// Enqueue admin CSS only on our plugin page
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook === 'toplevel_page_email-sbp') {
        wp_enqueue_style('email-sbp-admin-style', plugin_dir_url(__FILE__) . 'admin-style.css', [], '1.0.0');
    }
});

// Admin page content
function email_sbp_admin_page() {
    $excluded_pages = get_option('email_sbp_excluded_pages', []);
    $pages = get_pages(['sort_column' => 'post_title']);
    ?>
    <div class="email-sbp-admin-wrap">
        <h1>Email Subject and Body Populator</h1>
        <div class="email-sbp-info-box">
            <strong>Shortcode:</strong> <code>[emailpopulator]</code><br>
            <strong>Description:</strong> Use this shortcode on any page or post to generate a mailto: link with your custom subject and body. <br>
            <strong>Available placeholders:</strong> <code>{title}</code>, <code>{category}</code>, <code>{author}</code>, <code>{date}</code><br>
            <strong>Example:</strong> <br>
            Subject: <code>Hi, I'm interested in {title}</code><br>
            Body: <code>Category: {category}\nAuthor: {author}\nDate: {date}</code>
        </div>
        <form method="post" action="options.php">
            <?php
            settings_fields('email_sbp_options');
            do_settings_sections('email_sbp_options');
            ?>
            <table class="form-table email-sbp-admin-table">
                <tr valign="top">
                    <th scope="row">Email to be used</th>
                    <td><input type="email" name="email_sbp_email" value="<?php echo esc_attr(get_option('email_sbp_email', '')); ?>" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Subject Template</th>
                    <td><textarea name="email_sbp_subject_template" rows="2" cols="60" required><?php echo esc_textarea(get_option('email_sbp_subject_template', 'Hi, I\'m interested in {title}')); ?></textarea><br>
                    <small>Use placeholders: {title}, {category}, {author}, {date}</small></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Body Template</th>
                    <td><textarea name="email_sbp_body_template" rows="4" cols="60" required><?php echo esc_textarea(get_option('email_sbp_body_template', "Category: {category}\nAuthor: {author}\nDate: {date}")); ?></textarea><br>
                    <small>Use placeholders: {title}, {category}, {author}, {date}</small></td>
                </tr>
            </table>
            <fieldset class="email-sbp-link-style-group">
                <legend>Link Style Options</legend>
                <div class="email-sbp-link-style-grid">
                    <div>
                        <label>Link Color<br><input type="color" name="email_sbp_color" value="<?php echo esc_attr(get_option('email_sbp_color', '#0073aa')); ?>" /></label><br>
                        <label><input type="checkbox" name="email_sbp_underline" value="1" <?php checked(get_option('email_sbp_underline', '1') == '1'); ?> /> Underline Link?</label><br>
                        <label><input type="checkbox" name="email_sbp_bold" value="1" <?php checked(get_option('email_sbp_bold', '') == '1'); ?> /> Bold Link?</label>
                    </div>
                    <div>
                        <label>Link Hover Color<br><input type="color" name="email_sbp_hover_color" value="<?php echo esc_attr(get_option('email_sbp_hover_color', '#005177')); ?>" /></label><br>
                        <label><input type="checkbox" name="email_sbp_hover_underline" value="1" <?php checked(get_option('email_sbp_hover_underline', '') == '1'); ?> /> Underline on Hover?</label><br>
                        <label><input type="checkbox" name="email_sbp_hover_bold" value="1" <?php checked(get_option('email_sbp_hover_bold', '') == '1'); ?> /> Bold on Hover?</label>
                    </div>
                </div>
            </fieldset>
            <table class="form-table email-sbp-admin-table">
                <tr valign="top">
                    <th scope="row">Exclude Pages</th>
                    <td>
                        <select name="email_sbp_excluded_pages[]" multiple>
                            <?php foreach ($pages as $page): ?>
                                <option value="<?php echo esc_attr($page->ID); ?>" <?php if (is_array($excluded_pages) && in_array($page->ID, $excluded_pages)) echo 'selected'; ?>><?php echo esc_html($page->post_title); ?></option>
                            <?php endforeach; ?>
                        </select><br>
                        <small>Hold Ctrl (Windows) or Cmd (Mac) to select multiple pages to exclude from the emailpopulator logic.</small>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <div class="email-sbp-footer email-sbp-footer-centered">
            <?php
            // Inline SVG logo for best practice and to avoid non-enqueued image warning.
            ?>
            <span class="email-sbp-logo" style="height:38px;display:inline-block;vertical-align:middle;margin:0 10px 0 0;line-height:0;">
                <?php
                // Inline SVG from Logo.svg
                ?>
                <svg id="Layer_2" data-name="Layer 2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 882 361" style="height:38px;display:inline-block;vertical-align:middle;">
                  <defs>
                    <style>
                      .cls-1 { fill: #7cb880; }
                      .cls-2 { fill: #98ce9b; }
                      .cls-3 { fill: #a7534c; }
                      .cls-4 { fill: #324b34; }
                      .cls-5 { fill: #a7514b; }
                      .cls-6 { fill: #d0ebaf; }
                      .cls-7 { fill: #d77881; }
                      .cls-8 { fill: #a7524c; }
                      .cls-9 { fill: #d6febf; }
                    </style>
                  </defs>
                  <g id="Layer_2-2" data-name="Layer 2">
                    <g id="Layer_1-2" data-name="Layer 1-2">
                      <g>
                        <path class="cls-6" d="M358,1l-.5,360H.5L0,1h358ZM29,31v129l.6,2.02h128.81l.6-2.02V31l-.6-2.02H29.6l-.6,2.02ZM189,31v129l.6,2.02h128.81l.6-2.02V31l-.6-2.02h-128.81l-.6,2.02ZM29,191v130l289.4,1.02.6-2.02v-129l-.6-2.02H29.6l-.6,2.02Z"/>
                        <polygon class="cls-8" points="836 0 836 35 848 35 848 52 859 52 859 1.5 860.5 0 882 0 882 155 860.5 155 859 153.5 859 86 848 86 848 69 836 69 836 155 813 155 813 0 836 0"/>
                        <path class="cls-3" d="M734,18c2.42-2.41,7.09-.51,10,0V0h46v17l11.56.94.44,117.57-1.64,2.36-10.36.14v17h-46v-18l-10.01-.99V18.01h.01ZM779,18h-23v119h23V18Z"/>
                        <path class="cls-3" d="M653,18l10-1V0h46v17c3.86.52,9.1-1.43,12,1.5v33.5h-22.5l-1.29-1.62.79-32.38h-23v119h23l-.07-49.38c-.84-2.93-9.34-1.18-11.93-1.62v-17h35v67.5c-2.9,2.93-8.14.98-12,1.5v17h-46c-.2-5.63.27-11.37,0-17-.03-.66.05-1.34,0-2h-10.01V18h.01Z"/>
                        <path class="cls-8" d="M548,206v17h11v34h-23v-34h-23v120h23v-34h23v34h-11v17h-46c-.65-5.69-1.06-11.36,0-17h-12v-120h12.01v-17h46-.01Z"/>
                        <path class="cls-3" d="M594,69c3.71.47,17.08.62,20.5,0,1.12-.21,1.87-.51,2.5-1.5V0h21.5l1.5,1.5v67.5h-12v17h-11v67.5l-1.5,1.5h-21l-1.5-1.5v-67.5h-11v-17h-10.5l-.5-1,1-66h20l-.02,66.4c-.11,1.09,1.29.5,2.02.6Z"/>
                        <polygon class="cls-3" points="478 0 478 18 432 18 432 69 467 69 467 86 432 86 432 155 409 155 409 0 478 0"/>
                        <polygon class="cls-3" points="513 0 513 137 559 137 559 155 490 155 490 0 513 0"/>
                        <polygon class="cls-5" points="432 206 432 343 478 343 478 360 409 360 409 206 432 206"/>
                        <path class="cls-5" d="M571,68V0h22l1,69c-.73-.09-2.13.49-2.02-.6l.02-66.4h-20l-1,66Z"/>
                        <path class="cls-5" d="M653,18v118.01h10.01c.05.66-.04,1.33,0,2h-11.01l1.01-120h-.01Z"/>
                        <polygon class="cls-5" points="734 18 733.99 136.01 744 137 732.99 137.01 734 18"/>
                        <path class="cls-7" d="M319,191v129l-290,1v-130h290ZM314,196H34v119c1.28-.55,2.96,1,3.5,1h275l1.5-1.5v-118.5Z"/>
                        <path class="cls-5" d="M319,31v129h-25V31c8.32.01,16.68,0,25,0Z"/>
                        <path class="cls-5" d="M54,31v129h-25V31h25Z"/>
                        <path class="cls-5" d="M199,31v129h-10V31c3.32-.05,6.68.07,10,0Z"/>
                        <path class="cls-5" d="M159,31v129h-10V31c3.32.09,6.68-.06,10,0Z"/>
                        <polygon class="cls-9" points="29 191 29.6 188.98 318.4 188.98 319 191 29 191"/>
                        <polygon class="cls-9" points="319 320 318.4 322.02 29 321 319 320"/>
                        <path class="cls-9" d="M54,160h105l-.6,2.02H29.6l-.6-2.02h25Z"/>
                        <path class="cls-9" d="M199,160h5c8.32,0,16.68-.03,25,0h5c8.32.01,16.68,0,25,0h5c8.32,0,16.68.05,25,0h30l-.6,2.02h-128.81l-.6-2.02h10.01Z"/>
                        <path class="cls-9" d="M29,31l.6-2.02h128.81l.6,2.02c-3.32-.06-6.68.09-10,0-2.02-.05-3.2,0-5,0-8.32.04-16.68-.02-25,0h-5c-8.32.02-16.68-.01-25,0H29Z"/>
                        <path class="cls-9" d="M189,31l.6-2.02h128.81l.6,2.02c-8.32,0-16.68.01-25,0h-5c-8.32-.04-16.68,0-25,0h-35c-8.32,0-16.68.04-25,0-1.61,0-3.32-.03-5,0-3.32.07-6.68-.05-10,0h-.01Z"/>
                        <path class="cls-1" d="M314,196v118.5l-1.5,1.5H37.5c-.54,0-2.22-1.55-3.5-1v-119h280ZM299,205H44v6h255v-6ZM64,225v66l224.4,1.02.6-2.02v-65H64ZM299,305H44v6h255v-6Z"/>
                        <path class="cls-7" d="M294,31v129h-5V31h5Z"/>
                        <path class="cls-7" d="M59,31v129h-5V31h5Z"/>
                        <path class="cls-7" d="M204,31v129h-5V31c1.68-.03,3.39,0,5,0Z"/>
                        <path class="cls-7" d="M149,31v129h-5V31c1.8,0,2.98-.05,5,0Z"/>
                        <path class="cls-5" d="M84,31v129h-25V31h25Z"/>
                        <path class="cls-5" d="M114,31v129h-25V31c8.32-.01,16.68.02,25,0Z"/>
                        <path class="cls-5" d="M144,31v129h-25V31c8.32-.02,16.68.04,25,0Z"/>
                        <path class="cls-7" d="M119,31v129h-5V31h5Z"/>
                        <path class="cls-7" d="M89,31v129h-5V31h5Z"/>
                        <path class="cls-5" d="M229,31v129c-8.32-.03-16.68,0-25,0V31c8.32.04,16.68,0,25,0Z"/>
                        <path class="cls-5" d="M259,31v129c-8.32,0-16.68.01-25,0V31h25Z"/>
                        <path class="cls-5" d="M289,31v129c-8.32.05-16.68,0-25,0V31c8.32,0,16.68-.04,25,0Z"/>
                        <path class="cls-7" d="M264,31v129h-5V31h5Z"/>
                        <path class="cls-7" d="M234,31v129h-5V31h5Z"/>
                        <path class="cls-4" d="M289,225v65l-225,1v-66h225ZM84,242v32l.95,1.51,182.14.04,1.92-1.55v-32l-1.92-1.55-182.14.04-.95,1.51Z"/>
                        <rect class="cls-2" x="44" y="205" width="255" height="6"/>
                        <rect class="cls-2" x="44" y="305" width="255" height="6"/>
                        <polygon class="cls-2" points="289 290 288.4 292.02 64 291 289 290"/>
                        <path class="cls-6" d="M269,242v32H84v-32h185ZM109,250h-15v16h15v-16ZM144,250h-15v16h15v-16ZM184,250h-15v16h15v-16ZM224,250h-15v16h15v-16ZM259,250h-15v16h15v-16Z"/>
                        <polygon class="cls-9" points="269 242 84 242 84.95 240.49 267.08 240.45 269 242"/>
                        <polygon class="cls-9" points="269 274 267.08 275.55 84.95 275.51 84 274 269 274"/>
                        <rect class="cls-3" x="94" y="250" width="15" height="16"/>
                        <rect class="cls-3" x="129" y="250" width="15" height="16"/>
                        <rect class="cls-3" x="169" y="250" width="15" height="16"/>
                        <rect class="cls-3" x="244" y="250" width="15" height="16"/>
                        <rect class="cls-3" x="209" y="250" width="15" height="16"/>
                      </g>
                    </g>
                  </g>
                </svg>
            </span>
            <span>&copy; <?php echo esc_html( gmdate('Y') ); ?> <a href="https://flygonlc.com" target="_blank" rel="noopener noreferrer">Ari Daniel Bradshaw - Flygon LC</a></span>
        </div>
    </div>
    <?php
}

// Shortcode handler
add_shortcode('emailpopulator', function($atts) {
    if (!is_singular()) return '';
    $excluded = get_option('email_sbp_excluded_pages', []);
    global $post;
    $email = get_option('email_sbp_email', '');
    // If excluded, just show a simple mailto link with no subject/body population
    if (is_array($excluded) && in_array($post->ID, $excluded)) {
        if (!$email) return '';
        $color = get_option('email_sbp_color', '#0073aa');
        $hover_color = get_option('email_sbp_hover_color', '#005177');
        $underline = get_option('email_sbp_underline', '1') == '1';
        $bold = get_option('email_sbp_bold', '') == '1';
        $hover_underline = get_option('email_sbp_hover_underline', '') == '1';
        $hover_bold = get_option('email_sbp_hover_bold', '') == '1';
        $style = 'color:' . esc_attr($color) . ';';
        $style .= $underline ? 'text-decoration:underline;' : 'text-decoration:none;';
        $style .= $bold ? 'font-weight:bold;' : '';
        $unique = uniqid('emailpopulator_');
        $hover_style = 'color:' . esc_attr($hover_color) . ';';
        $hover_style .= $hover_underline ? 'text-decoration:underline !important;' : 'text-decoration:none !important;';
        $hover_style .= $hover_bold ? 'font-weight:bold;' : ($bold ? 'font-weight:bold;' : 'font-weight:normal;');
        $output = '<a href="mailto:' . esc_attr($email) . '" class="emailpopulator-btn ' . esc_attr($unique) . '" style="' . $style . '">' . esc_html($email) . '</a>';
        $output .= '<style>.' . $unique . ':hover { ' . $hover_style . ' }</style>';
        return $output;
    }
    $subject_tpl = get_option('email_sbp_subject_template', 'Hi, I\'m interested in {title}');
    $body_tpl = get_option('email_sbp_body_template', "Category: {category}\nAuthor: {author}\nDate: {date}");
    $color = get_option('email_sbp_color', '#0073aa');
    $hover_color = get_option('email_sbp_hover_color', '#005177');
    $underline = get_option('email_sbp_underline', '1') == '1';
    $bold = get_option('email_sbp_bold', '') == '1';
    $hover_underline = get_option('email_sbp_hover_underline', '') == '1';
    $hover_bold = get_option('email_sbp_hover_bold', '') == '1';
    $replacements = [
        '{title}' => get_the_title($post),
        '{category}' => ($cats = get_the_category($post->ID)) && $cats ? esc_html($cats[0]->name) : '',
        '{author}' => get_the_author_meta('display_name', $post->post_author),
        '{date}' => get_the_date('', $post),
    ];
    $subject = strtr($subject_tpl, $replacements);
    $body = strtr($body_tpl, $replacements);
    $mailto = 'mailto:' . rawurlencode($email)
        . '?subject=' . rawurlencode($subject)
        . '&body=' . rawurlencode($body);
    $style = 'color:' . esc_attr($color) . ';';
    $style .= $underline ? 'text-decoration:underline;' : 'text-decoration:none;';
    $style .= $bold ? 'font-weight:bold;' : '';
    $unique = uniqid('emailpopulator_');
    $hover_style = 'color:' . esc_attr($hover_color) . ';';
    $hover_style .= $hover_underline ? 'text-decoration:underline !important;' : 'text-decoration:none !important;';
    $hover_style .= $hover_bold ? 'font-weight:bold;' : ($bold ? 'font-weight:bold;' : 'font-weight:normal;');
    $output = '<a href="' . esc_url($mailto) . '" class="emailpopulator-btn ' . esc_attr($unique) . '" style="' . $style . '">' . esc_html($email) . '</a>';
    $output .= '<style>.' . $unique . ':hover { ' . $hover_style . ' }</style>';
    return $output;
});
