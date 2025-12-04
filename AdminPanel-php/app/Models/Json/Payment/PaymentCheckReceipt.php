<?php
/**
 * Created by PhpStorm.
 * User: Waqar
 * Date: 16/05/2019
 * Time: 12:31 PM
 */

namespace App\Models\Json\Payment;


class PaymentCheckReceipt
{
    private $updated;
    var $same;
    var $payment_entity;
    var $old_receipt;
    var $new_receipt;
    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return mixed
     */
    public function getSame()
    {
        return $this->same;
    }

    /**
     * @param mixed $same
     */
    public function setSame($same)
    {
        $this->same = $same;
    }

    /**
     * @return mixed
     */
    public function getPaymentEntity()
    {
        return $this->payment_entity;
    }

    /**
     * @param mixed $payment_entity
     */
    public function setPaymentEntity($payment_entity)
    {
        $this->payment_entity = $payment_entity;
    }

    /**
     * @return mixed
     */
    public function getOldReceipt()
    {
        return $this->old_receipt;
    }

    /**
     * @param mixed $old_receipt
     */
    public function setOldReceipt($old_receipt)
    {
        $this->old_receipt = $old_receipt;
    }

    /**
     * @return mixed
     */
    public function getNewReceipt()
    {
        return $this->new_receipt;
    }

    /**
     * @param mixed $new_receipt
     */
    public function setNewReceipt($new_receipt)
    {
        $this->new_receipt = $new_receipt;
    }


}