<?php
/**
 * CSV
 */
class CsvController extends BaseController {
    /**
     * ?????
     */
    public function downloadAction() {
        // ???????
        ini_set('memory_limit','-1');

        // ?????????
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // ?????
        header('Content-type: application/octet-stream');

        $type = $this->_getParam("type");
        if ($type == 1) {
            header('Content-Disposition: attachment; filename=sample1.csv');
            echo @file_get_contents(APPLICATION_PATH."/csv/sample1.csv");
        } elseif ($type == 2) {
            header('Content-Disposition: attachment; filename=sample2.csv');
            echo @file_get_contents(APPLICATION_PATH."/csv/sample2.csv");
        }
    }
}
