<?php
namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;

class SetPlayerGameTypePacket extends DataPacket {

	const NETWORK_ID = Info::SET_PLAYER_GAMETYPE_PACKET;

	public $gamemode;

	public function decode(){

	}

	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
		$this->buffer .= pack("N", $this->gamemode);
	}
}
