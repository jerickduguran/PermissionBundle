<?php

namespace Movent\PermissionBundle\Security;

use  Movent\PermissionBundle\Security\MaskBuilderInterface;
 
abstract class MoventBasePermission implements MoventPermissionInterface
{  	 
	protected $code;  
	protected $actions;
	protected $title;
	protected $mask; 
	protected $maskBuilder; 
	
	public function __construct($code,$actions,MaskBuilderInterface $maskBuilder)
	{  
		$this->code	   		= $code;
		$this->actions 		= $actions;	 
		$this->maskBuilder 	= $maskBuilder;	 
	}  

	public function getPermittedActions(){
		return $this->actions;
	}
	 
	public function getCode()
	{
		return $this->code;
	}
	
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}
	
	public function getTitle()
	{
		return $this->title;
	} 
	
	public function isValid()
	{		
		return true;
	}
	
	public function getMask($actions = null)
	{
		$builder = $this->maskBuilder;
		$builder->reset();
		if(is_null($actions)){
			foreach($this->actions as $_action){ 
				$builder->add($_action);		 
			}
		}else{
			if(is_array($actions)){
				foreach($actions as $_action){ 
					$builder->add($_action);		 
				}
			}else{ 
				$builder->add($actions);	
			}
		} 
		return $builder->get();
	}  
	
	public function getPermittedMaskedActions()
	{
		$actions = array();		
		$builder = $this->maskBuilder;
		
		if(is_array($this->actions)){
			foreach($this->actions as $group=>$action){ 
				if(is_array($action)){ 
					foreach($action as $_action){
						$_new_action 		    			=  $group."_".$_action;   
						$actions[$group][$_new_action]		=  array('mask'   => $builder->add($_new_action)->get(),
																	 'title'  => $_action,
																	 'code'   => $_action,
																	 'action' => $_new_action,
																	 'group'  => $group);
						$builder->reset();
					}
				}else{
					$actions[$action] = $builder->add($action)->get();
					$builder->reset();
				}
			}
		}  
		return $actions;
	}
	 
	public function getMaskByAction($action)
	{ 
		$builder = $this->maskBuilder;
		$builder->reset()->add($action);
		
		return $builder->get();
	}
	
	public function mergeActions($actions)
	{
		if(isset($actions[$this->code])){
			$current_actions = $this->actions;
			$this->actions   = array_merge($current_actions,$actions[$this->code]); 
		} 
		return $this;
	}
	
	public function validate()
	{
		if(count($this->actions) > 0){
			$builder = $this->maskBuilder;
			foreach($this->actions as $group => $action){
				if(is_array($action)){ 
					foreach($action as $_action){
						$_action = $group."_".$_action; 
						try{
							$builder->add($_action)->get();	 
							$builder->reset();
						}catch(\InvalidArgumentException $e){
							throw new \InvalidArgumentException(sprintf("%s 's '%s' action is not valid.", $this->title , $_action));
						}
					}
				}else{
					try{
						$builder->add($action)->get();	 
						$builder->reset();
					}catch(\InvalidArgumentException $e){
						throw new \InvalidArgumentException(sprintf("%s 's '%s' action is not valid.", $this->title , $action));
					} 
				}
			} 
		}
		
		return false;
	}
	
}