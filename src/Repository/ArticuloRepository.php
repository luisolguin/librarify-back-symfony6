<?php

namespace App\Repository;

use App\Entity\Articulo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Articulo>
 *
 * @method Articulo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Articulo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Articulo[]    findAll()
 * @method Articulo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticuloRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articulo::class);
    }

    public function save(Articulo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Articulo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Articulo[] Returns an array of Articulo objects
     */
    public function findByNombre(string $nombre): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.nombre = :nombre')
            ->setParameter('nombre', $nombre)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByNombre(string $nombre): ?Articulo
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.nombre = :nombre')
            ->setParameter('nombre', $nombre)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Articulo[] Returns an array of Articulo objects
     */
    public function findByCategoria(Categoria $categoria): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.categoria = :categoria')
            ->setParameter('categoria', $categoria)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneById(int $id): ?Articulo
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}