<?php
namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController
{
    
    public function addUser(
        Environment $twig,
        FormFactoryInterface $factory,
        Request $request,
        ObjectManager $manager,
        SessionInterface $session,
        UrlGeneratorInterface $urlGenerator,
        \Swift_Mailer $mailer
        ){
        
        $user = new User();
        $builder = $factory->createBuilder(FormType::class, $user);
        $builder->add('username', TextType::class,
            [
                'label' => 'FORM.USER.USERNAME'
            ]
            )
            ->add('firstname', TextareaType::class,
                [
                    'label' => 'FORM.USER.FIRSTNAME'
                ])
            ->add('lastname', TextType::class,
                [
                    'label' => 'FORM.USER.LASTNAME'
                ])
            ->add('email', TextType::class,
                [
                    'label' => 'FORM.USER.EMAIL'
                ])
            ->add('password', PasswordType::class,
                [
                    'label' => 'FORM.USER.PASSWORD.FIRST'
                ])
            ->add('password', RepeatedType::class,
                [   
                    'type' => PasswordType::class,
                    'invalid_message' => 'The password fields must must match :)',
                    'options' => ['attr' => ['class' => 'password-']],
                    'required' => true,
                    'first_options' =>['label' => 'FORM.USER.PASSWORD.FIRST'],
                    'second_options' => ['label' => 'FORM.USER.PASSWORD.SECOND'],
                ]
            )
            ->add('submit', SubmitType::class,
                ['label' => 'FORM.USER.SUBMIT']);
        
            $form = $builder->getForm();
            
            $form->handleRequest($request);
            if($form->isSubmitted()&& $form->isValid()){
                
                $manager->persist($user);
                $manager->flush();
                
                $message = new \Swift_Message();
                $message->setFrom('wf3pm@localhost.com')
                    ->setTo($user->getEmail())
                    ->setSubject('Validate your account')
                    ->setContentType('text/html')
                    ->setBody(
                        $twig->render(
                            'mail/account_creation.html.twig',
                            ['user' => $user]
                        )
                    )->addPart($twig->render(
                        'mail/account_creation.txt.twig',
                        ['user' => $user]
                        )
                        ,'text/plain'
                    );
                
                $mailer->send($message);
                $session->getFlashbag()->add('info', 'Your user is created');
                
                return new RedirectResponse($urlGenerator->generate('hompage'));
            }
        
            return new Response(
                $twig->render('User/addUser.html.twig',
                    [
                        'userFormular' => $form->createView()
                    ]
                    )
                );
        
    }
    
   public function activateUser(
       $token, 
       ObjectManager $manager,
       SessionInterface $session,
       UrlGeneratorInterface $urlGenerator
   ){
       $userRepository=$manager->getRepository(User::class);
       $user = $userRepository->findOneByEmailToken($token);
       
       if(!$user){
           throw new NotFoundHttpException('User not found for given token');
       }
       $user->setActive(true);
       $user->setEmailToken(NULL);
       
       $manager->flush();
       $session->getFlashbag()->add('info', 'Your account is activated ');
       
       return new RedirectResponse($urlGenerator->generate('hompage'));
       
       return new Response('hello guys :) :'.$token);
   }
   public function usernameExist(
       Request $request,
       UserRepository $userRepository
   ){
        $username = $request->request->get('username');
        $unavailable = false;
        
        if(!empty($username)){
            $unavailable = $userRepository->usernameExist($username);
        }
        
        return new JsonResponse(
            [
                'available' =>!$unavailable
            ]
        );
   }
   
   
}











