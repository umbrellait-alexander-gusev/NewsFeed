<?php

namespace AppBundle\Controller;

use AppBundle\Form\NewsType;
use ArrayObject;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
    public function addNewsAction(Request $request)
    {
        $categories = $this
                ->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findAll();

        $categoryChoices = new ArrayObject();
        foreach ($categories as $category) {
            $categoryChoices->offsetSet($category->getName(), $category->getId());
        }
        $categoryChoices->offsetSet('No category', 'null');
        $categoryList = ['choices' => $categoryChoices];

        $form = $this->createForm(NewsType::class);
        $form->add('category', ChoiceType::class, $categoryList);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $news = $form->getData();

            foreach ($categories as $category) {
                if ($news->getCategory() === $category->getId()) {
                    $news->setCategory($category);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($news);
            $em->flush();

            $this->addFlash('success', 'Saved new news');
            return $this->redirectToRoute('add_news');
        }

        return $this->render('@App/news/addNews.html.twig', [
                'news_form' => $form->createView()
        ]);
    }
}
