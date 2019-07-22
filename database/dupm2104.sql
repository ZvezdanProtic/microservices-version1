CREATE DATABASE  IF NOT EXISTS `microservicesv1` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `microservicesv1`;
-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: microservicesv1
-- ------------------------------------------------------
-- Server version	5.7.18-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `callconnecting`
--

DROP TABLE IF EXISTS `callconnecting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callconnecting` (
  `idcall` int(11) NOT NULL,
  `idagent` int(11) NOT NULL,
  `callaccepted` datetime NOT NULL COMMENT 'Call accepted by the Agent at this moment.',
  `callfinished` datetime NOT NULL COMMENT 'Call finished by mutual consensus at this moment.',
  `callendedprematurely` datetime NOT NULL COMMENT 'Call finished prematurely at this moment. Ended by Agent of Client.',
  `calltimeout` datetime DEFAULT NULL,
  PRIMARY KEY (`idcall`),
  KEY `fk_callconnectingagent` (`idagent`),
  CONSTRAINT `fk_callconnectingagent` FOREIGN KEY (`idagent`) REFERENCES `users` (`iduser`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_callconnectingcall` FOREIGN KEY (`idcall`) REFERENCES `callregistration` (`idcall`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `callconnecting`
--

--
-- Table structure for table `callprocessing`
--

DROP TABLE IF EXISTS `callprocessing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callprocessing` (
  `idcall` int(11) NOT NULL,
  `iduser` int(11) NOT NULL COMMENT 'This can be either ID of a client or of an agent. ',
  `rawsounddatareceptionstarted` datetime NOT NULL COMMENT 'Moment when the stream of raw sound data started.',
  `filename` varchar(100) NOT NULL COMMENT 'Name of the file used to store the raw data.',
  PRIMARY KEY (`idcall`,`iduser`,`rawsounddatareceptionstarted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `callprocessing`
--

--
-- Table structure for table `callregistration`
--

DROP TABLE IF EXISTS `callregistration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callregistration` (
  `idcall` int(11) NOT NULL AUTO_INCREMENT,
  `idclient` int(11) NOT NULL,
  `callrequested` datetime NOT NULL COMMENT 'Call requested by the Client at this moment.',
  `callcancel` datetime NOT NULL COMMENT 'Call cancelled by the Client at this moment.',
  `calltimeout` datetime NOT NULL COMMENT 'Timout for this call. If the agent does not pick up the call before this time, the user will have to request a call again.',
  PRIMARY KEY (`idcall`),
  KEY `fk_callregistrationuser` (`idclient`),
  CONSTRAINT `fk_callregistrationuser` FOREIGN KEY (`idclient`) REFERENCES `users` (`iduser`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `callregistration`
--

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `iduser` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(16) NOT NULL COMMENT 'Encoded username.',
  `password` blob NOT NULL COMMENT 'Encoded password.',
  `email` blob NOT NULL COMMENT 'Encoded email.',
  `confirmationToken` varchar(100) DEFAULT NULL,
  `confirmed` varchar(3) DEFAULT NULL,
  `activationToken` varchar(100) DEFAULT NULL,
  `active` varchar(3) DEFAULT NULL,
  `keyyear` int(11) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`iduser`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userslogin`
--

DROP TABLE IF EXISTS `userslogin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userslogin` (
  `iduser` int(11) NOT NULL,
  `loginstart` datetime NOT NULL COMMENT 'Login begin time.',
  `loginend` datetime NOT NULL COMMENT 'Login end time.',
  `logintimeout` datetime NOT NULL COMMENT 'Session timeout. If user tries to use this session after it is elapsed, it will not be possible. ',
  `oauthtoken` varchar(200) NOT NULL COMMENT 'Unique token, authenticating the user for this particular login session',
  PRIMARY KEY (`iduser`,`loginstart`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userslogin`
--
