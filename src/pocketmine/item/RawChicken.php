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

use function mt_rand;

class RawChicken extends Food
{
    public function __construct($meta = 0, $count = 1)
    {
        parent::__construct(self::RAW_CHICKEN, $meta, $count, "Raw Chicken");
    }

    public function getFoodRestore() : int
    {
        return 2;
    }

    public function getSaturationRestore() : float
    {
        return 1.2;
    }

    public function getAdditionalEffects() : array
    {
        $chance = mt_rand(0, 100);
        if ($chance >= 70) {
            return [Effect::getEffect(Effect::HUNGER)->setDuration(30 * 20)];
        } else {
            return [];
        }
    }
}
