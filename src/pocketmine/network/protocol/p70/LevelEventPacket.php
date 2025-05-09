<?php

namespace pocketmine\network\protocol\p70;

use function chr;
use function pack;
use function strrev;

class LevelEventPacket extends DataPacket
{
    const NETWORK_ID = Info::LEVEL_EVENT_PACKET;

    const EVENT_SOUND_CLICK = 1000;
    const EVENT_SOUND_CLICK_FAIL = 1001;
    const EVENT_SOUND_SHOOT = 1002;
    const EVENT_SOUND_DOOR = 1003;
    const EVENT_SOUND_FIZZ = 1004;

    const EVENT_SOUND_GHAST = 1007;
    const EVENT_SOUND_GHAST_SHOOT = 1008;
    const EVENT_SOUND_BLAZE_SHOOT = 1009;

    const EVENT_SOUND_DOOR_BUMP = 1010;
    const EVENT_SOUND_DOOR_CRASH = 1012;

    const EVENT_SOUND_BAT_FLY = 1015;
    const EVENT_SOUND_ZOMBIE_INFECT = 1016;
    const EVENT_SOUND_ZOMBIE_HEAL = 1017;
    const EVENT_SOUND_ENDERMAN_TELEPORT = 1018;

    const EVENT_SOUND_ANVIL_BREAK = 1020;
    const EVENT_SOUND_ANVIL_USE = 1021;
    const EVENT_SOUND_ANVIL_FALL = 1022;

    const EVENT_PARTICLE_SHOOT = 2000;
    const EVENT_PARTICLE_DESTROY = 2001;
    const EVENT_PARTICLE_SPLASH = 2002;
    const EVENT_PARTICLE_EYE_DESPAWN = 2003;
    const EVENT_PARTICLE_SPAWN = 2004;

    const EVENT_START_RAIN = 3001;
    const EVENT_START_THUNDER = 3002;
    const EVENT_STOP_RAIN = 3003;
    const EVENT_STOP_THUNDER = 3004;

    const EVENT_SOUND_BUTTON_CLICK = 3500;
    const EVENT_SOUND_BUTTON_RETURN = 3500;
    const EVENT_SOUND_EXPLODE = 3501;
    const EVENT_SOUND_SPELL = 3504;
    const EVENT_SOUND_SPLASH = 3506;
    const EVENT_SOUND_GRAY_SPLASH = 3507;

    const EVENT_SET_DATA = 4000;

    const EVENT_PLAYERS_SLEEPING = 9800;

    const EVENT_ADD_PARTICLE_MASK = 0x4000;

    public $evid;
    public $x;
    public $y;
    public $z;
    public $data;

    public function decode()
    {

    }

    public function encode()
    {
        $this->buffer = chr(self::NETWORK_ID);
        $this->offset = 0;
        ;
        $this->buffer .= pack("n", $this->evid);
        $this->buffer .= (ENDIANNESS === 0 ? pack("f", $this->x) : strrev(pack("f", $this->x)));
        $this->buffer .= (ENDIANNESS === 0 ? pack("f", $this->y) : strrev(pack("f", $this->y)));
        $this->buffer .= (ENDIANNESS === 0 ? pack("f", $this->z) : strrev(pack("f", $this->z)));
        $this->buffer .= pack("N", $this->data);
    }
}
