<?php
namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;

class CraftingEventPacket extends DataPacket{
	const NETWORK_ID = Info::CRAFTING_EVENT_PACKET;

	public $windowId;
	public $type;
	public $id;
	public $input = [];
	public $output = [];

	public function clean(){
		$this->input = [];
		$this->output = [];
		return parent::clean();
	}

	public function decode(){
		$this->windowId = ord($this->get(1));
		$this->type = (PHP_INT_SIZE === 8 ? unpack("N", $this->get(4))[1] << 32 >> 32 : unpack("N", $this->get(4))[1]);
		$this->id = $this->getUUID();

		$size = (PHP_INT_SIZE === 8 ? unpack("N", $this->get(4))[1] << 32 >> 32 : unpack("N", $this->get(4))[1]);
		for($i = 0; $i < $size and $i < 128; ++$i){
			$this->input[] = $this->getSlot();
		}

		$size = (PHP_INT_SIZE === 8 ? unpack("N", $this->get(4))[1] << 32 >> 32 : unpack("N", $this->get(4))[1]);
		for($i = 0; $i < $size and $i < 128; ++$i){
			$this->output[] = $this->getSlot();
		}
	}

	public function encode(){

	}

}
