<?php

/*
 *  ___	  _   _	_ _
 * | _ \__ _| |_| |  (_) |__
 * |   / _` | / / |__| | '_ \
 * |_|_\__,_|_\_\____|_|_.__/
 *
 * This project is not affiliated with Jenkins Software LLC nor RakNet.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Glowstone (iNotFlying)
 * @link vk.com/inotflying
 *
 */

namespace raklib\protocol;

#include <rules/RakLibPacket.h>

use raklib\RakLib;

use function chr;
use function str_repeat;
use function strlen;

class OPEN_CONNECTION_REQUEST_1 extends Packet
{
    public static $ID = 0x05;

    public $protocol = RakLib::PROTOCOL;
    public $mtuSize;

    public function encode()
    {
        parent::encode();
        $this->put(RakLib::MAGIC);
        $this->putByte($this->protocol);
        $this->put(str_repeat(chr(0x00), $this->mtuSize - 18));
    }

    public function decode()
    {
        parent::decode();
        $this->offset += 16; // Magic
        $this->protocol = $this->getByte();
        $this->mtuSize = strlen($this->get(true)) + 18;
    }
}
