<?php
/**
 * 画像アップロード
 */
   
    
class Logic_OrderDetail extends Logic_Base {
    /**
     * 一覧をフォーム形式で取得
     */
    public function getAllAsForm($order_id, $form) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $models = $db->fetchAll(
			"SELECT * FROM `dtb_order_detail` WHERE `order_id` = ? ORDER BY size", $order_id
		);
		
        if (!$models) {
            for($i=1; $i<=13; $i++) {
                $id = $i;

                $elem = new Zend_Form_Element_Text("quantity_{$id}");
                $elem->setOptions(array('size' => '3', 'onkeyup' => 'sum()'));
                $elem->setValue(0);
                $form->addElement($elem);
            }
        } else {
            foreach($models as $model) {
                $id = $model['id'];

                $elem = new Zend_Form_Element_Text("quantity_{$id}");
                $elem->setOptions(array('size' => '3', 'onkeyup' => 'sum()'));
                $elem->setValue($model['quantity']);
                $form->addElement($elem);
            }
        }

        return $form;
    }
	
    /**
     * データを保存（更新）
     */
    public function doSave($order_id, $params) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        $models = $db->fetchAll(
			"SELECT * FROM `dtb_order_detail` WHERE `order_id` = ? ORDER BY size", $order_id
		);
        
        if( $models ) {
            foreach ($models as $model) {
                $id = $model['id'];

                $db->update("dtb_order_detail", array(
                    'quantity'     => $params["quantity_{$id}"],
                ),"id = {$id}");
            }
        } else {
            for( $i=1; $i<=13; $i++) {
                if( $params['quantity_'.$i] ) {
                    $mod_id = $this->model('Dao_OrderDetail')->insert(
                        array(
                            'order_id'  => $order_id,
                            'size'      => $i,
                            'quantity'  => $params['quantity_'.$i],
                        )
                    );
                }
            }

        }

        
    }
}