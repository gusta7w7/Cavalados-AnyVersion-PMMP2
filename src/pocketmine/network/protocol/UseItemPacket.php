<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace pocketmine\network\protocol;

#include <rules/DataPacket.h>


class UseItemPacket extends DataPacket
{
    const NETWORK_ID = Info::USE_ITEM_PACKET;

    public $x;
    public $y;
    public $z;
    public $face;
    public $item;
    public $fx;
    public $fy;
    public $fz;
    public $posX;
    public $posY;
    public $posZ;
    public $slot;

    public function decode()
    {
        $this->x = $this->getInt();
        $this->y = $this->getInt();
        $this->z = $this->getInt();
        $this->face = $this->getByte();
        $this->fx = $this->getFloat();
        $this->fy = $this->getFloat();
        $this->fz = $this->getFloat();
        $this->posX = $this->getFloat();
        $this->posY = $this->getFloat();
        $this->posZ = $this->getFloat();
        $this->slot = $this->getInt();
        $this->item = $this->getSlot();
    }

    public function encode()
    {

    }
}
