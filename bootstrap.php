#!/usr/bin/php
<?php  
  $config = parse_ini_file('config/run.ini', true);

  $con = new mysqli(
          $config['mysql']['host'], 
          $config['mysql']['user'], 
          $config['mysql']['password'], 
          '', 
          $config['mysql']['port']
        );

  # check for connection error
  if ($con->connect_error) {
    throw new Exception('Connect Error ('.$con->connect_errno.') '.$con->connect_error);
  }

  $stmt = $con->query('CREATE DATABASE IF NOT EXISTS '.$config['mysql']['database'].' DEFAULT CHARSET=utf8');
  echo "database (".$config['mysql']['database'].") created\n";

  $appointment = "
    USE ".$config['mysql']['database']."; 
    DROP TABLE IF EXISTS `appointment`;
    CREATE TABLE `appointment` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `start_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
      `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
      `name` varchar(255) NOT NULL,
      `topic` varchar(255) NULL DEFAULT '',
      `description` varchar(255) NULL DEFAULT '',
      `location` varchar(255) NULL DEFAULT '',
      `state` varchar(12) DEFAULT 'NEW',
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) DEFAULT CHARSET=utf8;

    CREATE TRIGGER `created_at` BEFORE INSERT ON `appointment` 
      FOR EACH ROW SET NEW.created_at = NOW();
  ";

  $stmt = $con->multi_query($appointment);
  mysqli_errno($con).": ".mysqli_error($con)."\n";
  echo "table (appointment) created\n";
  echo "\n";
?>
