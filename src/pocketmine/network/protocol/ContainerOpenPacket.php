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


class ContainerOpenPacket extends DataPacket
{
    const NETWORK_ID = Info::CONTAINER_OPEN_PACKET;

    public $windowid;
    public $type;
    public $slots;
    public $x;
    public $y;
    public $z;
    public $entityId = -1;

    public function decode()
    {

    }

    public function encode()
    {
        $this->reset();
        $this->putByte($this->windowid);
        $this->putByte($this->type);
        $this->putShort($this->slots);
        $this->putInt($this->x);
        $this->putInt($this->y);
        $this->putInt($this->z);
        $this->putLong($this->entityId);
    }
}
