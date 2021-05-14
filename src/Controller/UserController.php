<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function index(UserRepository $userRepository): JsonResponse
    {

        $data = [];
        $users=$userRepository->findAll();

        foreach ($users as $user) {
            /**
             * `user_id`, `full_name`, `user_name`, `password`, `email`, `mobile`
             */

            $data[] = [
                'user_id'=>$user->getId(),
                'full_name'=>$user->getFullName(),
                'user_name'=>$user->getUserName(),
                'password'=>null,
                'email'=>$user->getEmail(),
                'mobile'=>$user->getMobile(),
            ];

        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/new", name="add_user", methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function add(Request $request,UserRepository $userRepository): JsonResponse
    {

        $full_name = $request->get('full_name');
        $user_name = $request->get('user_name');
        $password = $request->get('password');
        $email = $request->get('email');
        $mobile = $request->get('mobile');

        if (empty($full_name) || empty($user_name) || empty($password) || empty($email)) {

            throw new NotFoundHttpException('Invalid Inputs!');
        }

        if($userRepository->findOneBy(["user_name"=>$user_name])!=null) {

            throw new NotFoundHttpException('User already exists!');
        }

        $user= new User();

        $user->setFullName($full_name);
        $user->setUserName($user_name);
        $user->setPassword($password);
        $user->setEmail($email);
        $user->setMobile($mobile);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        //$this->customerRepository->saveCustomer($firstName, $lastName, $email, $phoneNumber);

        return new JsonResponse(['status' => 'User created!'], Response::HTTP_CREATED);
    }


    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        $data=['user_id'=>$user->getId(),
            'full_name'=>$user->getFullName(),
            'user_name'=>$user->getUserName(),
            'password'=>null,
            'email'=>$user->getEmail(),
            'mobile'=>$user->getMobile(),];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="post_edit", methods={"PUT"})
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function edit(Request $request, User $user): JsonResponse
    {
        $full_name = $request->get('full_name');
        $user_name = $request->get('user_name');
        $password = $request->get('password');
        $email = $request->get('email');
        $mobile = $request->get('mobile');

        if (empty($user_id) || empty($full_name) || empty($user_name) || empty($password) || empty($email)) {
            throw new NotFoundHttpException('Invalid user attributes!');
        }

        $user->setFullName($full_name);
        $user->setUserName($user_name);
        $user->setPassword($password);
        $user->setEmail($email);
        $user->setMobile($mobile);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['status' => 'User edited!'], Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function delete(Request $request, User $user): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(['status' => 'User deleted!'], Response::HTTP_OK);
    }
}
