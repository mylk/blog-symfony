<?php
    namespace Mylk\Bundle\BlogBundle\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    
    /**
     * @ORM\Entity
     * @ORM\Table(name="users")
     */
    class User{
        /**
         * @ORM\Column(type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;
        
        /**
         * @ORM\Column(type="string", length=50, nullable=false)
         */
        protected $username;
        
        /**
         * @ORM\Column(type="string", length=100, nullable=false)
         */
        protected $email;
        
        public function getId(){
            return $this->id;
        }
        
        public function getUsername(){
            return $this->username;
        }
        
        public function setUsername($username){
            $this->username = $username;
        }
        
        public function getEmail(){
            return $this->email;
        }
        
        public function setEmail($email){
            $this->email = $email;
        }
    }
?>