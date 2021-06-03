CREATE TABLE IF NOT EXISTS `civicrm_exactonline_log` (
    `id` INT NOT NULL AUTO_INCREMENT ,
    `tstamp` INT NOT NULL ,
    `request_uri` TEXT NOT NULL ,
    `request_headers` TEXT NULL ,
    `response_status_code` VARCHAR(255) NOT NULL ,
    `response_headers` TEXT NULL ,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;
