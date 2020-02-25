<?php

namespace hachkingtohach\API\Task;

use hachkingtohach\API\EconomySystem;
use pocketmine\scheduler\Task;

class UpdateData extends Task{
	
	public function __construct(EconomySystem $plugin){
        $this->plugin = $plugin;
	}
	
	public function onRun(int $currentTick){
		$data_name = $this->plugin->getConfig()->get("name_economy");
	    foreach ($data_name as $name){					
			$this->plugin->data_economy[$name] = $name;
			if(!file_exists($this->plugin->getDataFolder().$name."/")){
                @mkdir($this->plugin->getDataFolder().$name."/");
			}
		}
	}
}
