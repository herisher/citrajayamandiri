<?php
/**
 * トップ
 */
class Manager_IndexController extends ManagerBaseController {
    /**
     * デフォルト
     */
    public function indexAction() {
        $model = $this->model('Logic_Order')->getOutstanding();
        $this->view->models = $model;
    }

    /**
     * システム情報
     */
    public function sysinfoAction() {
        // ビューを無効にする
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        phpinfo();
    }
}
