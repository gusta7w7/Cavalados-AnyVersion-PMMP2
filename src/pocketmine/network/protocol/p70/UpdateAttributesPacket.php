<?php
namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;
use pocketmine\entity\Attribute;

class UpdateAttributesPacket extends DataPacket{

	const NETWORK_ID = Info::UPDATE_ATTRIBUTES_PACKET;

	public $entityId;

	/** @var Attribute[] */
	public $entries = [];

	public function decode(){
	}

	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
		$this->buffer .= Binary::writeLong($this->entityId);
		$this->buffer .= pack("n", \count($this->entries));
		foreach($this->entries as $entry){
			$this->buffer .= (ENDIANNESS === 0 ? pack("f", $entry->getMinValue()) : strrev(pack("f", $entry->getMinValue())));
			$this->buffer .= (ENDIANNESS === 0 ? pack("f", $entry->getMaxValue()) : strrev(pack("f", $entry->getMaxValue())));
			$this->buffer .= (ENDIANNESS === 0 ? pack("f", $entry->getValue()) : strrev(pack("f", $entry->getValue())));
			$this->putString($entry->getName());
		}
	}
}
