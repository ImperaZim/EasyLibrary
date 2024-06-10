<?php

declare(strict_types=1);

namespace internal\dialogue\player;

use internal\dialogue\dialogue\Dialogue;

final class PlayerDialogueInfo {

	public const STATUS_SENT = 0;
	public const STATUS_RECEIVED = 1;
	public const STATUS_CLOSED = 2;

	/**
	 * @param int $actor_runtime_id
	 * @param Dialogue $dialogue
	 * @param self::STATUS_* $status
	 * @param int $tick
	 */
	public function __construct(
		readonly public int $actor_runtime_id,
		public Dialogue $dialogue,
		public int $status,
		public int $tick
	){}
}