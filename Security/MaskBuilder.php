<?php

namespace Movent\PermissionBundle\Security;

use Movent\PermissionBundle\Security\MaskBuilderInterface;
 
class MaskBuilder implements MaskBuilderInterface
{  
	const MASK_ALL_ON       = 0; // NOT YET USED          
	const MASK_LIST         = 1;          
    const MASK_CREATE       = 2;          	
    const MASK_EDIT         = 4;          
    const MASK_DELETE       = 8;          
    const MASK_UNDELETE     = 16;          
    const MASK_DOWNLOAD     = 32;      

	protected $mask;

    /**
     * Constructor
     *
     * @param int     $mask optional; defaults to 0
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($mask = 0)
    {
        if (!is_int($mask)) {
            throw new \InvalidArgumentException('$mask must be an integer.');
        }

        $this->mask = $mask;
    }

    /**
     * Adds a mask to the permission
     *
     * @param mixed $mask
     *
     * @return MaskBuilder
     *
     * @throws \InvalidArgumentException
     */
    public function add($mask)
    {
        $this->mask |= $this->getMask($mask);

        return $this;
    }

    /**
     * Returns the mask of this permission
     *
     * @return int
     */
    public function get()
    {
        return $this->mask;
    } 
	
    public function remove($mask)
    {
        $this->mask &= ~$this->getMask($mask);

        return $this;
    }
 
    public function reset()
    {
        $this->mask = 0;

        return $this;
    }
 
    public static function getCode($mask)
    {
        if (!is_int($mask)) {
            throw new \InvalidArgumentException('$mask must be an integer.');
        }

        $reflection = new \ReflectionClass(get_called_class());
        foreach ($reflection->getConstants() as $name => $cMask) {
            if (0 !== strpos($name, 'MASK_') || $mask !== $cMask) {
                continue;
            }

            if (!defined($cName = 'static::CODE_'.substr($name, 5))) {
                throw new \RuntimeException('There was no code defined for this mask.');
            }

            return constant($cName);
        }

        throw new \InvalidArgumentException(sprintf('The mask "%d" is not supported.', $mask));
    }
 
    protected function getMask($code)
    {
        if (is_string($code)) {
            if (!defined($name = sprintf('static::MASK_%s', strtoupper($code)))) {
                throw new \InvalidArgumentException(sprintf('The code "%s" is not supported', $code));
            }

            return constant($name);
        }

        if (!is_int($code)){
            throw new \InvalidArgumentException($code . ' must be an integer.');
        }

        return $code;
    }	
}