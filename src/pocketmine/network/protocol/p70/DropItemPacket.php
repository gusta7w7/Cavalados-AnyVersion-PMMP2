<?php

namespace pocketmine\network\protocol\p70;

use function ord;

class DropItemPacket extends DataPacket
{
    const NETWORK_ID = Info::DROP_ITEM_PACKET;

    public $type;
    public $item;

    public function decode()
    {
        $this->type = ord($this->get(1));
        $this->item = $this->getSlot();
    }

    public function encode()
    {

    }
}
