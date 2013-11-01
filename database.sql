/*
SQLyog Enterprise - MySQL GUI v8.21 
MySQL - 5.5.33-cll-lve : Database - owlet
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`owlet` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `owlet`;

/*Table structure for table `urlrefs` */

DROP TABLE IF EXISTS `urlrefs`;

CREATE TABLE `urlrefs` (
  `id` mediumint(50) unsigned NOT NULL AUTO_INCREMENT,
  `strTinyURL` varchar(255) NOT NULL,
  `strFullURL` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`,`strTinyURL`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Data for the table `urlrefs` */

insert  into `urlrefs`(`id`,`strTinyURL`,`strFullURL`) values (1,'http://short.abendago.com/short/','http://www.google.com'),(2,'http://short.abendago.com/short/htub83','https://www.facebook.com/kevinfroese?fref=ts'),(3,'http://short.abendago.com/short/5b3ju7','https://www.facebook.com/kevinfroese?fref=ts'),(4,'http://short.abendago.com/short/ny4eej','http://www.abendago.com'),(5,'http://short.abendago.com/short/yhipqn','http://stackoverflow.com/questions/731233/activemq-or-rabbitmq-or-zeromq-or'),(6,'http://short.abendago.com/short/6kuzec','http://www.abendago.com'),(7,'http://short.abendago.com/short/72q597','http://www.abendago.com'),(8,'http://short.abendago.com/short/extxty','http://stackoverflow.com/questions/19720037/how-do-i-use-htaccess-to-collect-jsonp-callback-value'),(9,'http://short.abendago.com/short/z2e6va','http://stackoverflow.com/questions/19720037/how-do asdf -i-use-htaccess-to-collect-jsonp-callback-value'),(10,'http://short.abendago.com/short/bnwhj5','http://stackoverflowd.c'),(11,'http://short.abendago.com/short/hjz2jc','https://www.google.ca/?gws_rd=cr&ei=POFyUoGMKYKFyQGcvIDoDg#psj=1&q=0mq+bulletin+board'),(12,'http://short.abendago.com/short/3czlit','https://www.google.ca/?gws_rd=cr&ei=Gt1yUvy8EOHhygGWoYDwDA#psj=1&q=0mq+pass+message+from+website+php&start=20');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
