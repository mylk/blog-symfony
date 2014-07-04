<?php
    namespace Mylk\Bundle\BlogBundle\Form;

    use Symfony\Component\Form\AbstractType;
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
                ->add("username", "text", array("required" => true))
                ->add("email", "email", array("required" => true))
                ->add("content", "textarea", array(
                    "label" => "Comment",
                    "required" => true,
                    "attr" => array(
                        "rows" => 8,
                        "cols" => 40)
                    )
                )
                ->add("post", "hidden")
                ->add("captcha", "captcha")
                ->add("send", "submit");
        }

        /**
         * @param OptionsResolverInterface $resolver
         */
        public function setDefaultOptions(OptionsResolverInterface $resolver)
        {
            $resolver->setDefaults(array(
                'data_class' => 'Mylk\Bundle\BlogBundle\Entity\Comment'
            ));
        }

        /**
         * @return string
         */
        public function getName()
        {
            return 'mylk_bundle_blogbundle_comment';
        }
    }
?>