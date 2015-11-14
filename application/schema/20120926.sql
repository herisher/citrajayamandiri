DROP VIEW view_item_member;
CREATE VIEW `view_item_member` AS select `a`.`id` AS `id`,`a`.`member_id` AS `member_id`,`b`.`nickname` AS `nickname`,`b`.`email` AS `email`,`a`.`genre_id` AS `genre_id`,`a`.`category_id` AS `category_id`,`a`.`sub_category_id` AS `sub_category_id`,`a`.`item_name` AS `item_name`,`a`.`content` AS `content`,`a`.`image_url` AS `image_url`,`a`.`thumb_url` AS `thumb_url`,`a`.`buy_url` AS `buy_url`,`a`.`likes` AS `likes`,`a`.`reports` AS `reports`,`a`.`memo` AS `memo`,`a`.`disp_flag` AS `disp_flag`,`a`.`create_date` AS `create_date`,`a`.`update_date` AS `update_date` from (`dtb_item` `a` join `dtb_member` `b`) where (`a`.`member_id` = `b`.`id`);

CREATE VIEW `view_board` AS select `a`.`id` AS `id`,`a`.`member_id` AS `member_id`,`a`.`item_id` AS `item_id`,`a`.`title` AS `title`,`a`.`content` AS `content`,`a`.`disp_flag` AS `disp_flag`,`a`.`create_date` AS `create_date`,`b`.`item_name` AS `item_name`,`c`.`nickname` AS `nickname` from ((`dtb_board` `a` join `dtb_item` `b`) join `dtb_member` `c`) where ((`a`.`item_id` = `b`.`id`) and (`a`.`member_id` = `c`.`id`));

CREATE TABLE  `app_cawaii`.`dtb_mixi` (
`id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`mixi_id` VARCHAR( 13 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`mixi_token` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`mixi_refresh_token` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`mixi_expired_token` INT NOT NULL ,
`mixi_create_date_token` DATETIME NOT NULL ,
`member_id` BIGINT( 20 ) NOT NULL ,
`status` TINYINT( 4 ) NOT NULL DEFAULT  '1',
`create_date` DATETIME NULL ,
`modified_date` DATETIME NULL
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci



CREATE TABLE  `app_cawaii`.`dtb_twitter` (
`id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`twitter_id` BIGINT( 20 ) UNSIGNED NOT NULL ,
`oauth_data` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`status` SMALLINT( 4 ) NOT NULL DEFAULT  '1',
`member_id` BIGINT( 20 ) UNSIGNED NOT NULL ,
`create_date` DATETIME NULL ,
`update_date` DATETIME NULL
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci


CREATE TABLE  `app_cawaii`.`dtb_facebook` (
`id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`facebook_id` BIGINT( 20 ) UNSIGNED NOT NULL ,
`facebook_token_expire_date` DATETIME NOT NULL ,
`facebook_token_refresh_date` DATETIME NOT NULL ,
`facebook_token_key` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`status` SMALLINT( 4 ) NOT NULL DEFAULT  '1',
`member_id` BIGINT( 20 ) UNSIGNED NOT NULL ,
`create_date` DATETIME NULL ,
`update_date` DATETIME NULL
) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci