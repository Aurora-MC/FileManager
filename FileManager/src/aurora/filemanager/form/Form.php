<?php

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