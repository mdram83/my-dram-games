-- create test database
CREATE DATABASE IF NOT EXISTS `mydramgames-test`;

-- add permissions to test database
GRANT ALL PRIVILEGES ON `mydramgames-test`.* TO 'mydramgames'@'%';
FLUSH PRIVILEGES;
