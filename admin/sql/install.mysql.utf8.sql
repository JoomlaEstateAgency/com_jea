
-- --------------------------------------------------------

-- 
-- Structure de la table `#__jea_advantages`
-- 

DROP TABLE IF EXISTS `#__jea_advantages`;
CREATE TABLE `#__jea_advantages` (
  `id` int(11) NOT NULL auto_increment,
  `value` varchar(255) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `#__jea_areas`
-- 

DROP TABLE IF EXISTS `#__jea_areas`;
CREATE TABLE `#__jea_areas` (
  `id` int(11) NOT NULL auto_increment,
  `value` varchar(255) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `#__jea_conditions`
-- 

DROP TABLE IF EXISTS `#__jea_conditions`;
CREATE TABLE `#__jea_conditions` (
  `id` int(11) NOT NULL auto_increment,
  `value` varchar(255) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `#__jea_departments`
-- 

DROP TABLE IF EXISTS `#__jea_departments`;
CREATE TABLE `#__jea_departments` (
  `id` int(11) NOT NULL auto_increment,
  `value` varchar(255) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `name` (`value`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `#__jea_heatingtypes`
-- 

DROP TABLE IF EXISTS `#__jea_heatingtypes`;
CREATE TABLE `#__jea_heatingtypes` (
  `id` int(11) NOT NULL auto_increment,
  `value` varchar(255) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `#__jea_hotwatertypes`
-- 

DROP TABLE IF EXISTS `#__jea_hotwatertypes`;
CREATE TABLE `#__jea_hotwatertypes` (
  `id` int(11) NOT NULL auto_increment,
  `value` varchar(255) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `#__jea_properties`
-- 

DROP TABLE IF EXISTS `#__jea_properties`;
CREATE TABLE `#__jea_properties` (
  `id` int(11) NOT NULL auto_increment,
  `ref` varchar(20) NOT NULL default '',
  `type_id` int(11) NOT NULL default '0',
  `is_renting` tinyint(1) NOT NULL default '0',
  `price` decimal(12,2) NOT NULL default '0.00',
  `adress` varchar(255) NOT NULL default '',
  `town_id` int(11) NOT NULL default '0',
  `area_id` int(11) NOT NULL default '0',
  `zip_code` varchar(10) NOT NULL default '',
  `department_id` tinyint(3) NOT NULL default '0',
  `condition_id` int(11) NOT NULL default '0',
  `living_space` int(11) NOT NULL default '0',
  `land_space` int(11) NOT NULL default '0',
  `rooms` int(11) NOT NULL default '0',
  `charges` decimal(12,2) NOT NULL default '0.00',
  `fees` decimal(12,2) NOT NULL default '0.00' COMMENT 'honoraires',
  `hot_water_type` tinyint(1) NOT NULL default '0',
  `heating_type` tinyint(2) NOT NULL default '0' COMMENT 'type_chauffage',
  `bathrooms` tinyint(3) NOT NULL default '0',
  `toilets` tinyint(3) NOT NULL default '0',
  `availability` date NOT NULL default '0000-00-00',
  `floor` int(11) NOT NULL default '0',
  `advantages` varchar(255) NOT NULL default '' COMMENT 'criteres',
  `description` text NOT NULL,
  `slogan_id` int(11) NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `emphasis` tinyint(1) NOT NULL default '0' COMMENT 'mise en avant',
  `date_insert` date NOT NULL default '0000-00-00',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `ref` (`ref`),
  KEY `departement_id` (`department_id`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `#__jea_slogans`
-- 

DROP TABLE IF EXISTS `#__jea_slogans`;
CREATE TABLE `#__jea_slogans` (
  `id` int(11) NOT NULL auto_increment,
  `value` varchar(255) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `#__jea_towns`
-- 

DROP TABLE IF EXISTS `#__jea_towns`;
CREATE TABLE `#__jea_towns` (
  `id` int(11) NOT NULL auto_increment,
  `value` varchar(255) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure de la table `#__jea_types`
-- 

DROP TABLE IF EXISTS `#__jea_types`;
CREATE TABLE `#__jea_types` (
  `id` int(11) NOT NULL auto_increment,
  `value` varchar(255) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
