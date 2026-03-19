<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;

class UpdateEventCoordinatesSeeder extends Seeder
{
    /**
     * Esegue il seeding del database.
     */
    public function run(): void
    {
        // Coordinate città italiane principali
        $cityCoordinates = [
            'Roma' => ['lat' => 41.9028, 'lng' => 12.4964],
            'Milano' => ['lat' => 45.4642, 'lng' => 9.1900],
            'Napoli' => ['lat' => 40.8518, 'lng' => 14.2681],
            'Firenze' => ['lat' => 43.7696, 'lng' => 11.2558],
            'Venezia' => ['lat' => 45.4408, 'lng' => 12.3155],
            'Torino' => ['lat' => 45.0703, 'lng' => 7.6869],
            'Bologna' => ['lat' => 44.4949, 'lng' => 11.3426],
            'Palermo' => ['lat' => 38.1157, 'lng' => 13.3615],
            'Genova' => ['lat' => 44.4056, 'lng' => 8.9463],
            'Bari' => ['lat' => 41.1171, 'lng' => 16.8719],
            'Catania' => ['lat' => 37.5079, 'lng' => 15.0830],
            'Verona' => ['lat' => 45.4384, 'lng' => 10.9916],
            'Padova' => ['lat' => 45.4064, 'lng' => 11.8768],
            'Trieste' => ['lat' => 45.6495, 'lng' => 13.7768],
            'Perugia' => ['lat' => 43.1107, 'lng' => 12.3908],
        ];

        $events = Event::whereNull('latitude')->orWhereNull('longitude')->get();

        if ($events->isEmpty()) {
            $this->command->info('Tutti gli eventi hanno già le coordinate!');
            return;
        }

        $cities = array_keys($cityCoordinates);
        $updated = 0;

        foreach ($events as $event) {
            // Prova a trovare una città nel nome della location
            $cityFound = false;
            
            foreach ($cities as $city) {
                if (stripos($event->location, $city) !== false) {
                    $event->latitude = $cityCoordinates[$city]['lat'];
                    $event->longitude = $cityCoordinates[$city]['lng'];
                    $event->save();
                    $cityFound = true;
                    $updated++;
                    $this->command->info("✓ Evento '{$event->title}' aggiornato con coordinate di {$city}");
                    break;
                }
            }

            // Se non trova la città, assegna coordinate casuali da una città italiana
            if (!$cityFound) {
                $randomCity = $cities[array_rand($cities)];
                $event->latitude = $cityCoordinates[$randomCity]['lat'];
                $event->longitude = $cityCoordinates[$randomCity]['lng'];
                $event->save();
                $updated++;
                $this->command->info("✓ Evento '{$event->title}' aggiornato con coordinate di {$randomCity} (casuale)");
            }
        }

        $this->command->info("\n✅ Aggiornati {$updated} eventi con coordinate valide!");
    }
}
