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

namespace pocketmine\inventory;

use pocketmine\block\TrappedChest;
use pocketmine\level\Level;
use pocketmine\network\protocol\BlockEventPacket;
use pocketmine\Player;
use pocketmine\tile\Chest;

use function count;

class ChestInventory extends ContainerInventory
{
    public function __construct(Chest $tile)
    {
        parent::__construct($tile, InventoryType::get(InventoryType::CHEST));
    }

    /**
     * @return Chest
     */
    public function getHolder()
    {
        return $this->holder;
    }

    public function getContents($withAir = false)
    {
        if ($withAir) {
            $contents = [];
            for ($i = 0; $i < $this->getSize(); ++$i) {
                $contents[$i] = $this->getItem($i);
            }

            return $contents;
        }
        return parent::getContents();
    }

    public function onOpen(Player $who)
    {
        parent::onOpen($who);

        if (count($this->getViewers()) === 1) {
            $pk = new BlockEventPacket();
            $pk->x = $this->getHolder()->getX();
            $pk->y = $this->getHolder()->getY();
            $pk->z = $this->getHolder()->getZ();
            $pk->case1 = 1;
            $pk->case2 = 2;
            if (($level = $this->getHolder()->getLevel()) instanceof Level) {
                $level->addChunkPacket($this->getHolder()->getX() >> 4, $this->getHolder()->getZ() >> 4, $pk);
            }
        }

        if ($this->getHolder()->getLevel() instanceof Level) {
            /** @var TrappedChest $block */
            $block = $this->getHolder()->getBlock();
            if ($block instanceof TrappedChest) {
                if (!$block->isActivated()) {
                    $block->activate();
                }
            }
        }
    }

    public function onClose(Player $who)
    {
        if ($this->getHolder()->getLevel() instanceof Level) {
            /** @var TrappedChest $block */
            $block = $this->getHolder()->getBlock();
            if ($block instanceof TrappedChest) {
                if ($block->isActivated()) {
                    $block->deactivate();
                }
            }
        }

        if (count($this->getViewers()) === 1) {
            $pk = new BlockEventPacket();
            $pk->x = $this->getHolder()->getX();
            $pk->y = $this->getHolder()->getY();
            $pk->z = $this->getHolder()->getZ();
            $pk->case1 = 1;
            $pk->case2 = 0;
            if (($level = $this->getHolder()->getLevel()) instanceof Level) {
                $level->addChunkPacket($this->getHolder()->getX() >> 4, $this->getHolder()->getZ() >> 4, $pk);
            }
        }
        parent::onClose($who);
    }
}
