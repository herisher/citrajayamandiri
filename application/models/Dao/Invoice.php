<?php
/**
 * 管理者
 */
class Dao_Invoice extends Dao_Base {
    protected $_name    = 'dtb_invoice';
    protected $_primary = 'id';
	
    /**
     * statics
     */
    public static $statics = array(
        'payment_status' => array(
            '0' => 'Unpaid',
            '1' => 'Paid',
        ),
        'status' => array(
            '0' => 'No Receipt',
            '1' => 'Receipt Created',
        ),
        'order_by' => array(
            'invoice_no desc' => 'Invoice No.',
            'due_date desc' => 'Due Date',
        ),
    );
}
