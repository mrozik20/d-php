<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Order;





class SummaryOrdersExtension extends \Twig_Extension
{
    protected $em;
    
    public function __construct($em) {
        $this->em = $em;
    }
    
    public function getFunctions() 
    {
        return [
            new \Twig_SimpleFunction('getSummaryOrders', [$this, 'getSummaryOrders'])
        ];
    }
    
    public function getSummaryOrders($user)
    {   
        $firstOrder = $this->em->getRepository(Order::class)->getAuthorFirstOrder($user);
        if($firstOrder == null) return [0,0,null];

        $totalIncome = $this->em->getRepository(Order::class)->totalIncome($user);
        $countPurchases = $this->em->getRepository(Order::class)->countPurchases($user);
        $avarangeIncome = $totalIncome['sum'] / $countPurchases['count'];
        return [$totalIncome['sum'], $avarangeIncome, $firstOrder];
    }
    
    public function getName() 
    {
        return 'getSummaryOrders';
    }
    
    
}
