<?php
/**
 * ??????
 */
class IndexController extends BaseController {
    /**
     * ?????
     */
    public function indexAction() {
        $startTimeStamp = strtotime("2015/07/10");
        $endTimeStamp = strtotime("2015/07/26");

        $timeDiff = abs($endTimeStamp - $startTimeStamp);

        $numberDays = $timeDiff/86400;  // 86400 seconds in one day

        // and you might want to convert to integer
        $numberDays = intval($numberDays);
        echo "jumlah perjalanan = " . $numberDays . " hari";
        exit;
    }
}
