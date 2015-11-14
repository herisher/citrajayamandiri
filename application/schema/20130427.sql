alter table `dtb_product` add column `call` varchar(5) not null after `singkatan`;
update `dtb_product` set `call` = `singkatan`;
alter table `dtb_product` add column `type` tinyint(4) default 1 after `call`;
alter table `dtb_product` add column `image_url` varchar(200) default null after `type`;
alter table `dtb_product` add column `thumb_url` varchar(200) default null after `image_url`;

update `dtb_order` set `receipt_date` = `tanggal_terima`;
update `dtb_order` set `description` = `keterangan`;
alter table `dtb_order` add column `order_no` bigint(20) default null after `po_no`;
update `dtb_order` set `order_no` = `po_no`;
update `dtb_order` set `quantity` = `qty`;
alter table `dtb_order` add column `payment_term` int(11) default 2 after `payment`;
update `dtb_order` set `payment_term` = `payment`;
alter table `dtb_order` add column `document_flag` tinyint(4) default 1 after `status_flag`;

update `dtb_order_detail` set `quantity` = `qty_po`;
alter table `dtb_order_detail` add column `order_id` bigint(20) not null after `id`;
alter table `dtb_order_detail` drop column `po_id`;

update `dtb_delivery` set `description` = `keterangan_sj`;
update `dtb_delivery` set `quantity` = `qty_sj`;
update `dtb_delivery` set `delivery_no` = `sj_no`;
update `dtb_delivery` set `delivery_date` = `date`;

update `dtb_delivery_detail` set `quantity` = `qty_sj`;
update `dtb_delivery_detail` set `size` = `size_sj`;

update `dtb_invoice` set `quantity` = `qty_inv`;
alter table `dtb_invoice` add column `invoice_date` date default null after `date`;
update `dtb_invoice` set `invoice_date` = `date`;

alter table `dtb_delivery` add column `delivery_date` date default null after `quantity`;
alter table `dtb_delivery` drop column `date`;

update `dtb_receipt` set `receipt_no` = `tt_no`;
update `dtb_receipt` set `receipt_date` = `tt_tgl`;