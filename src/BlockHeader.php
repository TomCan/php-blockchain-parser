<?php

namespace TomCan\Blockchain;

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

        $this->version = BlockUtils::hexToNumber(substr($data, 0, 4));
        $this->hashPrevBlock = substr($data, 4, 32);
        $this->hashMerkleRoot = substr($data, 36, 32);

        $this->time = BlockUtils::hexToNumber(substr($data, 40, 4));
        $this->bits = BlockUtils::hexToNumber(substr($data, 44, 4));
        $this->nonce = BlockUtils::hexToNumber(substr($data, 0, 4));

    }

}
