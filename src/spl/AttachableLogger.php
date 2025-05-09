<?php

/*
 * PocketMine Standard PHP Library
 * Copyright (C) 2014 PocketMine Team <https://github.com/PocketMine/PocketMine-SPL>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
*/

interface AttachableLogger extends \Logger
{
    /**
     * @param LoggerAttachment $attachment
     */
    public function addAttachment(\LoggerAttachment $attachment);

    /**
     * @param LoggerAttachment $attachment
     */
    public function removeAttachment(\LoggerAttachment $attachment);

    public function removeAttachments();

    /**
     * @return \LoggerAttachment[]
     */
    public function getAttachments();
}
