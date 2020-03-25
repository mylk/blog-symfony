<?php

namespace Mylk\Bundle\BlogBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("title", TextType::class, array("required" => true))
            ->add("content", TextareaType::class, array(
                "required" => true,
                "attr" => array(
                    "rows" => 8,
                    "columns" => 40
                )
            ))
            ->add("sticky", CheckboxType::class, array("required" => false))
            ->add("commentsProtected", CheckboxType::class, array("required" => false))
            ->add("commentsClosed", CheckboxType::class, array("required" => false))
            ->add("tags", EntityType::class, array(
                "class" => "MylkBlogBundle:Tag",
                "choice_label" => "title",
                "multiple" => true,
                "required" => false
            ))
            ->add("category", EntityType::class, array(
                "class" => "MylkBlogBundle:Category",
                "choice_label" => "title",
                "required" => true
            ))
            ->add("save", SubmitType::class);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            "data_class" => "Mylk\Bundle\BlogBundle\Entity\Post"
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "mylk_bundle_blogbundle_post";
    }
}
