<?php
namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;

class ChangeDimensionPacket extends DataPacket{
	const NETWORK_ID = Info::CHANGE_DIMENSION_PACKET;

	const NORMAL = 0;
	const NETHER = 1;

	public $dimension;

	public function decode(){

	}

	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
		$this->buffer .= chr($this->dimension);
		$this->buffer .= chr(0);
	}

}
