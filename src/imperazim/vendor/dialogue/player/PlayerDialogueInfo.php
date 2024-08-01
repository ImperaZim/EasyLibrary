<?php

declare(strict_types = 1);

namespace imperazim\vendor\dialogue\player;

use imperazim\vendor\dialogue\Dialogue;

/**
* Class PlayerDialogueInfo
* @package imperazim\vendor\dialogue\player
*/
final class PlayerDialogueInfo {

  public const STATUS_SENT = 0;
  public const STATUS_RECEIVED = 1;
  public const STATUS_CLOSED = 2;

  /**
  * PlayerDialogueInfo constructor.
  * @param int $actor_runtime_id The runtime ID of the actor.
  * @param Dialogue $dialogue The dialogue instance.
  * @param int $status The status of the dialogue, one of the STATUS_* constants.
  * @param int $tick The tick count at the time of the dialogue.
  */
  public function __construct(
    readonly public int $actor_runtime_id,
    public Dialogue $dialogue,
    public int $status,
    public int $tick
  ) {}
}