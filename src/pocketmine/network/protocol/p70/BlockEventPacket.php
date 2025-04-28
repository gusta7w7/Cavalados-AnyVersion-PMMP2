<?php
namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;

class BlockEventPacket extends DataPacket{
	const NETWORK_ID = Info::BLOCK_EVENT_PACKET;

	public $x;
	public $y;
	public $z;
	public $case1;
	public $case2;

	public function decode(){

	}

	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
		$this->buffer .= pack("N", $this->x);
		$this->buffer .= pack("N", $this->y);
		$this->buffer .= pack("N", $this->z);
		$this->buffer .= pack("N", $this->case1);
		$this->buffer .= pack("N", $this->case2);
	}

}
