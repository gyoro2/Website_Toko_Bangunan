-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Nov 2025 pada 13.09
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `game_website`
--
CREATE DATABASE IF NOT EXISTS `game_website` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `game_website`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `data`
--

CREATE TABLE `data` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nim` varchar(50) NOT NULL,
  `asal_kampus` varchar(100) NOT NULL,
  `karakter_kesukaan` varchar(100) NOT NULL,
  `gambar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data`
--

INSERT INTO `data` (`id`, `nama`, `nim`, `asal_kampus`, `karakter_kesukaan`, `gambar`) VALUES
(1, 'Carlos Piero Parhusip', '121140193', 'ITERA', 'Firefly', 'Stelle And Firefly.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `form_data`
--

CREATE TABLE `form_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `data` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(7, 'carlos123', 'carlosparhusip01@gmail.com', '$2y$10$YMjp0CoHHkJjfwB/O/gTT.8JcWH6gYQpTb3pre9fywkU2jrQUjj3u', '2024-12-22 13:35:33'),
(8, 'admin000', 'admin@gmail.com', '$2y$10$0UGj30Tjk.f86HG.kWlfT.35Bl2XNy8GNRb54BZSTYclAQGW4jeqS', '2024-12-22 13:36:24');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `data`
--
ALTER TABLE `data`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `form_data`
--
ALTER TABLE `form_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `data`
--
ALTER TABLE `data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `form_data`
--
ALTER TABLE `form_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `form_data`
--
ALTER TABLE `form_data`
  ADD CONSTRAINT `form_data_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
