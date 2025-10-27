/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.23-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: tmpdb
-- ------------------------------------------------------
-- Server version	10.6.23-MariaDB-ubu2204

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `dd_catalog`
--

DROP TABLE IF EXISTS `dd_catalog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dd_catalog` (
  `ItemID` int(11) NOT NULL,
  `ItemName` varchar(255) NOT NULL,
  `ItemDesc` text DEFAULT NULL,
  `ItemPrice` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ItemThumb` varchar(255) DEFAULT NULL,
  `ItemFile` varchar(255) NOT NULL,
  `CatID` int(11) NOT NULL DEFAULT 0,
  `NotAvailable` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ItemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dd_catalog`
--

LOCK TABLES `dd_catalog` WRITE;
/*!40000 ALTER TABLE `dd_catalog` DISABLE KEYS */;
INSERT INTO `dd_catalog` VALUES (2,'Agreement with accountant','Agreement with accountant form.',4.95,'','dl/accountantagreement.pdf',3,0),(3,'Affidavit','General Affidavit form.',4.95,'','dl/affidavit.pdf',4,0),(4,'Minutes of the annual meeting of shareholders','Minutes of the annual meeting of shareholders form.',4.95,'','dl/annualmeetingshareholdeminutes.pdf',7,0),(5,'Antenuptial Agreement','Antenuptial Agreement',4.95,'','dl/antenuptial.pdf',4,0),(6,'Articles of Incorporation','Articles of Incorporation',4.95,'','dl/articlesofincorporation.pdf',7,0),(7,'Assignment of Entire Interest in Estate','Assignment of Entire Interest in Estate',4.95,'','dl/assignmentinteresteestate.pdf',10,0),(8,'Assignment of Lease by Lessee with Consent of Lessor','Assignment of Lease by Lessee with Consent of Lessor',4.95,'','dl/assignmentleaselessor.pdf',4,0),(9,'Assignment of Mortgage','Assignment of Mortgage',4.95,'','dl/assignmentofmortgage.pdf',2,0),(10,'Assignment of Savings Account','Assignment of Savings Account',4.95,'','dl/assignmentofsavings.pdf',3,0),(11,'Assignment of Stock Certificate','Assignment of Stock Certificate',4.95,'','dl/assignmentstockcertificate.pdf',7,0),(12,'Automobile Rental Agreement','Automobile Rental Agreement',4.95,'','dl/autorentalagreement.pdf',6,0),(13,'Balloon Mortgage Note','Balloon Mortgage Note\r\n',4.95,'','dl/baloonmortgagenote.pdf',2,0),(14,'Bill of Sale','Bill of Sale',4.95,'','dl/billofsale.pdf',4,0),(15,'Minutes of the Annual Meeting of the Board of Directors','Minutes of the Annual Meeting of the Board of Directors',4.95,'','dl/boarddirectorsminutes.pdf',7,0),(16,'Waiver of Norice of the First Meeting of the Board of Directors','Waiver of Norice of the First Meeting of the Board of Directors',4.95,'','dl/boarddirectorswaiver.pdf',7,0),(17,'Boat Rental Agreement','Boat Rental Agreement',4.95,'','dl/boatrentalagreement.pdf',5,0),(18,'Business Consultant Agreement','Business Consultant Agreement',4.95,'','dl/businessconsultagreement.pdf',8,0),(19,'Buy-Sell Agreement','Buy-Sell Agreement',4.95,'','dl/buysellagreement.pdf',4,0),(20,'Bylaws','Bylaws',0.00,'','dl/bylaws.pdf',7,0),(21,'Cardholder\'s Inquiry Concerning Billing Error','Cardholder\'s Inquiry Concerning Billing Error',4.95,'','dl/cardholderbillingerror.pdf',3,0),(22,'Cardholder\'s Report of Lost Credit Card','Cardholder\'s Report of Lost Credit Card',4.95,'','dl/cardholderlostcard.pdf',3,0),(23,'Cardholder\'s Report of Stolen Credid Card','Cardholder\'s Report of Stolen Credid Card',4.95,'','dl/cardholderstolencard.pdf',3,0),(24,'Collection Demand Letter','Collection Demand Letter',4.95,'','dl/collectiondemandletter.pdf',3,0),(25,'Consent of Lessor','Consent of Lessor',4.95,'','dl/consentoflessor.pdf',6,0),(26,'Construction Contract','Construction Contract',4.95,'','dl/constructioncontract.pdf',8,0),(27,'Contingent Fee Retainer','Contingent Fee Retainer',4.95,'','dl/contingentfeeretainer.pdf',8,0),(28,'Agreement Between Owner and Contractor','Agreement Between Owner and Contractor',4.95,'','dl/contractoragreement.pdf',8,0),(29,'Contract for Purchase and Sale','Contract for Purchase and Sale',4.95,'','dl/contractpurchasesale.pdf',4,0),(30,'Application for Reservation of Corporate Name','Application for Reservation of Corporate Name',4.95,'','dl/corporatenameapplcation.pdf',7,0),(31,'Resolution - Authorization for Issuance of Shares of Coporation in Exchange for Realty','Resolution - Authorization for Issuance of Shares of Coporation in Exchange for Realty',4.95,'','dl/corporatesharesexchangerealy.pdf',2,0),(32,'Declaration of Life Insurance Trust','Declaration of Life Insurance Trust',4.95,'','dl/declarelifeinsuretrust.pdf',3,0),(33,'Declaration of Revocable Trust','Declaration of Revocable Trust',4.95,'','dl/declarrevocabletrust.pdf',3,0),(34,'Employee Agreement','Employee Agreement',4.95,'','dl/employeeagreement.pdf',9,0),(35,'Memorandum of Employee Automoblie Expense Allowance','Memorandum of Employee Automoblie Expense Allowance',4.95,'','dl/employeeautoexpense.pdf',9,0),(36,'Lease Agreement for Furnished House','Lease Agreement for Furnished House',4.95,'','dl/furnishedhouseagreement.pdf',6,0),(37,'General Release','General Release',4.95,'','dl/generalrelease.pdf',2,0),(38,'Gifts Under Uniform Gifts to Minors Act','Gifts Under Uniform Gifts to Minors Act',4.95,'','dl/giftstominors.pdf',4,0),(39,'Installment Note','Installment Note',4.95,'','dl/installmentnote.pdf',3,0),(40,'Declaration of Irrevocable Trust','Declaration of Irrevocable Trust',4.95,'','dl/irrevocabletrust.pdf',3,0),(41,'Joint Venture Agreement','Joint Venture Agreement',4.95,'','dl/jointventure.pdf',7,0),(42,'Last Will and Testament','Last Will and Testament',4.95,'','dl/lastwill.pdf',10,0),(43,'Lease','Lease',4.95,'','dl/lease.pdf',6,0),(44,'Lease Agreement','Lease Agreement',4.95,'','dl/leaseagreement.pdf',6,0),(45,'Agreement for Extention of Lease','Agreement for Extention of Lease',4.95,'','dl/leaseextention.pdf',6,0),(46,'Assignment of Life Insurance Policy as Collateral','Assignment of Life Insurance Policy as Collateral',4.95,'','dl/lifeinsurancecollateral.pdf',3,0),(47,'Living Will (Female)','Living Will (Female)',4.95,'','dl/livingwillfemale.pdf',10,0),(48,'Living Will (Male)','Living Will (Male)',4.95,'','dl/livingwillmale.pdf',10,0),(49,'Management of Single Family House','Management of Single Family House',4.95,'','dl/managementsinglefamhouse.pdf',6,0),(50,'Ratification of Minutes of the Annual Meeting of Shareholders','Ratification of Minutes of the Annual Meeting of Shareholders',4.95,'','dl/minuteannualshare.pdf',7,0),(51,'Minutes of the First Meeting of the Board of Directors','Minutes of the First Meeting of the Board of Directors',4.95,'','dl/minutesfirstdirectors.pdf',7,0),(52,'Modification Agreement','Modification Agreement',4.95,'','dl/modificationagreement.pdf',7,0),(53,'Mortgage','Mortgage',4.95,'','dl/mortgage.pdf',2,0),(54,'Mortgage Assumption Agreement','Mortgage Assumption Agreement',4.95,'','dl/mortgageassumptionagreement.pdf',2,0),(55,'Mutual Rescission of Contract','Mutual Rescission of Contract',4.95,'','dl/mutualrescissioncontract.pdf',4,0),(56,'Receipt for Non-Refundable Deposit','Receipt for Non-Refundable Deposit',4.95,'','dl/nonrefunddepostreceipt.pdf',3,0),(57,'Option Agreement for Purchase of Real Property','Option Agreement for Purchase of Real Property',4.95,'','dl/optionpurchaserealestate.pdf',2,0),(58,'Notice of Overdue Rent','Notice of Overdue Rent',4.95,'','dl/overduerent.pdf',6,0),(59,'Parking Space Lease','Parking Space Lease',4.95,'','dl/parkingspacelease.pdf',6,0),(60,'Agreement for Permission to Sublet','Agreement for Permission to Sublet',0.00,'','dl/permissiontosublet.pdf',6,0),(61,'Power of Attorney','Power of Attorney',4.95,'','dl/powerofattorny.pdf',10,0),(62,'Promissory Note','Promissory Note',4.95,'','dl/promissorynote.pdf',3,0),(63,'Property Management Agreement','Property Management Agreement',4.95,'','dl/propoertymanagementagree.pdf',6,0),(64,'Proxy','Proxy',4.95,'','dl/proxy.pdf',7,0),(65,'Quit-Claim Deed','Quit-Claim Deed',4.95,'','dl/quitclaimdeed.pdf',2,0),(66,'Contract Employing Real Estate Broker for Sale of Property','Contract Employing Real Estate Broker for Sale of Property',4.95,'','dl/realestateagentcontract.pdf',2,0),(67,'Real Estate Salesman Independent Contractor Aggreement','Real Estate Salesman Independent Contractor Aggreement',4.95,'','dl/realestateagentcontractor.pdf',2,0),(68,'Assignment of Contract for Purchase of Real Estate','Assignment of Contract for Purchase of Real Estate',4.95,'','dl/realestatepurchasecontract.pdf',2,0),(69,'Assignment of Real Estate Purchase and Sale Agreement','Assignment of Real Estate Purchase and Sale Agreement',4.95,'','dl/realestepurchase.pdf',2,0),(70,'Contract Employing Real Estate Broker for Lease of Property','Contract Employing Real Estate Broker for Lease of Property',4.95,'','dl/realtybrokerlease.pdf',8,0),(71,'Rental Application','Rental Application',4.95,'','dl/rentalapplication.pdf',6,0),(72,'Rent Receipt','Rent Receipt',4.95,'','dl/rentreceipt.pdf',6,0),(73,'Assignment of Rents by Lessor with Repurchase Agreement','Assignment of Rents by Lessor with Repurchase Agreement',4.95,'','dl/rentslessrepurchase.pdf',6,0),(74,'Notice of Transfer of Reserved Name','Notice of Transfer of Reserved Name',4.95,'','dl/reservednametransfer.pdf',7,0),(75,'Retainer','Retainer',4.95,'','dl/retainer.pdf',4,0),(76,'Revocation of Trust','Revocation of Trust',4.95,'','dl/revocationoftrust.pdf',3,0),(77,'Revocation of Power of Attoney','Revocation of Power of Attoney',4.95,'','dl/revocationpowerattorny.pdf',4,0),(78,'Shareholders Agreement','Shareholders Agreement',4.95,'','dl/shareholdersagreement.pdf',7,0),(79,'Special Power of Attoney','Special Power of Attoney',4.95,'','dl/specialpowattorny.pdf',4,0),(80,'Special Warranty Deed','Special Warranty Deed',4.95,'','dl/specialwarrantydeed.pdf',2,0),(81,'Stock Purchase Agreement','Stock Purchase Agreement',4.95,'','dl/stockpurchaseagree.pdf',8,0),(82,'Stock Redemption Agreement','Stock Redemption Agreement',4.95,'','dl/stockredemption.pdf',8,0),(83,'Storage Space Lease','Storage Space Lease',4.95,'','dl/storagespacelease.pdf',6,0),(84,'Subscription Agreement','Subscription Agreement',4.95,'','dl/subscriptionagreement.pdf',8,0),(85,'Sale of Motor Vehicle','Sale of Motor Vehicle',4.95,'','dl/vehiclesale.pdf',5,0);
/*!40000 ALTER TABLE `dd_catalog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dd_categories`
--

DROP TABLE IF EXISTS `dd_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dd_categories` (
  `CatID` int(11) NOT NULL,
  `CatName` varchar(255) NOT NULL,
  `CatDesc` text DEFAULT NULL,
  `SortOrder` int(11) DEFAULT 0,
  `Active` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`CatID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dd_categories`
--

LOCK TABLES `dd_categories` WRITE;
/*!40000 ALTER TABLE `dd_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `dd_categories` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-27 14:49:31
