<?php
/**
 * ???
 */
class BaseController extends Zend_Controller_Action {
    /**
     * ??????
     * @override
     */
    protected function _redirect($url, array $options = array()) {
        // ????
        $this->db()->commit();

        return parent::_redirect($url, $options);
    }

    /**
     * ??????????????
     */
    public function db() {
        return Zend_Db_Table_Abstract::getDefaultAdapter();
    }

    /**
     * ?????????????
     */
    public function model($modelname, $option = array()) {
        $table = new $modelname($option);
        return $table;
    }

    /**
     * ????????
     */
    public function createNavigator($datas, $limit = 10) {
        // ????????????????????
        $module_name = $this->getRequest()->getModuleName();
        $class_name  = $this->getRequest()->getControllerName();
        $list_path   = '/' . $module_name . '/' . $class_name . '/list';
        $session     = new Zend_Session_Namespace($list_path);
        
        $paginator = null;
        if ( get_class($datas) === 'Zend_Db_Table_Select' ) {
            $paginator = new Zend_Paginator(
                new Zend_Paginator_Adapter_DbTableSelect( $datas )
            );
        } elseif ( get_class($datas) === 'Zend_Db_Select' ) {
            $paginator = new Zend_Paginator(
                new Zend_Paginator_Adapter_DbSelect( $datas )
            );
        } else {
            $paginator = new Zend_Paginator(
                new Zend_Paginator_Adapter_Array( $datas )
            );
        }
        
        // ???????
        if ($this->getRequest()->getParam('limit')) {
            $limit = $this->getRequest()->getParam('limit');
        }
        $session->limit = $limit;
        
        // ?????
        $paginator->setItemCountPerPage($limit);
        if ( $paginator->count() && $this->getRequest()->getParam('page') ) {
            $page = $this->getRequest()->getParam('page');
            $session->page = $page;
            $paginator->setCurrentPageNumber($page);
            if ( $page === '1' ) {
                $this->view->assign('first_page', true);
            } else {
                $this->view->assign('first_page', false);
            }
        } else {
            $session->page = 1;
            $this->view->assign('first_page', true);
        }
        
        $this->view->assign('pages', $paginator->getPages());
        $this->view->assign('paginator', $paginator);
    }

    /**
     * ????????
     */
    public function createLoginInfo() {
        if ($this->getRequest()->getModuleName() == 'manager') {
            $session = new Zend_Session_Namespace('admin');
            if ($session->id) {
                $model = $this->model('Dao_Admin')->retrieve($session->id);
                if ($model) {
                    $this->view->admin = $model->toArray();
                }
            }
        } else {
            $session = new Zend_Session_Namespace('login_member');
            if ($session->id) {
                $model = $this->model('Dao_Member')->retrieve($session->id);
                if ($model) {
                    $this->view->login_member = $model->toArray();
                }
            }
        }
    }

    /**
     * ????????
     */
    public function checkLogin() {
        if ($this->getRequest()->getModuleName() == 'manager') {
            $session = new Zend_Session_Namespace('admin');
            if (!$session->id) {
                $this->_redirect('/manager/login?return_path=' . urlencode($_SERVER['REDIRECT_URL']));
            }
        } elseif ($this->getRequest()->getModuleName() == 'sp') {
            $session = new Zend_Session_Namespace('login_member');
            if (!$session->id) {
                $this->_redirect('/sp/login?return_path=' . urlencode($_SERVER['REDIRECT_URL']));
            }
        } else {
            $session = new Zend_Session_Namespace('login_member');
            if (!$session->id) {
                $this->_redirect('/login?return_path=' . urlencode($_SERVER['REDIRECT_URL']));
            }
        }
    }

    /**
     * ?????????
     */
    public function createForm() {
        $module_name = $this->getRequest()->getModuleName();
        $class_name = $this->getRequest()->getControllerName();
        $func_name = $this->getRequest()->getActionName();
        $config_path = APPLICATION_PATH . '/../application/' . $module_name . '/configs/' . $class_name . '.ini';
        if ( file_exists($config_path) ) {
            $config = new Zend_Config_Ini($config_path, 'form');
            $form = new Zend_Form($config->$class_name->$func_name);
            $this->view->config = $config->$class_name->$func_name;
            $this->view->form = $form;
        }
        $this->view->module_name = $module_name;
        $this->view->class_name  = $class_name;
        $this->view->func_name   = $func_name;
    }

    /**
     * ??????????????
     */
    public function checkForm($form, $config, &$error_str) {
        foreach ( $config->elements as $key => $element ) {
            if ( isset($element->errors) ) {
                if ( $form->getElement($key)->getErrors() ) {
                    foreach ( $form->getElement($key)->getErrors() as $error ) {
                        if ( isset($element->errors->$error) ) {
                            $message = $element->errors->$error;
                        } else {
                            $message = $element->errors->other;
                        }
                        if ( isset($element->errors->place) ) {
                            $error_str[$element->errors->place] = $message;
                        } else {
                            $error_str[$key] = $message;
                        }
                    }
                }
            }
        }
    }

    /**
     * ?????
     */
    public function gobackList() {
        $module_name = $this->getRequest()->getModuleName();
        $class_name  = $this->getRequest()->getControllerName();
        $list_path   = '/' . $module_name . '/' . $class_name . '/list';
        $session = new Zend_Session_Namespace($list_path);
        $this->_redirect($list_path . '/page/' . $session->page . '/limit/' . $session->limit);
    }

    /**
     * ?????
     */
    public function gobackListAction() {
        $this->gobackList();
    }

    /**
     * ???????????????????
     */
    public function preDispatch() {
        // ????????html?????
        $this->_helper->viewRenderer->setViewSuffix('html');

        // ??????????????
        $config = Zend_Registry::get('config');
        $this->view->app = $config->app;

        // ????????
        if ($this->getRequest()->getModuleName() == 'manager') {
            $this->checkLogin();
        }

        // ????????
        $this->createLoginInfo();

        // ?????????
        $this->createForm();
    }

    /**
     * 404???
     */
    public function show404() {
        // ?????????
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // ????????
        header('HTTP/1.1 404 Not Found');
        echo '404 Not Found';
    }

    /**
     * ???????????????????
     */
    public function postDispatch() {
        if ($this->_helper->layout->isEnabled()) {
        }
    }
}
