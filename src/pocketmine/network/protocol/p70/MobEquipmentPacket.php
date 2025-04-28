<?php
namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;

class MobEquipmentPacket extends DataPacket{
	const NETWORK_ID = Info::MOB_EQUIPMENT_PACKET;

	public $eid;
	public $item;
	public $slot;
	public $selectedSlot;

	public function decode(){
		$this->eid = Binary::readLong($this->get(8));
		$this->item = $this->getSlot();
		$this->slot = ord($this->get(1));
		$this->selectedSlot = ord($this->get(1));
	}

	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
		$this->buffer .= Binary::writeLong($this->eid);
		$this->putSlot($this->item);
		$this->buffer .= chr($this->slot);
		$this->buffer .= chr($this->selectedSlot);
	}

}
