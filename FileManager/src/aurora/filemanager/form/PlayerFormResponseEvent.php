<?php

/**
 *    Copyright 2018 Codename-Aurora
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

declare(strict_types=1);

namespace aurora\filemanager\form;

use aurora\filemanager\FileManager;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

/**
 * Class PlayerFormResponseEvent
 * @package aurora\filemanager\form
 */
class PlayerFormResponseEvent extends PluginEvent {

    /** @var Player $player */
    protected $player;

    /** @var Form $form */
    protected $form;

    /**
     * PlayerFormResponseEvent constructor.
     * @param Player $player
     * @param Form $form
     */
    public function __construct(Player $player, Form $form) {
        $this->player = $player;
        $this->form = $form;
        parent::__construct(FileManager::getInstance());
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * @return Form
     */
    public function getForm(): Form {
        return $this->form;
    }
}