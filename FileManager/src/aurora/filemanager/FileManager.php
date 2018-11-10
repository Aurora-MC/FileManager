<?php

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
            $sender->sendMessage("§cYou have not permissions to use this command");
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
            if($directory != ".." && $directory != ".") {
                $form->addButton("§7" . $directory);
            }
        }

        $player->sendForm($form);
    }

    /**
     * @param PlayerFormResponseEvent $event
     */
    public function onResponse(PlayerFormResponseEvent $event) {
        $player = $event->getPlayer();
        $form = $event->getForm();
        $result = $form->responseData;

        $dirsInPath = scandir($form->formData["content"]);
        array_shift($dirsInPath);
        array_shift($dirsInPath);

        $target = $form->formData["content"] . DIRECTORY_SEPARATOR . $dirsInPath[$result];
        if(is_file($target)) {
            $player->sendMessage("§cInvalid file format");
            return;
        }
        $this->openWindow($player, $target);
    }

}