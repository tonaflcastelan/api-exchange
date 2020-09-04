<?php

namespace App\Repository;

use App\Entity\Exchange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Exchange|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exchange|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exchange[]    findAll()
 * @method Exchange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExchangeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Exchange::class);
        $this->manager = $manager;
    }
    
    public function saveExhange($from, $to, $amount)
    {
        $newExchange = new Exchange();

        $newExchange
            ->setFromCurrency($from)
            ->setToCurrency($to)
            ->setAmount($amount)
            ->setCreatedAt(new \DateTime());

        $this->manager->persist($newExchange);
        $this->manager->flush();
    }


    public function updateExchange(Exchange $exchange): Exchange
    {
        $this->manager->persist($exchange);
        $this->manager->flush();

        return $exchange;
    }
}
