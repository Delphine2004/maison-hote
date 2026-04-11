<?php

namespace App\DataFixtures;

use App\Entity\Room;
use App\Enum\RoomStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoomFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $roomData = [
            [
                'number' => '1',
                'name' => 'La Rêverie',
                'rate' => '80',
                'capacity' => '2',
                'description' => 'Laissez-vous porter par la douceur et la sérénité au sein de la chambre La Rêverie. Véritable invitation à l\'évasion, elle a été pensée dans les moindres détails pour offrir à ses hôtes un cocon d\'élégance et de quiétude. Ses tons apaisants, ses matières soigneusement choisies et son mobilier raffiné créent une atmosphère enveloppante où le temps semble suspendu. La literie haut de gamme garantit un sommeil profond et réparateur, tandis que la lumière tamisée invite naturellement à la détente. Que ce soit pour une escapade romantique ou une pause bien méritée, La Rêverie vous accueille avec chaleur et discrétion pour une expérience inoubliable.',
                'picture' => 'room-1.webp'
            ],
            [
                'number' => '2',
                'name' => 'Côté Jardin',
                'rate' => '85',
                'capacity' => '2',
                'description' => 'La chambre Côté Jardin offre le privilège rare de s\'éveiller au rythme de la nature, depuis l\'intimité d\'un espace élégamment aménagé. Tournée vers le verdoyant jardin de la propriété, elle bénéficie d\'une lumière naturelle généreuse qui sublime ses teintes douces et sa décoration soignée. Chaque élément a été sélectionné avec soin pour marier confort contemporain et charme authentique. Au fil des saisons, la vue sur le jardin se pare de nouvelles couleurs, offrant à chaque séjour une atmosphère unique. Une chambre idéale pour ceux qui souhaitent se ressourcer dans un cadre raffiné, bercés par le calme et la beauté du vivant.',
                'picture' => 'room-2.webp'
            ],
            [
                'number' => '3',
                'name' => 'Perle Dorée',
                'rate' => '90',
                'capacity' => '2',
                'description' => 'La chambre Perle Dorée rayonne d\'une élégance chaleureuse et lumineuse qui séduit dès le premier regard. Ses accents dorés, subtilement intégrés à une décoration au goût sûr, confèrent à cet espace une atmosphère précieuse et enveloppante. Le mobilier soigneusement choisi allie esthétique raffinée et confort absolu, pour que chaque instant passé dans cette chambre soit une véritable douceur. La literie de qualité supérieure, les textiles nobles et l\'éclairage savamment étudié participent à créer un cadre digne des plus belles maisons d\'hôtes. Séjourner dans la Perle Dorée, c\'est s\'offrir une expérience où luxe discret et art de vivre se rencontrent avec harmonie.',
                'picture' => 'room-3.webp'
            ],
            [
                'number' => '4',
                'name' => 'L\'Escapade',
                'rate' => '95',
                'capacity' => '2',
                'description' => 'Comme son nom l\'indique, la chambre L\'Escapade est une invitation à rompre avec le quotidien et à s\'accorder une parenthèse d\'exception. Dans un esprit à la fois élégant et dépaysant, elle a été aménagée pour procurer une sensation de liberté et de légèreté dès le seuil franchi. Sa décoration soignée, ses matières de qualité et son ambiance feutrée en font un refuge idéal pour les voyageurs en quête d\'authenticité et de raffinement. Chaque détail a été pensé pour sublimer votre séjour : du choix des couleurs à la sélection des équipements, rien n\'a été laissé au hasard. L\'Escapade vous promet un séjour ressourçant, loin des agitations du monde.',
                'picture' => 'room-4.webp'
            ],
            [
                'number' => '5',
                'name' => 'La Glycine',
                'rate' => '100',
                'capacity' => '2',
                'description' => 'Inspirée par la grâce et la poésie de la fleur qui lui prête son nom, la chambre La Glycine dégage un charme naturel empreint de douceur et d\'élégance. Ses teintes délicates, évoquant les nuances mauves et crème de la glycine en fleurs, créent une atmosphère romantique et apaisante d\'une grande finesse. Le mobilier choisi avec soin, les tissus raffinés et les touches décoratives soigneusement disposées en font un espace harmonieux où il fait bon se poser. Baignée d\'une lumière douce et flatteuse, cette chambre invite à la contemplation et au repos profond. La Glycine est une ode à la beauté simple des choses, sublimée par l\'exigence d\'un accueil haut de gamme.',
                'picture' => 'room-5.webp'
            ],
        ];

        foreach ($roomData as $data) {
            $room = new Room();
            $room->setNumber($data['number']);
            $room->setName($data['name']);
            $room->setRate($data['rate']);
            $room->setCapacity($data['capacity']);
            $room->setDescription($data['description']);
            $room->setPicture($data['picture']);
            $room->setStatus(RoomStatus::AVAILABLE);

            $manager->persist($room);

            $this->addReference('room_' . $data['number'], $room);
        }

        $manager->flush();
    }
}
