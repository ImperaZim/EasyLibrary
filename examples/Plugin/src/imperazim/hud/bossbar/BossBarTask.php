<?php

declare(strict_types = 1);

namespace imperazim\hud\bossbar;

use pocketmine\scheduler\Task;

use imperazim\hud\HudManager;
use imperazim\components\hud\BossBar;

/**
* Class BossBarTask
* @package imperazim\hud\bossbar
*/
final class BossBarTask extends Task {

  /** @var BossBar|null */
  private ?BossBar $bossbar = null;

  /**
  * Executes the task
  */
  public function onRun(): void {
    if (!$this->bossbar instanceof BossBar) {
      $this->bossbar = new BossBar();
    }

    $this->bossbar->setPercentage(1);
    $plugin = HudManager::getPlugin();
    $players = $plugin->getServer()->getOnlinePlayers();

    foreach ($players as $player) {
      $this->bossbar->setTitle(
        '§l>>§r Hello ' . $player->getName() . ' | Time: §e' . date("D M j G:i:s T Y") . ' §l<<'
      );
    }

    $this->bossbar->addPlayers($players);
  }

}