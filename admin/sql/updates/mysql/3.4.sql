UPDATE `#__jea_tools` SET `title`='com_jea_import',
`link`='index.php?option=com_jea&view=gateways&layout=import',
`icon`='download',
`params`=''
WHERE `title`='com_jea_import_from_jea';

UPDATE `#__jea_tools` SET `title`='com_jea_export',
`link`='index.php?option=com_jea&view=gateways&layout=export',
`icon`='upload',
`params`=''
WHERE `title`='com_jea_import_from_csv';

CREATE TABLE IF NOT EXISTS `#__jea_gateways` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(50) NOT NULL default '',
  `provider` varchar(50) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `published` tinyint(1) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `params` TEXT NOT NULL,
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

ALTER TABLE #__jea_properties ADD `provider` varchar(50) NOT NULL default '' COMMENT 'A gateway provider name';
