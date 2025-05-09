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

class SplashPotion extends Item
{
    public function __construct($meta = 0, $count = 1)
    {
        parent::__construct(self::SPLASH_POTION, $meta, $count, $this->getNameByMeta($meta));
    }

    public function getMaxStackSize() : int
    {
        return 1;
    }

    public function getNameByMeta(int $meta)
    {
        return "Splash " . Potion::getNameByMeta($meta);
    }
}
