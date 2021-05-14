<?php

namespace App\Controller;

use App\Entity\Branch;
use App\Entity\Post;
use App\Entity\User;
use App\Form\BranchType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
{

    /**
     * @Route("/", name="post_index", methods={"GET"})
     * @param PostRepository $postRepository
     * @return Response
     */
    public function index(PostRepository $postRepository): Response
    {

//        return $this->json([
//            'message' => 'Welcome to your new controller!',
//            'path' => 'src/Controller/PostController.php',
//        ]);
        $data = [];
        $posts=$postRepository->findAll();

        foreach ($posts as $post) {
            /**
             * `user_id`, `full_name`, `user_name`, `password`, `email`, `mobile`
             */
            $post_user=['user_id'=>$post->getUser()->getId(),
                'full_name'=>$post->getUser()->getFullName(),
                'user_name'=>$post->getUser()->getUserName(),
                'password'=>null,
                'email'=>$post->getUser()->getEmail(),
                'mobile'=>$post->getUser()->getMobile(),
            ];

            $data[] = [
                'post_id' => $post->getId(),
                'post_text' => $post->getPostText(),
                'created_on' => $post->getCreatedOn(),
                'post_user'=>$post_user
            ];

        }
        return new JsonResponse($data, Response::HTTP_OK);
    }


    /**
     * @Route("/{id}", name="post_show", methods={"GET"})
     * @param Post $post
     * @return Response
     */
    public function show(Post $post): Response
    {

        $post_user=['user_id'=>$post->getUser()->getId(),
            'full_name'=>$post->getUser()->getFullName(),
            'user_name'=>$post->getUser()->getUserName(),
            'password'=>null,
            'email'=>$post->getUser()->getEmail(),
            'mobile'=>$post->getUser()->getMobile(),
        ];

        $data=['post_id' => $post->getId(),
            'post_text' => $post->getPostText(),
            'created_on' => $post->getCreatedOn(),
            'post_user'=>$post_user];

        return new JsonResponse($data, Response::HTTP_OK);
    }


    /**
     * @Route("/{id}", name="post_edit", methods={"PUT"})
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function edit(Request $request, Post $post): JsonResponse
    {
        $post_text = $request->get('post_text');

        if (empty($post_text)) {
            throw new NotFoundHttpException('Invalid post text value!');
        }

        $post->setPostText($post_text);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($post);
        $entityManager->flush();

        return new JsonResponse(['status' => 'post edited!'], Response::HTTP_OK);
    }


    /**
     * @Route("/{id}", name="post_delete", methods={"DELETE"})
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function delete(Request $request, Post $post): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        return new JsonResponse(['status' => 'post deleted!'], Response::HTTP_OK);
    }

}
