ALTER TABLE `dtb_order` 
ADD COLUMN `sub_total` int(11) DEFAULT 0 AFTER `quantity`, 
ADD COLUMN `ppn` int(11) DEFAULT 0 AFTER `sub_total`, 
ADD COLUMN `total` int(11) DEFAULT 0  AFTER `ppn`;