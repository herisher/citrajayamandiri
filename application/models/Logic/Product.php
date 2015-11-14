<?php
/**
 * 画像アップロード
 */
   
    
class Logic_Product extends Logic_Base {    
    public function findByNo($product_no) {
		return $this->db()->fetchRow(
			"SELECT * FROM dtb_product WHERE product_no = ?", $product_no
		);
    }
}
