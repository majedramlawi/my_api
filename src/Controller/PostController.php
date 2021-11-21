<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
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
     * @param Request $request
     * @return JsonResponse
     */
    public function index(PostRepository $postRepository, Request $request): JsonResponse
    {
        $sort = $request->get('sort');
        $order = $request->get('order');

        if (empty($order)) $order='ASC';
        if (empty($sort)) $sort='id';

        $posts=$postRepository->findAllBy($sort,$order);

        $data = [];
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

        //return new JsonResponse($data, Response::HTTP_OK);
        $response_msg=['success'=>true,
            'data'=>$data,
            'msg_title'=>"Posts fetched Successfully",
            'msg_body'=>"All Posts have been fetched successfully."];

        return new JsonResponse($response_msg, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="post_show", methods={"GET"}, requirements={"id"="\d+"})
     * @param Int $id
     * @param PostRepository $postRepository
     * @return JsonResponse
     */
    public function show(Int $id,PostRepository $postRepository): JsonResponse
    {

        $post=$postRepository->find($id);
        if(is_null($post)){
            return new JsonResponse(null, Response::HTTP_OK);
        }

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

        //return new JsonResponse($data, Response::HTTP_OK);
        $response_msg=['success'=>true,
            'data'=>$data,
            'msg_title'=>"Post Fetched Successfully",
            'msg_body'=>"Posts has been fetched successfully."];

        return new JsonResponse($response_msg, Response::HTTP_OK);
    }

    /**
     * @Route("/user/{user_id}", name="user_posts", methods={"GET"})
     * @param UserRepository $userRepository
     * @param PostRepository $postRepository
     * @param Request $request
     * @return JsonResponse
     */
    public function user_posts(UserRepository $userRepository, PostRepository $postRepository, Request $request): JsonResponse
    {
        $user_id = $request->get('user_id');
       // dump($user_id);
//        if (empty($user_id)) {
//
//            $error=['success'=>false,
//                'data'=>null,
//                'msg_title'=>"Invalid Parameters",
//                'msg_body'=>"The parameters is not valid."];
//
//            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
//        }

        $user=$userRepository->find($user_id);
        //dump($user);

        if(is_null($user)){

            $response_msg=['success'=>true,
                'data'=>null,
                'msg_title'=>"Invalid User",
                'msg_body'=>"The selected user is not found!"];

            return new JsonResponse([], Response::HTTP_OK);//HTTP_BAD_REQUEST
        }

        $sort = $request->get('sort');
        $order = $request->get('order');

        if (empty($order)) $order='ASC';
        if (empty($sort)) $sort='id';

       // $posts=$postRepository->findAllBy($sort,$order);
        $posts=$postRepository->findUserPostsBy($user,$sort,$order);
        $data = [];
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
                'postId' => $post->getId(),
                'postText' => $post->getPostText(),
                'userIdFK' => $user_id,
                //'created_on' => $post->getCreatedOn(),
                //'post_user'=>$post_user
            ];
//
        }

        //return new JsonResponse($data, Response::HTTP_OK);
        $response_msg=['success'=>true,
            'data'=>$data,
            'msg_title'=>"Posts fetched Successfully",
            'msg_body'=>"All Posts have been fetched successfully."];

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
            //throw new NotFoundHttpException('Invalid post text value!');
            $error=['success'=>false,
                'data'=>null,
                'msg_title'=>"Invalid post text!",
                'msg_body'=>"Post text is not valid."];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);

        }

        $post->setPostText($post_text);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($post);
        $entityManager->flush();

        //return new JsonResponse(['status' => 'post edited!'], Response::HTTP_OK);
        $response_msg=['success'=>true,
            'data'=>null,
            'msg_title'=>"Post Edited Successfully",
            'msg_body'=>"Post has been changed successfully."];

        return new JsonResponse($response_msg, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="post_delete", methods={"DELETE"})
     * @param Post $post
     * @return JsonResponse
     */

    public function delete(Post $post): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        //return new JsonResponse(['status' => 'post deleted!'], Response::HTTP_OK);
        $response_msg=['success'=>true,
            'data'=>null,
            'msg_title'=>"Deleted Successfully",
            'msg_body'=>"Post has been deleted successfully."];

        return new JsonResponse($response_msg, Response::HTTP_OK);
    }

}
