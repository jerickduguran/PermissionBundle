<?php
 
namespace Movent\PermissionBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilderInterface;
use Movent\PermissionBundle\Form\DataTransformer\TreeTransformer;

use Movent\PermissionBundle\Security\TreeBuilder;

class RoleMaskType extends AbstractType
{  
	protected $_treeBuilder = null;
	
	public function __construct(TreeBuilder $treeBuilder )
    {
        $this->_treeBuilder  = $treeBuilder; 
    }
	 
	public function buildForm(FormBuilderInterface $builder, array $options)
    {  
        $transformer = new TreeTransformer($this->_treeBuilder);
        $builder->addModelTransformer($transformer);
    }
	
	public function buildView(FormView $view, FormInterface $form, array $options)
    {   
            $view->vars['permissions'] 		  =  $options['permissions']; 
            $view->vars['permission_tree_id'] =  $options['permission_tree_id'];
            $view->vars['tree'] 			  =  $this->_treeBuilder->render();
	}
	
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {  
		$resolver->setDefaults(array( 
                                   'permissions' 			=>  array(),
								   'permission_tree_id'		=>  $this->_treeBuilder->getId()
                               ));
    } 
	
	public function getParent()
    {
        return 'hidden';
    }

    public function getName()
    {
        return 'role_mask';
	}  
}
