<?php

declare(strict_types=1);

namespace internal\dialogue\exception;

class DialogueException extends \RuntimeException {

	/**
	 * Thrown when a player closes a dialogue, either from clicking
	 * the cross button or pressing Esc key.
	 */
	public const ERR_PLAYER_CLOSED = 100001;

	/**
	 * Thrown when a player disconnects the server without responding
	 * to a dialogue. Also thrown when sending a dialogue to a player
	 * who is no longer connected.
	 */
	public const ERR_PLAYER_DISCONNECTED = 100002;

	/**
	 * Thrown when a player sends an invalid response value to the
	 * server.
	 */
	public const ERR_PLAYER_RESPONSE_INVALID = 100003;
	
}