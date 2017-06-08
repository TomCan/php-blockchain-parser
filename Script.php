<?php

/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 8/06/2017
 * Time: 21:19
 */
class Script
{

    // data pushing opcodes
    const OP_0 = 0x0;
    const OP_1 = 0x51;
    const OP_16 = 0x60;
    const OP_PUSHDATA_MIN = 0x01;
    const OP_PUSHDATA_MAX = 0x4b;
    const OP_PUSHDATA1 = 0x4c;
    const OP_PUSHDATA2 = 0x4d;
    const OP_PUSHDATA4 = 0x4e;
    const OP_1NEGATE = 0x4f;

    // flow control statements
    const OP_NOP = 0x61;
    const OP_IF = 0x63;
    const OP_NOTIF = 0x64;
    const OP_ELSE = 0x67;
    const OP_ENDIF = 0x68;
    const OP_VERIFY = 0x69;
    const OP_RETURN = 0x6a;

    // stack opcodes
    const OP_TOALTSTACK = 0x6b;
    const OP_FROMALTSTACK = 0x6c;
    const OP_IFDUP = 0x73;
    const OP_DEPTH = 0x74;
    const OP_DROP = 0x75;
    const OP_DUP = 0x76;
    const OP_NIP = 0x77;
    const OP_OVER = 0x78;
    const OP_PICK = 0x79;
    const OP_ROLL = 0x7a;
    const OP_ROT = 0x7b;
    const OP_SWAP = 0x7c;
    const OP_TUCK = 0x7d;
    const OP_2DROP = 0x6d;
    const OP_2DUP = 0x6e;
    const OP_3DUP = 0x6f;
    const OP_2OVER = 0x70;
    const OP_2ROT = 0x71;
    const OP_2SWAP = 0x72;

    // splice
    const OP_SIZE = 0x82;

    // bitwise logic
    const OP_EQUAL = 0x87;
    const OP_EQUALVERIFY = 0x88;

    // arithmetic
    const OP_1ADD = 0x8b;
    const OP_1SUB = 0x8c;
    const OP_NEGATE = 0x8f;
    const OP_ABS = 0x90;
    const OP_NOT = 0x91;
    const OP_0NOTEQUAL = 0x92;
    const OP_ADD = 0x93;
    const OP_SUB = 0x94;
    const OP_BOOLAND = 0x9a;
    const OP_BOOLOR = 0x9b;
    const OP_NUMEQUAL = 0x9c;
    const OP_NUMEQUALVERIFY = 0x9d;
    const OP_NUMNOTEQUAL = 0x9e;
    const OP_LESSTHAN = 0x9f;
    const OP_GREATERTHAN = 0xa0;
    const OP_LESSTHANOREQUAL = 0xa1;
    const OP_GREATERTHANOREQUAL = 0xa2;
    const OP_MIN = 0xa3;
    const OP_MAX = 0xa4;
    const OP_WITHIN = 0xa5;

    // crypto
    const OP_RIPEMD160 = 0xa6;
    const OP_SHA1 = 0xa7;
    const OP_SHA256 = 0xa8;
    const OP_HASH160 = 0xa9;
    const OP_HASH256 = 0xaa;
    const OP_CODESEPARATOR = 0xab;
    const OP_CHECKSIG = 0xac;
    const OP_CHECKSIGVERIFY = 0xad;
    const OP_CHECKMULTISIG = 0xae;
    const OP_CHECKMULTISIGVERIFY = 0xaf;

    // locktime
    const OP_CHECKLOCKTIMEVERIFY  = 0xb1;
    const OP_CHECKSEQUENCEVERIFY  = 0xb2;

    // reserved
    const OP_RESERVED = 0x50;
    const OP_VER = 0x62;
    const OP_VERIF = 0x65;
    const OP_VERNOTIF = 0x66;
    const OP_RESERVED1 = 0x89;
    const OP_RESERVED2 = 0x8a;

    // ignored
    const OP_NOP1 = 0xb0;
    const OP_NOP4 = 0xb3;
    const OP_NOP10 = 0xb9;


    public $pubkeys;
    public $stack;

    public function __construct()
    {
    }

    public function parse($data) {

        BlockUtils::Log("script: " . bin2hex($data));

        $this->stack = array();
        $this->pubkeys = array();

        $position = 0;
        while ($position < strlen($data)) {

            $opcode = ord($data{$position});

            BlockUtils::Log("$position: " . $opcode . " 0x" . dechex($opcode));

            // check ranges first
            $toPush = null;
            if ($opcode >= self::OP_1 && $opcode <= self::OP_16) {
                // push X bytes on the stack
                $toPush = 1 + $opcode - self::OP_1;
            } elseif ($opcode >= self::OP_PUSHDATA_MIN && $opcode <= self::OP_PUSHDATA_MAX) {
                $toPush = $opcode;
            } elseif ($opcode = self::OP_PUSHDATA1) {
                $toPush = BlockUtils::hexToNumber(substr($data, $position + 1, 1));
            } elseif ($opcode = self::OP_PUSHDATA2) {
                $toPush = BlockUtils::hexToNumber(substr($data, $position + 1, 2));
            } elseif ($opcode = self::OP_PUSHDATA4) {
                $toPush = BlockUtils::hexToNumber(substr($data, $position + 1, 4));
            }

            if ($toPush !== null) {

                // push bytes on stack
                BlockUtils::Log("Push $toPush: " . bin2hex(substr($data, $position + 1, $toPush)));
                $this->stack[] = substr($data, $position + 1, $toPush);
                $position += $toPush;

            } else {

                switch (ord($data{$position})) {

                    /*
                     * other pushing commands
                     */

                    case self::OP_0:
                        $this->stack[] = "";
                        break;
                    case self::OP_1:
                        $this->stack[] = 1;
                        break;
                    case self::OP_1NEGATE:
                        $this->stack[] = -1;
                        break;

                        /*
                         * for now, just try to extract hashes
                         *
                         * in most cases, this is the case if a HASH OP_ is followed by a pubkey and OP_EQUALVERIFY
                         *
                         */
                    case self::OP_RIPEMD160:
                    case self::OP_SHA1:
                    case self::OP_SHA256:
                    case self::OP_HASH160:
                    case self::OP_HASH256:
                        BlockUtils::Log("HASH");
                        $this->stack[] = "HASH";
                        break;
                    case self::OP_EQUALVERIFY:
                        BlockUtils::Log("EQUALVERIFY");
                        var_dump($this->stack);
                        if ($this->stack[count($this->stack) - 2] == "HASH") {
                            // assume last value on stack is the pubkey
                            $this->pubkeys[] = $this->stack[count($this->stack) - 1];
                        } elseif ($this->stack[count($this->stack) - 1] == "HASH") {
                            // assume second to last value on stack is the pubkey
                            $this->pubkeys[] = $this->stack[count($this->stack) - 2];
                        }
                        break;

                }

            }

            // increment position
            $position++;
        }

    }

}