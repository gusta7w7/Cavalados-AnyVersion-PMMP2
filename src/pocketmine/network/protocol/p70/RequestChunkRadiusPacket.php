<?php

namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;

class RequestChunkRadiusPacket extends DataPacket{
	const NETWORK_ID = Info::REQUEST_CHUNK_RADIUS_PACKET;
	public $radius;

	public function decode(){
		$this->radius = $this->getInt();
	}

	public function encode(){
	}
}