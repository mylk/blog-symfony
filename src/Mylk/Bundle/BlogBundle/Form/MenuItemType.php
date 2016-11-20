<?php

namespace Mylk\Bundle\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MenuItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("title", "text")
            ->add("url", "text", array("label" => "URL"))
            ->add("type", "choice", array(
                "choices" => array(
                    "url" => "URL",
                    "route" => "Route"
                )
            ))
            ->add("parent", "entity", array(
                "class" => "MylkBlogBundle:MenuItem",
                "choice_label" => "title",
                "required" => false,
                "empty_data" => null,
                "choice_label" => "parentTreeTitles"
            ))
            ->add("save", "submit");
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mylk\Bundle\BlogBundle\Entity\MenuItem'
        ));
    }

    public function getName()
    {
        return 'mylk_bundle_blogbundle_menuitem';
    }
}
