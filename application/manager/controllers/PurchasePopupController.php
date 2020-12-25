<?php
/**
 * 栄養士
 */
class Manager_PurchasePopupController extends BaseController {
    const NAMESPACE_LIST = "/manager/purchase-popup/list";
    const NAMESPACE_MATERIAL = "/manager/purchase-popup/material";

    /**
     * 検索条件作成
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_Order');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        
        // セッションから検索条件を復元する
        $where = array(); 
        if ($session->post) {
            $id_list = array();
            foreach ((array)$session->post as $key => $value) {
                // 検索条件セット
                if ( $key === 'id' && $value != null ) {
                    $where['id = ?'] = $value;
                }
                if ( $key === 'status_flag' && $value != null ) {
                    $where['status_flag = ?'] = $value;
                }
                if ( $key === 'payment_flag' && $value != null ) {
                    $where['payment_flag = ?'] = $value;
                }
                if ( $key === 'order_no' && $value != null ) {
                    $where['order_no like ?'] = '%'.$value.'%';
                }
                if ( $key === 'article' && $value != null ) {
                    $where['product_id IN (SELECT id FROM dtb_product WHERE article like ?)'] = '%'.$value.'%';
                }
                if ( $key === 'project' && $value != null ) {
                    $where['product_id IN (SELECT id FROM dtb_product WHERE project like ?)'] = '%'.$value.'%';
                }
            }
        }
        
        return $table->createWherePhrase($where, $order_by);
    }

    /**
     * 検索条件復元
     */
    private function restoreSearchForm($form) {
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        if ($session->post) {
            $form->setDefaults($session->post);
        }
    }

    /**
     * 一覧
     */
    public function listAction() {
        $this->_helper->layout->disableLayout();
        
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        
        // フォーム設定読み込み
        $form = $this->view->form;
        $form->getElement('status_flag')->setMultiOptions(array('' => '▼Choose') + Dao_Order::$statics['status_flag']);
        $form->getElement('payment_flag')->setMultiOptions(array('' => '▼Choose') + Dao_Order::$statics['payment_flag']);
          
        // 検索・クリア
        if ( $this->getRequest()->isPost() ) {
            if ( $this->getRequest()->getParam('clear') ) {
                // クリア
                Zend_Session::namespaceUnset(self::NAMESPACE_LIST);
            } elseif ( $this->getRequest()->getParam('search') ) {
                // 検索開始
                $form->setDefaults($_POST);
                $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
                $session->post = $_POST;
                $this->_redirect('/manager/purchase-popup/list');
            } elseif ( $this->getRequest()->getParam('select') ) {
                $purchase_session = new Zend_Session_Namespace('/manager/purchase/list');
                $order = $this->model('Dao_Order')->retrieve($this->getRequest()->getParam('select'));
                $order = $order->toArray();
                $product = $this->model('Dao_Product')->retrieve($order['product_id']);
                $products = $product->toArray();
                $purchase_session->order_list = $order;
                foreach($products as $key => $value) {
                    if($key != 'id') {
                        $purchase_session->order_list[$key] = $value;
                    }
                }
                Zend_Session::namespaceUnset( self::NAMESPACE_LIST );
                $this->view->need_close = true;
            }
        }
        
        // 検索条件復元
        $this->restoreSearchForm($form);
        
        // 一覧取得
        $this->createNavigator(
            $this->createWherePhrase()
        );
        
        // 表示用カスタマイズ
        $models = array();
        foreach ($this->view->paginator as $model) {
            $model = $model->toArray();
            $product = $this->model('Dao_Product')->retrieve($model['product_id']);
            $products = $product->toArray();
            foreach($products as $key => $value) {
                if($key != 'id') {
                    $model[$key] = $value;
                }
            }
            $model['disp_status'] = Dao_Order::$statics['status_flag'][$model['status_flag']];
            $model['disp_payment'] = Dao_Order::$statics['payment_flag'][$model['payment_flag']];
            array_push($models, $model);
        }
        $this->view->models = $models;
    }
    
    private function createWherePhrase2($order_by = 'id desc') {
        $table = $this->model('Dao_Material');
        $session = new Zend_Session_Namespace(self::NAMESPACE_MATERIAL);
        
        // セッションから検索条件を復元する
        $where = array(); 
        if ($session->post) {
            $id_list = array();
            foreach ((array)$session->post as $key => $value) {
                // 検索条件セット
                if ( $key === 'id' && $value ) {
                    $where['id = ?'] = $value;
                }
                if ( $key === 'part_no' && $value ) {
                    $where['part_no like ?'] = '%'.$value.'%';
                }
                if ( $key === 'material_desc' && $value ) {
                    $where['material_desc like ?'] = '%'.$value.'%';
                }
            }
        }
		
        return $table->createWherePhrase($where, $order_by);
    }

    /**
     * 検索条件復元
     */
    private function restoreSearchForm2($form) {
        $session = new Zend_Session_Namespace(self::NAMESPACE_MATERIAL);
        if ($session->post) {
            $form->setDefaults($session->post);
        }
    }

    public function materialAction() {
        $this->_helper->layout->disableLayout();
		
		$session = new Zend_Session_Namespace(self::NAMESPACE_MATERIAL);
        
        // フォーム設定読み込み
        $form = $this->view->form;
		
        // 検索・クリア
        if ( $this->getRequest()->isPost() ) {
            if ( $this->getRequest()->getParam('clear') ) {
                // クリア
                Zend_Session::namespaceUnset(self::NAMESPACE_MATERIAL);
            } elseif ( $this->getRequest()->getParam('search') ) {
                // 検索開始
                $form->setDefaults($_POST);
                $session = new Zend_Session_Namespace(self::NAMESPACE_MATERIAL);
                $session->post = $_POST;
                $this->_redirect('/manager/purchase-popup/material');
            } elseif ( $this->getRequest()->getParam('select') ) {
				$purchase_session = new Zend_Session_Namespace('/manager/purchase/list');
                $material_id = $this->getRequest()->getParam('select');
                $this_material = $this->model('Dao_Material')->retrieve($material_id);
                $material_list = $purchase_session->material_list;
                if( !in_array($material_id, $material_list)) 
				    $purchase_session->material_list[$material_id] = $this_material;
				Zend_Session::namespaceUnset( self::NAMESPACE_MATERIAL );
				$this->view->need_close = true;
            }
        } 
        
        // 検索条件復元
        $this->restoreSearchForm2($form);
        
        // 一覧取得
        $this->createNavigator(
            $this->createWherePhrase2()
        );
        
        // 表示用カスタマイズ
        $models = array();
        foreach ($this->view->paginator as $model) {
            $model = $model->toArray();
            array_push($models, $model);
        }
        $this->view->models = $models;
    }
}
