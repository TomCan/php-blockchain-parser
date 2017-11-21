<?php

namespace TomCan\Blockchain;

class TransactionInput
{

    public $previousTransactionHash;
    public $previousTxoutIndex;
    public $txinScriptLength;
    public $txinScript;
    public $sequenceNumber;

    public function __construct($data, &$position)
    {

        BlockUtils::Log("Constructing txin @ $position " . dechex($position + 8));

        $this->previousTransactionHash = substr($data, $position, 32);
        $position += 32;

        BlockUtils::Log(bin2hex($this->previousTransactionHash));

        $this->previousTxoutIndex = BlockUtils::hexToNumber(substr($data, $position, 4));
        $position += 4;

        BlockUtils::Log($this->previousTxoutIndex);

        $this->txinScriptLength = BlockUtils::varInt($data, $position);

        BlockUtils::Log($this->txinScriptLength);

        if ($this->txinScriptLength > 0) {
            $this->txinScript = substr($data, $position, $this->txinScriptLength);
            $position += $this->txinScriptLength;
        }

        BlockUtils::Log($this->txinScript);

        $this->sequenceNumber = BlockUtils::hexToNumber(substr($data, $position, 4));
        $position += 4;

        BlockUtils::Log($this->sequenceNumber);

    }

}