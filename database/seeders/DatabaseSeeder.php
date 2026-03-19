<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@events.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $user1 = User::create([
            'name' => 'Mario Rossi',
            'email' => 'mario@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $user2 = User::create([
            'name' => 'Laura Bianchi',
            'email' => 'laura@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $user3 = User::create([
            'name' => 'Giuseppe Verdi',
            'email' => 'giuseppe@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $event1 = Event::create([
            'title' => 'Concerto di Musica Classica',
            'description' => 'Una serata indimenticabile con l\'Orchestra Sinfonica della città. Programma: Mozart, Beethoven e Brahms. Un evento imperdibile per gli amanti della musica classica.',
            'location' => 'Teatro Comunale, Via Roma 123',
            'notes' => 'Si consiglia abbigliamento elegante. È vietato l\'ingresso con bevande e cibo.',
            'max_participants' => 200,
            'cost' => 25.00,
            'event_date' => Carbon::now()->addDays(15),
            'event_time' => '20:30',
            'registration_deadline' => Carbon::now()->addDays(10),
            'event_type' => 'cultural',
            'status' => 'published',
            'created_by' => $admin->id,
        ]);

        $event2 = Event::create([
            'title' => 'Torneo di Calcetto',
            'description' => 'Torneo amichevole di calcetto a 5. Aperto a tutti i livelli di esperienza. Premi per le prime tre squadre classificate.',
            'location' => 'Centro Sportivo Comunale, Via dello Sport 45',
            'notes' => 'Portare scarpe da calcetto e abbigliamento sportivo. Acqua e spogliatoi disponibili.',
            'max_participants' => 50,
            'cost' => null,
            'event_date' => Carbon::now()->addDays(20),
            'event_time' => '15:00',
            'registration_deadline' => Carbon::now()->addDays(15),
            'event_type' => 'sports',
            'status' => 'published',
            'created_by' => $admin->id,
        ]);

        $event3 = Event::create([
            'title' => 'Workshop di Fotografia',
            'description' => 'Corso pratico di fotografia digitale per principianti. Imparerai le basi della composizione, esposizione e post-produzione.',
            'location' => 'Sala Conferenze, Biblioteca Civica',
            'notes' => 'Portare la propria fotocamera digitale o smartphone.',
            'max_participants' => 30,
            'cost' => 15.00,
            'event_date' => Carbon::now()->addDays(25),
            'event_time' => '10:00',
            'registration_deadline' => Carbon::now()->addDays(20),
            'event_type' => 'educational',
            'status' => 'published',
            'created_by' => $admin->id,
        ]);

        $event4 = Event::create([
            'title' => 'Festa di Primavera',
            'description' => 'Grande festa all\'aperto per celebrare l\'arrivo della primavera. Musica dal vivo, stand gastronomici e attività per bambini.',
            'location' => 'Parco Cittadino, Ingresso Nord',
            'notes' => 'In caso di maltempo l\'evento sarà rinviato.',
            'max_participants' => null,
            'cost' => null,
            'event_date' => Carbon::now()->addDays(30),
            'event_time' => '14:00',
            'registration_deadline' => Carbon::now()->addDays(28),
            'event_type' => 'recreational',
            'status' => 'published',
            'created_by' => $admin->id,
        ]);

        $event5 = Event::create([
            'title' => 'Mostra d\'Arte Contemporanea',
            'description' => 'Esposizione di opere di artisti locali emergenti. Vernissage con aperitivo offerto.',
            'location' => 'Galleria d\'Arte Moderna, Piazza Garibaldi 7',
            'notes' => 'L\'evento è gratuito ma è richiesta la prenotazione.',
            'max_participants' => 100,
            'cost' => null,
            'event_date' => Carbon::now()->addDays(12),
            'event_time' => '18:00',
            'registration_deadline' => Carbon::now()->addDays(10),
            'event_type' => 'cultural',
            'status' => 'published',
            'created_by' => $admin->id,
        ]);

        $event6 = Event::create([
            'title' => 'Conferenza sul Cambiamento Climatico',
            'description' => 'Incontro con esperti sul tema del cambiamento climatico e sostenibilità ambientale. Dibattito aperto al pubblico.',
            'location' => 'Auditorium Università, Campus Universitario',
            'notes' => 'Evento in fase di organizzazione. Maggiori dettagli a breve.',
            'max_participants' => 150,
            'cost' => null,
            'event_date' => Carbon::now()->addDays(40),
            'event_time' => '16:00',
            'registration_deadline' => Carbon::now()->addDays(35),
            'event_type' => 'educational',
            'status' => 'draft',
            'created_by' => $admin->id,
        ]);

        $event1->participants()->attach($user1->id, ['registered_at' => Carbon::now()]);
        $event1->participants()->attach($user2->id, ['registered_at' => Carbon::now()]);
        
        $event2->participants()->attach($user1->id, ['registered_at' => Carbon::now()]);
        $event2->participants()->attach($user3->id, ['registered_at' => Carbon::now()]);
        
        $event3->participants()->attach($user2->id, ['registered_at' => Carbon::now()]);
        
        $event5->participants()->attach($user1->id, ['registered_at' => Carbon::now()]);
        $event5->participants()->attach($user2->id, ['registered_at' => Carbon::now()]);
        $event5->participants()->attach($user3->id, ['registered_at' => Carbon::now()]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@events.com / password');
        $this->command->info('Users: mario@example.com, laura@example.com, giuseppe@example.com / password');
    }
}
