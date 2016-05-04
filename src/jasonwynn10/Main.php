<?php
namespace jasonwynn10\Main;

use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\block\Chest;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase implements Listener{
  public $active = true; // By default plugin feature is enabled
  public function onEnable() {
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->getLogger()->notice(TF::BOLD.TF::GREEN."Enabled!");
  }
  public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
    if(strtolower($command->getName()) === "dchest") {
      // No args passed? Toggle
      if(!isset($args[0])){
        $this->active = $this->active ? false : true;
        $sender->sendMessage(" [DChest] Plugin " . ( $this->active === false ? "de" : "" ) . "activated." );
        return true;
      }
      switch ( strtolower( $args[ 0 ] ) ) {
        case '1':
        case 'on':
        case 'true':
        case 'enable':
          $this->active = true;
          $sender->sendMessage( "[DChest] Plugin activated" );
          return true;
          break;
        case '0':
        case 'off':
        case 'false':
        case 'disable':
          $this->active = false;
          $sender->sendMessage( "[DChest] Plugin deactivated" );
          return true;
          break;
        default:
          $sender->sendMessage( "[DChest] Could not resolve your request." );
          return false; // Send usage message.
          break;
      }
    }
    return false;
  }
  public function onChestPlace(BlockPlaceEvent $event) {
    if($event->isCancelled()) return;
    if(!$this->activated) return;
    if(!$event->getBlock() instanceof Chest) return;
    $player = $event->getPlayer();
    $chest = $event->getBlock();
    for($side = 2; $side <= 5; ++$side){
      if(($chest->getDamage() === 4 or $chest->getDamage() === 5) and ($side === 4 or $side === 5)){
        continue;
      }elseif(($chest->getDamage() === 3 or $chest->getDamage() === 2) and ($side === 2 or $side === 3)){
        continue;
      }
      $c = $chest->getSide($side);
      if($c instanceof Chest and $c->getDamage() === $chest->getDamage()){
        $event->setCancelled(true);
        $player->sendPopup(TF::RED."Double chests are disabled");
        break;
      }
    }
  }
  public function onDisable() {
    $this->getLogger()->notice(TF::BOLD.TF::GREEN."Disabled!");
  }
}
