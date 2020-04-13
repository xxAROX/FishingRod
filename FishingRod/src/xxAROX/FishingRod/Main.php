<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\FishingRod;
use pocketmine\entity\Entity;
use pocketmine\item\ItemFactory;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
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
		ItemFactory::registerItem(new Rod(), true);
		Entity::registerEntity(Hook::class, false, ["FishingHook", "minecraft:fishinghook"]);
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
