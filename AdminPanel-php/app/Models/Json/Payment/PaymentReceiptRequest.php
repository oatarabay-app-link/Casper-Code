<?php
/**
 * Created by PhpStorm.
 * User: Waqar
 * Date: 16/05/2019
 * Time: 12:37 PM
 */

namespace App\Models\Json\Payment;


class PaymentReceiptRequest
{
private $receipt;
private $update;

    /**
     * @return mixed
     */
    public function getReceipt()
    {
        return $this->receipt;
    }

    /**
     * @param mixed $receipt
     */
    public function setReceipt($receipt)
    {
        $this->receipt = $receipt;
    }

    /**
     * @return mixed
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * @param mixed $update
     */
    public function setUpdate($update)
    {
        $this->update = $update;
    }

}