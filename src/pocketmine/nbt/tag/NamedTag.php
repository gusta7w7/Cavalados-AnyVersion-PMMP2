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

namespace pocketmine\nbt\tag;

abstract class NamedTag extends Tag
{
    protected $__name;

    /**
     * @param string                                                      $name
     * @param bool|float|double|int|byte|short|array|CompoundTag|ListTag|string $value
     */
    public function __construct($name = "", $value = null)
    {
        $this->__name = ($name === null || $name === false) ? "" : $name;
        if ($value !== null) {
            $this->value = $value;
        }
    }

    public function getName()
    {
        return $this->__name;
    }

    public function setName($name)
    {
        $this->__name = $name;
    }
}
