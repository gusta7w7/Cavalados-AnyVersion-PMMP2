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

#ifndef COMPILE
use raklib\Binary;

use function count;
use function sort;

use const SORT_NUMERIC;

#endif

#include <rules/RakLibPacket.h>

abstract class AcknowledgePacket extends Packet
{
    /** @var int[] */
    public $packets = [];

    public function encode()
    {
        parent::encode();
        $payload = "";
        sort($this->packets, SORT_NUMERIC);
        $count = count($this->packets);
        $records = 0;

        if ($count > 0) {
            $pointer = 1;
            $start = $this->packets[0];
            $last = $this->packets[0];

            while ($pointer < $count) {
                $current = $this->packets[$pointer++];
                $diff = $current - $last;
                if ($diff === 1) {
                    $last = $current;
                } elseif ($diff > 1) {
                    // Forget about duplicated packets (bad queues?)
                    if ($start === $last) {
                        $payload .= "\x01";
                        $payload .= Binary::writeLTriad($start);
                        $start = $last = $current;
                    } else {
                        $payload .= "\x00";
                        $payload .= Binary::writeLTriad($start);
                        $payload .= Binary::writeLTriad($last);
                        $start = $last = $current;
                    }
                    ++$records;
                }
            }

            if ($start === $last) {
                $payload .= "\x01";
                $payload .= Binary::writeLTriad($start);
            } else {
                $payload .= "\x00";
                $payload .= Binary::writeLTriad($start);
                $payload .= Binary::writeLTriad($last);
            }
            ++$records;
        }

        $this->putShort($records);
        $this->buffer .= $payload;
    }

    public function decode()
    {
        parent::decode();
        $count = $this->getShort();
        $this->packets = [];
        $cnt = 0;
        for ($i = 0; $i < $count && !$this->feof() && $cnt < 4096; ++$i) {
            if ($this->getByte() === 0) {
                $start = $this->getLTriad();
                $end = $this->getLTriad();
                if (($end - $start) > 512) {
                    $end = $start + 512;
                }
                for ($c = $start; $c <= $end; ++$c) {
                    $this->packets[$cnt++] = $c;
                }
            } else {
                $this->packets[$cnt++] = $this->getLTriad();
            }
        }
    }

    public function clean()
    {
        $this->packets = [];
        return parent::clean();
    }
}
