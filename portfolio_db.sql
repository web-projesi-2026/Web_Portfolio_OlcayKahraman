-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 10 Haz 2026, 12:05:03
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `portfolio_db`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `issuer` varchar(150) NOT NULL,
  `category` varchar(80) NOT NULL DEFAULT 'other',
  `date_earned` date NOT NULL,
  `credential_url` varchar(500) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `certificates`
--

INSERT INTO `certificates` (`id`, `title`, `issuer`, `category`, `date_earned`, `credential_url`, `description`, `created_at`) VALUES
(1, 'Web Gelistirme Temelleri', 'BTK Akademi', 'web', '2024-03-15', 'https://btkakademi.gov.tr', 'HTML, CSS ve JavaScript temellerini kapsayan 40 saatlik egitim.', '2026-06-10 09:21:25'),
(2, 'C# ile Nesne Yonelimli Programlama', 'Udemy', 'backend', '2024-06-01', 'https://udemy.com', 'OOP prensipleri, SOLID, Design Patterns.', '2026-06-10 09:21:25'),
(3, 'ASP.NET Core MVC', 'Udemy', 'backend', '2024-08-20', 'https://udemy.com', 'ASP.NET Core ile tam kapsamli web uygulamasi gelistirme.', '2026-06-10 09:21:25'),
(4, 'Flutter ve Dart ile Mobil Gelistirme', 'Udemy', 'mobile', '2024-11-10', 'https://udemy.com', 'Cross-platform mobil uygulama gelistirme temelleri.', '2026-06-10 09:21:25'),
(5, 'SQL ve Veritabani Yonetimi', 'BTK Akademi', 'database', '2025-01-05', 'https://btkakademi.gov.tr', 'SQL sorgulama, iliskisel veritabani tasarimi ve optimizasyon.', '2026-06-10 09:21:25'),
(6, 'Git ve GitHub', 'BTK Akademi', 'tools', '2025-02-18', 'https://btkakademi.gov.tr', 'Surum kontrolu, branch yonetimi ve GitHub isbirlikleri.', '2026-06-10 09:21:25');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `messages`
--

INSERT INTO `messages` (`id`, `name`, `email`, `subject`, `message`, `is_read`, `created_at`) VALUES
(1, 'Ziyaretci Test', 'test@example.com', 'Merhaba', 'Bu bir test mesajidir.', 0, '2026-06-10 09:21:24'),
(2, 'Fatih KARA', 'fatihkara@gmail.com', 'Yeni Kullanıcı', 'fgeergresgsgsgsRG', 0, '2026-06-10 10:01:10');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `badge` varchar(80) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '',
  `category` varchar(50) NOT NULL DEFAULT 'frontend',
  `description` text NOT NULL,
  `tags` varchar(500) NOT NULL DEFAULT '',
  `github_url` varchar(500) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `projects`
--

INSERT INTO `projects` (`id`, `title`, `badge`, `image`, `category`, `description`, `tags`, `github_url`, `created_at`) VALUES
(1, 'StreamAtlas', 'Web App', 'streamatlas.png', 'frontend', 'Film ve dizi kesifetmeye yarayan modern bir streaming platformu arayuzu.', 'HTML/CSS,JavaScript,API', 'https://github.com/OlcayKAHRAMAN2005', '2026-06-10 09:21:24'),
(2, 'GSWEB', 'ASP.NET', 'GSWEB_afis.png', 'backend', 'Galatasaray taraftar platformu. ASP.NET Core MVC ile gelistirilmis icerik yonetim sistemi.', 'C#,ASP.NET,SQL Server', 'https://github.com/OlcayKAHRAMAN2005', '2026-06-10 09:21:24'),
(3, 'Portfolio Site', 'Frontend', 'frontend.png', 'frontend', 'Kisisel portfolyo web sitesi. Modern CSS, tema degistirme ve dinamik icerik yonetimi.', 'HTML,CSS,JavaScript', 'https://github.com/OlcayKAHRAMAN2005', '2026-06-10 09:21:24'),
(4, 'Mobile App', 'Flutter', 'mobile.png', 'mobile', 'Flutter ile gelistirilen cross-platform mobil uygulama. Android ve iOS icin tek kod tabani.', 'Flutter,Dart,Mobile', 'https://github.com/OlcayKAHRAMAN2005', '2026-06-10 09:21:24');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
