<?php

declare(strict_types=1);

namespace internal\bedrock;

use pocketmine\math\Vector3;

class BedrockMath {

	/**
	 * Centers the given vector.
	 *
	 * @param Vector3 $vector3 The vector to center.
	 *
	 * @return Vector3 The centered vector.
	 */
	public static function center(Vector3 $vector3) : Vector3 {
		return $vector3->add(0.5, 0.5, 0.5);
	}
}
