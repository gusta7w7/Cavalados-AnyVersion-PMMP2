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
use pocketmine\item\Tool;
use pocketmine\math\AxisAlignedBB;
use pocketmine\Player;

class Slab extends Transparent
{
    const STONE = 0;
    const SANDSTONE = 1;
    const WOODEN = 2;
    const COBBLESTONE = 3;
    const BRICK = 4;
    const STONE_BRICK = 5;
    const QUARTZ = 6;
    const NETHER_BRICK = 7;

    protected $id = self::SLAB;

    public function __construct($meta = 0)
    {
        $this->meta = $meta;
    }

    public function getHardness()
    {
        return 2;
    }

    public function getName() : string
    {
        static $names = [
            0 => "Stone",
            1 => "Sandstone",
            2 => "Wooden",
            3 => "Cobblestone",
            4 => "Brick",
            5 => "Stone Brick",
            6 => "Quartz",
            7 => "",
        ];
        return (($this->meta & 0x08) > 0 ? "Upper " : "") . $names[$this->meta & 0x07] . " Slab";
    }

    public function getBurnChance() : int
    {
        $type = $this->meta & 0x07;
        if ($type == self::WOODEN) {
            return 5;
        }
        return 0;
    }

    public function getBurnAbility() : int
    {
        $type = $this->meta & 0x07;
        if ($type == self::WOODEN) {
            return 5;
        }
        return 0;
    }

    protected function recalculateBoundingBox()
    {

        if (($this->meta & 0x08) > 0) {
            return new AxisAlignedBB(
                $this->x,
                $this->y + 0.5,
                $this->z,
                $this->x + 1,
                $this->y + 1,
                $this->z + 1
            );
        } else {
            return new AxisAlignedBB(
                $this->x,
                $this->y,
                $this->z,
                $this->x + 1,
                $this->y + 0.5,
                $this->z + 1
            );
        }
    }

    public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null)
    {
        $this->meta &= 0x07;
        if ($face === 0) {
            if ($target->getId() === self::SLAB && ($target->getDamage() & 0x08) === 0x08 && ($target->getDamage() & 0x07) === ($this->meta & 0x07)) {
                $this->getLevel()->setBlock($target, Block::get(Item::DOUBLE_SLAB, $this->meta), true);

                return true;
            } elseif ($block->getId() === self::SLAB && ($block->getDamage() & 0x07) === ($this->meta & 0x07)) {
                $this->getLevel()->setBlock($block, Block::get(Item::DOUBLE_SLAB, $this->meta), true);

                return true;
            } else {
                $this->meta |= 0x08;
            }
        } elseif ($face === 1) {
            if ($target->getId() === self::SLAB && ($target->getDamage() & 0x08) === 0 && ($target->getDamage() & 0x07) === ($this->meta & 0x07)) {
                $this->getLevel()->setBlock($target, Block::get(Item::DOUBLE_SLAB, $this->meta), true);

                return true;
            } elseif ($block->getId() === self::SLAB && ($block->getDamage() & 0x07) === ($this->meta & 0x07)) {
                $this->getLevel()->setBlock($block, Block::get(Item::DOUBLE_SLAB, $this->meta), true);

                return true;
            }
            //TODO: check for collision
        } else {
            if ($block->getId() === self::SLAB) {
                if (($block->getDamage() & 0x07) === ($this->meta & 0x07)) {
                    $this->getLevel()->setBlock($block, Block::get(Item::DOUBLE_SLAB, $this->meta), true);

                    return true;
                }

                return false;
            } else {
                if ($fy > 0.5) {
                    $this->meta |= 0x08;
                }
            }
        }

        if ($block->getId() === self::SLAB && ($target->getDamage() & 0x07) !== ($this->meta & 0x07)) {
            return false;
        }
        $this->getLevel()->setBlock($block, $this, true, true);

        return true;
    }

    public function getDrops(Item $item) : array
    {
        if ($item->isPickaxe() >= 1) {
            return [
                [$this->id, $this->meta & 0x07, 1],
            ];
        } else {
            return [];
        }
    }

    public function getToolType()
    {
        return Tool::TYPE_PICKAXE;
    }
}
