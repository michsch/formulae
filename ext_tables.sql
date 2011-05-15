#
# Table structure for table 'tx_formulae_formulas'
#
CREATE TABLE tx_formulae_formulas (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	formula text,
	firstname tinytext,
	lastname tinytext,
	title int(11) DEFAULT '0' NOT NULL,
	company tinytext,
	street tinytext,
	city tinytext,
	email tinytext,
	gtc tinyint(3) DEFAULT '0' NOT NULL,
	votes tinytext,
	finalvotes tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);