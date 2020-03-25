<?php

namespace Mylk\Bundle\BlogBundle\Form;

use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("username", TextType::class, array("required" => true))
            ->add("email", EmailType::class, array("required" => true))
            ->add("content", TextareaType::class, array(
                "label" => "Comment",
                "required" => true,
                "attr" => array(
                    "rows" => 8,
                    "cols" => 40
                )
            ))
            ->add("post", HiddenType::class)
            ->add("captcha", CaptchaType::class)
            ->add("send", SubmitType::class);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            "data_class" => "Mylk\Bundle\BlogBundle\Entity\Comment"
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "mylk_bundle_blogbundle_comment";
    }
}
