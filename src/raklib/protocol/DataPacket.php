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

use function strlen;
use function substr;

#include <rules/RakLibPacket.h>

abstract class DataPacket extends Packet
{
    /** @var EncapsulatedPacket[] */
    public $packets = [];

    public $seqNumber;

    public function encode()
    {
        parent::encode();
        $this->putLTriad($this->seqNumber);
        foreach ($this->packets as $packet) {
            $this->put($packet instanceof EncapsulatedPacket ? $packet->toBinary() : (string) $packet);
        }
    }

    public function length()
    {
        $length = 4;
        foreach ($this->packets as $packet) {
            $length += $packet instanceof EncapsulatedPacket ? $packet->getTotalLength() : strlen($packet);
        }

        return $length;
    }

    public function decode()
    {
        parent::decode();
        $this->seqNumber = $this->getLTriad();

        while (!$this->feof()) {
            $offset = 0;
            $data = substr($this->buffer, $this->offset);
            $packet = EncapsulatedPacket::fromBinary($data, false, $offset);
            $this->offset += $offset;
            if (strlen($packet->buffer) === 0) {
                break;
            }
            $this->packets[] = $packet;
        }
    }

    public function clean()
    {
        $this->packets = [];
        $this->seqNumber = null;
        return parent::clean();
    }
}
