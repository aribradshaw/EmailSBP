# Email Subject and Body Populator Flygon LC

A WordPress plugin to easily generate mailto: links with auto-populated subject and body fields, based on the current page or post context. Includes a user-friendly admin sidebar for configuration.

## Features
- **Shortcode**: Use `[emailpopulator]` to generate a mailto: link on any page or post.
- **Admin Sidebar**: Adds an "Email SBP" menu in the WordPress admin for easy configuration.
- **Customizable Fields**: Choose which fields to include in the email subject and body:
  - Page/Post Title
  - Post Category
  - Page/Post Author
  - Date
- **Dynamic Content**: The mailto: link auto-fills with the selected fields from the current page or post.
- **Credit**: Copyright and credit to Ari Daniel Bradshaw - Flygon LC displayed in the admin page.

## Installation
1. Download or clone this repository.
2. Copy the plugin folder to your WordPress `wp-content/plugins` directory.
3. Activate the plugin from the WordPress admin dashboard.

## Usage
1. Go to the new **Email SBP** menu in the WordPress admin sidebar.
2. Set the email address to use and select which fields you want to include in the email subject and body.
3. Add the `[emailpopulator]` shortcode to any page or post where you want the mailto: link to appear.

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
