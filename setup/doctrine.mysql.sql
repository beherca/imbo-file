CREATE TABLE IF NOT EXISTS `fileinfo` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `publicKey` varchar(255) COLLATE utf8_danish_ci NOT NULL,
    `fileIdentifier` char(32) COLLATE utf8_danish_ci NOT NULL,
    `size` int(10) unsigned NOT NULL,
    `extension` varchar(5) COLLATE utf8_danish_ci NOT NULL,
    `mime` varchar(20) COLLATE utf8_danish_ci NOT NULL,
    `added` int(10) unsigned NOT NULL,
    `updated` int(10) unsigned NOT NULL,
    `checksum` char(32) COLLATE utf8_danish_ci NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `file` (`publicKey`,`fileIdentifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `filemetadata` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `fileId` int(10) unsigned NOT NULL,
    `tagName` varchar(255) COLLATE utf8_danish_ci NOT NULL,
    `tagValue` varchar(255) COLLATE utf8_danish_ci NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fileId` (`fileId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `fileshorturl` (
    `shortUrlId` char(7) COLLATE utf8_danish_ci NOT NULL,
    `publicKey` varchar(255) COLLATE utf8_danish_ci NOT NULL,
    `fileIdentifier` char(32) COLLATE utf8_danish_ci NOT NULL,
    `extension` char(3) COLLATE utf8_danish_ci DEFAULT NULL,
    `query` text COLLATE utf8_danish_ci NOT NULL,
    PRIMARY KEY (`shortUrlId`),
    KEY `params` (`publicKey`,`fileIdentifier`,`extension`,`query`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE IF NOT EXISTS `storage_files` (
    `publicKey` varchar(255) COLLATE utf8_danish_ci NOT NULL,
    `fileIdentifier` char(32) COLLATE utf8_danish_ci NOT NULL,
    `data` blob NOT NULL,
    `updated` int(10) unsigned NOT NULL,
    PRIMARY KEY (`publicKey`,`fileIdentifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;