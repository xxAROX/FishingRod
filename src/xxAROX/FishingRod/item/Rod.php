<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\FishingRod\item;

use pocketmine\entity\animation\Animation;
use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\entity\Entity;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\player\Player;
use xxAROX\FishingRod\entity\Hook;
use xxAROX\FishingRod\Main;


/**
 * Class Rod
 * @package xxAROX\FishingRod\item
 * @author xxAROX
 * @date 13.04.2020 - 13:37
 * @project FishingRod
 */
class Rod extends Durable
{
	public function getMaxStackSize(): int{
		return 1;
	}

	public function getCooldownTicks(): int{
		return 5;
	}

	public function getMaxDurability(): int{
		return 355;
	}

	public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult{
		if (!$player->hasItemCooldown($this)) {
			$player->resetItemCooldown($this);

			if (Main::getFishingHook($player) === NULL) {
				$motion = $player->getDirectionVector();
				$motion = $motion->multiply(0.4);
				$hook = new Hook($player->getLocation(), $player, new CompoundTag());
				$hook->spawnToAll();
			} else {
				$hook = Main::getFishingHook($player);
				$hook->delete();
				Main::setFishingHook(NULL, $player);
			}
			$player->broadcastAnimation(new ArmSwingAnimation($player));
			return ItemUseResult::SUCCESS();
		}
		return ItemUseResult::FAIL();
	}

	public function getProjectileEntityType(): string{
		return "Hook";
	}

	public function getThrowForce(): float{
		return 0.9;
	}
}
