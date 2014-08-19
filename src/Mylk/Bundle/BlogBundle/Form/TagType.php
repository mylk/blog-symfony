<?php
    namespace Mylk\Bundle\BlogBundle\Form;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolverInterface;

    class TagType extends AbstractType{
        /**
         * @param FormBuilderInterface $builder
         * @param array $options
         */
        public function buildForm(FormBuilderInterface $builder, array $options){
            $builder
                ->add("title", "text", array("required" => true))
                ->add("save", "submit");
        }

        /**
         * @param OptionsResolverInterface $resolver
         */
        public function setDefaultOptions(OptionsResolverInterface $resolver){
            $resolver->setDefaults(array(
                "data_class" => "Mylk\Bundle\BlogBundle\Entity\Tag"
            ));
        }

        /**
         * @return string
         */
        public function getName(){
            return "mylk_bundle_blogbundle_tag";
        }
    }
?>