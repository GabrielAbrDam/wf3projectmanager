<?php
namespace App\Controller;

use Twig\Environment;
use App\Entity\Product;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Form\CommentType;
use App\Entity\Comment;

class ProductController
{
    public function addProduct(
        Environment $twig, 
        FormFactoryInterface $factory, 
        Request $request,
        ObjectManager $manager,
        SessionInterface $session
     ){
        
        $product = new Product();
        $builder = $factory->createBuilder(FormType::class, $product);
        $builder->add('name', TextType::class)
            ->add('description', TextareaType::class, ["required" => false])
            ->add('version', TextType::class)
            ->add('submit', SubmitType::class);
        
            $form = $builder->getForm();
            
            $form->handleRequest($request);
            if($form->isSubmitted()&& $form->isValid()){
                
                $manager->persist($product);
                $manager->flush();
                
                $session->getFlashBag()->add('info', 'Your product was created');
                
            
                return new RedirectResponse('/');
                
            }
        
        return new Response(
            $twig->render('Product/addProduct.html.twig',
                [
                    'formular' => $form->createView()
                ]
                )
            );
    }
    
    public function displayProduct
    (
        Environment $twig,
        ProductRepository $productRepository)
    {
        
        $products= $productRepository->findAll();
        return new Response
        (
            $twig->render(
                'Product/displayProduct.html.twig',
                ['products' => $products]
            )
        );
    }
    
    public function detailProduct (
        Environment $twig,
        ProductRepository $productRepository,
        FormFactoryInterface $formFactory,
        $id  
    ) {
        $product=$productRepository->find($id);
        if(!$product){
            throw new NotFoundHttpException();
        }
        
        $comment= new Comment();
        $form = $formFactory->create(
            CommentType::class,
            $comment,
            ['stateless' => true]
        );
        
        return new Response(
        $twig->render(
            'Product/detailProduct.html.twig',
            [
                'product' => $product,
                'form'=> $form->createView()
            ]
            )
        );
    }
    
}

