<?php
/**
 * 画像アップロード
 */
   
    
class Logic_PurchaseDetail extends Logic_Base {
    /**
     * 一覧をフォーム形式で取得
     */
    public function getAllAsForm($purchase_id, $form, $order_id = 0) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $models = $db->fetchAll(
            "SELECT * FROM `dtb_purchase_detail` WHERE `purchase_id` = ?", $purchase_id
        );
        
        if (!$models) {
			for($i=0; $i<5; $i++){
				$models[$i] = array(
					"material_id"	=>	"",
					"qty"	=>	"",
					"id"	=>	"",
					"id"	=>	"",
					"id"	=>	"",
				);
			}
			$models = array(
				array(
					"id"	=>	"",
					
				)
			)
            $form->setDefault('quantity', $total);
        }

        foreach($models as $model) {
            $id = $model['id'];

            $elem = new Zend_Form_Element_Text("quantity_{$id}");
            $elem->setOptions(array('size' => '3', 'onkeyup' => 'sum()', 'style' => 'text-align:right'));
            $elem->setValue($model['quantity']);
            $form->addElement($elem);
        }
        return $form;
    }
    
    /**
     * データを保存（更新）
     */
    public function doSave($delivery_id, $params, $order_id = 0) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        $models = $db->fetchAll(
            "SELECT * FROM `dtb_delivery_detail` WHERE `delivery_id` = ? ORDER BY size", $delivery_id
        );
        
        if(count($models)) {
            foreach ($models as $model) {
                $id = $model['id'];

                $db->update("dtb_delivery_detail", array(
                    'quantity'     => $params["quantity_{$id}"],
                ),"id = {$id}");
            }
        } else {
            $size_order = $db->fetchAll(
                "SELECT * FROM `dtb_order_detail` WHERE `order_id` = ?", $order_id
            );
            foreach($size_order as $pair) {
                $db->insert("dtb_delivery_detail", array(
                    'delivery_id'    => $delivery_id,
                    'size'            => $pair['size'],
                    'quantity'        => $params["quantity_".$pair['id']],
                ));
            }
        }
    }
}