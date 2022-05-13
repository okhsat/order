<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Product;
use App\Repository\ProductRepository;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/product/{id}", name="get-product", methods={"GET"})
     */
    public function show(int $id, ProductRepository $productRepository, SerializerInterface $serializer)
    {
        $product = $productRepository->find($id);
        
        if ( ! $product ) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }
        
        $product_data = $serializer->normalize($product, null);
        
        return $this->json([
            'product' => $product_data
        ]);
    }
    
    /**
     * @Route("/api/product", name="create-product", methods={"POST"})
     */
    public function create(Request $request, ProductRepository $productRepository, SerializerInterface $serializer)
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var \App\Entity\User $user */
        $user    = $this->getUser();
        $data    = json_decode($request->getContent());
        $product = $productRepository->findOneBy([
            'name' => $data->name
        ]);
        
        if ( $product ) {
            throw new \Exception('Product with this name already exists!');
        }
        
        $product = new Product();
        
        $product->setName( $data->name );
        $product->setPrice( isset($data->price) ? $data->price : 0 );
        $product->setStock( isset($data->stock) ? $data->stock : 0 );
        $product->setDescription( isset($data->description) ? $data->description : null );
        $product->setCreatedBy( $user->getId() );
        $product->setStatus('A');
        $product->setCreatedAt( new \DateTime() );        
        $productRepository->add($product, true);
        
        $product_data = $serializer->normalize($product, null);
        
        return $this->json([
            'product' => $product_data
        ]);
    }
    
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }
}
