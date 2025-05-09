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

use pocketmine\event\block\BlockGrowEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\Server;

use function mt_rand;

class MelonStem extends Crops
{
    protected $id = self::MELON_STEM;

    public function getName() : string
    {
        return "Melon Stem";
    }

    public function __construct($meta = 0)
    {
        $this->meta = $meta;
    }

    public function onUpdate($type)
    {
        if ($type === Level::BLOCK_UPDATE_NORMAL) {
            if ($this->getSide(0)->isTransparent() === true) {
                $this->getLevel()->useBreakOn($this);
                return Level::BLOCK_UPDATE_NORMAL;
            }
        } elseif ($type === Level::BLOCK_UPDATE_RANDOM) {
            if (mt_rand(0, 2) == 1) {
                if ($this->meta < 0x07) {
                    $block = clone $this;
                    ++$block->meta;
                    Server::getInstance()->getPluginManager()->callEvent($ev = new BlockGrowEvent($this, $block));
                    if (!$ev->isCancelled()) {
                        $this->getLevel()->setBlock($this, $ev->getNewState(), true);
                    }

                    return Level::BLOCK_UPDATE_RANDOM;
                } else {
                    for ($side = 2; $side <= 5; ++$side) {
                        $b = $this->getSide($side);
                        if ($b->getId() === self::MELON_BLOCK) {
                            return Level::BLOCK_UPDATE_RANDOM;
                        }
                    }
                    $side = $this->getSide(mt_rand(2, 5));
                    $d = $side->getSide(0);
                    if ($side->getId() === self::AIR && ($d->getId() === self::FARMLAND || $d->getId() === self::GRASS || $d->getId() === self::DIRT)) {
                        Server::getInstance()->getPluginManager()->callEvent($ev = new BlockGrowEvent($side, new Melon()));
                        if (!$ev->isCancelled()) {
                            $this->getLevel()->setBlock($side, $ev->getNewState(), true);
                        }
                    }
                }
            }

            return Level::BLOCK_UPDATE_RANDOM;
        }

        return false;
    }

    public function getDrops(Item $item) : array
    {
        return [
            [Item::MELON_SEEDS, 0, mt_rand(0, 2)],
        ];
    }
}
