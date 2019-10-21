<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class NewsController extends Controller
{
    /**
     * @Route("/news_list", name="news")
     * @Template()
     */
    public function newsListAction()
    {
        $news = $this
                ->getDoctrine()
                ->getRepository('AppBundle:News')
                ->findActive();

        $category = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findAll();

        return ['news_list' => $news, 'category_list' => $category];
    }

    /**
     * @Route("/news_list/{id}", name="news_item")
     * @Template()
     */
    public function oneNewsAction(Request $request)
    {
        $newsId = $request->get('id');
        $news = $this
                ->getDoctrine()
                ->getRepository('AppBundle:News')
                ->find($newsId);

        if (!$news) {
            throw $this->createNotFoundException('News not found');
        }

        return ['one_news' => $news];
    }

    /**
     * @Route("/add_news", name="add_news")
     * @Template()
     */
    public function addNewsAction()
    {
    }
}
