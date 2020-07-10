<?php

/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 25.06.2020
 * Time: 23:50.
 */

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_index")
     */
    public function index()
    {
        return $this->render('admin/index/index.html.twig', [
            'h1' => 'Сергей Фомин',
        ]);
    }
}
