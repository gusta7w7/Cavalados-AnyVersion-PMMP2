<?php

namespace pocketmine\network\protocol\p70;

use function chr;
use function pack;
use function strlen;
use function unpack;

use const PHP_INT_SIZE;

class BatchPacket extends DataPacket
{
    const NETWORK_ID = Info::BATCH_PACKET;

    public $payload;

    public function decode()
    {
        $size = (PHP_INT_SIZE === 8 ? unpack("N", $this->get(4))[1] << 32 >> 32 : unpack("N", $this->get(4))[1]);
        $this->payload = $this->get($size);
    }

    public function encode()
    {
        $this->buffer = chr(self::NETWORK_ID);
        $this->offset = 0;
        ;
        $this->buffer .= pack("N", strlen($this->payload));
        $this->buffer .= $this->payload;
    }
}
