<?php

declare(strict_types = 1);

namespace imperazim\components\hud\scoreboard;

use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;

final class ScoreLine extends ScorePacketEntry {
  
	public int $type;
	public int $score;
  public int $scoreboardId;
	public string $objectiveName;

  /**
  * ScoreLine constructor.
  * @param string $message
  * @param int $score
  */
  public function __construct(int $score = 0, string $message = "") {
    if ($score < 1 || $score > 15) {
      throw new \InvalidArgumentException("Score must be between 1 and 15. Given: $score");
    }
    $this->score = $score;
    $this->scoreboardId = $score;
    $this->customName = $message;
    $this->type = self::TYPE_FAKE_PLAYER;
  }


  /**
  * Set the score.
  * @param int $score
  */
  public function setScore(int $score): void {
    if ($score < 1 || $score > 15) {
      throw new \InvalidArgumentException("Score must be between 1 and 15. Given: $score");
    }
    $this->score = $score;
  }

  /**
  * Set the message.
  * @param string $message
  */
  public function setMessage(string $message): void {
    $this->customName = $message;
  }

  /**
  * Set the objective name.
  * @param string $objectiveName
  */
  public function setObjectiveName(string $objectiveName): void {
    $this->objectiveName = $objectiveName;
  }

  /**
  * Set the scoreboard ID.
  * @param int $scoreboardId
  */
  public function setScoreboardId(int $scoreboardId): void {
    $this->scoreboardId = $scoreboardId;
  }
}