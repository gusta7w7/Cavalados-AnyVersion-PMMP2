<?php

namespace pocketmine\network\protocol\p70;

use function chr;
use function pack;

class SetSpawnPositionPacket extends DataPacket
{
    const NETWORK_ID = Info::SET_SPAWN_POSITION_PACKET;

    public $x;
    public $y;
    public $z;

    public function decode()
    {

    }

    public function encode()
    {
        $this->buffer = chr(self::NETWORK_ID);
        $this->offset = 0;
        ;
        $this->buffer .= pack("N", $this->x);
        $this->buffer .= pack("N", $this->y);
        $this->buffer .= pack("N", $this->z);
    }
}
