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

namespace pocketmine\item;

use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;

class EnchantedGoldenApple extends Food
{
    public function __construct($meta = 0, $count = 1)
    {
        parent::__construct(self::ENCHANTED_GOLDEN_APPLE, $meta, $count, "Enchanted Golden Apple");
    }

    public function canBeConsumedBy(Entity $entity) : bool
    {
        return $entity instanceof Human && $this->canBeConsumed();
    }

    public function getFoodRestore() : int
    {
        return 4;
    }

    public function getSaturationRestore() : float
    {
        return 9.6;
    }

    public function getAdditionalEffects() : array
    {
        return [
            Effect::getEffect(Effect::REGENERATION)->setDuration(600)->setAmplifier(4),
            Effect::getEffect(Effect::ABSORPTION)->setDuration(2400)->setAmplifier(0),
            Effect::getEffect(Effect::DAMAGE_RESISTANCE)->setDuration(6000)->setAmplifier(0),
            Effect::getEffect(Effect::FIRE_RESISTANCE)->setDuration(6000)->setAmplifier(0),
        ];
    }
}
