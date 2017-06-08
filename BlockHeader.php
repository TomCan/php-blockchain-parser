<?php

/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 7/06/2017
 * Time: 22:25
 */
class BlockHeader
{

    public $version;
    public $hashPrevBlock;
    public $hashMerkleRoot;
    public $time;
    public $bits;
    public $nonce;

    public function __construct() {
    }

    public function fromRawData($data) {

        if (strlen($data) != 80) {
            throw new Exception("Invalid header data");
        }

        $version = BlockUtils::hexToNumber(substr($data, 0, 4));
        $hashPrevBlock = substr($data, 4, 32);
        $hashMerkleRoot = substr($data, 36, 32);

        $time = BlockUtils::hexToNumber(substr($data, 40, 4));
        $bits = BlockUtils::hexToNumber(substr($data, 44, 4));
        $nonce = BlockUtils::hexToNumber(substr($data, 0, 4));

    }

}