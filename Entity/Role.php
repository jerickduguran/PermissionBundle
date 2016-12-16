<?php

namespace Movent\PermissionBundle\Entity;

/**
 * Role
 */
class Role
{
    /**
     * @var string
     */
    private $name; 

    /**
     * @var integer
     */
    private $id;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return Role
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    } 
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    } 
	
	public function __toString()
	{
		return $this->getId() ? $this->getName() : 'New';
	}
}
