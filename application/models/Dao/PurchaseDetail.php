<?php
/**
 * ???
 */
class Dao_PurchaseDetail extends Dao_Base {
    protected $_name    = 'dtb_purchase_detail';
    protected $_primary = 'id';

    /**
     * statics
     */
    public static $statics = array(
        'order_by' => array(
            'purchase_id asc' => 'Purchase No.',
            'create_date desc' => 'Create Date',
        ),
    );
}
