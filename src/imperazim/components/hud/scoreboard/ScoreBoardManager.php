<?php

declare(strict_types = 1);

namespace imperazim\components\hud\scoreboard;

use imperazim\components\hud\exception\HudException;

use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;

/**
* Class ScoreBoardManager
* @package imperazim\components\hud\scoreboard
*/
final class ScoreBoardManager {

  /** @var Array<string, string> */
  public static array $scoreboards = [];

  /**
  * Send a scoreboard for a player.
  * @param Player $player
  * @param scoreboard $scoreboard
  */
  public static function sendToPlayer(Player $player, scoreboard $scoreboard): void {
    try {
      if (isset(self::$scoreboards[$player->getName()])) {
        self::removeFromPlayer($player);
      }
      self::$scoreboards[$player->getName()] = $scoreboard;
      $player->getNetworkSession()->sendDataPacket($scoreboard);

      $lines = $scoreboard->getLines();
      $player->getNetworkSession()->sendDataPacket($lines);
    } catch (HudException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Removes the scoreboard of a player.
  * @param Player $player
  */
  public static function removeFromPlayer(Player $player): void {
    try {
      if (isset(self::$scoreboards[$player->getName()])) {
        $scoreboard = self::$scoreboards[$player->getName()];
        $objectiveName = $scoreboard->getObjectiveName();
        if ($objectiveName !== null) {
          $pk = new RemoveObjectivePacket();
          $pk->objectiveName = $objectiveName;
          $player->getNetworkSession()->sendDataPacket($pk);
          unset(self::$scoreboards[$player->getName()]);
        }
      }
    } catch (HudException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Clears a specific line on the scoreboard for a player.
  * @param Player $player
  * @param int $score
  */
  public static function clearLine(Player $player, int $score): void {
    try {
      if (isset(self::$scoreboards[$player->getName()])) {
        $scoreboard = self::$scoreboards[$player->getName()];
        $scoreboard->setLine(new ScoreLine($score, ""));
        self::sendToPlayer($player, $scoreboard);
      }
    } catch (HudException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Clears all lines on the scoreboard for a player.
  * @param Player $player
  */
  public static function clearAllLines(Player $player): void {
    try {
      if (isset(self::$scoreboards[$player->getName()])) {
        for ($i = 1; $i <= 15; $i++) {
          self::clearLine($player, $i);
        }

      }
    } catch (HudException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Updates the scoreboard for a player.
  * @param Player $player
  */
  public static function updateToPlayer(Player $player): void {
    try {
      if (isset(self::$scoreboards[$player->getName()])) {
        $scoreboard = self::$scoreboards[$player->getName()];
        self::sendToPlayer($player, $scoreboard);
      }
    } catch (HudException $e) {
      new \crashdump($e);
    }
  }

  /**
  * Get's ScoreBoard from a player.
  * @return ScoreBoard|null
  */
  public static function getScoreBoardFromPlayer(Player $player): ?ScoreBoard {
    if (isset(self::$scoreboards[$player->getName()])) {
      return self::$scoreboards[$player->getName()];
    }
    return null;
  }
}