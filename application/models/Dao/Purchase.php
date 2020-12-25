<?php
/**
 * 管理者
 */
class Dao_Purchase extends Dao_Base {
    protected $_name    = 'dtb_purchase';
    protected $_primary = 'id';
    
    /**
     * statics
     */
    public static $statics = array(
        'purchase_type' => array(
            '0' => 'Normal',    //include : material purchase, cutting, sewing, finishing
            '1' => 'Shortage',    //include : all goods already sent to BATA
        ),
        'order_by' => array(
            'purchase_no asc' => 'Purchase No.',
            'order_id desc' => 'Order',
        ),
    );
}
