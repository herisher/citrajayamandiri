<?php
/**
 * 管理者
 */
class Dao_Order extends Dao_Base {
    protected $_name    = 'dtb_order';
    protected $_primary = 'id';
    
    /**
     * statics
     */
    public static $statics = array(
        'status_flag' => array(
            '4' => 'Waiting Material',  //include : No Material
            '0' => 'Process',           //include : material purchase, cutting, sewing, finishing
            '1' => 'Delivery Finish',   //include : all goods already sent to BATA
            '2' => 'Invoice Finish',    //include : all invoice already sent to BATA
            '3' => 'Returned',          //include : order is returned due to price changes or taken over by 
        ),//not yet implemented
        'payment_flag' => array(
            '0' => 'Unpaid',
            '1' => 'Paid',
        ),
        'document_flag' => array(
            '0' => 'Not yet received',
            '1' => 'Received',
        ),
        'order_by' => array(
            'plan desc' => 'Plan',
            'order_no desc' => 'Order No',
            'update_date desc' => 'Update Date'
        ),
        'outstanding_order' => array(
            "`p`.`project`" => 'Project'
        ),
    );
}
 