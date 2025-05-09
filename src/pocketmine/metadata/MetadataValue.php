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

namespace pocketmine\metadata;

use pocketmine\plugin\Plugin;

abstract class MetadataValue
{
    /** @var \WeakRef<Plugin> */
    protected $owningPlugin;

    protected function __construct(Plugin $owningPlugin)
    {
        $this->owningPlugin = new \WeakRef($owningPlugin);
    }

    /**
     * @return Plugin
     */
    public function getOwningPlugin()
    {
        return $this->owningPlugin->get();
    }

    /**
     * Fetches the value of this metadata item.
     *
     * @return mixed
     */
    abstract public function value();

    /**
     * Invalidates this metadata item, forcing it to recompute when next
     * accessed.
     */
    abstract public function invalidate();
}
