<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/blog", name="blog")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/{page}", name="list", defaults={"page": 6}, requirements={"page"="\d+"})
     */
    public function list($page, Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $repository = $doctrine->getRepository(BlogPost::class);
        $items = $repository->findAll();

        return $this->json(
            [
                'page' => $page,
                'limit' => $limit,
                'data' => array_map(function (BlogPost $item) {
                    return $this->generateUrl('blog_by_slug', ['slug' => $item->getSlug()]);
                }, $items)
            ]
        );
    }

    /**
     * @Route("/post/{id}", name="_by_id", requirements={"id"="\d+"})
     * @ParamConverter("post", class="App:BlogPost")
     */
    public function post(BlogPost $id): JsonResponse
    {
        return $this->json($id);
    }

    /**
     * @Route("/post/{slug}", name="_by_slug")
     * @ParamConverter("post", class="App:BlogPost", options={"mapping": {"slug": "slug"}})
     */
    public function postBySlug(BlogPost $slug): JsonResponse
    {
        return $this->json($slug);
    }

    /**
     * @Route("/add", name="_add")
     */
    public function add(Request $request, ManagerRegistry $doctrine, $format = 'json'): JsonResponse
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()
            ->setPropertyNamingStrategy(
                new \JMS\Serializer\Naming\SerializedNameAnnotationStrategy(
                    new \JMS\Serializer\Naming\IdenticalPropertyNamingStrategy()
                )
            )
            ->build();

        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, $format);

        $em = $doctrine->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }

    /**
     * @Route("/post/{id}", name="_delete")
     */
    public function delete(BlogPost $post, ManagerRegistry $doctrine): JsonResponse
    {
        $em = $doctrine->getManager();
        $em->remove($post);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}