<?php
namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;

class ContainerOpenPacket extends DataPacket{
	const NETWORK_ID = Info::CONTAINER_OPEN_PACKET;

	public $windowid;
	public $type;
	public $slots;
	public $x;
	public $y;
	public $z;
	public $entityId = -1;

	public function decode(){

	}

	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
		$this->buffer .= chr($this->windowid);
		$this->buffer .= chr($this->type);
		$this->buffer .= pack("n", $this->slots);
		$this->buffer .= pack("N", $this->x);
		$this->buffer .= pack("N", $this->y);
		$this->buffer .= pack("N", $this->z);
		if($this->entityId != -1){
			$this->buffer .= Binary::writeLong($this->entityId);
		}
	}

}
