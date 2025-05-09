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

namespace pocketmine\block;

use pocketmine\entity\Entity;
use pocketmine\item\enchantment\enchantment;
use pocketmine\item\Item;
use pocketmine\item\Tool;

class Cobweb extends Flowable
{
    protected $id = self::COBWEB;

    public function __construct()
    {

    }

    public function hasEntityCollision()
    {
        return true;
    }

    public function getName() : string
    {
        return "Cobweb";
    }

    public function getHardness()
    {
        return 4;
    }

    public function getToolType()
    {
        return Tool::TYPE_SHEARS;
    }

    public function onEntityCollide(Entity $entity)
    {
        $entity->resetFallDistance();
    }

    public function getDrops(Item $item) : array
    {
        if ($item->isShears()) {
            return [
                [Item::COBWEB, 0, 1],
            ];
        } elseif ($item->isSword() >= Tool::TIER_WOODEN) {
            if ($item->getEnchantmentLevel(Enchantment::TYPE_MINING_SILK_TOUCH) > 0) {
                return [
                    [Item::COBWEB, 0, 1],
                ];
            } else {
                return [
                    [Item::STRING, 0, 1],
                ];
            }
        }
        return [];
    }
}
