-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 09. Jan 2019 um 04:41
-- Server-Version: 10.1.36-MariaDB
-- PHP-Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `security-shop`
--

--
-- Daten für Tabelle `products`
--

INSERT INTO `products` (`name`, `price`, `description`, `stock`) VALUES
('Large prime number', 250, 'Numbers in stock are between 2,147,483,647 and 2<sup>82,589,933</sup>-1. If you need larger primes, feel free to contact us for a custom offer.', 30),
('Elliptic curve', 1337, 'You need this for solid security. Trust us.', 7),
('Private key', 15000, 'IMPORTANT: To use this product, you also need to purchase a corresponding public key.', 100),
('One-time password', 5, 'You need these for two-factor authentication in this shop. Secure your account now!', 1000),
('Prepared statement', 12, 'Up to 3 parameters included for FREE!', 27),
('One-way function (reusable)', 1337, 'Make sure to add some salt!', 25),
('Public key', 800, 'IMPORTANT: To use this product, you also need to purchase a corresponding private key.', 200),
('Bits of entropy (5 pack)', 420, 'Add these to your passwords to make them harder to guess!', 555),
('CSRF-Token', 25, 'SPECIAL for new customers: in your first 3 session, POST requests on this site can be made for free. After that you will have to provide a purchased CSRF-Token.', 333);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
