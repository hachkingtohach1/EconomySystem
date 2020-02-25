<?php

namespace hachkingtohach\API;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;

use hachkingtohach\API\task\UpdateData;

class EconomySystem extends PluginBase implements Listener {
	
	// Array data get from UpdateData
	public $data_economy = [];
	
	private static $instance;	
	
    public function onLoad(): void {
        self::$instance = $this;
	}
  
    public function onEnable(): void {
		$this->registerScheduler();
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		$this->getResource("config.yml");		
		$this->getLogger()->info(C::GREEN."Enable!\n".C::AQUA."Plugin by @DragoVN, thanks for using!");
	}
	
	public function registerScheduler(){
		$this->getScheduler()->scheduleRepeatingTask(new UpdateData($this), 20);
	}
	
    public function onDisable(): void {
		$this->getLogger()->info(C::RED."Disable!\n".C::AQUA."Plugin by @DragoVN, thanks for using!");
	}

    public static function getInstance(): EconomySystem {
		return self::$instance;
	}
    	
	public function getDataFolderZ($name_data){
		$file_data = new Config($this->getDataFolder().$this->getNameEconomy($name_data).
		                    "/".$this->getNameEconomy($name_data).".yml", Config::YAML);
		return $file_data;
	}	
	
	// Check ene economy by one player
	public function checkEconomyPlayer($name_data, $name_player){
        if(!($this->getDataFolderZ($name_data)->exists(strtolower($name_player)))){
            $this->getDataFolderZ($name_data)->set(strtolower($name_player),"0");
            $this->getDataFolderZ($name_data)->save();
            return;
		}
	}
	
	// get name one economy
	public function getNameEconomy($name_data){
		if(empty($this->data_economy[$name_data])){
			return;
		} else return $name_data;
	}	
	
	public function getEconomyAllPlayers($name_data) {
        $get_all_data = $this->getDataFolderZ($name_data)->getAll();
        return $get_all_data;
    }	
	
    public function viewEconomyPlayer($name_data, Player $player) {
		$this->checkEconomyPlayer($name_data, strtolower($player->getName()));
        $player_economy = $this->getDataFolderZ($name_data)->get(strtolower($player->getName()));
        return $player_economy;
    }
	
	// Check one value like money, ...
	public function checkAmount($amount) {
		$amount = round((int)$amount, 2);
        if($amount <= 0 || !is_numeric($amount)) return;
	}
	
	public function saveData($name_data, $playername, $economy) {
		$this->getDataFolderZ($name_data)->set(strtolower($playername),"".$economy."");
        $this->getDataFolderZ($name_data)->save();
	}
	
    public function addEconomyPlayer($name_data, $playername, $amount) {
        $this->checkAmount($amount);
		$this->checkEconomyPlayer($name_data, strtolower($playername));
		$add = $this->calculate($name_data, $playername, $amount, 2);
		$this->saveData($name_data, $playername, $add);
	}
	
	/** Calculate for add and reduce one economy 
	 *  $name_data is name economy
	 * $name_player is name player instance Player or not
	 * $id: 1 is minus, 2 is plus
	**/
	public function calculate($name_data, $name_player, $amount, int $id) {
		if($id == 1){
		    $reduce = $this->getDataFolderZ($name_data)->get(strtolower($name_player)) - $amount;
		}
		if($id == 2){
			$reduce = $this->getDataFolderZ($name_data)->get(strtolower($name_player)) + $amount;
		} else return;
		return $reduce;
	}
	
    public function reduceEconomyPlayer($name_data, $playername, $amount) {
        $this->checkAmount($amount);
		$this->checkEconomyPlayer($name_data, strtolower($playername));
        $player_economy = $this->getDataFolderZ($name_data)->get(strtolower($playername)) - $amount;
		$this->saveData($name_data, $playername, $player_economy);
	}
	
	public function sendEconomyForPlayer($name_data, $name_1, $name_2, $amount) {
		$this->checkAmount($amount);
		$reduce_player_1 = $this->calculate($name_data, strtolower($name_1), $amount, 1); 
		$add_player_2 = $this->calculate($name_data, strtolower($name_2), $amount, 2);	
		$this->checkEconomyPlayer($name_data, strtolower($name_1));
		$this->checkEconomyPlayer($name_data, strtolower($name_2));				
		$this->saveData($name_data, $name_1, $reduce_player_1);
		$this->saveData($name_data, $name_1, $add_player_2);
	}
}
