ALTER TABLE #__jea_properties ADD `publish_down` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `modified`;
ALTER TABLE #__jea_properties ADD `publish_up` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `modified`;
ALTER TABLE #__jea_properties ADD `access` int(11) NOT NULL default '0' AFTER `published`;

UPDATE #__jea_properties SET `publish_up`=`created`, access=1;
