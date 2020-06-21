<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 22.06.2020
 * Time: 0:49.
 */

namespace App\Service;

use App\Entity\SiteProperty;
use App\Repository\SitePropertyRepository;

class BasePropertizer
{
    public function setMetaProperties(SitePropertyRepository $sitePropertyRepository): SiteProperty
    {
        $result = $sitePropertyRepository->findAll();
        if (!$result) {
            $result = [];
            $result[] = new SiteProperty();
        }
        return $result[0];
    }
}
