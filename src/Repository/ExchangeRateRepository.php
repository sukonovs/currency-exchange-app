<?php

namespace App\Repository;

use App\Entity\ExchangeRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method ExchangeRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExchangeRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExchangeRate[]    findAll()
 * @method ExchangeRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    public function saveRates(ArrayCollection $rates): void
    {
        $em = $this->getEntityManager();

        if ($rates->exists(fn($key, $rate) => !$rate instanceof ExchangeRate)) {
            throw new \RuntimeException('Cannot save rogue Entities');
        }

        $rates->map(fn(ExchangeRate $rate) => $em->persist($rate));

        $em->flush();
    }

    public function findLast(): ?ExchangeRate
    {
        return $this->findOneBy([], ['date' => 'DESC']);
    }

    public function findDates(int $limit = 100): array
    {
        return $this->createQueryBuilder('er')
            ->select('er.date')
            ->orderBy('er.date', 'DESC')
            ->groupBy('er.date')
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    public function getRatesQueryBuilder(\DateTime $date): QueryBuilder
    {
        return $this->createQueryBuilder('er')
            ->where('er.date = :date')
            ->setParameter('date', $date);
    }

    public function findForCurrency(string $currency, int $limit = 100): array
    {
        return $this->createQueryBuilder('er')
            ->where('er.currency = :currency')
            ->setParameter('currency', $currency)
            ->orderBy('er.date', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }
}
