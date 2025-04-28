<?php
namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;

class SetSpawnPositionPacket extends DataPacket{
	const NETWORK_ID = Info::SET_SPAWN_POSITION_PACKET;

	public $x;
	public $y;
	public $z;

	public function decode(){

	}

	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
		$this->buffer .= pack("N", $this->x);
		$this->buffer .= pack("N", $this->y);
		$this->buffer .= pack("N", $this->z);
	}

}
