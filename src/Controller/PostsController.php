<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @Route("/posts", name="blog_posts")
     * @return Response
     */
    public function posts()
    {
        $posts = $this->postRepository->findAll();

        return $this->render('posts/index.html.twig', ['posts' => $posts]);
    }

    /**
     * @Route("/posts/new", name="new_blog_post")
     * @param Request $request
     * @param Slugify $slugify
     * @return Response
     * @throws \Exception
     */
    public function addPost(Request $request, Slugify $slugify)
    {
        $post = new Post;
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            echo 123;
            $post->setSlug($slugify->slugify($post->getTitle()));
            $post->setCreatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('blog_posts');
        }
        return $this->render('posts/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/posts/{slug}", name="blog_show")
     * @param Post $post
     * @return Response
     */
    public function post(Post $post)
    {
        return $this->render('posts/show.html.twig', ['post' => $post]);
    }
}
