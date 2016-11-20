<?php

namespace Mylk\Bundle\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="menu_items")
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
     * @ORM\Column(name="type", type="string", length=20)
     */
    protected $type;

    /**
     * @ORM\ManyToOne(targetEntity="MenuItem", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="parent")
     */
    protected $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

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

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;

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

    public function getParentTreeTitles()
    {
        $titles = array();

        $parent = $this->getParent();
        while ($parent) {
            $titles[] = $parent->getTitle();
            
            $parent = $parent->getParent();
        }

        $titles[] = $this->getTitle();

        return \implode(" -> ", $titles);
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function getChildrenTree()
    {
        $menuItems = array();
        $menuItems[] = $this;

        $children = $this->getChildren();
        foreach ($children as $child) {
            $grandChildren = $child->getChildrenTree();
            $grandChildren = \gettype($grandChildren) === "object" ? array($grandChildren) : $grandChildren;
            $menuItems = \array_merge($menuItems, $grandChildren);
        }

        return $menuItems;
    }

    public function toArray()
    {
        return array(
            "id" => $this->getId(),
            "title" => $this->getTitle(),
            "url" => $this->getUrl(),
            "type" => $this->getType(),
            "parent" => ($this->getParent() !== null) ? $this->getParent()->getId() : null
        );
    }
}
