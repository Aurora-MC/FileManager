<?php

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