<?php
namespace pocketmine\network\protocol\p70;

use pocketmine\utils\p70\Binary;

class TextPacket extends DataPacket{
	const NETWORK_ID = Info::TEXT_PACKET;

	const TYPE_RAW = 0;
	const TYPE_CHAT = 1;
	const TYPE_TRANSLATION = 2;
	const TYPE_POPUP = 3;
	const TYPE_TIP = 4;
	const TYPE_SYSTEM = 5;

	public $type;
	public $source = "";
	public $message;
	public $parameters = [];

	public function decode(){
		$this->type = ord($this->get(1));
		switch($this->type){
			case self::TYPE_POPUP:
            case self::TYPE_TIP: //0.14.2 fix
			case self::TYPE_CHAT:
				$this->source = $this->getString();
			case self::TYPE_RAW:
			case self::TYPE_SYSTEM:
				$this->message = $this->getString();
				break;

			case self::TYPE_TRANSLATION:
				$this->message = $this->getString();
				$count = ord($this->get(1));
				for($i = 0; $i < $count; ++$i){
					$this->parameters[] = $this->getString();
				}
		}
	}

	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
        if($this->type == self::TYPE_TIP){
            $this->buffer .= chr(self::TYPE_POPUP);
        }else{
            $this->buffer .= chr($this->type);
        }
		switch($this->type){
			case self::TYPE_POPUP:
            case self::TYPE_TIP: //0.14.2 fix
			case self::TYPE_CHAT:
				$this->putString($this->source);
			case self::TYPE_RAW:
			case self::TYPE_SYSTEM:
				$this->putString($this->message);
				break;

			case self::TYPE_TRANSLATION:
				$this->putString($this->message);
				$this->buffer .= chr(\count($this->parameters));
				foreach($this->parameters as $p){
					$this->putString($p);
				}
		}
	}

}
