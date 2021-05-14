<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use http\Exception\UnexpectedValueException;
use HttpInvalidParamException;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
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

        $full_name = trim($request->get('full_name'));
        $user_name = $request->get('user_name');
        $password = $request->get('password');
        $email = $request->get('email');
        $mobile = $request->get('mobile');

        if (empty($full_name) || empty($user_name) || empty($password) || empty($email)) {

            $error=['success'=>false,
                'data'=>null,
                'msg_title'=>"Invalid Parameters",
                'msg_body'=>"The parameters is not valid."];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $error=['success'=>false,
                    'data'=>null,
                    'msg_title'=>"Invalid Email Format",
                    'msg_body'=>"The Email address format is not valid!"];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        if(!$this->isValidUserName($user_name)){

            $error=['success'=>false,
                'data'=>null,
                'msg_title'=>"Invalid Username!",
                'msg_body'=>"The username you have entered is not valid!"];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        if($userRepository->findOneBy(["user_name"=>$user_name])!=null) {

            $error=['success'=>false,
                'data'=>null,
                'msg_title'=>"User already exists!",
                'msg_body'=>"The username you have entered is already exists!"];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        if($userRepository->findOneBy(["email"=>$email])!=null) {

            $error=['success'=>false,
                'data'=>null,
                'msg_title'=>"User Email Already Exists!",
                'msg_body'=>"The email you have entered is already exists!"];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
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

        $response_msg=['success'=>true,
            'data'=>null,
            'msg_title'=>"User Created Successfully",
            'msg_body'=>"User has been created successfully."];

        return new JsonResponse($response_msg, Response::HTTP_CREATED);
    }

    private function isValidUserName($user_name): bool
    {

        if(is_numeric($user_name)) return false;
        if (substr_count($user_name, ' ')>0)  return false;

        return true;
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
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): JsonResponse
    {
        $full_name = $request->get('full_name');
        $user_name = $request->get('user_name');
        $password = $request->get('password');
        $email = $request->get('email');
        $mobile = $request->get('mobile');

        if (empty($full_name) || empty($user_name) || empty($password) || empty($email)) {

            $error=['success'=>false,
                'data'=>null,
                'msg_title'=>"Invalid Parameters",
                'msg_body'=>"The parameters is not valid."];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $error=['success'=>false,
                'data'=>null,
                'msg_title'=>"Invalid Email Format",
                'msg_body'=>"The Email address format is not valid!"];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        if(!$this->isValidUserName($user_name)){

            $error=['success'=>false,
                'data'=>null,
                'msg_title'=>"Invalid Username!",
                'msg_body'=>"The username you have entered is not valid!"];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        if($userRepository->findUserNameBy($user->getId(),$user_name)!=null) {

            $error=['success'=>false,
                'data'=>null,
                'msg_title'=>"User already exists!",
                'msg_body'=>"The username you have entered is already exists!"];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        if($userRepository->findUserEmailBy($user->getId(),$email)!=null) {

            $error=['success'=>false,
                'data'=>null,
                'msg_title'=>"User Email Already Exists!",
                'msg_body'=>"The email you have entered is already exists!"];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        $user->setFullName($full_name);
        $user->setUserName($user_name);
        $user->setPassword($password);
        $user->setEmail($email);
        $user->setMobile($mobile);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        //return new JsonResponse(['status' => 'User edited!'], Response::HTTP_OK);
        $response_msg=['success'=>true,
            'data'=>null,
            'msg_title'=>"Edited Successfully",
            'msg_body'=>"User has been changed successfully."];

        return new JsonResponse($response_msg, Response::HTTP_OK);//HTTP_CREATED
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

        $response_msg=['success'=>true,
            'data'=>null,
            'msg_title'=>"Deleted Successfully",
            'msg_body'=>"User has been deleted successfully."];

        return new JsonResponse($response_msg, Response::HTTP_OK);//HTTP_CREATED
    }
}
