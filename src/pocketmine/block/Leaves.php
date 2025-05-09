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

use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\item\enchantment\enchantment;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;

use function min;
use function mt_rand;

class Leaves extends Transparent
{
    const OAK = 0;
    const SPRUCE = 1;
    const BIRCH = 2;
    const JUNGLE = 3;
    const ACACIA = 0;
    const DARK_OAK = 1;

    const WOOD_TYPE = self::WOOD;

    protected $id = self::LEAVES;

    public function __construct($meta = 0)
    {
        $this->meta = $meta;
    }

    public function getHardness()
    {
        return 0.2;
    }

    public function getToolType()
    {
        return Tool::TYPE_SHEARS;
    }

    public function getBurnChance() : int
    {
        return 30;
    }

    public function getBurnAbility() : int
    {
        return 60;
    }

    public function getName() : string
    {
        static $names = [
            self::OAK    => "Oak Leaves",
            self::SPRUCE => "Spruce Leaves",
            self::BIRCH  => "Birch Leaves",
            self::JUNGLE => "Jungle Leaves",
        ];
        return $names[$this->meta & 0x03];
    }

    private function findLog(Block $pos, array $visited, $distance, &$check, $fromSide = null)
    {
        ++$check;
        $index = $pos->x . "." . $pos->y . "." . $pos->z;
        if (isset($visited[$index])) {
            return false;
        }
        if ($pos->getId() === static::WOOD_TYPE) {
            return true;
        } elseif ($pos->getId() === $this->id && $distance < 3) {
            $visited[$index] = true;
            $down = $pos->getSide(0)->getId();
            if ($down === static::WOOD_TYPE) {
                return true;
            }
            if ($fromSide === null) {
                for ($side = 2; $side <= 5; ++$side) {
                    if ($this->findLog($pos->getSide($side), $visited, $distance + 1, $check, $side) === true) {
                        return true;
                    }
                }
            } else { //No more loops
                switch ($fromSide) {
                    case 2:
                        if ($this->findLog($pos->getSide(2), $visited, $distance + 1, $check, $fromSide) === true) {
                            return true;
                        } elseif ($this->findLog($pos->getSide(4), $visited, $distance + 1, $check, $fromSide) === true) {
                            return true;
                        } elseif ($this->findLog($pos->getSide(5), $visited, $distance + 1, $check, $fromSide) === true) {
                            return true;
                        }
                        break;
                    case 3:
                        if ($this->findLog($pos->getSide(3), $visited, $distance + 1, $check, $fromSide) === true) {
                            return true;
                        } elseif ($this->findLog($pos->getSide(4), $visited, $distance + 1, $check, $fromSide) === true) {
                            return true;
                        } elseif ($this->findLog($pos->getSide(5), $visited, $distance + 1, $check, $fromSide) === true) {
                            return true;
                        }
                        break;
                    case 4:
                        if ($this->findLog($pos->getSide(2), $visited, $distance + 1, $check, $fromSide) === true) {
                            return true;
                        } elseif ($this->findLog($pos->getSide(3), $visited, $distance + 1, $check, $fromSide) === true) {
                            return true;
                        } elseif ($this->findLog($pos->getSide(4), $visited, $distance + 1, $check, $fromSide) === true) {
                            return true;
                        }
                        break;
                    case 5:
                        if ($this->findLog($pos->getSide(2), $visited, $distance + 1, $check, $fromSide) === true) {
                            return true;
                        } elseif ($this->findLog($pos->getSide(3), $visited, $distance + 1, $check, $fromSide) === true) {
                            return true;
                        } elseif ($this->findLog($pos->getSide(5), $visited, $distance + 1, $check, $fromSide) === true) {
                            return true;
                        }
                        break;
                }
            }
        }

        return false;
    }

    public function onUpdate($type)
    {
        if ($type === Level::BLOCK_UPDATE_NORMAL) {
            if (($this->meta & 0b00001100) === 0) {
                $this->meta |= 0x08;
                $this->getLevel()->setBlock($this, $this, false, false, true);
            }
        } elseif ($type === Level::BLOCK_UPDATE_RANDOM) {
            if (($this->meta & 0b00001100) === 0x08) {
                $this->meta &= 0x03;
                $visited = [];
                $check = 0;

                Server::getInstance()->getPluginManager()->callEvent($ev = new LeavesDecayEvent($this));

                if ($ev->isCancelled() || $this->findLog($this, $visited, 0, $check) === true) {
                    $this->getLevel()->setBlock($this, $this, false, false);
                } else {
                    $this->getLevel()->useBreakOn($this);

                    return Level::BLOCK_UPDATE_NORMAL;
                }
            }
        }

        return false;
    }

    public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null)
    {
        $this->meta |= 0x04;
        $this->getLevel()->setBlock($this, $this, true);
    }

    public function getDrops(Item $item) : array
    {
        $drops = [];
        if ($item->isShears() || $item->getEnchantmentLevel(Enchantment::TYPE_MINING_SILK_TOUCH) > 0) {
            $drops[] = [$this->id, $this->meta & 0x03, 1];
        } else {
            $fortunel = $item->getEnchantmentLevel(Enchantment::TYPE_MINING_FORTUNE);
            $fortunel = min(3, $fortunel);
            $rates = [20, 16, 12, 10];
            if (mt_rand(1, $rates[$fortunel]) === 1) { //Saplings
                $drops[] = [Item::SAPLING, $this->meta & 0x03, 1];
            }
            $rates = [200, 180, 160, 120];
            if (($this->meta & 0x03) === self::OAK && mt_rand(1, $rates[$fortunel]) === 1) { //Apples
                $drops[] = [Item::APPLE, 0, 1];
            }
        }
        return $drops;
    }
}
