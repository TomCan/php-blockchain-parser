<?php

/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 7/06/2017
 * Time: 22:02
 */
class Block
{

    public function __construct()
    {
    }

    public function fromFilePointer($fp) {

        $magic = fread($fp, 4);

        if (bin2hex($magic) != "f9beb4d9") {
            throw new Exception("Invalid magic number");
        }

        $blocksize = BlockUtils::hexToNumber(fread($fp, 4));

        $data = fread($fp, $blocksize);

        $header = new BlockHeader();
        $header->fromRawData( substr($data, 0, 80) );

        $position = 80;
        $transactionCounter = BlockUtils::varInt($data, $position);

        for ($i = 0; $i < $transactionCounter; $i++) {
            $transaction = new Transaction($data, $position);
            var_dump($transaction);
        }

    }

}