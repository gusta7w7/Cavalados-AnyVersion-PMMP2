<?php

namespace pocketmine\network\protocol\p70;


class MapInfoRequestPacket extends DataPacket {

	const NETWORK_ID = Info::MAP_INFO_REQUEST_PACKET;

	/** @var int */
	public $mapId;

	public function decode(){
		$this->mapId = $this->getLong();
	}

	public function encode(){
        $this->reset();
		$this->putLong($this->mapId);
	}

}