<?php

    namespace Mylk\Bundle\BlogBundle\Entity;

    use Symfony\Component\Security\Core\Role\RoleInterface;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Table(name="roles")
     * @ORM\Entity()
     */
    class Role implements RoleInterface{
        public function __construct(){
            $this->users = new ArrayCollection();
        }

        /**
         * @ORM\Column(name="id", type="integer")
         * @ORM\Id()
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        private $id;

        /**
         * @ORM\Column(name="title", type="string", length=30)
         */
        private $title;

        /**
         * @ORM\Column(name="role_name", type="string", length=20, unique=true)
         */
        private $roleName;

        /**
         * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
         */
        private $users;
        
        public function getId(){
            return $this->id;
        }
        
        public function getTitle(){
            return $this->title;
        }
        
        public function setTitle($title){
            $this->title = $title;
        }
        
        public function getRoleName(){
            return $this->roleName;
        }
        
        public function setRoleName($roleName){
            $this->roleName = $roleName;
        }
        
        public function getUsers(){
            return $this->users;
        }
        
        public function getRole(){
            return $this->getRoleName();
        }
    }