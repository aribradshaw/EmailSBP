# Email Subject and Body Populator Flygon LC

A WordPress plugin to generate mailto: links with customizable subject and body fields, using dynamic data from the current page or post. Features a user-friendly admin page to set the email address, subject/body templates (with placeholders), link style options (color, underline, bold, hover effects), and page exclusions. Includes a shortcode, detailed instructions, and a credit footer.

## Features
- **Shortcode**: Use `[emailpopulator]` to generate a mailto: link on any page or post.
- **Admin Page**: Adds an "Email SBP" menu in the WordPress admin for easy configuration.
- **Customizable Email**: Set the recipient email address, subject template, and body template with support for placeholders:
  - `{title}` (Page/Post Title)
  - `{category}` (First Post Category)
  - `{author}` (Page/Post Author)
  - `{date}` (Publish Date)
- **Link Style Options**: Choose link color, hover color, underline, bold, and hover effects for the mailto link.
- **Exclude Pages**: Select specific pages where the shortcode should not output a link.
- **Dynamic Content**: The mailto link auto-fills with the selected fields from the current page or post.
- **Instructions & Examples**: The admin page provides clear usage instructions and placeholder examples.
- **Credit Footer**: Displays copyright and credit to Ari Daniel Bradshaw - Flygon LC in the admin page.
- **Plugin Row Link**: Adds a credit link to the plugin row in the WordPress Plugins list.

## Installation
1. Download or clone this repository.
2. Copy the plugin folder to your WordPress `wp-content/plugins` directory.
3. Activate the plugin from the WordPress admin dashboard.

## Usage
1. In your WordPress admin, go to the **Email SBP** menu.
2. Enter the recipient email address.
3. Customize the subject and body templates using placeholders (`{title}`, `{category}`, `{author}`, `{date}`) as needed.
4. Adjust link style options (color, underline, bold, hover effects) to match your site.
5. (Optional) Select any pages to exclude from showing the mailto link.
6. Click **Save Changes**.
7. Add the `[emailpopulator]` shortcode to any page or post where you want the mailto link to appear. The link will use your configured settings and auto-populate with the current page/post data.

## Example
If you are on a page for a property called "View House" and have selected Title and Author, the generated email will look like:

```
Subject: Hi, I'm interested in View House
Body:
Author: John Doe
```

## License
Copyright (c) Ari Daniel Bradshaw - Flygon LC

---
This plugin was developed by Ari Daniel Bradshaw - Flygon LC.
