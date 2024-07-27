<?php

declare(strict_types = 1);

namespace imperazim\hud\scoreboard;

use pocketmine\scheduler\Task;

use imperazim\hud\HudManager;
use imperazim\components\hud\ScoreBoard;

/**
* Class ScoreBoardTask
* @package imperazim\hud\scoreboard
*/
final class ScoreBoardTask extends Task {

  /**
  * Executes the task
  */
  public function onRun(): void {
    $plugin = HudManager::getPlugin();
    $players = $plugin->getServer()->getOnlinePlayers();

    foreach ($players as $player) {

      /**
      * Checking if a scoreboard exists is not necessary, you can just create it again with ScoreBoard::create(...), the function will rewrite the scoreboard automatically!
      */
      if (!isset(ScoreBoard::$scoreboards[$player->getName()])) {
        ScoreBoard::create(
          player: $player,
          objectiveName: 'scoreboard',
          displayName: '§l§e' . strtoupper($player->getName())
        );
      }

      /**
      * Changes the lines of the player's scoreboard, as previously stated, because you can only send a new scoreboard without needing to check if it exists, you can pass the lines in the create itself using:
      *
      *   ScoreBoard::create(
      *     player: $player,
      *     objectiveName: '...',
      *     displayName: '...'
      *     lines: [line => text]
      *   );
      * 
      */
      ScoreBoard::setLines(
        player: $player,
        lines: [
          1 => 'Date: ' . date("D M j G:i:s T Y"),
          2 => 'Worlf: ' . $player->getWorld()->getDisplayName()
        ]
      );
    }
  }

}