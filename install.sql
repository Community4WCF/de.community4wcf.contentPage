DROP TABLE IF EXISTS wcf1_content_page;
CREATE TABLE wcf1_content_page (
	pageID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	subject VARCHAR(255) NOT NULL DEFAULT '',
	message MEDIUMTEXT NOT NULL,
	shortDescription text NOT NULL,
	time INT(10) NOT NULL DEFAULT 0,
	icon varchar(255) NOT NULL,
	canSeeGroupIDs varchar(255) NOT NULL,
	userID INT(10) NOT NULL,
	menuItemID int(10) unsigned NOT NULL,
	attachments SMALLINT(5) NOT NULL DEFAULT 0,
	pollID INT(10) NOT NULL DEFAULT 0,
	enableSmilies TINYINT(1) NOT NULL DEFAULT 1,
	enableHtml TINYINT(1) NOT NULL DEFAULT 0,
	enableBBCodes TINYINT(1) NOT NULL DEFAULT 1,
	FULLTEXT KEY (subject, message),
	KEY (time)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;