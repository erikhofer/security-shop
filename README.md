# Security Shop

In this study project we aim to implement a secure webshop.

## Prerequisites
* Webserver + PHP
* MySQL Database Server

## Installation
* Make the `src` directory accessible via the webserver. If you use xampp you can use [xampp-here](https://github.com/erikhofer/xampp-here) to do this.
* Create a database called `security-shop`.
* Execute first `sql/schema.sql`, then `sql/products.sql` to preload the database schema and sample content.
* To configure the database connection, copy the file `src/config/config-local.php.dist` to `src/config/config-local.php`. There you can override the values defined in `src/config/config.php`.