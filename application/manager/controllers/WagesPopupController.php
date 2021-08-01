<?php
/**
 * 栄養士
 */
class Manager_WagesPopupController extends BaseController {
    const NAMESPACE_LIST = "/manager/wages-popup/list";
    const NAMESPACE_WORK = "/manager/wages-popup/work";

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
                $this->_redirect('/manager/wages-popup/list');
            } elseif ( $this->getRequest()->getParam('select') ) {
                $wages_session = new Zend_Session_Namespace('/manager/wages/list');
                $order = $this->model('Dao_Order')->retrieve($this->getRequest()->getParam('select'));
                $order = $order->toArray();
                $product = $this->model('Dao_Product')->retrieve($order['product_id']);
                $products = $product->toArray();
                $wages_session->order_list = $order;
                foreach($products as $key => $value) {
                    if($key != 'id') {
                        $wages_session->order_list[$key] = $value;
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
        $table = $this->model('Dao_TypeWork');
        $session = new Zend_Session_Namespace(self::NAMESPACE_WORK);
        
        // セッションから検索条件を復元する
        $where = array(); 
        if ($session->post) {
            $id_list = array();
            foreach ((array)$session->post as $key => $value) {
                // 検索条件セット
                if ( $key === 'id' && $value ) {
                    $where['id = ?'] = $value;
                }
                if ( $key === 'name' && $value ) {
                    $where['name like ?'] = '%'.$value.'%';
                }
            }
        }
		
        return $table->createWherePhrase($where, $order_by);
    }

    /**
     * 検索条件復元
     */
    private function restoreSearchForm2($form) {
        $session = new Zend_Session_Namespace(self::NAMESPACE_WORK);
        if ($session->post) {
            $form->setDefaults($session->post);
        }
    }

    public function workAction() {
        $this->_helper->layout->disableLayout();
		
		$session = new Zend_Session_Namespace(self::NAMESPACE_WORK);
        
        // フォーム設定読み込み
        $form = $this->view->form;
		
        // 検索・クリア
        if ( $this->getRequest()->isPost() ) {
            if ( $this->getRequest()->getParam('clear') ) {
                // クリア
                Zend_Session::namespaceUnset(self::NAMESPACE_WORK);
            } elseif ( $this->getRequest()->getParam('search') ) {
                // 検索開始
                $form->setDefaults($_POST);
                $session = new Zend_Session_Namespace(self::NAMESPACE_WORK);
                $session->post = $_POST;
                $this->_redirect('/manager/wages-popup/work');
            } elseif ( $this->getRequest()->getParam('select') ) {
				$wages_session = new Zend_Session_Namespace('/manager/wages/list');
                $work_id = $this->getRequest()->getParam('select');
                $this_work = $this->model('Dao_TypeWork')->retrieve($work_id);
                $work_list = $wages_session->work_list;
                if( !in_array($work_id, $work_list)) 
				    $wages_session->work_list[$work_id] = $this_work;
				Zend_Session::namespaceUnset( self::NAMESPACE_WORK );
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
