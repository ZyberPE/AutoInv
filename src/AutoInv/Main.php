<?php

namespace AutoInv;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\player\Player;

class Main extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("AutoInv enabled!");
    }

    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();

        if (!$player->hasPermission("autoinv.use")) {
            return;
        }

        // Get drops
        $drops = $event->getDrops();
        if (empty($drops)) {
            return;
        }

        $inventory = $player->getInventory();
        $allAdded = true;

        foreach ($drops as $item) {
            if (!$inventory->canAddItem($item)) {
                $allAdded = false;
                break;
            }
        }

        if ($allAdded) {
            // Cancel normal drops
            $event->setDrops([]);

            foreach ($drops as $item) {
                $inventory->addItem($item);
            }
        } else {
            // Inventory full — let drops happen normally
            $player->sendMessage("§cYour inventory is full!");
        }
    }
}
