<?php

namespace TomCan\Blockchain;

class TransactionOutput
{

    public $value;
    public $txoutScriptLength;
    public $txoutScript;

    public function __construct($data, &$position)
    {

        BlockUtils::Log("Constructing txout @ $position " . dechex($position + 8));

        $this->value = BlockUtils::hexToNumber(substr($data, $position, 8));
        $position += 8;

        $this->txoutScriptLength = BlockUtils::varInt($data, $position);

        BlockUtils::Log($this->txoutScriptLength);

        if ($this->txoutScriptLength > 0) {
            $this->txoutScript = substr($data, $position, $this->txoutScriptLength);
            $position += $this->txoutScriptLength;
        }

        BlockUtils::Log($this->txoutScript);

    }

}