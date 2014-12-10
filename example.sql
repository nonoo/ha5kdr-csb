CREATE TABLE IF NOT EXISTS `nmhh-csb` (
  `partnercode` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `zip` varchar(15) NOT NULL,
  `city` varchar(100) NOT NULL,
  `streethouse` varchar(100) NOT NULL,
  `licensenumber` varchar(20) NOT NULL,
  `callsign` varchar(15) NOT NULL,
  `communityorprivate` varchar(15) NOT NULL,
  `state` varchar(15) NOT NULL,
  `levelofexam` varchar(15) NOT NULL,
  `morse` varchar(15) NOT NULL,
  `licensedate` datetime NOT NULL,
  `validity` datetime NOT NULL,
  `chiefoperator` varchar(100) NOT NULL,
  PRIMARY KEY (`partnercode`,`callsign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
