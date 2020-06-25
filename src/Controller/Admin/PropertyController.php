<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 26.06.2020
 * Time: 1:25.
 */

namespace App\Controller\Admin;

use App\Repository\SitePropertyRepository;
use App\Service\BasePropertizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController
{
    /**
     * @Route("/admin/properties", name="admin_properties")
     */
    public function index(SitePropertyRepository $sitePropertyRepository, BasePropertizer $basePropertizer)
    {
        $siteProp = $basePropertizer->setMetaProperties($sitePropertyRepository);
        return $this->render('admin/index/property.html.twig', [
            'h1' => 'Сергей Фомин',
            'siteProp' => $siteProp,
        ]);
    }
}
