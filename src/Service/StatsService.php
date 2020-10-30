<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StatsService {
    private $manager;

    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;

        return $this;
    }

    public function getStats() {
        $users = $this->getUsersCount();
        $ads = $this->getAdsCount();
        $bookings = $this->getBookings();
        $comments = $this->getComments();

        return compact('users','ads','bookings','comments');
    }

    public function getUsersCount() {
       return $this->manager->createQuery('SELECT COUNT(u) FROM  APP\Entity\User u')->getSingleScalarResult();
    }

    public function getAdsCount() {
        return $this->manager->createQuery('SELECT COUNT(a) FROM  APP\Entity\Ad a')->getSingleScalarResult();
    }

    public function getBookings() {
        return $this->manager->createQuery('SELECT COUNT(b) FROM  APP\Entity\Booking b')->getSingleScalarResult();
    }

    public function getComments() {
        return $this->manager->createQuery('SELECT COUNT(c) FROM  APP\Entity\Comment c')->getSingleScalarResult();
    }

    public function getAdsStats($direction) {
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as moyenneNote, a.title, a.id, u.firstName, u.lastName, u.picture
            FROM App\Entity\Comment c
            JOIN c.ad a
            JOIN a.author u
            GROUP BY a
            ORDER BY moyenneNote ' . $direction
        )
        ->setMaxResults(5)
        ->getResult();
    }
}