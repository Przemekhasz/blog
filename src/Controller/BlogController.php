<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/blog", name="blog")
 */
class BlogController extends AbstractController
{
    private const POSTS = [
      [
          'id' => 1,
          'slug' => 'hello-world',
          'title' => 'hello world'
      ],
      [
          'id' => 2,
          'slug' => 'another-post',
          'title' => 'this is another post!'
      ],
      [
          'id' => 3,
          'slug' => 'last-example',
          'title' => 'this is the last example!'
      ]
    ];

    /**
     * @Route("/{page}", name="list", defaults={"page": 6}, requirements={"page"="\d+"})
     */
    public function list($page, Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);

        return $this->json(
            [
                'page' => $page,
                'limit' => $limit,
                'data' => array_map(function ($item) {
                    return $this->generateUrl('blog_by_slug', ['slug' => $item['slug']]);
                }, self::POSTS)
            ]
        );
    }

    /**
     * @Route("/{id}", name="_by_id", requirements={"id"="\d+"})
     */
    public function post(int $id): JsonResponse
    {
        return $this->json(
            self::POSTS[array_search($id, array_column(self::POSTS, 'id'))]
        );
    }

    /**
     * @Route("/{slug}", name="_by_slug")
     */
    public function postBySlug($slug): JsonResponse
    {
        return $this->json(
            self::POSTS[array_search($slug, array_column(self::POSTS, 'slug'))]
        );
    }

    /**
     * @Route("/add", name="_add", methods={"POST"})
     */
    public function add(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        /** @var Serializer $serializer */
        $serializer = $this->getParameter('serializer');

        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $em = $doctrine->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }
}