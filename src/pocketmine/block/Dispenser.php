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

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Dispenser as TileDispenser;
use pocketmine\tile\Tile;

use function abs;

class Dispenser extends Solid
{
    protected $id = self::DISPENSER;

    public function __construct($meta = 0)
    {
        $this->meta = $meta;
    }

    public function canBeActivated() : bool
    {
        return true;
    }

    public function getHardness()
    {
        return 3.5;
    }

    public function getName() : string
    {
        return "Dispenser";
    }

    public function getToolType()
    {
        return Tool::TYPE_PICKAXE;
    }

    public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null)
    {
        $dispenser = null;
        if ($player instanceof Player) {
            $pitch = $player->getPitch();
            if (abs($pitch) >= 45) {
                if ($pitch < 0) {
                    $f = 4;
                } else {
                    $f = 5;
                }
            } else {
                $f = $player->getDirection();
            }
        } else {
            $f = 0;
        }
        $faces = [
            3 => 3,
            0 => 4,
            2 => 5,
            1 => 2,
            4 => 0,
            5 => 1
        ];
        $this->meta = $faces[$f];

        $this->getLevel()->setBlock($block, $this, true, true);
        $nbt = new CompoundTag("", [
            new ListTag("Items", []),
            new StringTag("id", Tile::DISPENSER),
            new IntTag("x", $this->x),
            new IntTag("y", $this->y),
            new IntTag("z", $this->z)
        ]);
        $nbt->Items->setTagType(NBT::TAG_Compound);

        if ($item->hasCustomName()) {
            $nbt->CustomName = new StringTag("CustomName", $item->getCustomName());
        }

        if ($item->hasCustomBlockData()) {
            foreach ($item->getCustomBlockData() as $key => $v) {
                $nbt->{$key} = $v;
            }
        }

        Tile::createTile(Tile::DISPENSER, $this->getLevel()->getChunk($this->x >> 4, $this->z >> 4), $nbt);

        return true;
    }

    public function activate()
    {
        $tile = $this->getLevel()->getTile($this);
        if ($tile instanceof TileDispenser) {
            $tile->activate();
        }
    }

    public function onActivate(Item $item, Player $player = null)
    {
        if ($player instanceof Player) {
            $t = $this->getLevel()->getTile($this);
            $dispenser = null;
            if ($t instanceof TileDispenser) {
                $dispenser = $t;
            } else {
                $nbt = new CompoundTag("", [
                    new ListTag("Items", []),
                    new StringTag("id", Tile::DISPENSER),
                    new IntTag("x", $this->x),
                    new IntTag("y", $this->y),
                    new IntTag("z", $this->z)
                ]);
                $nbt->Items->setTagType(NBT::TAG_Compound);
                $dispenser = Tile::createTile(Tile::DISPENSER, $this->getLevel()->getChunk($this->x >> 4, $this->z >> 4), $nbt);
            }

            if ($player->isCreative() && $player->getServer()->limitedCreative) {
                return true;
            }

            $player->addWindow($dispenser->getInventory());
        }

        return true;
    }

    public function getDrops(Item $item) : array
    {
        return [
            [$this->id, 0, 1],
        ];
    }
}
