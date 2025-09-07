<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Owner>
 */
class OwnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $italianFirstNames = [
            'Mario', 'Francesco', 'Alessandro', 'Lorenzo', 'Matteo', 'Andrea', 'Gabriele', 'Mattia',
            'Davide', 'Riccardo', 'Luca', 'Marco', 'Antonio', 'Giuseppe', 'Stefano', 'Paolo',
            'Paola', 'Francesca', 'Alessandra', 'Anna', 'Giulia', 'Maria', 'Chiara', 'Sara',
            'Elena', 'Valentina', 'Federica', 'Martina', 'Silvia', 'Cristina', 'Elisa', 'Laura',
            'Roberto', 'Simone', 'Daniele', 'Michele', 'Fabio', 'Giovanni', 'Claudio', 'Massimo',
            'Lucia', 'Monica', 'Giovanna', 'Teresa', 'Roberta', 'Daniela', 'Simona', 'Barbara'
        ];
        
        $italianLastNames = [
            'Rossi', 'Russo', 'Ferrari', 'Esposito', 'Bianchi', 'Romano', 'Colombo', 'Ricci',
            'Marino', 'Greco', 'Bruno', 'Gallo', 'Conti', 'De Luca', 'Mancini', 'Costa',
            'Giordano', 'Rizzo', 'Lombardi', 'Moretti', 'Barbieri', 'Fontana', 'Santoro',
            'Mariani', 'Rinaldi', 'Caruso', 'Ferrara', 'Galli', 'Martini', 'Leone', 'Longo',
            'Gentile', 'Martinelli', 'Vitale', 'Lombardo', 'Serra', 'Coppola', 'De Santis',
            'D\'Angelo', 'Marchetti', 'Parisi', 'Villa', 'Conte', 'Ferretti', 'Palumbo',
            'Pellegrini', 'Piazza', 'Neri', 'Poli', 'Marrone', 'Calvi', 'Verdi', 'Barone',
            'Sanna', 'Fabbri', 'Caputo', 'Benedetti', 'Donati', 'Farina', 'Fiore', 'Testa',
            'Guerra', 'Ferri', 'Giuliani', 'Monti', 'Grassi', 'Battaglia', 'Castelli', 'Rossetti',
            'Bianco', 'Pagano', 'Ruggiero', 'Sorrentino', 'Carbone', 'Rossini', 'Pellegrino'
        ];
        
        return [
            'first_name' => fake()->randomElement($italianFirstNames),
            'last_name' => fake()->randomElement($italianLastNames),
        ];
    }
}
