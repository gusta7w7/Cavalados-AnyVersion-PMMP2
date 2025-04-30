<?php

namespace pocketmine\network\protocol\p70;

class ChunkRadiusUpdatePacket extends DataPacket
{
    const NETWORK_ID = Info::CHUNK_RADIUS_UPDATE_PACKET;
    public $radius;

    public function decode()
    {
    }

    public function encode()
    {
        $this->reset();
        $this->putInt($this->radius);
    }
}
