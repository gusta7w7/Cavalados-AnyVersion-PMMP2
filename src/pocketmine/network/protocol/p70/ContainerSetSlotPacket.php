<?php
namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;
use pocketmine\item\Item;

class ContainerSetSlotPacket extends DataPacket{
	const NETWORK_ID = Info::CONTAINER_SET_SLOT_PACKET;

	public $windowid;
	public $slot;
	public $hotbarSlot;
	/** @var Item */
	public $item;

	public function decode(){
		$this->windowid = ord($this->get(1));
		$this->slot = unpack("n", $this->get(2))[1];
		$this->hotbarSlot = unpack("n", $this->get(2))[1];
		$this->item = $this->getSlot();
	}

	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
		$this->buffer .= chr($this->windowid);
		$this->buffer .= pack("n", $this->slot);
		$this->buffer .= pack("n", $this->hotbarSlot);
		$this->putSlot($this->item);
	}

}
