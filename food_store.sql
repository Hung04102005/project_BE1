-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 25, 2024 lúc 04:36 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `food_store`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(24, 0, 29, 4, '2024-12-21 10:01:31'),
(27, 0, 4, 1, '2024-12-25 01:53:17'),
(28, 0, 3, 1, '2024-12-25 02:15:19'),
(52, 0, 5, 2, '2024-12-25 12:52:15');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `config`
--

CREATE TABLE `config` (
  `name` varchar(255) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `config`
--

INSERT INTO `config` (`name`, `value`) VALUES
('file_upload_limit', '2'),
('logo', 'default_logo.png'),
('slider_images', 'slider1.jpg,slider3.jpg,slider4.jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `created_at`) VALUES
(1, 7, 135000.00, 'pending', '2024-12-18 17:56:28'),
(2, 7, 600000.00, 'pending', '2024-12-18 17:58:59'),
(3, 7, 360000.00, 'pending', '2024-12-18 18:00:51'),
(4, 7, 1420000.00, 'pending', '2024-12-18 18:15:01'),
(5, 7, 110000.00, 'pending', '2024-12-18 18:25:29'),
(6, 7, 120000.00, 'pending', '2024-12-18 19:03:55'),
(7, 6, 220000.00, 'pending', '2024-12-18 23:17:37'),
(8, 6, 55000.00, 'pending', '2024-12-21 10:02:59'),
(9, 6, 55000.00, 'pending', '2024-12-25 01:46:04'),
(10, 6, 715000.00, 'pending', '2024-12-25 08:06:43'),
(11, 6, 370000.00, 'pending', '2024-12-25 08:09:43'),
(12, 6, 165000.00, 'pending', '2024-12-25 08:10:37'),
(13, 6, 0.00, 'pending', '2024-12-25 08:10:43'),
(14, 6, 165000.00, 'pending', '2024-12-25 08:11:16'),
(15, 6, 165000.00, 'pending', '2024-12-25 08:11:38'),
(16, 6, 330000.00, 'pending', '2024-12-25 08:14:03'),
(17, 6, 0.00, 'pending', '2024-12-25 08:14:07'),
(21, 6, 165000.00, 'pending', '2024-12-25 08:18:16'),
(22, 6, 0.00, 'pending', '2024-12-25 08:18:18'),
(23, 6, 165000.00, 'pending', '2024-12-25 08:18:43'),
(24, 6, 65000.00, 'pending', '2024-12-25 08:21:15'),
(25, 6, 0.00, 'pending', '2024-12-25 08:22:01'),
(26, 6, 165000.00, 'pending', '2024-12-25 08:22:12'),
(27, 6, 0.00, 'pending', '2024-12-25 08:22:15'),
(28, 6, 165000.00, 'pending', '2024-12-25 08:25:14'),
(29, 6, 299000.00, 'pending', '2024-12-25 08:26:46'),
(30, 6, 165000.00, 'pending', '2024-12-25 08:31:51'),
(31, 6, 165000.00, 'pending', '2024-12-25 08:33:32'),
(32, 6, 165000.00, 'pending', '2024-12-25 08:49:51'),
(33, 6, 120000.00, 'pending', '2024-12-25 09:45:46'),
(34, 6, 335000.00, 'pending', '2024-12-25 13:05:37'),
(35, 6, 300000.00, 'pending', '2024-12-25 15:19:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 7, 3, 45000.00),
(2, 2, 3, 5, 120000.00),
(3, 3, 3, 3, 120000.00),
(4, 4, 4, 9, 55000.00),
(5, 4, 10, 5, 185000.00),
(6, 5, 4, 2, 55000.00),
(7, 6, 3, 1, 120000.00),
(8, 7, 4, 4, 55000.00),
(9, 8, 4, 1, 55000.00),
(10, 9, 4, 1, 55000.00),
(11, 10, 1, 11, 65000.00),
(12, 11, 10, 2, 185000.00),
(13, 12, 5, 1, 165000.00),
(14, 14, 5, 1, 165000.00),
(15, 15, 5, 1, 165000.00),
(16, 16, 5, 2, 165000.00),
(20, 21, 5, 1, 165000.00),
(21, 23, 5, 1, 165000.00),
(22, 24, 1, 1, 65000.00),
(23, 26, 5, 1, 165000.00),
(24, 28, 5, 1, 165000.00),
(25, 29, 5, 1, 165000.00),
(26, 29, 21, 1, 45000.00),
(27, 29, 8, 1, 89000.00),
(28, 30, 5, 1, 165000.00),
(29, 31, 5, 1, 165000.00),
(30, 32, 5, 1, 165000.00),
(31, 33, 3, 1, 120000.00),
(32, 34, 6, 2, 75000.00),
(33, 34, 10, 1, 185000.00),
(34, 35, 24, 2, 150000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_featured` tinyint(1) DEFAULT 0,
  `is_on_sale` tinyint(1) DEFAULT 0,
  `is_new` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `old_price`, `image`, `category`, `rating`, `created_at`, `is_featured`, `is_on_sale`, `is_new`) VALUES
(1, 'Phở Bò Đặc Biệt', 'Phở bò truyền thống với thịt bò tươi, bánh phở mềm và nước dùng đậm đà', 65000.00, NULL, 'pho-bo.jpg', 'Món Việt', 4.8, '2024-12-14 06:19:04', 1, 0, 0),
(3, 'Gà Nướng Phô Mai', 'Gà nướng phủ phô mai tan chảy với sốt đặc biệt', 120000.00, NULL, 'cheese-chicken.jpg', 'Món Âu', 4.5, '2024-12-14 06:19:04', 0, 0, 0),
(4, 'Bún Chả Hà Nội', 'Bún chả truyền thống với thịt nướng, chả viên và nước mắm chua ngọt', 55000.00, NULL, 'bun-cha.jpg', 'Món Việt', 4.9, '2024-12-14 06:19:04', 0, 0, 1),
(5, 'Pizza Hải Sản', 'Pizza với hải sản tươi, phô mai Mozzarella và sốt cà chua', 165000.00, NULL, 'seafood-pizza.jpg', 'Món Âu', 4.6, '2024-12-14 06:19:04', 1, 0, 0),
(6, 'Mì Xào Hải Sản', 'Mì xào với tôm, mực và rau củ tươi', 75000.00, 85000.00, 'seafood-noodles.jpg', 'Món Á', 4.4, '2024-12-14 06:19:04', 0, 1, 0),
(7, 'Cơm Gà Xối Mỡ', 'Cơm gà với da gà giòn, ăn kèm kim chi và nước mắm', 45000.00, NULL, 'chicken-rice.jpg', 'Món Việt', 4.3, '2024-12-14 06:19:04', 1, 0, 0),
(8, 'Hamburger Bò', 'Burger bò với phô mai, rau sống và sốt đặc biệt', 89000.00, 120000.00, 'beef-burger.jpg', 'Món Âu', 4.5, '2024-12-14 06:19:04', 0, 1, 0),
(10, 'Bánh Mì Thịt Nướng', 'Bánh mì giòn với thịt nướng, paté và rau sống', 185000.00, 250000.00, 'banh-mi.jpg', 'Món Việt', 4.6, '2024-12-14 06:19:04', 0, 1, 0),
(20, 'Bún Bò Huế', 'Bún bò Huế cay nồng với sả, ớt, thịt bò và chả cua thơm ngon', 55000.00, NULL, 'bun-bo-hue.jpg', 'Món Việt', 4.7, '2024-12-14 06:19:04', 0, 0, 1),
(21, 'Cơm Gà Hải Nam', 'Cơm gà hấp thơm ngon kèm nước chấm gừng đặc biệt', 45000.00, NULL, 'com-ga-hai-nam.jpg', 'Món Á', 4.5, '2024-12-14 06:20:04', 0, 0, 1),
(22, 'Mì Xào Hải Sản', 'Mì trứng xào với tôm, mực, cải xanh và nấm tươi', 65000.00, NULL, 'mi-xao-hai-san.jpg', 'Món Á', 4.6, '2024-12-14 06:21:04', 0, 0, 0),
(23, 'Sushi Cá Hồi', 'Cơm sushi cuộn cá hồi tươi Na Uy và bơ', 85000.00, NULL, 'sushi-ca-hoi.jpg', 'Món Nhật', 4.8, '2024-12-14 06:22:04', 0, 0, 0),
(24, 'Gà Nướng Muối Ớt', 'Gà nướng với muối ớt đặc biệt, da giòn thịt mềm', 150000.00, NULL, 'ga-nuong-muoi-ot.jpg', 'Món Việt', 4.7, '2024-12-14 06:23:04', 0, 0, 0),
(25, 'Lẩu Thái Hải Sản', 'Lẩu Thái chua cay với hải sản tươi sống đa dạng', 250000.00, NULL, 'lau-thai-hai-san.jpg', 'Món Thái', 4.9, '2024-12-14 06:24:04', 0, 0, 0),
(26, 'Cơm Rang Dương Châu', 'Cơm rang với thịt xá xíu, tôm khô và trứng', 60000.00, NULL, 'com-rang-duong-chau.jpg', 'Món Á', 4.5, '2024-12-14 06:25:04', 0, 0, 0),
(27, 'Bánh Xèo Miền Trung', 'Bánh xèo giòn với nhân tôm thịt và rau sống', 70000.00, NULL, 'banh-xeo.jpg', 'Món Việt', 4.6, '2024-12-14 06:26:04', 0, 0, 0),
(28, 'Cá Hồi Nướng Teriyaki', 'Cá hồi nướng sốt teriyaki kèm cơm trắng và rau', 120000.00, NULL, 'ca-hoi-nuong.jpg', 'Món Nhật', 4.8, '2024-12-14 06:27:04', 0, 0, 0),
(29, 'Bò Lúc Lắc', 'Bò xào lúc lắc với ớt chuông và hành tây', 95000.00, NULL, 'bo-luc-lac.jpg', 'Món Việt', 4.7, '2024-12-14 06:28:04', 0, 0, 0),
(30, 'Pad Thai', 'Phở xào Thái với tôm, đậu phộng và giá đỗ', 75000.00, NULL, 'pad-thai.jpg', 'Món Thái', 4.6, '2024-12-14 06:29:04', 0, 0, 1),
(31, 'Cơm Niêu Singapore', 'Cơm niêu với gà rô ti và nước sốt đặc biệt', 85000.00, NULL, 'com-nieu-singapore.jpg', 'Món Á', 4.7, '2024-12-14 06:30:04', 0, 0, 0),
(32, 'Mực Xào Sa Tế', 'Mực tươi xào với sa tế và rau củ', 90000.00, NULL, 'muc-xao-sa-te.jpg', 'Món Việt', 4.5, '2024-12-14 06:31:04', 0, 0, 0),
(33, 'Ramen Tonkotsu', 'Mì ramen với nước dùng xương heo đặc biệt', 95000.00, NULL, 'ramen-tonkotsu.jpg', 'Món Nhật', 4.8, '2024-12-14 06:32:04', 0, 0, 0),
(34, 'Gỏi Cuốn Tôm Thịt', 'Gỏi cuốn tươi với tôm, thịt và rau herbs', 45000.00, NULL, 'goi-cuon.jpg', 'Món Việt', 4.6, '2024-12-14 06:33:04', 0, 0, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `review_text`, `created_at`, `comment`) VALUES
(1, 8, 6, 5, NULL, '2024-12-25 07:55:56', 'ngon quá'),
(2, 8, 6, 5, NULL, '2024-12-25 07:56:10', 'ngon quá nha'),
(3, 5, 6, 5, NULL, '2024-12-25 08:49:16', 'Nhìn thèm quá'),
(4, 6, 6, 5, NULL, '2024-12-25 13:04:34', 'Món này hơi có tí mụn nhưng nét như sonny hihi');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `email`, `password`, `role`, `created_at`, `status`) VALUES
(1, 'admin', '', '', '$2y$10$661HrRDqHWTxLpyLmBocGu3wm4u1QMiv1.TTT4nv8AoFExXiFc1WC', 'admin', '2024-12-14 06:47:29', 'active'),
(6, 'phihung0410', 'Người dùng Phi Hung', 'trongtin1122nak@gmail.com', '$2y$10$OWYzdeqOAoDPKFLPjpcJ5edrrEEgPfUF/Jz3m.y87M3/0jocFPqJ6', 'user', '2024-12-18 11:39:08', 'active'),
(7, 'phihung1122nak', 'Nguyễn Phi Hùng', 'phihung1122nak@gmail.com', '$2y$10$pbLoszuknmWpguIKhLTswOoITOcAsFKkvScygrweHmALMAnaX21vS', 'user', '2024-12-18 13:41:10', 'inactive');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`name`);

--
-- Chỉ mục cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT cho bảng `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
