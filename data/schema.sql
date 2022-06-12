DROP TABLE IF EXISTS `down_vote`;
DROP TABLE IF EXISTS `items`;
DROP TABLE IF EXISTS `pin`;
DROP TABLE IF EXISTS `users`;

-- Table structure for table `down_vote`

CREATE TABLE `down_vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)

-- Table structure for table `items`

CREATE TABLE `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `price` decimal(6,2) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `post_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pin` tinyint(1) NOT NULL DEFAULT '0',
  `down_vote` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)

-- Table structure for table `pin`

CREATE TABLE `pin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) 

-- Table structure for table `users`

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `f_name` varchar(255) DEFAULT NULL,
  `l_name` varchar(255) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) 
