<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\FishingRod;

use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;
use xxAROX\FishingRod\entity\Hook;
use xxAROX\FishingRod\item\Rod;


/**
 * Class Main
 * @package xxAROX\FishingRod
 * @author xxAROX
 * @date 13.04.2020 - 13:36
 * @project FishingRod
 */
class Main extends PluginBase
{
	private static $fishing = [];
	private static $instance;
	const PREFIX = "§eStimoMC §8» §7";
	private $prefix = self::PREFIX;


	public function onLoad(): void{
		self::$instance = $this;
	}

	public function onEnable(): void{
		ItemFactory::getInstance()->register(new Rod(new ItemIdentifier(ItemIds::FISHING_ROD, 0)), true);
		EntityFactory::getInstance()->register(Hook::class, function(World $world, CompoundTag $nbt) : Hook{
			return new Hook(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
		}, ["Fishing Hook", "minecraft:fishing_hook"], EntityLegacyIds::FISHING_HOOK);
	}

	public function onDisable(): void{
	}

	public function getPrefix(): string{
		return $this->prefix;
	}

	public static function getInstance(): self{
		return self::$instance;
	}

	public static function getFishingHook(Player $player): ?Hook{
		return self::$fishing[$player->getName()] ?? null;
	}

	public static function setFishingHook(?Hook $fish, Player $player){
		self::$fishing[$player->getName()] = $fish;
	}
}
