<?php

/*
 *
 *  _____   _____   __   _   _   _____  __    __  _____
 * /  ___| | ____| |  \ | | | | /  ___/ \ \  / / /  ___/
 * | |     | |__   |   \| | | | | |___   \ \/ /  | |___
 * | |  _  |  __|  | |\   | | | \___  \   \  /   \___  \
 * | |_| | | |___  | | \  | | |  ___| |   / /     ___| |
 * \_____/ |_____| |_|  \_| |_| /_____/  /_/     /_____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author iTX Technologies
 * @link https://itxtech.org
 *
 */

namespace pocketmine\item;

class DiamondBoots extends Armor
{
    public function __construct($meta = 0, $count = 1)
    {
        parent::__construct(self::DIAMOND_BOOTS, $meta, $count, "Diamond Boots");
    }

    public function getArmorTier()
    {
        return Armor::TIER_DIAMOND;
    }

    public function getArmorType()
    {
        return Armor::TYPE_BOOTS;
    }

    public function getMaxDurability()
    {
        return 430;
    }

    public function getArmorValue()
    {
        return 3;
    }

    public function isBoots()
    {
        return true;
    }
}
