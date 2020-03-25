<?php

namespace Mylk\Bundle\BlogBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MenuItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("title", TextType::class)
            ->add("url", TextType::class, array("label" => "URL"))
            ->add("type", ChoiceType::class, array(
                "choices" => array(
                    "url" => "URL",
                    "route" => "Route"
                )
            ))
            ->add("parent", EntityType::class, array(
                "class" => "MylkBlogBundle:MenuItem",
                "choice_label" => "title",
                "required" => false,
                "empty_data" => null,
                "choice_label" => "parentTreeTitles"
            ))
            ->add("save", SubmitType::class);
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
