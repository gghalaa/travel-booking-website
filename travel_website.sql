-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 27, 2025 at 09:55 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travel_website`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `trip_id` int(11) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `passengers` int(11) NOT NULL DEFAULT 1,
  `class` enum('economy','business','first-class') DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `trip_id`, `booking_date`, `status`, `start_date`, `end_date`, `passengers`, `class`, `amount`) VALUES
(1, 2, 1, '2025-03-01', 'cancelled', '2025-04-01', '2025-04-07', 1, 'economy', 0.00),
(2, 3, 2, '2025-03-05', 'confirmed', '2025-07-27', '2025-08-02', 1, 'economy', 0.00),
(3, 4, 3, '2025-03-10', 'confirmed', '2025-06-22', '2025-06-28', 1, 'economy', 0.00),
(4, 5, 4, '2025-03-15', 'confirmed', '2025-07-06', '2025-07-12', 1, 'economy', 0.00),
(5, 6, 5, '2025-03-20', 'pending', '2025-05-20', '2025-05-26', 1, 'economy', 0.00),
(6, 7, 6, '2025-03-25', 'confirmed', '2025-04-25', '2025-05-01', 1, 'economy', 0.00),
(7, 8, 7, '2025-03-30', 'cancelled', '2025-04-06', '2025-04-12', 1, 'economy', 0.00),
(8, 9, 8, '2025-04-01', 'cancelled', '2025-04-20', '2025-04-26', 1, 'economy', 0.00),
(9, 10, 9, '2025-04-05', 'confirmed', '2025-06-29', '2025-07-05', 1, 'economy', 0.00),
(10, 2, 10, '2025-04-06', 'confirmed', '2025-05-18', '2025-05-24', 1, 'economy', 0.00),
(138, 18, 6, '2025-04-23', 'pending', '2025-06-15', '2025-06-21', 10, 'first-class', 12000.00),
(140, 18, 10, '2025-04-23', 'pending', '2025-05-10', '2025-05-16', 3, 'first-class', 5700.00),
(141, 19, 1, '2025-04-23', 'cancelled', '2025-05-01', '2025-05-07', 1, 'business', 1300.00),
(146, 18, 7, '2025-04-24', 'confirmed', '2025-06-01', '2025-06-07', 5, 'economy', 10000.00),
(147, 18, 7, '2025-04-24', 'cancelled', '2025-04-01', '2025-04-07', 4, 'business', 8400.00),
(153, 18, 6, '2025-04-27', 'cancelled', '2025-04-28', '2025-05-04', 1, 'economy', 1000.00),
(154, 18, 6, '2025-04-27', 'cancelled', '2025-03-15', '2025-03-21', 1, 'economy', 1000.00),
(155, 18, 6, '2025-04-27', 'cancelled', '2025-12-21', '2025-12-27', 1, 'economy', 1000.00),
(156, 18, 6, '2025-04-27', 'pending', '2025-06-15', '2025-06-21', 5, 'economy', 5000.00),
(157, 18, 6, '2025-04-27', 'cancelled', '2025-06-22', '2025-06-28', 3, 'economy', 3000.00),
(158, 18, 6, '2025-04-27', 'cancelled', '2025-08-03', '2025-08-09', 3, 'first-class', 3600.00),
(159, 18, 6, '2025-04-27', 'cancelled', '2026-01-01', '2026-01-07', 5, 'business', 5500.00),
(160, 18, 7, '2025-04-27', 'confirmed', '2025-04-28', '2025-05-04', 2, 'business', 4200.00),
(161, 18, 9, '2025-04-27', 'pending', '2025-06-29', '2025-07-05', 2, 'business', 4000.00);

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `trip_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `itinerary` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`trip_id`, `title`, `summary`, `price`, `location`, `description`, `itinerary`) VALUES
(1, 'Bali Adventure', 'Beach + culture tour', 1200.00, 'Bali, Indonesia', 'Explore Seminyak\'s golden beaches, trek to hidden waterfalls in Ubud, and unwind with daily yoga sessions. Immerse yourself in Bali\'s rich culture and tranquil environment.', 'Day 1: Arrival in Bali - Relax at the resort.\r\nDay 2: Ubud & Sacred Monkey Forest Sanctuary.\r\nDay 3: Explore Waterfalls and Temples.\r\nDay 4: Traditional Cooking Class.\r\nDay 5: Yoga Session by the Beach.\r\nDay 6: Explore Local Markets & Cultural Experiences.\r\nDay 7: Departure.'),
(2, 'Rome Exploration', 'Historical tour', 1500.00, 'Rome, Italy', 'Experience Rome\'s iconic landmarks, from the Colosseum to the Vatican, with guided tours that immerse you in the city\'s rich history.', 'Day 1: Arrival in Rome - Explore the Spanish Steps.\nDay 2: Colosseum & Roman Forum Tour.\nDay 3: Vatican Museums & Sistine Chapel.\nDay 4: Pantheon & Trevi Fountain.\nDay 5: Day Trip to Tivoli Gardens.\nDay 6: Explore Trastevere District.\nDay 7: Departure.'),
(3, 'Tokyo Highlights', 'Modern Japan tour', 1800.00, 'Tokyo, Japan', 'Discover the vibrant and fast-paced city of Tokyo, with visits to futuristic neighborhoods, temples, and traditional tea ceremonies.', 'Day 1: Arrival in Tokyo - Shibuya Crossing & Meiji Shrine.\nDay 2: Tsukiji Fish Market & Senso-ji Temple.\nDay 3: Tokyo Skytree & Asakusa.\nDay 4: Explore Akihabara & Odaiba.\nDay 5: Day Trip to Mt. Fuji.\nDay 6: Explore Harajuku & Omotesando.\nDay 7: Departure.'),
(4, 'Paris Getaway', 'Experience the city', 1400.00, 'Paris, France', 'Experience Paris\' rich culture with visits to famous landmarks like the Eiffel Tower, the Louvre, and Montmartre.', 'Day 1: Arrival in Paris - Explore Montmartre.\nDay 2: Eiffel Tower & Seine River Cruise.\nDay 3: Louvre Museum Tour.\nDay 4: Day Trip to Versailles Palace.\nDay 5: Notre-Dame Cathedral & Sainte-Chapelle.\nDay 6: Champs-Élysées & Arc de Triomphe.\nDay 7: Departure.'),
(5, 'Amazon Expedition', 'Rainforest safari', 2200.00, 'Brazil', 'Embark on an exciting Amazon rainforest adventure, where you\'ll encounter wildlife and explore remote jungle habitats.', 'Day 1: Arrival in Manaus - Evening Boat Tour.\nDay 2: Jungle Trekking & Wildlife Viewing.\nDay 3: Visit to Indigenous Village.\nDay 4: Canoeing on the Amazon River.\nDay 5: Exploring the Meeting of the Waters.\nDay 6: Piranha Fishing & Jungle Survival Skills.\nDay 7: Departure.'),
(6, 'New York Escape', 'Urban tour', 1000.00, 'New York, USA', 'Explore the iconic sites of New York City, from Times Square to Central Park, with plenty of time to experience the best of the Big Apple.', 'Day 1: Arrival in NYC - Times Square & Broadway Show.\nDay 2: Central Park & Museum of Modern Art.\nDay 3: Statue of Liberty & Ellis Island.\nDay 4: Empire State Building & 5th Avenue Shopping.\nDay 5: Brooklyn Bridge & DUMBO.\nDay 6: Museum of Natural History & Night Views from Top of the Rock.\nDay 7: Departure.'),
(7, 'Dubai Luxury Tour', 'Desert + luxury shopping', 2000.00, 'Dubai, UAE', 'Indulge in luxury shopping, explore the desert, and experience Dubai\'s cosmopolitan charm with exclusive experiences.', 'Day 1: Arrival in Dubai - Desert Safari.\nDay 2: Burj Khalifa & Dubai Mall.\nDay 3: Dubai Marina & Palm Jumeirah.\nDay 4: Visit to the Dubai Aquarium & Underwater Zoo.\nDay 5: Explore Dubai Old Town & Al Fahidi Fort.\nDay 6: Luxury Shopping in Mall of the Emirates.\nDay 7: Departure.'),
(8, 'Sydney Explorer', 'Beaches + wildlife', 1600.00, 'Sydney, Australia', 'Relax on stunning beaches, explore Sydney\'s vibrant wildlife, and immerse yourself in the city\'s art and culture scene.', 'Day 1: Arrival in Sydney - Bondi Beach.\nDay 2: Sydney Opera House & Harbour Bridge.\nDay 3: Taronga Zoo & Wildlife Tour.\nDay 4: Blue Mountains Day Trip.\nDay 5: Royal Botanic Garden & Circular Quay.\nDay 6: Manly Beach & Coastal Walks.\nDay 7: Departure.'),
(9, 'Iceland Sighting', 'Northern lights tour', 1900.00, 'Reykjavik, Iceland', 'Chase the mesmerizing Northern Lights in Iceland, and explore its stunning natural beauty, including glaciers and volcanoes.', 'Day 1: Arrival in Reykjavik - Blue Lagoon Spa.\nDay 2: Golden Circle Tour & Geysir.\nDay 3: South Coast Tour & Waterfalls.\nDay 4: Ice Caving & Glacier Hiking.\nDay 5: Whale Watching & Northern Lights Chase.\nDay 6: Jokulsarlon Glacier Lagoon.\nDay 7: Departure.'),
(10, 'Greece Discovery', 'Islands + ancient sites', 1700.00, 'Athens, Greece', 'Discover the ancient ruins and the beautiful islands of Greece, where history meets stunning scenery and turquoise waters.', 'Day 1: Arrival in Athens - Acropolis & Parthenon.\r\nDay 2: Day Trip to Santorini Island.\r\nDay 3: Visit to Mykonos & Delos Island.\r\nDay 4: Explore Temple of Poseidon & Cape Sounion.\r\nDay 5: Cultural walking tour of Athens & Greek Cuisine.\r\nDay 6: Explore Athens\' Ancient Agora.\r\nDay 7: Departure.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `email`) VALUES
(1, 'admin', '$2y$10$h11SVPvEEDDZ7NWqGiIPNuLpdRzMoT.7M9xZOGFghPW79IpFkIn1y', 'admin@dreamscape.com'),
(2, 'alice', '$2y$10$WXZ1il7bTTRo6ozN3/2oaOVOmJI8woB.WbOpq4cnQVM/6MZ.UmPJW', 'alice@mail.com'),
(3, 'bob', '$2y$10$WXZ1il7bTTRo6ozN3/2oaOVOmJI8woB.WbOpq4cnQVM/6MZ.UmPJW', 'bob@mail.com'),
(4, 'charlie', '$2y$10$f1fLojAhzHRcC1T51Ct0ru2k4Afr56Zr8GMqAGbQdlBrky3I2WlzC', 'charlie@mail.com'),
(5, 'diana', '$2y$10$RDagn/dEjyxoiPvbf2gxDOmrhtY5X7ydmBzJY7jTo2yE28fIHMTzi', 'diana@mail.com'),
(6, 'eric', '$2y$10$vgFODYa3sULL1I95VMUmU.z5TyNUp6mvm/O5waA0V8naNSm4avqRS', 'eric@mail.com'),
(7, 'fiona', '$2y$10$Bhy/ePd9uj0WXVilz/9TSe77mrRamZ7SCH6F16JcASlWR3/2vmWJm', 'fiona@mail.com'),
(8, 'george', '$2y$10$BqBkltJuEMIg7h4TktO7BenqDuq83X09QmoTo63pn5enTi3hunQ7G', 'george@mail.com'),
(9, 'helen', '$2y$10$rIPW56/lxR8AGKI1r0FHFu26IBOlXKxYZZLqtCTQ1fOVRT0mBNC/u', 'helen@mail.com'),
(10, 'isaac', '$2y$10$aoj74bWMAIIZ361spFEoMev0iJMs/GrZOzzG3WHRjHXAMj1dgFpg6', 'isaac@mail.com'),
(18, 'ghala', '$2y$10$1uYli..9BTbrUPnZGLhBJexEISbilyu3sZHGsRCnyOKCSAJAVGOC6', 'ghala@gmail.com'),
(19, 'hiba', '$2y$10$WI.coNfu2ei1Ufyd4YYF4.XItU.IJvcJ2c7Bt2dm8ONYLmwAdb8Oa', 'hiba@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `trip_id` (`trip_id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`trip_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `trip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`trip_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
