ALTER TABLE `dtb_invoice` ADD COLUMN `payment_status` tinyint(4) DEFAULT 0 AFTER `due_date`;
