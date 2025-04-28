<?php
namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;

class AdventureSettingsPacket extends DataPacket{
	const NETWORK_ID = Info::ADVENTURE_SETTINGS_PACKET;

	public $flags;
	public $userPermission;
	public $globalPermission;

	public function decode(){

	}

	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
		$this->buffer .= pack("N", $this->flags);
                $this->buffer .= pack("N", $this->userPermission);
                $this->buffer .= pack("N", $this->globalPermission);
	}

}
