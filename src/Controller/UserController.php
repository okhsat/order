<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\User;
use App\Repository\UserRepository;

class UserController extends AbstractController
{
    /**
     * @Route("/api/user", name="user", methods={"GET"})
     */
    public function show(SerializerInterface $serializer)
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var \App\Entity\User $user */
        $user      = $this->getUser();
        $user_data = $serializer->normalize($user, null);
        
        unset($user_data['password']);
        
        return $this->json([
            'user' => $user_data
        ]);
    }
    
    /**
     * @Route("/api/user", name="register", methods={"POST"})
     */
    public function create(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, SerializerInterface $serializer)
    {
        $data = json_decode($request->getContent());
        $user = $userRepository->findOneBy([
            'email' => $data->email
        ]);
        
        if ( $user ) {
            throw new \Exception('User with this email has already been registered!');
        }
        
        $user = new User();
        
        $user->setEmail( $data->email );
        $user->setName( $data->name );
        $user->setStatus('A');
        $user->setCreatedAt( new \DateTime() );
        
        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword($user, $data->password);
        
        $user->setPassword($hashedPassword);
        $userRepository->add($user, true);
                
        $user_data = $serializer->normalize($user, null);
        
        unset($user_data['password']);
        
        return $this->json([
            'user' => $user_data
        ]);
    }
    
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
