<?php

/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 7/06/2017
 * Time: 23:01
 */
class Transaction
{

    public $version;
    public $inCounter;
    public $inputs;
    public $outCounter;
    public $outputs;
    public $lockTime;

    public function __construct($data, &$position)
    {

        $this->version = BlockUtils::hexToNumber(substr($data, $position, 4));
        $position += 4;

        $this->inCounter = BlockUtils::varInt($data, $position);
        BlockUtils::Log("Incounter " . $this->inCounter);

        $this->inputs = array();
        for ($i=0; $i<$this->inCounter;$i++) {
            $this->inputs[] = new TransactionInput($data, $position);
        }

        $this->outCounter = BlockUtils::varInt($data, $position);

        BlockUtils::Log("Outcounter " . $this->outCounter . " @ " . dechex($position + 8));

        $this->outputs = array();
        for ($i=0; $i<$this->outCounter;$i++) {
            $this->outputs[] = new TransactionOutput($data, $position);
        }

        $this->lockTime = BlockUtils::hexToNumber(substr($data, $position, 4));
        $position += 4;

    }

}