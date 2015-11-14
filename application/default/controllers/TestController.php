<?php
/**
 * ???
 */
class TestController extends BaseController {
    /**
     * ?????
     */
    public function indexAction() {
        if (!$this->view->app->debug) {
            // ?????????
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();

            // ????????
            header('HTTP/1.1 404 Not Found');
            echo '404 Not Found';
        }
    }
}
