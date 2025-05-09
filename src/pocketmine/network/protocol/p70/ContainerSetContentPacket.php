<?php

namespace pocketmine\network\protocol\p70;

use function chr;
use function count;
use function ord;
use function pack;
use function unpack;

use const PHP_INT_SIZE;

class ContainerSetContentPacket extends DataPacket
{
    const NETWORK_ID = Info::CONTAINER_SET_CONTENT_PACKET;

    const SPECIAL_INVENTORY = 0;
    const SPECIAL_ARMOR = 0x78;
    const SPECIAL_CREATIVE = 0x79;

    public $windowid;
    public $slots = [];
    public $hotbar = [];

    public function clean()
    {
        $this->slots = [];
        $this->hotbar = [];
        return parent::clean();
    }

    public function decode()
    {
        $this->windowid = ord($this->get(1));
        $count = unpack("n", $this->get(2))[1];
        for ($s = 0; $s < $count && !$this->feof(); ++$s) {
            $this->slots[$s] = $this->getSlot();
        }
        if ($this->windowid === self::SPECIAL_INVENTORY) {
            $count = unpack("n", $this->get(2))[1];
            for ($s = 0; $s < $count && !$this->feof(); ++$s) {
                $this->hotbar[$s] = (PHP_INT_SIZE === 8 ? unpack("N", $this->get(4))[1] << 32 >> 32 : unpack("N", $this->get(4))[1]);
            }
        }
    }

    public function encode()
    {
        $this->buffer = chr(self::NETWORK_ID);
        $this->offset = 0;
        ;
        $this->buffer .= chr($this->windowid);
        $this->buffer .= pack("n", count($this->slots));
        foreach ($this->slots as $slot) {
            $this->putSlot($slot);
        }
        if ($this->windowid === self::SPECIAL_INVENTORY && count($this->hotbar) > 0) {
            $this->buffer .= pack("n", count($this->hotbar));
            foreach ($this->hotbar as $slot) {
                $this->buffer .= pack("N", $slot);
            }
        } else {
            $this->buffer .= pack("n", 0);
        }
    }
}
