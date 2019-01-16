<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Doctrine\Common\Collections\ArrayCollection;

class MergeAccountsRepository extends EntityRepository
{
    public function getMergeClients(Instance $instance, $type = 0){
        $em = $this->getEntityManager();

        $query = $em->getRepository(MergeAccounts::class)->createQueryBuilder('ma')
            ->innerJoin("ma.authorBase", 'authorBase')->addSelect("authorBase")
            ->innerJoin("ma.authorSecond", 'authorSecond')->addSelect("authorSecond")
            ->where("ma.instance=:instance")->setParameter("instance", $instance);
            if($type == 0){
                $query->andWhere('ma.type=0');
            }

        $result =[];
        foreach($query->getQuery()->getResult() as $item) {
            $orders = '';
            if (!empty($item->getOrderNumber())) {
                $orders = 'Orders: '.implode(', ', $item->getOrderNumber());
            }
            
            $result[] = [
                'id' => $item->getId(),
                'authors' => [
                    [
                        'id'=>$item->getAuthorBase()->getId(),
                        'name'=>$item->getAuthorBase()->getName() . ' ' . $item->getAuthorBase()->getSurname(),
                        'avatar'=>($item->getAuthorBase()->getViewAvatar()!='') ? $item->getAuthorBase()->getViewAvatar(): '/img/customer.png',
                        'phone' => $item->getIsPhone() == true ? $item->getAuthorBase()->getPhone() : null,
                        'email' => $item->getIsEmail() == true ? $item->getAuthorBase()->getMail() : null,
                        'order' => $orders,
                    ],
                    [
                        'id'=>$item->getAuthorSecond()->getId(),
                        'name'=>$item->getAuthorSecond()->getName() . ' ' . $item->getAuthorSecond()->getSurname(),
                        'avatar'=>($item->getAuthorSecond()->getViewAvatar() != '') ? $item->getAuthorSecond()->getViewAvatar(): '/img/customer.png',
                        'phone' => $item->getIsPhone() == true ? $item->getAuthorSecond()->getPhone() : null,
                        'order' => $orders,
                    ]
                ]
            ];
        }

		return $result;
    }
}
