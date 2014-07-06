<?php
    namespace Mylk\Bundle\BlogBundle\Form;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolverInterface;

    class PostType extends AbstractType{
        /**
         * @param FormBuilderInterface $builder
         * @param array $options
         */
        public function buildForm(FormBuilderInterface $builder, array $options){
            $builder
                ->add("title", "text", array("required" => true))
                ->add("content", "textarea", array(
                    "required" => true,
                    "attr" => array(
                        "rows" => 8,
                        "columns" => 40
                    )
                ))
                ->add("sticky", "checkbox", array("required" => false))
                ->add("tag", "entity", array(
                    "class"    => "MylkBlogBundle:Tag",
                    "property" => "title",
                    "multiple" => true,
                    "required" => false
                ))
                ->add("category", "entity", array(
                    "class"    => "MylkBlogBundle:Category",
                    "property" => "title",
                    "required" => true
                ))
                ->add("save", "submit");
        }

        /**
         * @param OptionsResolverInterface $resolver
         */
        public function setDefaultOptions(OptionsResolverInterface $resolver){
            $resolver->setDefaults(array(
                "data_class" => "Mylk\Bundle\BlogBundle\Entity\Post"
            ));
        }

        /**
         * @return string
         */
        public function getName(){
            return "mylk_bundle_blogbundle_post";
        }
    }
?>