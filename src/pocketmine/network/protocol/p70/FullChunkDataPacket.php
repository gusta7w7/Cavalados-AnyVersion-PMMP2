<?php
namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;

class FullChunkDataPacket extends DataPacket{
	const NETWORK_ID = Info::FULL_CHUNK_DATA_PACKET;
	
	const ORDER_COLUMNS = 0;
	const ORDER_LAYERED = 1;

	public $chunkX;
	public $chunkZ;
	public $order = self::ORDER_COLUMNS;
	public $data;

	public function decode(){

	}

	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
        $this->buffer .= pack("N", $this->chunkX);
        $this->buffer .= pack("N", $this->chunkZ);
        $this->buffer .= chr($this->order);
		$this->buffer .= pack("N", \strlen($this->data));
		$this->buffer .= $this->data;
	}

}
