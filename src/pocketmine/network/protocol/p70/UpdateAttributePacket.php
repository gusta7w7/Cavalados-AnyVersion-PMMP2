<?php
namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;
use pocketmine\entity\Attribute;

class UpdateAttributePacket extends DataPacket{
	const NETWORK_ID = Info::UPDATE_ATTRIBUTES_PACKET;


	public $entityId;
	/** @var Attribute[] */
	public $entries;
	
	public $minValue;
	public $maxValue;
	public $name;
	public $value;

	public function decode(){

	}

	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;

		$this->buffer .= Binary::writeLong($this->entityId);

		$this->buffer .= pack("n", 1);

		$this->buffer .= (ENDIANNESS === 0 ? pack("f", $this->minValue) : strrev(pack("f", $this->minValue)));
		$this->buffer .= (ENDIANNESS === 0 ? pack("f", $this->maxValue) : strrev(pack("f", $this->maxValue)));
		$this->buffer .= (ENDIANNESS === 0 ? pack("f", $this->value) : strrev(pack("f", $this->value)));
		$this->putString($this->name);
	}

}
