# Universal-Calendar

Built with PHP for the API backend, possibly switching to node in the near future.

AngularJS on the Frontend, currently everything is located in one app.js file but it will be modularized in the future.

** Includes the building blocks for a simple booking system as well **
MYSQL required.

Run booking table create script:

```CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `date_booked` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `start` time NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `order_id` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=161;```

Edit backend>booking>calendar>controller's constructor with your mysqli details.
