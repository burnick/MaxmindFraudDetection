<?php
$installer = $this;
$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('maxmind_order_fraudcheck')};

CREATE TABLE IF NOT EXISTS {$this->getTable('maxmind_order_fraudcheck')} (
   `entity_id` int(10) unsigned NOT NULL,
   `distance` int(11) NOT NULL,
   `countryMatch` enum('yes','no'),
   `countryCode` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `freeMail`  enum('yes','no'),  
   `anonymousProxy` enum('yes','no', 'na'),
   `binMatch`  enum('yes','no', 'notfound', 'na'),
   `binCountry`  enum('yes','no', 'notfound', 'na'),
   `err` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `proxyScore` decimal(10,2),
   `ip_region` int(11) NOT NULL,
   `ip_city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `ip_latitude` decimal(10,6),
   `ip_longitude` decimal(10,6),
   `binName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `ip_isp` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `ip_org` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `binNameMatch` enum('yes','no', 'notfound', 'na'),
   `binPhoneMatch` enum('yes','no', 'notfound', 'na'),
   `binPhone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `custPhoneInBillingLoc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `highRiskCountry` enum('yes','no', 'notfound', 'na'),
   `queriesRemaining` int(11) NOT NULL,
   `cityPostalMatch` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `shipCityPostalMatch` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `maxmindID` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `ip_asnum` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `ip_userType` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `ip_countryConf` int(11) NOT NULL,
   `ip_regionConf` int(11) NOT NULL,
   `ip_cityConf` int(11) NOT NULL,
   `ip_postalCode` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `ip_postalConf` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `ip_accuracyRadius` int(11) NOT NULL,
   `ip_netSpeedCell` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `ip_metroCode` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
   `ip_areaCode` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
   `ip_timeZone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `ip_regionName`  varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `ip_domain`  varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `ip_countryName`  varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `ip_continentCode`  varchar(50) COLLATE utf8_unicode_ci NOT NULL,
   `ip_corporateProxy` enum('yes','no'),
   `shipForward`  enum('yes','no', 'notfound', 'na'),
   `riskScore` decimal(10,4),
   `prepaid` enum('yes','no', 'notfound', 'na'),
   `minfraud_version` decimal(10,4),
   `service_level`   varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   PRIMARY KEY (`entity_id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Maxmind Order Fraund Check';

    ");

$installer->endSetup();
?>