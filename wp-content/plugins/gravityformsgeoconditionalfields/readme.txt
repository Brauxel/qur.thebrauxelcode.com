=== Gravity Forms Geo Conditional Fields ===
Contributors: nathanfranklinau
Author: Nathan Franklin
Author URI: http://www.nathanfranklin.com.au
Requires at least: 3.0
Tested up to: 4.4.2
Version: 2.0.0
Network: true

== Copyright ==
Copyright 2014 Nathan Franklin

This software is NOT to be distributed without prior written permission from respective author.
This software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

== Description ==

At times, you need to display certain fields for certain users based on their geographical location, be it their country or continent. Perhaps a user is from Australia and you need to collect a different set of information to those users who are in the rest of the world. Perhaps in certain countries you may need to collect extra information for verification purposes or feed your users different terms and conditions based on their geographical location. What about even using conditional logic to send specific notifications to an admin or user based on which part of the world the form is being submitted from.

If you can identify with any of these scenarios, then you have come to the right place. The Gravity Forms Geo Conditional Fields plugins creates an additional 2 hidden field types that you can use to determine what country and what continent the user filling out your form is from. Whether it’s purely for viewing this information when a submission is made, geographically customising who receives notifications using conditional logic or customising the fields the user will complete based on their geographical location, this field is for you.

It’s simple. Just install and activate the plugin and you’re good to go. All you need to do is add a Geo Conditional (country or continent) field into your form and then save it. The plugin will immediately start collecting data the next time your form is submitted.

The plugin will determine the users geographical location based on their IP address which a unique identifier of the user completing your form. It will perform a lookup using the MaxMind GeoLite2 database to find a match. The plugin will also ensure this database is automatically downloaded on a monthly basis to ensure it stays up to date and current.

The plugin has been set up for translation and includes the English translation.

You must have Gravity Forms 1.9.x or higher installed and activated on your WordPress site.

This product includes GeoLite2 data created by MaxMind, available from
<a href="http://www.maxmind.com">http://www.maxmind.com</a>. The accuracy of this plugin is subject to the accuracy of the GeoLite2 database provided by MaxMind.

You are welcome to email me@nathanfranklin.com.au if you require support or have any questions/comments.

== Changelog ==

= 2.0.0 =
* Major refactor for better compatibility with newer versions of Gravity Forms. NOTE: This update requires Gravity Forms 1.9.x in order to run.
* CHANGED: Converted field to conform to GF_Field model.
* CHANGED: Raw value original was 'Country/Continent [code]'. Raw value is now returned as just 'code'. This is only applicable for developers who may be extending the plugin.

= 1.27 =
* FIXED: Bug which would cause the plugin to not initialize when WordPress was installed in a directory other than default.

= 1.26 =
* FIXED: Localisation namespace inconsistencies

= 1.25 =
* FIXED: Fixed some Undefined index notices.

= 1.24 =
* FIXED: PHP5.3 compatibility - created a skeleton for JsonSerializable interface for MaxMind GeoIp

= 1.2 =
* ADDED: Support for newer style feed conditional logic.
* INFO: Support of older style feeds with merge tags.

= 1.2 =
* ADDED: Merge Tag {geo_country} - AU/US/IL etc..
* ADDED: Merge Tag {geo_country_display} - Australia, United States, Ireland etc..
* ADDED: Merge Tag {geo_continent} - NA/SA etc..
* ADDED: Merge Tag {geo_continent_display} - North America, South America etc...
* ADDED: Ability to use custom merge tags in hidden field default values for feed conditional logic (such as PayPal), and notification routing (separate from notification conditional logic)
* FIXED: Incorrectly changing field display values using gform_get_input_value instead of correct filters.

= 1.01 =
* FIXED: Oceania was regarded as Australia which meant Continent conditional did not work.

= 1.0 =
* Initial Release.
