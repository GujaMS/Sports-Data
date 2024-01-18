# Sports Data Plugin Documentation


## 1. Overview

The Sports Data plugin enhances your WordPress site with real-time sports data, providing dynamic visualizations and updates on various sports matches. This documentation aims to guide you through the installation, configuration, and usage of the plugin.

## 2. Installation

To install the Sports Data plugin, follow these steps:

- Download the plugin ZIP file.
- Upload and activate the plugin through the WordPress admin panel.

## 3. Configuration

After installation, navigate to the "Sports Data" settings page in the admin panel. Here, you can configure the plugin to display live data for different sports.

## 4. Admin Panel

The admin panel provides an interface to configure plugin settings. It includes:

- **Sports Selector Dropdown:** Choose a sport from the dropdown.
- **Get Live Data Button:** Click to fetch and display live data for the selected sport.

## 5. Shortcode

The Sports Data plugin can be embedded anywhere on your site using the shortcode `[sports_data]`. This shortcode displays live sports data based on the selected configuration.

Usage:
[sports_data]

## 6. JavaScript and AJAX

The plugin utilizes JavaScript and AJAX to fetch live sports data without reloading the page. The `sports-data-script.js` file handles the interaction, and AJAX requests are made to the WordPress `admin-ajax.php` endpoint.

### Example Code

// JavaScript code for fetching live data
// ...

function getLiveData() {
    // ...
    $.post(ajaxurl, data, function (response) {
        // Update the content with live data
    }).fail(function (xhr, textStatus, errorThrown) {
        // Handle AJAX request failures
    });
}

// ...

## 7. Styles

Styles for the plugin are defined in the `sports-data-style.css` file. This file includes the necessary CSS rules for styling the live sports data display.

## 8. Error Handling

The plugin includes error handling mechanisms for AJAX requests. If there is an issue fetching live data, appropriate error messages will be displayed to the user.

## 9. Developer Notes

- The plugin follows object-oriented programming (OOP) standards for better structure and maintainability.
- The `SportsDataPlugin` class encapsulates functionality and hooks into WordPress actions.
- Data received from the API is processed and organized by the `process_sports_data` method.

## 10. FAQs

**Q: Why do I see an error when fetching live data?**
A: Check the plugin configuration and ensure that the selected sport is valid.

**Q: Can I customize the appearance of the live data display?**
A: Yes, you can modify the styles in the `sports-data-style.css` file to match your site's design.
