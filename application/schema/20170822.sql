alter table `dtb_invoice` add column `receipt_flag` tinyint(4) default 0 after `status`;
update `dtb_invoice` set `receipt_flag` = 1;
