<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Order;
use App\Entity\Product;

class OrderController extends AbstractController
{
    /**
     * @Route("/api/orders", name="list-orders", methods={"GET"})
     */
    public function list(ManagerRegistry $doctrine, SerializerInterface $serializer)
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var \App\Entity\User $user */
        $user   = $this->getUser();
        $orders = $doctrine->getRepository(Order::class)->findBy([
            'customer' => $user
        ], [
            'created_at' => 'DESC'
        ]);
        
        $orders_data = $serializer->normalize($orders, null);
        
        return $this->json([
            'orders' => $orders_data
        ]);
    }
    
    /**
     * @Route("/api/order/{id}", name="get-order", methods={"GET"})
     */
    public function show(int $id, ManagerRegistry $doctrine, SerializerInterface $serializer)
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var \App\Entity\User $user */
        $user  = $this->getUser();
        $order = $doctrine->getRepository(Order::class)->findOneBy([
            'id'       => $id,
            'customer' => $user,
        ]);
        
        if ( ! $order ) {
            throw $this->createNotFoundException('No order found for you with id '.$id);
        }
        
        $order_data = $serializer->normalize($order, null);
        
        return $this->json([
            'order' => $order_data
        ]);
    }
    
    /**
     * @Route("/api/order/{id}", name="update-order", methods={"PUT"})
     */
    public function update(int $id, Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer)
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var \App\Entity\User $user */
        $user              = $this->getUser();
        $entityManager     = $doctrine->getManager();
        $orderRepository   = $doctrine->getRepository(Order::class);
        $productRepository = $doctrine->getRepository(Product::class);
        $data              = json_decode($request->getContent());
        $order             = $doctrine->getRepository(Order::class)->findOneBy([
            'id'       => $id,
            'customer' => $user,
        ]);
        $product           = $productRepository->find( (int) $data->product_id );
        
        if ( ! $order ) {
            throw $this->createNotFoundException('No order found for you with id '.$id);
        }
        
        if ( ! $product ) {
            throw $this->createNotFoundException('No product found for id '.$data->product_id);
        }
        
        if ( $order->getShippingDate() <= new \DateTime() ) {
            throw new \Exception("The order sent, so cannot be updated!");
        }
        
        $order->setProduct( $product );
        $order->setQuantity(    isset($data->quantity)     ? $data->quantity                                  : 0 );
        $order->setFullAddress( isset($data->full_address) ? $data->full_address                              : null );
        $order->setTotalPrice(  isset($data->quantity)     ? round($data->quantity * $product->getPrice(), 2) : 0 );
        $order->setStatus('A');
        $order->setUpdatedAt( new \DateTime() );
        $entityManager->flush();
        
        $order_data = $serializer->normalize($order, null);
        
        return $this->json([
            'order' => $order_data
        ]);
    }
    
    /**
     * @Route("/api/order", name="create-order", methods={"POST"})
     */
    public function create(Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer)
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var \App\Entity\User $user */
        $user              = $this->getUser();
        $orderRepository   = $doctrine->getRepository(Order::class);
        $productRepository = $doctrine->getRepository(Product::class);
        $data              = json_decode($request->getContent());
        $product           = $productRepository->find( (int) $data->product_id );
        $order             = new Order();
        
        if ( ! $product ) {
            throw $this->createNotFoundException('No product found for id '.$data->product_id);
        }
        
        $order->setCustomer( $user );
        $order->setProduct( $product );
        $order->setQuantity(    isset($data->quantity)     ? $data->quantity                                  : 0 );
        $order->setFullAddress( isset($data->full_address) ? $data->full_address                              : null );
        $order->setTotalPrice(  isset($data->quantity)     ? round($data->quantity * $product->getPrice(), 2) : 0 );
        $order->setShippingDate( new \DateTime('+ 2 days') );
        $order->setStatus('A');
        $order->setCreatedAt( new \DateTime() );
        $orderRepository->add($order, true);
        
        $order_data = $serializer->normalize($order, null);
        
        return $this->json([
            'order' => $order_data
        ]);
    }
    
    #[Route('/order', name: 'app_order')]
    public function index(): Response
    {
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }
}
