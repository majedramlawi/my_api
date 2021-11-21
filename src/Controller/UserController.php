<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        $response_msg=['success'=>true,
            'data'=>$data,
            'msg_title'=>"Fetched Successfully",
            'msg_body'=>"List of All Users fetched Successfully."];

        return new JsonResponse($response_msg, Response::HTTP_OK);//HTTP_OK

    }

    /**
     * @Route("/signup", name="signup", methods={"POST"})
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
                'message'=>"The parameters is not valid."];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $error=['success'=>false,
                    'data'=>null,
                    'msg_title'=>"Invalid Email Format",
                    'message'=>"The Email address format is not valid!"];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        if(!$this->isValidUserName($user_name)){

            $error=['success'=>false,
                'data'=>null,
                'msg_title'=>"Invalid Username!",
                'message'=>"The username you have entered is not valid!"];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        if($userRepository->findOneBy(["user_name"=>$user_name])!=null) {

            $error=['success'=>false,
                'data'=>null,
                'msg_title'=>"User already exists!",
                'message'=>"The username you have entered is already exists!"];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        if($userRepository->findOneBy(["email"=>$email])!=null) {

            $error=['success'=>false,
                'data'=>null,
                'msg_title'=>"User Email Already Exists!",
                'message'=>"The email you have entered is already exists!"];

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

        $user= $userRepository->findOneBy(["user_name"=>$user_name,"password"=>$password]);

        if(is_null($user)) {

            $error=['isSuccessful'=>false,
                'message'=>"Invalid Username or Password"];

            return new JsonResponse($error, Response::HTTP_NOT_ACCEPTABLE);//HTTP_NOT_ACCEPTABLE
        }

        $data=['id'=>$user->getId(),
            'name'=>$user->getFullName(),
            'email'=>$user->getEmail(),
            'email_verified_at'=>null,
            'created_at'=>null,
            'updated_at'=>null,
            'user_posts'=>null];

        $response_msg=['success'=>true,
            'user'=>$data,//null
            'msg_title'=>"User Created Successfully",
            'message'=>"User has been created successfully."];

        return new JsonResponse($response_msg, Response::HTTP_OK);
    }//end signup

    private function isValidUserName($user_name): bool
    {

        if(is_numeric($user_name)) return false;
        if (substr_count($user_name, ' ')>0)  return false;

        return true;
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     * @param Int $id
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
//    public function show(User $user): JsonResponse
//    {
//        $data=['user_id'=>$user->getId(),
//            'full_name'=>$user->getFullName(),
//            'user_name'=>$user->getUserName(),
//            'password'=>null,
//            'email'=>$user->getEmail(),
//            'mobile'=>$user->getMobile(),];
//
//        return new JsonResponse($data, Response::HTTP_OK);
//    }
    public function show(Int $id, UserRepository $userRepository): JsonResponse
    {

        $user=$userRepository->find($id);

        if(is_null($user)){

            $response_msg=['success'=>true,
                'data'=>null,
                'msg_title'=>"User not Found!",
                'msg_body'=>"The selected user is not exists!"];

            return new JsonResponse($response_msg, Response::HTTP_OK);
        }

        $data=['user_id'=>$user->getId(),
            'full_name'=>$user->getFullName(),
            'user_name'=>$user->getUserName(),
            'password'=>null,
            'email'=>$user->getEmail(),
            'mobile'=>$user->getMobile(),];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="user_edit", methods={"PUT"})
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

        return new JsonResponse($response_msg, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     * @param User $user
     * @return JsonResponse
     */
    public function delete(User $user): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $response_msg=['success'=>true,
            'data'=>null,
            'msg_title'=>"Deleted Successfully",
            'msg_body'=>"User has been deleted successfully."];

        return new JsonResponse($response_msg, Response::HTTP_OK);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function login(Request $request,UserRepository $userRepository): JsonResponse
    {

        $user_name = $request->get('user_name');
        $password = $request->get('password');

        if (empty($user_name) || empty($password)) {

            $error=['success'=>false,
                'data'=>null,
                'msg_title'=>"Invalid username or password",
                'msg_body'=>"Provide username and password!"];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        if(!$this->isValidUserName($user_name)){

            $error=['success'=>false,
                'data'=>null,
                'msg_title'=>"Invalid Username!",
                'msg_body'=>"The username you have entered is not valid!"];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }
        $user= $userRepository->findOneBy(["user_name"=>$user_name,"password"=>$password]);

        if(is_null($user)) {

            $error=['success'=>false,
                'data'=>null,
                'msg_title'=>"Wrong username or password!",
                'msg_body'=>"Invalid Username or Password"];

            return new JsonResponse($error, Response::HTTP_NOT_ACCEPTABLE);//HTTP_NOT_ACCEPTABLE
        }

        $data=['user_id'=>$user->getId(),
            'full_name'=>$user->getFullName(),
            'user_name'=>$user->getUserName(),
            'password'=>null,
            'email'=>$user->getEmail(),
            'mobile'=>$user->getMobile(),];

        $response_msg=['success'=>true,
            'data'=>$data,
            'msg_title'=>"Login Successfully!",
            'msg_body'=>"User has been successfully retrieved."];

        return new JsonResponse($response_msg, Response::HTTP_ACCEPTED);
    }

    /**
     * @Route("/login2", name="login2", methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function login2(Request $request,UserRepository $userRepository): JsonResponse
    {

        $user_name = $request->get('email');
        $password = $request->get('password');

        if (empty($user_name) || empty($password)) {

            $error=['isSuccessful'=>false,
                'message'=>"Provide username and password!"];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        if(!$this->isValidUserName($user_name)){

            $error=['isSuccessful'=>false,
                'message'=>"The username you have entered is not valid!"];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }
        $user= $userRepository->findOneBy(["user_name"=>$user_name,"password"=>$password]);


        if(is_null($user)) {

            $error=['isSuccessful'=>false,
                'message'=>"Invalid Username or Password"];

            return new JsonResponse($error, Response::HTTP_NOT_ACCEPTABLE);//HTTP_NOT_ACCEPTABLE
        }
        //get user posts
        //dump($user->getPosts()->count());
        $posts=array();
        foreach ($user->getPosts() as $post){//LearningPathCourses
            /** @var $post Post */
            ////post_id post_id post_text created_on
            $posts[]=array("post_id"=>$post->getId(),"post_text"=>$post->getPostText(),"created_on"=>$post->getCreatedOn()->format("Y-m-d H:i:s"),"user_id_FK"=>$post->getUser()->getId(),);
                //dump($post->getPostText());

        }
//dump($posts);
//dump($learningPath->getCourses()->count());
        //$user->getPosts().count()
        $data=['id'=>$user->getId(),
            'name'=>$user->getFullName(),
            'email'=>$user->getEmail(),
            'email_verified_at'=>null,
            'created_at'=>null,
            'updated_at'=>null,
            'user_posts'=>$posts,];

        $response_msg=['isSuccessful'=>true,
            'message'=>"Login Successfully!!",
            'user'=>$data,];

        return new JsonResponse($response_msg, Response::HTTP_ACCEPTED);
    }
}
