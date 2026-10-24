-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 28, 2026 at 04:49 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blog_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `blog_id` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `status` enum('under_review','approved','archived','deleted') DEFAULT 'under_review',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`blog_id`, `title`, `content`, `image_path`, `video_path`, `author_id`, `status`, `created_at`) VALUES
('DcYzBFb1ulrb8YYw32JW', 'computer', 'A computer is a machine that can be programmed to automatically carry out sequences of arithmetic or logical operations (computation). Modern digital electronic computers can perform generic sets of operations known as programs, which enable computers to perform a wide range of tasks. The term computer system may refer to a nominally complete computer that includes the hardware, operating system, software, and peripheral equipment needed and used for full operation, or to a group of computers that are linked and function together, such as a computer network or computer cluster.\r\n\r\nNumbering only a few in the 1930s, today computers are ubiquitous over a broad range of industrial and consumer products that use computers as control systems. The price, size, and complexity of computers can range from simple single-purpose devices like key fobs, microwave ovens, and remote controls up to supercomputers and factories using industrial robots. Computers are at the core of general-purpose devices such as personal computers and mobile devices, such as smartphones. Computers power the Internet, which links billions of computers and users.', 'uploads/img_1790606677_comp.png', NULL, 2, 'approved', '2026-09-28 14:44:37'),
('fWk1ltj0Y4', 'Smartphone', 'smart phones allows user to make calls and also used for entertainment purposes', NULL, NULL, 2, 'deleted', '2026-09-28 11:17:21'),
('jrEBagFFK', 'Smartphone', 'smart phones allows user to make calls and also used for entertainment purposes', NULL, NULL, 2, 'approved', '2026-09-28 11:17:07'),
('N4FmXZb43E', 'Artificial Intelligence (AI)', 'Ai is becoming one of the booming topics today in education there are sayings that ai will take over humans several scientists claim that these claims are false and often used to scare people \r\n\r\nbut there is a incident where claudes internal model hacked hugging face (Ai model) to complete a task', NULL, NULL, 2, 'approved', '2026-09-28 11:28:56'),
('qSSVIVa61z', 'Quantum computing', '\r\nCopilot Search Branding\r\n\r\nLike\r\n\r\nDislike\r\nQuantum computing leverages quantum mechanics to perform computations that can surpass classical computers for certain complex problems.\r\nCore Principles\r\nQuantum computing relies on quantum mechanics phenomena such as superposition, entanglement, and interference. Unlike classical bits, which are either 0 or 1, qubits can exist in a superposition of states, allowing quantum computers to process multiple possibilities simultaneously \r\n\r\n. Entanglement links qubits so that the state of one affects the other, enabling highly correlated computations. Interference is used to amplify correct outcomes while canceling incorrect ones, forming the basis of quantum algorithms \r\n\r\n\r\nQuantum Hardware\r\nCurrent quantum computers are largely experimental and include technologies such as superconducting qubits, which isolate electrical currents to reduce resistance, and ion traps, which confine individual atoms using electromagnetic fields \r\n\r\n. Companies like Rigetti Computing and Intel are developing cloud-accessible quantum processors, with Intel having built a 49-qubit processor as of 2019 \r\n. Despite progress, qubits are prone to quantum decoherence, where interaction with the environment introduces errors, making error correction a major research focus \r\n\r\n', NULL, NULL, 2, 'approved', '2026-09-28 11:36:19'),
('v9zMrFnajgcUAb3QbBmN', 'computer', 'A computer is a machine that can be programmed to automatically carry out sequences of arithmetic or logical operations (computation). Modern digital electronic computers can perform generic sets of operations known as programs, which enable computers to perform a wide range of tasks. The term computer system may refer to a nominally complete computer that includes the hardware, operating system, software, and peripheral equipment needed and used for full operation, or to a group of computers that are linked and function together, such as a computer network or computer cluster.\r\n\r\nNumbering only a few in the 1930s, today computers are ubiquitous over a broad range of industrial and consumer products that use computers as control systems. The price, size, and complexity of computers can range from simple single-purpose devices like key fobs, microwave ovens, and remote controls up to supercomputers and factories using industrial robots. Computers are at the core of general-purpose devices such as personal computers and mobile devices, such as smartphones. Computers power the Internet, which links billions of computers and users.', 'uploads/img_1790606432_comp.png', NULL, 2, 'deleted', '2026-09-28 14:40:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','employee','user') NOT NULL,
  `status` enum('pending','approved') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `status`) VALUES
(1, 'admin1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'approved'),
(2, 'employee1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee', 'approved'),
(3, 'user1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`blog_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
