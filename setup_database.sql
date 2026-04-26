CREATE DATABASE IF NOT EXISTS motbic;
CREATE USER IF NOT EXISTS 'motbic'@'localhost' IDENTIFIED BY 'your_password_here';
GRANT ALL PRIVILEGES ON motbic.* TO 'motbic'@'localhost';
FLUSH PRIVILEGES;
