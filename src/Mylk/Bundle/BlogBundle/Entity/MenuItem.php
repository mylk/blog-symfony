<?php

namespace Mylk\Bundle\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="menu")
 */
class MenuItem
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $url;

    /**
     * @ORM\Column(name="url_discr", type="string", length=20)
     */
    protected $urlDiscr;

    /**
     * @ORM\ManyToOne(targetEntity="MenuItem")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    protected $parent;

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function getUrlDiscr()
    {
        return $this->urlDiscr;
    }

    public function setUrlDiscr($urlDiscr)
    {
        $this->urlDiscr = $urlDiscr;

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    public function toArray()
    {
        return array(
            "id" => $this->getId(),
            "title" => $this->getTitle(),
            "url" => $this->getUrl(),
            "urlDiscr" => $this->getUrlDiscr(),
            "parent" => ($this->getParent() !== null) ? $this->getParent()->getId() : null
        );
    }
}
