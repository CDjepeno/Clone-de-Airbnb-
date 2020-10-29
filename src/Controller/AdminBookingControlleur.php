<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Service\PaginationService;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingControlleur extends AbstractController
{
    /**
     * Permet d'afficher la liste des resvations
     * 
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_bookings_index")
     */
    public function index(BookingRepository $bookings,$page, PaginationService $pagination )
    {
        $pagination->setEntityclass(Booking::class)
                    ->setPage($page);

        return $this->render('admin/booking/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Permet de supprimer une réservation
     *
     * @Route("/admin/booking/{id}/delete", name="admin_booking_delete")
     * 
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     * 
     * @return Responser
     */
    public function delete(Booking $booking, EntityManagerInterface $manager) {
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            "success",
            "La reservation de {$booking->getBooker()->fullname()} à bien été supprimer"
        );

        return $this->redirectToRoute("admin_bookings_index");
    }

    /**
     * Permet d'éditer une reservation
     *
     * @Route("/admin/bookings/{id}/edit", name="admin_booking_edit")
     * 
     * @return Response
     */
    public function edit(Booking $booking, Request $request, EntityManagerInterface $manager) {
        $form = $this->createForm(AdminBookingType::class, $booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $booking->setAmount(0);
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                "success",
                "La réservation n°{$booking->getId()} a bien été modifiée"
            );

            return $this->redirectToRoute('admin_bookings_index');
        }
        return $this->render('admin/booking/edit.html.twig',[
            "form" => $form->createView(),
            "booking" => $booking

        ]);
    }
}
