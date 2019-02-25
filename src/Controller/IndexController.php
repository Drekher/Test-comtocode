<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    function index()
    {
        return $this->render('index.html.twig');
    }
}

