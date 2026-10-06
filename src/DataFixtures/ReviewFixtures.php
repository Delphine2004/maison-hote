<?php

namespace App\DataFixtures;

use App\Entity\Review;
use App\Entity\Booking;

use App\Enum\ReviewStatus;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $reviewData = [

            [
                'rate'    => 5,
                'comment' => 'Excellent séjour, la chambre était propre et très confortable. Je reviendrai avec plaisir.',
                'status'  => ReviewStatus::CONFIRMED,
                'booking' => $this->getReference('booking_1', Booking::class),
            ],
            [
                'rate'    => 4,
                'comment' => 'Très bon séjour. La chambre était agréable et le personnel très accueillant.',
                'status'  => ReviewStatus::CONFIRMED,
                'booking' => $this->getReference('booking_2', Booking::class),
            ],
            [
                'rate'    => 5,
                'comment' => 'Tout était parfait, de l’accueil jusqu’au départ. Rien à redire.',
                'status'  => ReviewStatus::CONFIRMED,
                'booking' => $this->getReference('booking_3', Booking::class),
            ],
            [
                'rate'    => 3,
                'comment' => 'Séjour correct dans l’ensemble. Quelques petits détails pourraient être améliorés.',
                'status'  => ReviewStatus::PENDING,
                'booking' => $this->getReference('booking_4', Booking::class),
            ],
            [
                'rate'    => 4,
                'comment' => 'Chambre confortable et établissement agréable. Bon rapport qualité-prix.',
                'status'  => ReviewStatus::CONFIRMED,
                'booking' => $this->getReference('booking_5', Booking::class),
            ],
            [
                'rate'    => 2,
                'comment' => 'La chambre était assez bruyante et certains équipements mériteraient d’être rénovés.',
                'status'  => ReviewStatus::REJECTED,
                'booking' => $this->getReference('booking_6', Booking::class),
            ],
            [
                'rate'    => 5,
                'comment' => 'Une très belle expérience. L’endroit est calme et parfaitement entretenu.',
                'status'  => ReviewStatus::CONFIRMED,
                'booking' => $this->getReference('booking_7', Booking::class),
            ],
            [
                'rate'    => 4,
                'comment' => 'Très bonne expérience, personnel sympathique et chambre agréable.',
                'status'  => ReviewStatus::PENDING,
                'booking' => $this->getReference('booking_8', Booking::class),
            ],
            [
                'rate'    => 3,
                'comment' => 'Séjour agréable mais sans plus. La prestation correspondait globalement aux attentes.',
                'status'  => ReviewStatus::CONFIRMED,
                'booking' => $this->getReference('booking_9', Booking::class),
            ],
            [
                'rate'    => 5,
                'comment' => 'Magnifique séjour ! La chambre était impeccable et le service excellent.',
                'status'  => ReviewStatus::CONFIRMED,
                'booking' => $this->getReference('booking_10', Booking::class),
            ],
            [
                'rate'    => 1,
                'comment' => 'Très déçu par mon séjour. Plusieurs problèmes ont rendu l’expérience particulièrement désagréable.',
                'status'  => ReviewStatus::REJECTED,
                'booking' => $this->getReference('booking_11', Booking::class),
            ],
            [
                'rate'    => 4,
                'comment' => 'Bon séjour, chambre propre et lit confortable. Je recommande cet établissement.',
                'status'  => ReviewStatus::CONFIRMED,
                'booking' => $this->getReference('booking_12', Booking::class),
            ],
            [
                'rate'    => 5,
                'comment' => 'Un séjour exceptionnel. Tout était propre, calme et parfaitement organisé.',
                'status'  => ReviewStatus::CONFIRMED,
                'booking' => $this->getReference('booking_13', Booking::class),
            ],
            [
                'rate'    => 3,
                'comment' => 'Correct pour quelques nuits. L’accueil était sympathique mais la chambre un peu petite.',
                'status'  => ReviewStatus::PENDING,
                'booking' => $this->getReference('booking_14', Booking::class),
            ],
            [
                'rate'    => 4,
                'comment' => 'Très bon séjour, avec une chambre confortable et un personnel disponible.',
                'status'  => ReviewStatus::CONFIRMED,
                'booking' => $this->getReference('booking_15', Booking::class),
            ],
            [
                'rate'    => 2,
                'comment' => 'La propreté n’était pas au niveau attendu et plusieurs éléments étaient vieillissants.',
                'status'  => ReviewStatus::REJECTED,
                'booking' => $this->getReference('booking_16', Booking::class),
            ],
            [
                'rate'    => 5,
                'comment' => 'Parfait ! Très belle chambre, excellent accueil et emplacement idéal.',
                'status'  => ReviewStatus::CONFIRMED,
                'booking' => $this->getReference('booking_17', Booking::class),
            ],
            [
                'rate'    => 4,
                'comment' => 'Très agréable séjour. Nous avons particulièrement apprécié le calme de l’établissement.',
                'status'  => ReviewStatus::PENDING,
                'booking' => $this->getReference('booking_18', Booking::class),
            ],
            [
                'rate'    => 3,
                'comment' => 'Une expérience satisfaisante dans l’ensemble. Quelques améliorations seraient appréciées.',
                'status'  => ReviewStatus::CONFIRMED,
                'booking' => $this->getReference('booking_19', Booking::class),
            ],
            [
                'rate'    => 5,
                'comment' => 'Excellent établissement. Nous avons passé un très bon moment et reviendrons certainement.',
                'status'  => ReviewStatus::CONFIRMED,
                'booking' => $this->getReference('booking_20', Booking::class),
            ],
            [
                'rate'    => 4,
                'comment' => 'Bon rapport qualité-prix. La chambre était propre et le personnel agréable.',
                'status'  => ReviewStatus::CONFIRMED,
                'booking' => $this->getReference('booking_21', Booking::class),
            ],
            [
                'rate'    => 2,
                'comment' => 'Le séjour ne correspondait malheureusement pas à nos attentes.',
                'status'  => ReviewStatus::REJECTED,
                'booking' => $this->getReference('booking_22', Booking::class),
            ],
            [
                'rate'    => 5,
                'comment' => 'Très belle découverte. Tout était parfaitement préparé à notre arrivée.',
                'status'  => ReviewStatus::CONFIRMED,
                'booking' => $this->getReference('booking_23', Booking::class),
            ],
            [
                'rate'    => 3,
                'comment' => 'Séjour correct. L’établissement est agréable mais quelques points sont perfectibles.',
                'status'  => ReviewStatus::PENDING,
                'booking' => $this->getReference('booking_24', Booking::class),
            ],
            [
                'rate'    => 4,
                'comment' => 'Très bon séjour, nous avons apprécié le confort et la qualité de l’accueil.',
                'status'  => ReviewStatus::CONFIRMED,
                'booking' => $this->getReference('booking_25', Booking::class),
            ],
            [
                'rate'    => 5,
                'comment' => 'Une excellente expérience du début à la fin. Nous recommandons vivement cet établissement.',
                'status'  => ReviewStatus::CONFIRMED,
                'booking' => $this->getReference('booking_26', Booking::class),
            ],

        ];

        foreach ($reviewData as $data) {
            $review = new Review();
            $review->setRate($data['rate']);
            $review->setComment($data['comment']);
            $review->setStatus($data['status']);
            $review->setBooking($data['booking']);

            $manager->persist($review);
        }
        $manager->flush();
    }


    public function getDependencies(): array
    {
        return [
            BookingFixtures::class
        ];
    }
}
