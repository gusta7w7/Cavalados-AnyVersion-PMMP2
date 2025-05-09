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

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\Player;

use function mt_rand;

class DeadBush extends Flowable
{
    protected $id = self::DEAD_BUSH;

    public function __construct($meta = 0)
    {
        $this->meta = $meta;
    }

    public function getName() : string
    {
        return "Dead Bush";
    }

    public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null)
    {
        $down = $this->getSide(0);
        if ($down->getId() === Block::SAND || $down->getId() === Block::PODZOL ||
            $down->getId() === Block::HARDENED_CLAY || $down->getId() === Block::STAINED_CLAY) {
            $this->getLevel()->setBlock($block, $this, true);
            return true;
        }
        return false;
    }

    public function onUpdate($type)
    {
        if ($type === Level::BLOCK_UPDATE_NORMAL) {
            if ($this->getSide(0)->isTransparent() === true) {
                $this->getLevel()->useBreakOn($this);

                return Level::BLOCK_UPDATE_NORMAL;
            }
        }

        return false;
    }

    public function getDrops(Item $item) : array
    {
        if ($item->isShears()) {
            return [
                [Item::DEAD_BUSH, 0, 1],
            ];
        } else {
            return [
                [Item::STICK, 0, mt_rand(0, 2)],
            ];
        }

    }
}
