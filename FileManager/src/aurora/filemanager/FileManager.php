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

namespace aurora\filemanager;

use aurora\filemanager\form\Form;
use aurora\filemanager\form\PlayerFormResponseEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

/**
 * Class FileManager
 * @package aurora\filemanager
 */
class FileManager extends PluginBase implements Listener {

    /** @var FileManager $instance */
    private static $instance;

    /** @var array $browsingPlayers */
    protected $browsingPlayers = [];

    public function onEnable() {
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * @return FileManager
     */
    public static function getInstance(): FileManager {
        return self::$instance;
    }

    /**
     * @param CommandSender $sender
     * @param Command $command
     * @param string $label
     * @param array $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if(!$sender->hasPermission("fm.action.open")) {
            $sender->sendMessage("§cYou have no permission to open this form");
            return false;
        }
        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in-game");
            return false;
        }
        if($command->getName() !== "filemanager") return false;

        $this->openWindow($sender);
        return false;
    }

    /**
     * @param Player $player
     * @param string|null $path
     */
    public function openWindow(Player $player, ?string $path = null) {
        if($path === null) $path = $this->getServer()->getDataPath();
        $form = new Form("FileManager", $path);
        $dirsInPath = scandir($path);
        array_shift($dirsInPath);
        array_shift($dirsInPath);
        foreach ($dirsInPath as $directory) {
            $form->addButton("§7" . $directory);
        }

        $form->addButton("§cBack");
        $player->sendForm($form);
    }

    /**
     * @param PlayerFormResponseEvent $event
     */
    public function onResponse(PlayerFormResponseEvent $event) {
        $player = $event->getPlayer();
        $form = $event->getForm();
        $result = $form->responseData;
        
        if(!$player->hasPermission("fm.action.open")) {
            $player->sendMessage("§cYou have no permission to open this form");
            return false;
        }

        try {
            if(isset($this->browsingPlayers[$player->getName()])) {
                $this->openWindow($player, $this->browsingPlayers[$player->getName()]);
                unset($this->browsingPlayers[$player->getName()]);
                return;
            }

            $dirsInPath = scandir($form->formData["content"]);
            array_shift($dirsInPath);
            array_shift($dirsInPath);

            if(isset($dirsInPath[$result])) {
                $target = $form->formData["content"] . DIRECTORY_SEPARATOR . $dirsInPath[$result];
                if(is_file($target)) {
                    $supported = ["txt", "json", "yml", "yaml", "properties", "lock", "cmd", "sh", "php"];
                    $e = explode(".", basename($target));
                    $suffix = array_pop($e);
                    if(in_array($suffix, $supported)) {
                        $text = (string)file_get_contents($target);
                        $textForm = new Form(basename($target), $text);
                        $textForm->addButton("§9Close");
                        $this->browsingPlayers[$player->getName()] = $form->formData["content"];
                        $player->sendForm($textForm);
                        return;
                    }
                    $player->sendMessage("§cInvalid file");
                    return;
                }
                $this->openWindow($player, $target);
                return;
            }

            if($result === null) return;

            // back button
            $path = dirname($form->formData["content"]);
            $this->openWindow($player, $path);
        }
        catch (\Exception $exception) {
            $player->sendMessage("§cPermissions denied!");
        }
    }
}
