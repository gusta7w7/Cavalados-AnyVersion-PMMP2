<?php
namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;

class RemovePlayerPacket extends DataPacket{
	const NETWORK_ID = Info::REMOVE_PLAYER_PACKET;

	public $eid;
	public $clientId;

	public function decode(){

	}

	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
		$this->buffer .= Binary::writeLong($this->eid);
		$this->putUUID($this->clientId);
	}

}
