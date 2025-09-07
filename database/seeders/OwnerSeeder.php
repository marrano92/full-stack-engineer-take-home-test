<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owners = [
            ['first_name' => 'Mario', 'last_name' => 'Rossi'],
            ['first_name' => 'Paola', 'last_name' => 'Bianchi'],
            ['first_name' => 'Alessandro', 'last_name' => 'Neri'],
            ['first_name' => 'Anna', 'last_name' => 'Marrone'],
            ['first_name' => 'Francesco', 'last_name' => 'Calvi'],
            ['first_name' => 'Paolo', 'last_name' => 'Verdi'],
            ['first_name' => 'Alessandra', 'last_name' => 'Rossi'],
            ['first_name' => 'Francesca', 'last_name' => 'Neri'],
            ['first_name' => 'Maria', 'last_name' => 'Bianchi'],
            ['first_name' => 'Cristiano', 'last_name' => 'Verdi'],
            ['first_name' => 'Lorenzo', 'last_name' => 'Romano'],
            ['first_name' => 'Giulia', 'last_name' => 'Ferrari'],
            ['first_name' => 'Matteo', 'last_name' => 'Esposito'],
            ['first_name' => 'Chiara', 'last_name' => 'Colombo'],
            ['first_name' => 'Andrea', 'last_name' => 'Ricci'],
            ['first_name' => 'Elena', 'last_name' => 'Marino'],
            ['first_name' => 'Gabriele', 'last_name' => 'Greco'],
            ['first_name' => 'Valentina', 'last_name' => 'Bruno'],
            ['first_name' => 'Mattia', 'last_name' => 'Gallo'],
            ['first_name' => 'Federica', 'last_name' => 'Conti'],
            ['first_name' => 'Davide', 'last_name' => 'De Luca'],
            ['first_name' => 'Martina', 'last_name' => 'Mancini'],
            ['first_name' => 'Riccardo', 'last_name' => 'Costa'],
            ['first_name' => 'Silvia', 'last_name' => 'Giordano'],
            ['first_name' => 'Luca', 'last_name' => 'Rizzo'],
            ['first_name' => 'Cristina', 'last_name' => 'Lombardi'],
            ['first_name' => 'Marco', 'last_name' => 'Moretti'],
            ['first_name' => 'Elisa', 'last_name' => 'Barbieri'],
            ['first_name' => 'Antonio', 'last_name' => 'Fontana'],
            ['first_name' => 'Laura', 'last_name' => 'Santoro'],
            ['first_name' => 'Giuseppe', 'last_name' => 'Mariani'],
            ['first_name' => 'Sara', 'last_name' => 'Rinaldi'],
            ['first_name' => 'Stefano', 'last_name' => 'Caruso'],
            ['first_name' => 'Monica', 'last_name' => 'Ferrara'],
            ['first_name' => 'Roberto', 'last_name' => 'Galli'],
            ['first_name' => 'Giovanna', 'last_name' => 'Martini'],
            ['first_name' => 'Simone', 'last_name' => 'Leone'],
            ['first_name' => 'Teresa', 'last_name' => 'Longo'],
            ['first_name' => 'Daniele', 'last_name' => 'Gentile'],
            ['first_name' => 'Roberta', 'last_name' => 'Martinelli'],
        ];
        
        foreach ($owners as $owner) {
            \App\Models\Owner::create($owner);
        }
    }
}
