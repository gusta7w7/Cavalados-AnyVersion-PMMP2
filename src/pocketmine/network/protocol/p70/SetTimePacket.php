<?php
namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;

class SetTimePacket extends DataPacket{
	const NETWORK_ID = Info::SET_TIME_PACKET;

	public $time;
	public $started = \true;

	public function decode(){

	}

	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
		$this->buffer .= pack("N", (int) $this->time);
		$this->buffer .= chr($this->started ? 1 : 0);
	}

}
