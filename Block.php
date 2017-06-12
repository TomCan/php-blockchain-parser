<?php

/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 7/06/2017
 * Time: 22:02
 */
class Block
{

    public $header;
    public $transactions;

    public $data;

    public function __construct()
    {
    }

    public function fromFilePointer($fp) {

        $magic = fread($fp, 4);

        if (bin2hex($magic) != "f9beb4d9") {
            throw new Exception("Invalid magic number");
        }

        $blocksize = BlockUtils::hexToNumber(fread($fp, 4));

        $this->data = fread($fp, $blocksize);

        $this->header = new BlockHeader();
        $this->header->fromRawData( substr($this->data, 0, 80) );

        $position = 80;
        $transactionCounter = BlockUtils::varInt($this->data, $position);

        $this->transactions = array();
        for ($i = 0; $i < $transactionCounter; $i++) {
            $this->transactions[] = new Transaction($this->data, $position);
        }

    }

}
