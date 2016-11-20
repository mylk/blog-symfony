<?php

namespace Mylk\Bundle\BlogBundle\Service;

class MenuGeneratorService
{
    public function prepareMenu($menuItems, $parentId = null)
    {
        $menu = array();

        foreach ($menuItems as $item) {
            $item = $item->toArray();

            if ($item["parent"] == $parentId) {
                $menu[$item["id"]] = $item;
                $children = $this->prepareMenu($menuItems, $item["id"]);

                if ($children) {
                    $menu[$item["id"]]["children"] = $children;
                }
            }
        }

        return $menu;
    }
}
