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

use pocketmine\Player;
use pocketmine\Server;

/**
 * Class Form
 * @package aurora\filemanager\form
 */
class Form implements \pocketmine\form\Form {

    /** @var array $formData */
    public $formData = [];

    /** @var mixed $responseData */
    public $responseData;

    /**
     * Form constructor.
     * @param string $title
     * @param string $content
     */
    public function __construct(string $title = "TITLE", string $content = "Content") {
        $this->formData["type"] = "form";
        $this->setTitle($title);
        $this->setContent($content);
    }

    /**
     * @param string $text
     */
    public function setTitle(string $text) {
        $this->formData["title"] = $text;
    }

    /**
     * @param string $text
     */
    public function setContent(string $text) {
        $this->formData["content"] = $text;
    }

    /**
     * @param string $text
     */
    public function addButton(string $text) {
        $this->formData["buttons"][] = ["text" => $text];
    }

    /**
     * @param Player $player
     * @param mixed $data
     */
    public function handleResponse(Player $player, $data): void {
        $this->responseData = $data;
        Server::getInstance()->getPluginManager()->callEvent(new PlayerFormResponseEvent($player, $this));
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return $this->formData;
    }

    /**
     * @return mixed
     */
    public function getResponse() {
        return $this->responseData;
    }
}