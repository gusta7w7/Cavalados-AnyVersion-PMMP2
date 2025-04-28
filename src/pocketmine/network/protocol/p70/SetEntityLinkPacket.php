<?php
namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;

class SetEntityLinkPacket extends DataPacket{
	const NETWORK_ID = Info::SET_ENTITY_LINK_PACKET;
	const TYPE_REMOVE = 0;
	const TYPE_RIDE = 1;
	const TYPE_PASSENGER = 2;

	public $from;
	public $to;
	public $type;

	public function decode(){

	}

	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
		$this->buffer .= Binary::writeLong($this->from);
		$this->buffer .= Binary::writeLong($this->to);
		$this->buffer .= chr($this->type);
	}

}
