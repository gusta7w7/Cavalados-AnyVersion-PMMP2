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


class SetEntityLinkPacket extends DataPacket
{
    const NETWORK_ID = Info::SET_ENTITY_LINK_PACKET;

    const TYPE_REMOVE = 0;
    const TYPE_RIDE = 1;
    const TYPE_PASSENGER = 2;


    public $from;
    public $to;
    public $type;

    public function decode()
    {

    }

    public function encode()
    {
        $this->reset();
        $this->putLong($this->from);
        $this->putLong($this->to);
        $this->putByte($this->type);
    }
}
