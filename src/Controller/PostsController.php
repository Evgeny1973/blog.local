<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @return RedirectResponse
     * @throws \Exception
     */
    public function addPost(Request $request, Slugify $slugify, EntityManagerInterface $em)
    {
        $post = new Post;
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug($slugify->slugify($post->getTitle()));
            $post->setCreatedAt(new \DateTime);

            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('blog_posts');
        }
        return $this->render('posts/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/posts/{slug}/edit", name="blog_post_edit")
     * @param Post $post
     * @param Request $request
     * @param Slugify $slugify
     * @return Response
     * @return RedirectResponse
     */
    public function edit(Post $post, Request $request, Slugify $slugify)
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form ->isValid()) {
            $post->setSlug($slugify->slugify($post->getTitle()));
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            return $this->redirectToRoute('blog_show', ['slug' => $post->getSlug()]);
        }
        return $this->render('posts/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/posts/{slug}/delete", name="blog_post_delete")
     * @param Post $post
     * @return RedirectResponse
     */
    public function delete(Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute('blog_posts');
    }

    /**
     * @Route("/posts/search", name="blog_search")
     * @param Request $request
     * @return Response
     */
    public function search(Request $request): Response
    {
        $query = $request->query->get('q');
        $posts = $this->postRepository->searchByQuery($query);
        return $this->render('posts/query_post.html.twig', ['posts' => $posts]);
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