--
-- Database: `material_db`
--
CREATE DATABASE IF NOT EXISTS `material_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `material_db`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `dp_paid_amount` decimal(10,2) NOT NULL,
  `remaining_70_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('DP_PENDING','DP_PAID','70_PENDING','70_PAID','CANCELLED') NOT NULL DEFAULT 'DP_PENDING',
  `shipping_status` enum('DRAFT','READY_SHIP','SHIPPED','ARRIVED_LOC','COMPLETED') NOT NULL DEFAULT 'DRAFT',
  `delivery_address` text NOT NULL,
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`product_id`, `name`, `sku`, `price`, `unit`, `description`, `image_url`) VALUES
(4, 'Plavon PVC LM-21 6 Meter', '1', 90000.00, '6 Meter', 'Panel PVC Laminasi dengan kode produk Lm21. Panel ini memiliki motif serat kayu yang realistis, didominasi oleh warna cokelat kemerahan atau oranye kecokelatan yang hangat dan cerah, memberikan kesan estetika kayu alami. Secara fisik, panel ini menampilkan permukaan bertekstur kayu dengan tepi berwarna putih yang berfungsi sebagai mekanisme kunci (interlocking) untuk pemasangan.', 'uploads/1763838652_LM21.jpeg'),
(7, 'Plavon PVC LM-21 4 Meter', '2', 60000.00, '4 Meter', 'Panel PVC Laminasi dengan kode produk Lm21. Panel ini memiliki motif serat kayu yang realistis, didominasi oleh warna cokelat kemerahan atau oranye kecokelatan yang hangat dan cerah, memberikan kesan estetika kayu alami. Secara fisik, panel ini menampilkan permukaan bertekstur kayu dengan tepi berwarna putih yang berfungsi sebagai mekanisme kunci (interlocking) untuk pemasangan.', 'uploads/1763838999_LM21.jpeg'),
(8, 'Plavon PVC CLM-15 6 Meter', '3', 90000.00, '6 Meter', 'Panel Plafon PVC Polos Putih (Kode CLM-15) adalah solusi modern untuk penutup plafon yang menawarkan tampilan bersih dan minimalis dengan warna putih polos. Dibuat dari PVC laminasi, material ini sangat praktis karena tahan air, anti rayap, dan tidak memerlukan pengecatan, menjadikannya pilihan yang awet dan mudah dirawat.', 'uploads/1763839015_CLM-15.jpeg'),
(9, 'Plavon PVC CLM-15 4 Meter', '4', 60000.00, '4 Meter', 'Panel Plafon PVC Polos Putih (Kode CLM-15) adalah solusi modern untuk penutup plafon yang menawarkan tampilan bersih dan minimalis dengan warna putih polos. Dibuat dari PVC laminasi, material ini sangat praktis karena tahan air, anti rayap, dan tidak memerlukan pengecatan, menjadikannya pilihan yang awet dan mudah dirawat.', 'uploads/1763839030_CLM-15.jpeg'),
(10, 'Plavon PVC LM19 6 Meter', '5', 90000.00, '6 Meter', 'Panel Plafon PVC Motif Kayu (Kode Lm19) ini menampilkan motif serat kayu yang realistis dengan dominasi warna merah kecokelatan yang pekat dan hangat, memberikan nuansa elegan dan alami pada ruangan. Sebagai alternatif kayu, panel PVC laminasi ini sangat praktis karena tahan air, anti rayap, dan bebas perawatan, ideal untuk memperindah interior hunian Anda.', 'uploads/1763839045_LM19.jpeg'),
(11, 'Plavon PVC LM19 4 Meter', '6', 60000.00, '4 Meter', 'Panel Plafon PVC Motif Kayu (Kode Lm19) ini menampilkan motif serat kayu yang realistis dengan dominasi warna merah kecokelatan yang pekat dan hangat, memberikan nuansa elegan dan alami pada ruangan. Sebagai alternatif kayu, panel PVC laminasi ini sangat praktis karena tahan air, anti rayap, dan bebas perawatan, ideal untuk memperindah interior hunian Anda.', 'uploads/1763839055_LM19.jpeg'),
(13, 'Plavon PVC Clm21 6 Meter', '7', 90000.00, '6 Meter', 'Panel Plafon PVC Motif Kayu (Kode CLM21) ini memiliki tampilan serat kayu yang cerah dan bertekstur alami dengan warna cokelat oranye kemerahan yang menawan, cocok untuk menciptakan suasana hangat dan dinamis.', 'uploads/1763880478_CLM21.jpeg'),
(14, 'Plavon PVC Clm21 4 Meter', '8', 60000.00, '4 Meter', 'Panel Plafon PVC Motif Kayu (Kode CLM21) ini memiliki tampilan serat kayu yang cerah dan bertekstur alami dengan warna cokelat oranye kemerahan yang menawan, cocok untuk menciptakan suasana hangat dan dinamis.', 'uploads/1763880645_CLM21.jpeg'),
(15, 'Plavon PVC Clm19 6 Meter', '9', 90000.00, '6 Meter', 'Panel Plafon PVC Motif Kayu (Kode CLM19) ini menonjolkan warna merah gelap (maroon) dengan kilau glossy dan serat kayu yang rapat, memberikan tampilan interior yang mewah dan berkelas', 'uploads/1763880720_CLM19.jpeg'),
(16, 'Plavon PVC Clm19 4 Meter', '10', 60000.00, '4 Meter', 'Panel Plafon PVC Motif Kayu (Kode CLM19) ini menonjolkan warna merah gelap (maroon) dengan kilau glossy dan serat kayu yang rapat, memberikan tampilan interior yang mewah dan berkelas', 'uploads/1763880819_CLM19.jpeg'),
(17, 'Plavon PVC LM-15 6 Meter', '11', 90000.00, '6 Meter', 'Panel Plafon PVC Motif Kayu (Kode Lm19) ini menampilkan motif serat kayu yang realistis dengan dominasi warna merah kecokelatan yang pekat dan hangat, memberikan nuansa elegan dan alami pada ruangan. Sebagai alternatif kayu, panel PVC laminasi ini sangat praktis karena tahan air, anti rayap, dan bebas perawatan, ideal untuk memperindah interior hunian Anda.', 'uploads/1763881005_LM-15.jpeg'),
(18, 'Plavon PVC LM-15 4 Meter', '12', 60000.00, '4 Meter', 'Panel Plafon PVC Motif Kayu (Kode Lm19) ini menampilkan motif serat kayu yang realistis dengan dominasi warna merah kecokelatan yang pekat dan hangat, memberikan nuansa elegan dan alami pada ruangan. Sebagai alternatif kayu, panel PVC laminasi ini sangat praktis karena tahan air, anti rayap, dan bebas perawatan, ideal untuk memperindah interior hunian Anda.', 'uploads/1763881038_LM-15.jpeg'),
(19, 'Plavon PVC LM-17 6 Meter', '13', 90000.00, '6 Meter', 'Panel Plafon PVC Motif Kayu (Kode LM-17) ini menampilkan motif serat kayu yang tebal dan bertekstur kuat, didominasi warna cokelat kemerahan (meranti/jati) dengan kontras serat gelap, menciptakan kesan rustic yang hangat.', 'uploads/1763881141_LM-17.jpeg'),
(20, 'Plavon PVC LM-17 4 Meter', '14', 60000.00, '4 Meter', 'Panel Plafon PVC Motif Kayu (Kode LM-17) ini menampilkan motif serat kayu yang tebal dan bertekstur kuat, didominasi warna cokelat kemerahan (meranti/jati) dengan kontras serat gelap, menciptakan kesan rustic yang hangat.', 'uploads/1763881171_LM-17.jpeg'),
(21, 'Plavon PVC Glossy T1 6 Meter', '15', 72000.00, '6 Meter', 'Panel Plafon PVC Motif Kayu Cokelat ini menampilkan serat kayu yang menawan dengan profil alur tengah (nat) berwarna keemasan, memberikan tampilan plafon yang mewah dan berdimensi.', 'uploads/1763882626_WhatsApp Image 2025-11-21 at 17.07.44.jpeg'),
(22, 'Plavon PVC Glossy T1 4 Meter', '16', 48000.00, '4 Meter', 'Panel Plafon PVC Motif Kayu Cokelat Tua ini menampilkan serat kayu yang menawan dengan profil alur tengah (nat) berwarna keemasan, memberikan tampilan plafon yang mewah dan berdimensi.', 'uploads/1763882681_WhatsApp Image 2025-11-21 at 17.07.44.jpeg'),
(23, 'Plavon PVC Glossy T2 6 Meter', '17', 72000.00, '6 Meter', 'Panel Plafon PVC Motif Kayu dengan Nat Emas ini memiliki motif serat kayu berwarna cokelat kemerahan (mahogany) yang elegan, diperindah dengan dua garis nat (alur) berwarna kuning keemasan yang tegas di tengah.', 'uploads/1763882805_WhatsApp Image 2025-11-21 at 17.07.44 (1).jpeg'),
(24, 'Plavon PVC Glossy T2 4 Meter', '18', 48000.00, '4 Meter', 'Panel Plafon PVC Motif Kayu dengan Nat Emas ini memiliki motif serat kayu berwarna cokelat kemerahan (mahogany) yang elegan, diperindah dengan dua garis nat (alur) berwarna kuning keemasan yang tegas di tengah.', 'uploads/1763882866_WhatsApp Image 2025-11-21 at 17.07.44 (1).jpeg'),
(25, 'Plavon PVC Glossy SP-7706 6 Meter', '19', 72000.00, '6 Meter', 'Panel Plafon PVC Motif Kayu Putih / Abu-abu Muda (Kode SP-7706) ini menampilkan motif wood grain yang cerah dengan dominasi warna putih keabuan (light grey wood), memberikan kesan ruangan yang modern, lapang, dan Skandinavia.', 'uploads/1763882996_SP-7706.jpeg'),
(26, 'Plavon PVC Glossy SP-7706 4 Meter', '20', 48000.00, '4 Meter', 'Panel Plafon PVC Motif Kayu Putih / Abu-abu Muda (Kode SP-7706) ini menampilkan motif wood grain yang cerah dengan dominasi warna putih keabuan (light grey wood), memberikan kesan ruangan yang modern, lapang, dan Skandinavia.', 'uploads/1763883031_SP-7706.jpeg'),
(27, 'Plavon PVC Glossy T3 6 Meter', '21', 72000.00, '6 Meter', 'Panel Plafon PVC Motif Kayu Putih Pucat dengan Alur Chrome ini memiliki motif wood grain yang sangat tipis dan dominan warna putih transparan/pucat dengan efek mengilap (glossy), ideal untuk desain ruangan yang sangat terang dan modern.', 'uploads/1763883282_WhatsApp Image 2025-11-21 at 17.07.44 (2).jpeg'),
(28, 'Plavon PVC Glossy T3 4 Meter', '22', 48000.00, '4 Meter', 'Panel Plafon PVC Motif Kayu Putih Pucat dengan Alur Chrome ini memiliki motif wood grain yang sangat tipis dan dominan warna putih transparan/pucat dengan efek mengilap (glossy), ideal untuk desain ruangan yang sangat terang dan modern.', 'uploads/1763883319_WhatsApp Image 2025-11-21 at 17.07.44 (2).jpeg'),
(29, 'Plavon PVC Glossy SP-7701  6 Meter', '23', 72000.00, '6 Meter', 'Panel Plafon PVC Polos Putih Gading dengan Nat Emas (Kode SP-7701) ini menampilkan permukaan putih gading atau off-white polos yang mengilap (glossy), diperindah dengan dua garis nat (alur) berwarna emas yang memberikan sentuhan elegan dan mewah.', 'uploads/1763883467_SP-7701.jpeg'),
(30, 'Plavon PVC Glossy SP-7701 4 Meter', '24', 48000.00, '4 Meter', 'Panel Plafon PVC Polos Putih Gading dengan Nat Emas (Kode SP-7701) ini menampilkan permukaan putih gading atau off-white polos yang mengilap (glossy), diperindah dengan dua garis nat (alur) berwarna emas yang memberikan sentuhan elegan dan mewah.', 'uploads/1763883509_SP-7701.jpeg'),
(31, 'Plavon PVC Glossy SP-8016B 6 Meter', '25', 72000.00, '6 Meter', 'Panel Plafon PVC Motif Kombinasi Floral dan Garis (Kode SP-8016B) ini menawarkan desain unik yang memadukan permukaan polos putih dengan motif ukiran floral di satu sisi, dan sisi lainnya menampilkan garis-garis vertikal hitam/biru gelap dan chrome, menciptakan kesan sangat modern dan dekoratif.', 'uploads/1763883603_SP-8016B.jpeg'),
(32, 'Plavon PVC Glossy SP-8016B 4 Meter', '26', 48000.00, '4 Meter', 'Panel Plafon PVC Motif Kombinasi Floral dan Garis (Kode SP-8016B) ini menawarkan desain unik yang memadukan permukaan polos putih dengan motif ukiran floral di satu sisi, dan sisi lainnya menampilkan garis-garis vertikal hitam/biru gelap dan chrome, menciptakan kesan sangat modern dan dekoratif.', 'uploads/1763883707_SP-8016B.jpeg'),
(33, 'Plavon PVC Glossy SP-7714 6 Meter', '27', 72000.00, '6 Meter', 'Panel Plafon PVC Motif Kayu Cokelat dengan Nat Emas (Kode SP-7714) ini menampilkan serat kayu berwarna cokelat sedang/tua yang hangat dengan lapisan glossy (mengilap), diperindah dengan dua nat (alur) berwarna kuning emas yang elegan dan kontras.', 'uploads/1763884857_SP-7714.jpeg'),
(34, 'Plavon PVC Glossy SP-7714 4 Meter', '28', 48000.00, '4 Meter', 'Panel Plafon PVC Motif Kayu Cokelat dengan Nat Emas (Kode SP-7714) ini menampilkan serat kayu berwarna cokelat sedang/tua yang hangat dengan lapisan glossy (mengilap), diperindah dengan dua nat (alur) berwarna kuning emas yang elegan dan kontras.', 'uploads/1763884890_SP-7714.jpeg'),
(35, 'Plavon PVC Glossy T4 6 Meter', '29', 72000.00, '6 Meter', 'Panel Plafon PVC Motif Kayu Polos Tanpa Nat ini menampilkan permukaan motif kayu dengan serat yang halus, didominasi warna cokelat oranye kemerahan yang cerah dan mengilap (glossy), memberikan kesan hangat pada ruangan.', 'uploads/1763884999_WhatsApp Image 2025-11-21 at 17.07.44 (3).jpeg'),
(36, 'Plavon PVC Glossy T4 4 Meter', '30', 48000.00, '4 Meter', 'Panel Plafon PVC Motif Kayu Polos Tanpa Nat ini menampilkan permukaan motif kayu dengan serat yang halus, didominasi warna cokelat oranye kemerahan yang cerah dan mengilap (glossy), memberikan kesan hangat pada ruangan.', 'uploads/1763885186_WhatsApp Image 2025-11-21 at 17.07.44 (3).jpeg'),
(37, 'Plavon PVC Glossy T5 6 Meter', '31', 72000.00, '6 Meter', 'Panel Plafon PVC Motif Kayu Putih Pucat dengan Nat Chrome (Kode SP-7707) ini menampilkan motif serat kayu yang tipis dan menawan dengan warna putih pudar/krem muda yang mengilap, sangat ideal untuk menciptakan kesan ruangan yang terang dan luas.', 'uploads/1763885309_WhatsApp Image 2025-11-21 at 17.07.44 (4).jpeg'),
(38, 'Plavon PVC Glossy T5 4 Meter', '32', 48000.00, '4 Meter', 'Panel Plafon PVC Motif Kayu Putih Pucat dengan Nat Chrome (Kode SP-7707) ini menampilkan motif serat kayu yang tipis dan menawan dengan warna putih pudar/krem muda yang mengilap, sangat ideal untuk menciptakan kesan ruangan yang terang dan luas.', 'uploads/1763885338_WhatsApp Image 2025-11-21 at 17.07.44 (4).jpeg'),
(39, 'Plavon PVC Glossy T6 6 Meter', '33', 72000.00, '6 Meter', 'Panel Plafon PVC Motif Kombinasi Floral dan Garis (Merek Rona) ini menampilkan desain dekoratif mewah, memadukan bagian motif ukiran bunga (floral) timbul berwarna putih pudar dan bagian garis vertikal cokelat/hitam pekat yang memberikan kontras modern.', 'uploads/1763886302_WhatsApp Image 2025-11-21 at 17.07.44 (5).jpeg'),
(40, 'Plavon PVC Glossy T6 4 Meter', '34', 48000.00, '4 Meter', 'Panel Plafon PVC Motif Kombinasi Floral dan Garis (Merek Rona) ini menampilkan desain dekoratif mewah, memadukan bagian motif ukiran bunga (floral) timbul berwarna putih pudar dan bagian garis vertikal cokelat/hitam pekat yang memberikan kontras modern.', 'uploads/1763886326_WhatsApp Image 2025-11-21 at 17.07.44 (5).jpeg'),
(41, 'Wall Board Putih 40 cm x 290 cm', '35', 150000.00, 'Lembar', 'Panel Dinding PVC Motif Kayu Putih Polos ini memiliki permukaan berwarna putih salju dengan hint serat kayu yang sangat halus dan mengilap (glossy), ideal untuk menciptakan dinding aksen yang terang, bersih, dan modern. Panel PVC ini adalah solusi Wall Board yang praktis karena tahan lembab, anti rayap, dan minim perawatan, memberikan tampilan dinding yang rapi tanpa perlu finishing tambahan.', 'uploads/1763886616_WhatsApp Image 2025-11-21 at 17.10.49.jpeg'),
(42, 'Wall Board Kain Abu-Abu Muda 40 cm x 290 cm', '36', 150000.00, 'Lembar', 'Panel Dinding PVC Motif Kain Abu-abu Muda ini menampilkan tekstur unik menyerupai serat linen atau kain tenun dengan warna abu-abu muda netral yang bening (light grey), memberikan kesan elegan dan minimalis. Panel ini ideal untuk dinding interior karena tahan lembab, anti rayap, dan minim perawatan, menciptakan wall feature dengan sentuhan tekstil tanpa kerumitan.', 'uploads/1763886806_Wall Board Abu Bening.jpeg'),
(43, 'Wall Board Polos Abu-Abu Gelap Muda 40 cm x 290 cm', '37', 150000.00, 'Lembar', 'Panel Dinding PVC Polos Abu-abu Gelap (Solid Grey) ini menampilkan permukaan polos dengan warna abu-abu slate atau dark taupe yang pekat dan modern, memberikan kesan elegan, minimalis, dan industrial. Panel PVC ini adalah solusi Wall Board yang fungsional karena tahan lembab, anti rayap, dan minim perawatan, ideal untuk menciptakan dinding aksen yang matte dan kontras.', 'uploads/1763886938_Wall Board Abu Gelap.jpeg'),
(44, 'Wall Board Marmer Puti Carrara 40 cm x 290 cm', '38', 150000.00, 'Lembar', 'Panel Dinding PVC Motif Marmer Putih Carrara ini menampilkan permukaan putih bersih yang mengilap (high gloss), diperindah dengan pola urat (vein) marmer berwarna abu-abu gelap yang khas dan mewah. Panel PVC ini adalah solusi Wall Board yang tahan air dan anti rayap, memberikan tampilan dinding atau backsplash yang elegan menyerupai batu marmer asli dengan harga yang lebih terjangkau dan bebas perawatan.', 'uploads/1763887036_Marmer Putih Carrara.jpeg'),
(47, 'WPC Coklat Merah 16 cm x 295 cm', '39', 120000.00, 'Lembar', 'Wall Panel WPC/PVC Motif Kayu 3D Cokelat Merah ini memiliki profil vertikal bergelombang (fluted) atau slat yang menciptakan efek 3D berlekuk yang dramatis. Panel dekoratif ini berwarna cokelat kemerahan pekat dengan serat kayu halus dan sangat ideal untuk dinding aksen karena kokoh, tahan lembab, anti rayap, dan minim perawatan.', 'uploads/1763887857_WPC Coklat Merah.jpeg'),
(48, 'Wall Panel Kayu Kenari 87001-1 30 cm x 295 cm', '40', 135000.00, 'Lembar', 'Wall Panel PVC/WPC Slat Motif Kayu Kenari (Kode 87001-1) ini memiliki profil rata yang menciptakan ilusi panel bilah kayu vertikal (slat panel) dengan alur berwarna hitam, memberikan tampilan modern, minimalis, dan akustik. Panel ini memiliki motif kayu berwarna cokelat sedang/tua (walnut), menjadikannya pilihan ideal untuk dinding aksen yang tahan lembab, anti rayap, dan bebas perawatan.', 'uploads/1763887975_Wall Panel 87001-1.jpeg'),
(49, 'Panel Dinding PVC Motif Marmer Hitam Garis Emas 8231 120 cm x 295 cm', '41', 600000.00, 'Lembar', 'Panel Dinding PVC Motif Marmer Hitam Garis Emas (Kode 8231) ini menampilkan permukaan high glossy dengan warna dasar hitam pekat yang mewah, dihiasi urat (vein) marmer tipis berwarna emas dan putih yang dramatis. Panel PVC ini merupakan pilihan premium untuk dinding aksen atau countertop (atap meja) karena tahan air, anti rayap, dan minim perawatan, memberikan tampilan elegan menyerupai marmer Nero Portoro.', 'uploads/1763888168_Panel Dinding PVC Motif Marmer Hitam Garis Emas 8231.jpeg'),
(50, 'Panel Dinding PVC Motif Marmer Abu-abu Campur 8041-B 120 cm x 295 cm', '42', 600000.00, 'Lembar', 'Panel Dinding PVC Motif Marmer Abu-abu Campur (Kode 8041-B) ini menampilkan pola marmer yang kompleks dan artistik, memadukan urat tebal berwarna putih, abu-abu gelap, dan hitam yang kontras dengan aksen keemasan. Panel PVC high glossy ini adalah pilihan Wall Board yang sangat dekoratif karena tahan air dan anti rayap, cocok untuk menciptakan dinding aksen mewah dengan tampilan batu alam yang dramatis.', 'uploads/1763888633_8041-B.jpeg'),
(51, 'Panel Dinding PVC Motif Marmer Putih Calacatta 8230 120 cm x 295 cm', '43', 600000.00, 'Lembar', 'Panel Dinding PVC Motif Marmer Putih Calacatta (Kode 8230) ini memiliki permukaan high glossy berwarna putih murni, diperindah dengan urat (vein) marmer yang elegan dan menyebar berwarna abu-abu muda, meniru tampilan marmer Calacatta. Panel PVC ini adalah pilihan modern untuk dinding atau backsplash karena tahan air, anti rayap, dan minim perawatan, memberikan kesan ruangan yang bersih, terang, dan mewah.', 'uploads/1763888893_8230.jpeg'),
(52, 'Panel Dinding PVC Motif Marmer Putih Urat Emas 80006T 120 cm x 295 cm', '44', 600000.00, 'Lembar', 'Panel Dinding PVC Motif Marmer Putih Urat Emas (Kode 80006T) ini menampilkan permukaan high glossy berwarna putih cemerlang, diperindah dengan urat (vein) marmer yang dramatis berwarna abu-abu dan aksen emas metalik yang mewah. Panel PVC ini adalah pilihan ideal untuk dinding aksen atau backsplash karena tahan air, anti rayap, dan minim perawatan, memberikan tampilan ruangan yang sangat elegan dan berkelas.', 'uploads/1763889064_80006T.jpeg'),
(53, 'Panel Dinding PVC Motif Marmer Putih Urat Abu-abu Tebal 8834 120 cm x 295 cm', '45', 600000.00, 'Lembar', 'Panel Dinding PVC Motif Marmer Putih Urat Abu-abu Tebal (Kode 8834) ini menampilkan permukaan high glossy berwarna putih cerah dengan urat marmer yang tebal dan menyebar berwarna abu-abu kebiruan, memberikan kesan visual yang kuat dan mewah. Panel PVC ini adalah solusi modern untuk dinding aksen atau backsplash karena tahan air, anti rayap, dan minim perawatan, menghadirkan nuansa batu alam yang dramatis.', 'uploads/1763889180_8834.jpeg'),
(54, 'Panel Dinding PVC Motif Marmer Hitam Urat Emas/Pelangi 8107 120 cm x 295 cm', '46', 600000.00, 'Lembar', 'Panel Dinding PVC Motif Marmer Hitam Urat Emas/Pelangi (Kode 8167) ini menampilkan permukaan high glossy berwarna hitam pekat yang mewah, dihiasi urat (vein) halus berwarna emas metalik dan sentuhan iridescent (pelangi/berkilau) yang menciptakan efek dramatis. Panel PVC ini adalah pilihan premium untuk dinding aksen atau countertop karena tahan air, anti rayap, dan minim perawatan, memberikan tampilan ruangan yang sangat elegan dan eksklusif.', 'uploads/1763889454_8107.jpeg'),
(55, 'Panel Dinding PVC Motif Marmer Dramatis Biru-Abu 3087-7B 120 cm x 295 cm', '47', 600000.00, 'Lembar', 'Panel Dinding PVC Motif Marmer Dramatis Biru-Abu (Kode 3087-7B) ini menampilkan pola marmer yang sangat unik dengan campuran urat tebal berwarna biru keabu-abuan, hitam, dan putih yang menyapu diagonal, menciptakan kesan abstrak yang mewah. Panel PVC high glossy ini adalah pilihan Wall Board yang sangat statement karena tahan air dan anti rayap, ideal untuk menciptakan dinding aksen yang modern dan berani.', 'uploads/1763889424_3087-7B.jpeg'),
(56, 'Panel Dinding PVC Motif Marmer Putih Urat Hitam Dramatis 3076-A 120 cm x 295 cm', '48', 600000.00, 'Lembar', 'Panel Dinding PVC Motif Marmer Putih Urat Hitam Dramatis (Kode 3076-A) ini menampilkan pola marmer dengan urat hitam pekat dan abu-abu yang tebal, menyebar secara acak di atas dasar putih cemerlang yang mengilap (high glossy). Panel PVC ini adalah pilihan Wall Board yang elegan dan kontras karena tahan air, anti rayap, dan minim perawatan, ideal untuk menciptakan dinding aksen yang kuat.', 'uploads/1763889936_3076-A.jpeg'),
(58, 'Panel Dinding PVC Motif Marmer Putih Urat Hitam dan Emas 8267 120 cm x 295 cm', '49', 600000.00, 'Lembar', 'Panel Dinding PVC Motif Marmer Putih Urat Hitam dan Emas (Kode 8267) ini menampilkan pola marmer yang sangat mewah dengan urat tebal berwarna hitam, abu-abu, dan aksen emas metalik yang menyebar di atas dasar putih cemerlang. Panel PVC high glossy ini adalah pilihan Wall Board premium yang tahan air dan anti rayap, cocok untuk menciptakan dinding aksen yang eksklusif dan mencolok.', 'uploads/1763899031_8267.jpeg'),
(59, 'Lis PVC Siku Motif Kayu C-02 4 Meter', '50', 40000.00, '4 Meter', 'Lis PVC Siku Motif Kayu (Kode C-02) ini adalah profil list finishing sudut berbentuk siku (L-shape) yang digunakan untuk menutupi sambungan tepi Wall Board atau Plafon PVC. Lis ini memiliki motif kayu berwarna cokelat sedang yang hangat dan berfungsi sebagai pelengkap dekoratif yang tahan air, anti rayap, dan minim perawatan.', 'uploads/1763899665_C-02.jpeg'),
(60, 'Lis PVC Siku Motif Kayu C-01 4 Meter', '51', 40000.00, '4 Meter', 'Lis PVC Siku Putih (Kode C-01) ini adalah profil list finishing sudut berbentuk siku (L-shape) yang digunakan untuk menutupi sambungan atau tepi panel Plafon/Wall Board PVC Anda. Lis ini memiliki warna putih polos yang netral dan berfungsi sebagai pelengkap dekoratif yang tahan air, anti rayap, dan minim perawatan, ideal untuk sentuhan akhir yang bersih.', 'uploads/1763899725_C-01.jpeg'),
(61, 'Lis PVC Siku Motif Kayu B-01 4 Meter', '52', 40000.00, '4 Meter', 'Lis PVC Sudut Luar Putih (Kode B-01) ini adalah profil list finishing berbentuk sudut yang digunakan khusus untuk menutupi sambungan atau sudut luar (cembung) pada pemasangan Plafon atau Wall Board PVC. Lis ini memiliki warna putih polos yang bersih, ideal untuk memberikan sentuhan akhir yang rapi, tahan air, dan anti rayap pada proyek interior Anda.', 'uploads/1763899791_B-01.jpeg'),
(62, 'Lis PVC Siku Motif Kayu B-02 4 Meter', '53', 40000.00, '4 Meter', 'Lis PVC Sudut Luar Motif Kayu (Kode B-02) ini adalah profil list finishing berbentuk sudut yang digunakan khusus untuk menutupi sambungan atau sudut luar (cembung) pada pemasangan Plafon atau Wall Board PVC. Lis ini memiliki motif kayu berwarna cokelat sedang/tua yang serasi dengan panel motif kayu, berfungsi memberikan sentuhan akhir yang rapi, tahan air, dan anti rayap.', 'uploads/1763899852_B-02.jpeg'),
(63, 'Wall Board PVC Motif Kayu Muda Cokelat WB 016)', '54', 150000.00, 'Lembar', 'Wall Board PVC Motif Kayu Muda Cokelat (Kode WB 016) ini menampilkan motif serat kayu yang halus dengan dominasi warna cokelat muda kecokelatan yang cerah dan alami, sangat ideal untuk menciptakan kesan ruangan yang terang dan hangat. Panel PVC ini adalah solusi Wall Board yang tahan air, anti rayap, dan minim perawatan, memberikan tampilan dinding yang rapi dan elegan tanpa perlu finishing tambahan.', 'uploads/1763900035_WB 016.jpeg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `shop_settings`
--

CREATE TABLE `shop_settings` (
  `id` int(11) NOT NULL,
  `shop_latitude` decimal(10,8) NOT NULL,
  `shop_longitude` decimal(11,8) NOT NULL,
  `price_per_km` decimal(10,2) NOT NULL,
  `min_km` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `shop_settings`
--

INSERT INTO `shop_settings` (`id`, `shop_latitude`, `shop_longitude`, `price_per_km`, `min_km`) VALUES
(1, -6.38423335, 106.85251730, 10000.00, 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `stock`
--

CREATE TABLE `stock` (
  `stock_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_available` int(11) NOT NULL DEFAULT 0,
  `quantity_locked` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `stock`
--

INSERT INTO `stock` (`stock_id`, `product_id`, `quantity_available`, `quantity_locked`) VALUES
(4, 4, 1000, 0),
(5, 7, 1000, 0),
(6, 8, 1000, 0),
(7, 9, 1000, 0),
(8, 10, 1000, 0),
(9, 11, 1000, 0),
(10, 13, 1000, 0),
(11, 14, 1000, 0),
(12, 15, 1000, 0),
(13, 16, 1000, 0),
(14, 17, 1000, 0),
(15, 18, 1000, 0),
(16, 19, 1000, 0),
(17, 20, 1000, 0),
(18, 21, 1000, 0),
(19, 22, 1000, 0),
(20, 23, 1000, 0),
(21, 24, 1000, 0),
(22, 25, 1000, 0),
(23, 26, 1000, 0),
(24, 27, 1000, 0),
(25, 28, 1000, 0),
(26, 29, 1000, 0),
(27, 30, 1000, 0),
(28, 31, 1000, 0),
(29, 32, 1000, 0),
(30, 33, 1000, 0),
(31, 34, 1000, 0),
(32, 35, 1000, 0),
(33, 36, 1000, 0),
(34, 37, 1000, 0),
(35, 38, 1000, 0),
(36, 39, 1000, 0),
(37, 40, 1000, 0),
(38, 41, 1000, 0),
(39, 42, 1000, 0),
(40, 43, 1000, 0),
(41, 44, 1000, 0),
(42, 47, 1000, 0),
(43, 48, 1000, 0),
(44, 49, 1000, 0),
(45, 50, 1000, 0),
(46, 51, 1000, 0),
(47, 52, 1000, 0),
(48, 53, 1000, 0),
(49, 54, 1000, 0),
(50, 55, 1000, 0),
(51, 56, 1000, 0),
(52, 58, 1000, 0),
(53, 59, 1000, 0),
(54, 60, 1000, 0),
(55, 61, 1000, 0),
(56, 62, 1000, 0),
(57, 63, 1000, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('customer','admin','logistics') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `phone`, `password_hash`, `role`, `created_at`) VALUES
(5, 'Admin', 'carlosparhusip01@gmail.com', '0895410035090', '$2y$10$Yl7PtD9TTf6KvA2sdCS0ZerF1C9pnmOVp4eYTzn88FAi7Ovr5lpim', 'admin', '2025-11-21 07:12:44'),
(9, 'Carlos Piero Parhusip', 'carlos@gmail.com', '0895410035090', '$2y$10$hE49Tq.PmDjInSjL9OoSSeEWPm7vr5F69qw2pFg6W4sQwwYE1MhX.', 'customer', '2025-11-21 07:16:27');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Indeks untuk tabel `shop_settings`
--
ALTER TABLE `shop_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`stock_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT untuk tabel `stock`
--
ALTER TABLE `stock`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ketidakleluasaan untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Ketidakleluasaan untuk tabel `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
--
-- Database: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Dumping data untuk tabel `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2019-10-21 13:37:09', '{\"Console\\/Mode\":\"collapse\"}');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Struktur dari tabel `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indeks untuk tabel `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indeks untuk tabel `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indeks untuk tabel `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indeks untuk tabel `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indeks untuk tabel `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indeks untuk tabel `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indeks untuk tabel `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indeks untuk tabel `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indeks untuk tabel `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indeks untuk tabel `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indeks untuk tabel `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indeks untuk tabel `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indeks untuk tabel `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indeks untuk tabel `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indeks untuk tabel `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indeks untuk tabel `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indeks untuk tabel `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Database: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
