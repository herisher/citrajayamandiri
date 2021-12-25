<?php
/**
 * 栄養士
 */
class Manager_DeliveryPlanPopupController extends BaseController {
    const NAMESPACE_INDEX = "/manager/delivery-plan-popup";

    /**
     * 検索条件作成
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_Order');
        $session = new Zend_Session_Namespace(self::NAMESPACE_INDEX);
        
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
        $session = new Zend_Session_Namespace(self::NAMESPACE_INDEX);
        if ($session->post) {
            $form->setDefaults($session->post);
        }
    }

    public function indexAction() {
        $this->_helper->layout->disableLayout();
        
        $session = new Zend_Session_Namespace(self::NAMESPACE_INDEX);
        
        // フォーム設定読み込み
        $form = $this->view->form;
        
        $form->getElement('status_flag')->setMultiOptions(array('' => '▼Choose') + Dao_Order::$statics['status_flag']);
        $form->getElement('order_by')->setMultiOptions(array('' => '▼Choose') + Dao_Order::$statics['outstanding_order']);

        // 検索・クリア
        if ( $this->getRequest()->isPost() ) {
            if ( $this->getRequest()->getParam('clear') ) {
                // クリア
                Zend_Session::namespaceUnset(self::NAMESPACE_INDEX);
            } elseif ( $this->getRequest()->getParam('search') ) {
                // 検索開始
                $form->setDefaults($_POST);
                $session = new Zend_Session_Namespace(self::NAMESPACE_INDEX);
                $session->post = $_POST;
                $this->_redirect('/manager/delivery-plan-popup');
            } elseif ( $this->getRequest()->getParam('select') ) {
                $delivery_plan_session = new Zend_Session_Namespace('/manager/delivery-plan/list');
                $order_id = $this->getRequest()->getParam('select');
                $order_list = $delivery_plan_session->order_list;
                if( !in_array($order_id, $order_list)) 
                    $delivery_plan_session->order_list[] = $order_id;
                Zend_Session::namespaceUnset( self::NAMESPACE_INDEX );
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
        $models = $this->model('Logic_Order')->getOutstandingByYear($session);
        // foreach ($this->view->paginator as $model) {
        //     $model = $model->toArray();
        //     array_push($models, $model);
        // }
        $this->view->models = $models;
    }
}
