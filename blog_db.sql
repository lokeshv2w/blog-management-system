-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2026 at 11:03 AM
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
-- Database: `blog_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'demo', 'demo', '2026-03-23 15:36:17'),
(2, 'demo2', 'demo2', '2026-03-23 15:36:26'),
(3, 'Web Development', 'web-development', '2026-03-23 15:47:36'),
(4, 'Artificial Intelligence', 'artificial-intelligence', '2026-03-23 15:47:36'),
(5, 'Machine Learning', 'machine-learning', '2026-03-23 15:47:37'),
(6, 'Data Science', 'data-science', '2026-03-23 15:47:37'),
(7, 'Cloud Computing', 'cloud-computing', '2026-03-23 15:47:37'),
(8, 'Cybersecurity', 'cybersecurity', '2026-03-23 15:47:37'),
(9, 'Mobile Development', 'mobile-development', '2026-03-23 15:47:37'),
(10, 'DevOps', 'devops', '2026-03-23 15:47:37'),
(11, 'Blockchain', 'blockchain', '2026-03-23 15:47:37'),
(12, 'UI/UX Design', 'ui-ux-design', '2026-03-23 15:47:37');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `name`, `email`, `content`, `status`, `created_at`) VALUES
(1, 2, 'Julie Sosa Lambert', 'guxo@mailinator.com', 'This is first comment', 'approved', '2026-03-23 16:01:39'),
(2, 2, 'Lokesh V2web', 'guxo@mailinator.com', 'demo comments', 'approved', '2026-03-23 16:02:09'),
(3, 2, 'John Doe', 'test@test.com', 'sdf sdfvd rweed d esbf', 'approved', '2026-03-23 16:06:06');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_mega_menu` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `label`, `url`, `parent_id`, `sort_order`, `is_mega_menu`) VALUES
(1, 'Home', 'index.php', NULL, 0, 1),
(3, 'Categories', '#', NULL, 2, 1),
(7, 'Artificial Intelligence', 'index.php?category=artificial-intelligence', 3, 0, 0),
(8, 'Data Science', 'index.php?category=data-science', 3, 1, 0),
(9, 'The Rise of Generative AI in Code', 'post.php?id=3', 8, 0, 0),
(12, 'About Us', 'page.php?slug=about-us', NULL, 1, 0),
(13, 'Contact', 'page.php?slug=contact', NULL, 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `featured_image` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `title`, `slug`, `content`, `status`, `created_at`, `updated_at`, `featured_image`, `meta_description`) VALUES
(1, 'About Us', 'about-us', '<h2>Welcome to DevBlog</h2>\r\n<p>This is your new default About page. You can edit this from the Admin Panel.</p>', 'published', '2026-03-23 17:13:43', '2026-03-23 17:38:22', 'about-us-hero-1774267702.jpg', ''),
(2, 'Contact', 'contact', '<h2>Get in Touch</h2><p>Feel free to reach out to us for any business inquiries.</p>', 'published', '2026-03-23 17:13:43', '2026-03-23 17:13:43', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `slug`, `content`, `image`, `category_id`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'My First Post', 'my-first-post', '<h2 data-section-id=\"1fj1duv\" data-start=\"87\" data-end=\"139\">🌿 A Simple Guide to Staying Productive Every Day</h2>\r\n<h3 data-section-id=\"8kl1ly\" data-start=\"141\" data-end=\"159\">Introduction</h3>\r\n<p data-start=\"160\" data-end=\"310\">Staying productive doesn&rsquo;t have to be complicated. With a few small habits, you can make your day more organized, less stressful, and more meaningful.</p>\r\n<hr data-start=\"312\" data-end=\"315\">\r\n<h3 data-section-id=\"11epm3w\" data-start=\"317\" data-end=\"354\">✨ 1. Start Your Day with a Plan</h3>\r\n<p data-start=\"355\" data-end=\"485\">Take 5&ndash;10 minutes each morning to write down what you want to accomplish.<br data-start=\"428\" data-end=\"431\">Keep your list short&mdash;3 to 5 important tasks is enough.</p>\r\n<hr data-start=\"487\" data-end=\"490\">\r\n<h3 data-section-id=\"b6l87y\" data-start=\"492\" data-end=\"531\">⏰ 2. Focus on One Thing at a Time</h3>\r\n<p data-start=\"532\" data-end=\"650\">Multitasking can reduce your efficiency.<br data-start=\"572\" data-end=\"575\">Instead, give your full attention to one task before moving on to the next.</p>\r\n<hr data-start=\"652\" data-end=\"655\">\r\n<h3 data-section-id=\"1i7x09v\" data-start=\"657\" data-end=\"687\">☕ 3. Take Regular Breaks</h3>\r\n<p data-start=\"688\" data-end=\"760\">Working non-stop can make you tired.<br data-start=\"724\" data-end=\"727\">Try the <strong data-start=\"735\" data-end=\"757\">Pomodoro technique</strong>:</p>\r\n<ul data-start=\"761\" data-end=\"810\">\r\n<li data-section-id=\"ky7vew\" data-start=\"761\" data-end=\"784\">Work for 25 minutes</li>\r\n<li data-section-id=\"huescb\" data-start=\"785\" data-end=\"810\">Take a 5-minute break</li>\r\n</ul>\r\n<hr data-start=\"812\" data-end=\"815\">\r\n<h3 data-section-id=\"1xy64xt\" data-start=\"817\" data-end=\"848\">📵 4. Reduce Distractions</h3>\r\n<p data-start=\"849\" data-end=\"956\">Keep your phone away or turn off notifications while working.<br data-start=\"910\" data-end=\"913\">A quiet environment helps you stay focused.</p>', 'uploads/post_69c112a25f107.jpg', 1, 1, 'published', '2026-03-23 15:44:58', '2026-03-23 15:44:58'),
(2, 'Getting Started with Modern Web Development', 'getting-started-with-modern-web-development', '<p>The web development landscape is constantly evolving. In this guide, we explore the top frameworks to learn this year such as Next.js, SvelteKit, and Vue.</p>\r\n<h2>The Core Technologies</h2>\r\n<p>Remember that HTML, CSS, and vanilla JavaScript form the foundation of everything you build.</p>', 'uploads/post_69c1135f6aa7e.jpg', 3, 1, 'published', '2026-03-23 15:47:37', '2026-03-23 15:48:07'),
(3, 'The Rise of Generative AI in Code', 'the-rise-of-generative-ai-in-code', '<p>Artificial Intelligence is changing how we write code. Tools like GitHub Copilot and ChatGPT are acting as pair programmers, helping developers ship faster than ever.</p>\r\n<p>Instead of manually looking up documentation, developers can now describe the logic they want, and the AI will generate the initial scaffolding.</p>', 'uploads/post_69c11369a8001.jpg', 4, 1, 'published', '2026-03-23 15:47:37', '2026-03-23 15:48:17'),
(4, 'Understanding Machine Learning Algorithms', 'understanding-machine-learning-algorithms', '<p>Machine learning doesn\'t have to be intimidating. We break down standard algorithms like linear regression, decision trees, and neural networks into plain English.</p>\r\n<p>At its core, it\'s just math recognizing patterns in large sets of data.</p>', 'uploads/post_69c11375c80c9.jpg', 5, 1, 'published', '2026-03-23 15:47:37', '2026-03-23 15:48:29'),
(5, 'Why Data Science is the Sexiest Job of the 21st Century', 'why-data-science-is-the-sexiest-job-of-the-21st-century', '<p>With data growing exponentially, companies desperately need experts who can analyze, interpret, and drive business decisions based on massive datasets.</p>\r\n<ul>\r\n<li>Python</li>\r\n<li>R</li>\r\n<li>SQL</li>\r\n<li>Tableau</li>\r\n</ul>', 'uploads/post_69c1137f5b3e0.png', 6, 1, 'published', '2026-03-23 15:47:37', '2026-03-23 15:48:39'),
(6, 'AWS vs Azure vs Google Cloud: Which to Choose?', 'aws-vs-azure-vs-google-cloud-which-to-choose', '<p>Cloud computing has revolutionized infrastructure. We compare the big three providers based on pricing, ease of use, and enterprise capabilities.</p>\r\n<p>While AWS holds the market share, Azure integrates seamlessly with Microsoft enterprise products, and GCP offers top-tier machine learning tools.</p>', 'uploads/post_69c113901cd8c.jpg', 7, 1, 'published', '2026-03-23 15:47:37', '2026-03-23 15:48:56'),
(7, 'Top 5 Cybersecurity Threats in 2024', 'top-5-cybersecurity-threats-in-2024', '<p>Cyber threats are becoming more sophisticated. Learn about ransomware attacks, zero-day vulnerabilities, phishing schemes, and how to defend against them.</p>\r\n<h2>The Best Defense</h2>\r\n<p>User education remains the strongest defense against social engineering attacks.</p>', 'uploads/post_69c113ac51041.png', 8, 1, 'published', '2026-03-23 15:47:37', '2026-03-23 15:49:24'),
(8, 'Building Cross-Platform Mobile Apps with React Native', 'building-cross-platform-mobile-apps-with-react-native', '<p>React Native gives you the power to build natively-rendering mobile applications for iOS and Android using JavaScript and React.</p>\r\n<p>By maintaining a single codebase, companies save significant development time and resources while still delivering a native feel.</p>', 'uploads/post_69c113ee3e7f7.jpg', 9, 1, 'published', '2026-03-23 15:47:37', '2026-03-23 15:50:30'),
(9, 'Introduction to Docker and Kubernetes', 'introduction-to-docker-and-kubernetes', '<p>Containerization and orchestration are crucial for modern DevOps. We simplify the concepts of containers and how Kubernetes manages them at scale.</p>\r\n<p>Imagine Docker as the shipping container, and Kubernetes as the port manager automating the movement of those ships.</p>', 'uploads/post_69c113bb3f3a9.jpg', 10, 1, 'published', '2026-03-23 15:47:37', '2026-03-23 15:49:39'),
(10, 'Beyond Cryptocurrency: The Future of Blockchain Tech', 'beyond-cryptocurrency-the-future-of-blockchain-tech', '<p>While Bitcoin introduced blockchain to the world, its underlying technology has applications ranging from supply chain transparency to secure voting systems.</p>\r\n<p>A decentralized ledger ensures that no single entity has control over the data, making it essentially immutable.</p>', 'uploads/post_69c113cb49e94.jpg', 11, 1, 'published', '2026-03-23 15:47:37', '2026-03-23 15:49:55'),
(11, 'Designing User-Centric Interfaces with Figma', 'designing-user-centric-interfaces-with-figma', '<p>Great UI/UX is the key to software adoption. Learn how Figma has become the industry standard for collaborative design and prototyping.</p>\r\n<p>Its cloud-based nature allows real-time collaboration between designers and developers, shrinking the gap between a mock-up and production code.</p>', 'uploads/post_69c113d6ebd7a.png', 12, 1, 'published', '2026-03-23 15:47:37', '2026-03-23 15:50:06');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'site_title', 'DevBlog'),
(2, 'site_description', 'A modern, premium blog management system with dynamic animations and fluid typography.'),
(3, 'footer_text', 'DevBlog CMS. All rights reserved.'),
(4, 'contact_email', 'contact@example.com');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','author') DEFAULT 'author',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$AFuOk3OKYaNNEHi6Tj0Jbeh6BkFIocOhR8kCO8EyhYZe1g.uZsRAC', 'admin', '2026-03-23 15:32:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
