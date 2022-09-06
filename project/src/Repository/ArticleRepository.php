<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function add(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }



    public function listingArticle($id):?array {
        $em = $this->getEntityManager();

        $query = $em->createQuery(
            'SELECT a
            FROM '.Article::class.' a
            WHERE a.id > :id
            AND a.published = true
            ORDER BY a.lastUpdateDate ASC'
        )->setParameter('id',$id);

        return $query->getResult();
    }

    public function listingArticleQB($id, $publier = false):?array{
        $qb = $this->createQueryBuilder('a')
                   ->where('a.id > :id')
                   ->setParameter('id', $id)
                   ->orderby('a.lastUpdateDate', 'ASC');

        if($publier) {
            $qb->andWhere('a.published = :published')
               ->setParameter('published', true);
        }

        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function listingArticleRawSql($id):mixed{
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT * FROM Article WHERE id > :id ORDER BY last_update_date ASC";
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['id' => $id]);

        return $result->fetchAllAssociative();
    }
}
