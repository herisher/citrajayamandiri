<?php
/**
 * ???
 */
class Dao_Delivery extends Dao_Base {
    protected $_name    = 'dtb_delivery';
    protected $_primary = 'id';
        
    /**
     * statics
     */
    public static $statics = array(
        'status' => array(
            '0' => 'No Invoice',
            '1' => 'Invoice Created',
        ),
        'order_by' => array(
            'delivery_no asc' => 'Delivery No.',
            'order_id desc' => 'Order',
        ),
    );
}
