-- --------------------------------------------------------
--
-- Table structure for table `company`
--
CREATE TABLE IF NOT EXISTS `company` (
  `company_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `telephone` varchar(255) COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `street_address` mediumtext COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `suburb` varchar(255) COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `state` varchar(12) COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `postcode` varchar(12) COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `country` varchar(12) COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'References country.code',
  `website` varchar(255) COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `abn` varchar(255) COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=100000 COMMENT='Stores the company details';

-- --------------------------------------------------------
--
-- Data dump for table `company`
--
INSERT INTO `company` (`company_id`, `name`, `telephone`, `fax`, `street_address`, `suburb`, `state`, `postcode`, `country`, `website`, `abn`) VALUES
   (NULL, 'SavveCentral3', '+61 2 9901 4522', '+61 2 9906 1380 ', 'Level 7, 33 Chandos Street', 'St Leonards', 'NSW', '2065', 'AU', 'www.savvecentral.com.au', NULL),
   (NULL, 'SavveProducts', '+61 2 9901 4522', '+61 2 9906 1380 ', 'Level 7, 33 Chandos Street', 'St Leonards', 'NSW', '2065', 'AU', 'www.savveproducts.com.au', NULL),
   (NULL, 'SavveMarketplace', '+61 2 9901 4522', '+61 2 9906 1380 ', 'Level 7, 33 Chandos Street', 'St Leonards', 'NSW', '2065', 'AU', 'www.savvemarketplace.com.au', NULL);