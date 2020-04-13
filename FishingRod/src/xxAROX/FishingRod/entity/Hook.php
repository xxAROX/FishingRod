<?php
namespace xxAROX\FishingRod\entity;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\level\Level;
use pocketmine\math\RayTraceResult;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\utils\Random;
use xxAROX\FishingRod\item\Rod;
use xxAROX\FishingRod\Main;


/**
 * Class Hook
 * @package xxAROX\FishingRod\entity
 * @author xxAROX
 * @date 13.04.2020 - 13:39
 * @project FishingRod
 */
class Hook extends Projectile
{
	public const NETWORK_ID = self::FISHING_HOOK;

	public $height = 0.25;
	public $width = 0.25;
	protected $gravity = 0.1;


	/**
	 * Hook constructor.
	 * @param Level $level
	 * @param CompoundTag $nbt
	 * @param Entity|null $owner
	 */
	public function __construct(Level $level, CompoundTag $nbt, ?Entity $owner = NULL){
		parent::__construct($level, $nbt, $owner);

		if ($owner instanceof Player) {
			$this->setPosition($this->add(0, $owner->getEyeHeight() - 0.1));
			$this->setMotion($owner->getDirectionVector()->multiply(0.4));
			Main::setFishingHook($this, $owner);
			$this->handleHookCasting($this->motion->x, $this->motion->y, $this->motion->z, 1.5, 1.0);
		}
	}

	/**
	 * Function onHitEntity
	 * @param Entity $entityHit
	 * @param RayTraceResult $hitResult
	 * @return void
	 */
	public function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void{
		$event = new ProjectileHitEntityEvent($this, $hitResult, $entityHit);
		#$event->call();
		$damage = $this->getResultDamage();

		if ($this->getOwningEntity() !== NULL) {
			$ev = new EntityDamageByEntityEvent($this, $entityHit, EntityDamageEvent::CAUSE_PROJECTILE, $damage);
			$entityHit->attack($ev);
			$entityHit->setMotion($this->getOwningEntity()->getDirectionVector()->multiply(0.3)->add(0, 0.3, 0));
		}
		$this->isCollided = TRUE;
		$this->flagForDespawn();
	}

	/**
	 * Function onHitBlock
	 * @param Block $blockHit
	 * @param RayTraceResult $hitResult
	 * @return void
	 */
	protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void{
		parent::onHitBlock($blockHit, $hitResult);
	}

	/**
	 * Function handleHookCasting
	 * @param float $x
	 * @param float $y
	 * @param float $z
	 * @param float $f1
	 * @param float $f2
	 * @return void
	 */
	public function handleHookCasting(float $x, float $y, float $z, float $f1, float $f2){
		$rand = new Random();
		$f = sqrt($x * $x + $y * $y + $z * $z);
		$x = $x / (float)$f;
		$y = $y / (float)$f;
		$z = $z / (float)$f;
		$x = $x + $rand->nextSignedFloat() * 0.007499999832361937 * (float)$f2;
		$y = $y + $rand->nextSignedFloat() * 0.007499999832361937 * (float)$f2;
		$z = $z + $rand->nextSignedFloat() * 0.007499999832361937 * (float)$f2;
		$x = $x * (float)$f1;
		$y = $y * (float)$f1;
		$z = $z * (float)$f1;
		$this->motion->x += $x;
		$this->motion->y += $y;
		$this->motion->z += $z;
	}

	/**
	 * Function entityBaseTick
	 * @param int $tickDiff
	 * @return bool
	 */
	public function entityBaseTick(int $tickDiff = 1): bool{
		$hasUpdate = parent::entityBaseTick($tickDiff);
		$owner = $this->getOwningEntity();
		if ($owner instanceof Player) {
			if (!$owner->getInventory()->getItemInHand() instanceof Rod or !$owner->isAlive() or $owner->isClosed())
				$this->flagForDespawn();
		} else $this->flagForDespawn();

		return $hasUpdate;
	}

	/**
	 * Function close
	 * @return void
	 */
	public function close(): void{
		parent::close();

		$owner = $this->getOwningEntity();
		if ($owner instanceof Player) {
			Main::setFishingHook(NULL, $owner);
		}
	}

	/**
	 * Function getGrapplingSpeed
	 * @param float $dist
	 * @return float
	 */
	private function getGrapplingSpeed(float $dist): float{
		if ($dist > 600):
			$motion = 0.26;
		elseif ($dist > 500):
			$motion = 0.24;
		elseif ($dist > 300):
			$motion = 0.23;
		elseif ($dist > 200):
			$motion = 0.201;
		elseif ($dist > 100):
			$motion = 0.17;
		elseif ($dist > 40):
			$motion = 0.11;
		else:
			$motion = 0.8;
		endif;

		return $motion;
	}

	/**
	 * Function applyGravity
	 * @return void
	 */
	public function applyGravity(): void{
		if ($this->isUnderwater())
			$this->motion->y += $this->gravity; else parent::applyGravity();
	}
}
