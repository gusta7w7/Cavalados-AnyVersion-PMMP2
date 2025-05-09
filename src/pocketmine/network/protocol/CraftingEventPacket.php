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


class CraftingEventPacket extends DataPacket
{
    const NETWORK_ID = Info::CRAFTING_EVENT_PACKET;

    public $windowId;
    public $type;
    public $id;
    public $input = [];
    public $output = [];

    public function clean()
    {
        $this->input = [];
        $this->output = [];
        return parent::clean();
    }

    public function decode()
    {
        $this->windowId = $this->getByte();
        $this->type = $this->getInt();
        $this->id = $this->getUUID();

        $size = $this->getInt();
        for ($i = 0; $i < $size && $i < 128; ++$i) {
            $this->input[] = $this->getSlot();
        }

        $size = $this->getInt();
        for ($i = 0; $i < $size && $i < 128; ++$i) {
            $this->output[] = $this->getSlot();
        }
    }

    public function encode()
    {

    }
}
