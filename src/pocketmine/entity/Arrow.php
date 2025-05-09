<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace pocketmine\entity;

use pocketmine\item\Potion;
use pocketmine\level\format\FullChunk;
use pocketmine\level\particle\CriticalParticle;
use pocketmine\level\particle\MobSpellParticle;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;

use function mt_rand;

class Arrow extends Projectile
{
    const NETWORK_ID = 80;

    public $width = 0.5;
    public $length = 0.5;
    public $height = 0.5;

    protected $gravity = 0.05;
    protected $drag = 0.01;

    protected $damage = 2;

    protected $isCritical;
    protected $potionId;

    public function __construct(FullChunk $chunk, CompoundTag $nbt, Entity $shootingEntity = null, $critical = false)
    {
        $this->isCritical = (bool) $critical;
        if (!isset($nbt->Potion)) {
            $nbt->Potion = new ShortTag("Potion", 0);
        }
        parent::__construct($chunk, $nbt, $shootingEntity);
        $this->potionId = $this->namedtag["Potion"];
    }

    public function getPotionId() : int
    {
        return $this->potionId;
    }

    public function onUpdate($currentTick)
    {
        if ($this->closed) {
            return false;
        }

        $this->timings->startTiming();

        $hasUpdate = parent::onUpdate($currentTick);

        if (!$this->hadCollision && $this->isCritical) {
            $this->level->addParticle(new CriticalParticle($this->add(
                $this->width / 2 + mt_rand(-100, 100) / 500,
                $this->height / 2 + mt_rand(-100, 100) / 500,
                $this->width / 2 + mt_rand(-100, 100) / 500
            )));
        } elseif ($this->onGround) {
            $this->isCritical = false;
        }

        if ($this->potionId != 0) {
            if (!$this->onGround || ($this->onGround && ($currentTick % 4) == 0)) {
                $color = Potion::getColor($this->potionId - 1);
                $this->level->addParticle(new MobSpellParticle($this->add(
                    $this->width / 2 + mt_rand(-100, 100) / 500,
                    $this->height / 2 + mt_rand(-100, 100) / 500,
                    $this->width / 2 + mt_rand(-100, 100) / 500
                ), $color[0], $color[1], $color[2]));
            }
            $hasUpdate = true;
        }

        if ($this->age > 1200) {
            $this->kill();
            $hasUpdate = true;
        }

        $this->timings->stopTiming();

        return $hasUpdate;
    }

    public function spawnTo(Player $player)
    {
        $pk = new AddEntityPacket();
        $pk->type = Arrow::NETWORK_ID;
        $pk->eid = $this->getId();
        $pk->x = $this->x;
        $pk->y = $this->y;
        $pk->z = $this->z;
        $pk->speedX = $this->motionX;
        $pk->speedY = $this->motionY;
        $pk->speedZ = $this->motionZ;
        $pk->metadata = $this->dataProperties;
        $player->dataPacket($pk);

        parent::spawnTo($player);
    }
}
