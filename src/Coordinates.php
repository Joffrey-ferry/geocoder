<?php

namespace Neoxiel\Geocoder;

/**
 * Classe permettant de manipuler les coordonnées géographiques.
 * L'objet est instancié avec la longitude et la latitude.
 * car l'API adresse du gouvernement retourne la longitude en premier et la latitude en second.
 * @see https://adresse.data.gouv.fr/api-doc/adresse
 * @see https://github.com/geocoders/geocodejson-spec/blob/master/draft/README.md
 * 
 * @package JoffreyFerry\Geocoder
 */
class Coordinates
{
    /**
     * Constructeur de la classe Coordinates.
     * 
     * @param float $lng Longitude
     * @param float $lat Latitude
     */
    public function __construct(public readonly float $lng, public readonly float $lat) 
    {
    }

    /**
     * Crée une instance de la classe Coordinates à partir d'un tableau.
     * 
     * @param array $array Tableau contenant la longitude et la latitude.
     * @return self Instance de la classe Coordinates.
     */ 
    public static function make(Array $array): self    
    {
        return new self(
            $array[0],
            $array[1]
        );
    }

    /**
     * Récupère la latitude.
     * 
     * @return float La latitude.
     */
    public function getLat(): float
    {
        return $this->lat;
    }
    
    /**
     * Récupère la longitude.
     * 
     * @return float La longitude.
     */
    public function getLng(): float
    {
        return $this->lng;
    }

    /**
     * Récupère les coordonnées géographiques sous forme de tableau.
     * 
     * @return array Tableau contenant la latitude et la longitude.
     */
    public function getLatLng(): array
    {
        return [
            $this->lat,
            $this->lng,
        ];
    }

    /**
     * Récupère les coordonnées géographiques sous forme de tableau.
     * 
     * @return array Tableau contenant la longitude et la latitude.
     */
    public function getLngLat(): array
    {
        return [
            $this->lng,
            $this->lat,
        ];
    }

    /**
     * Récupère les coordonnées géographiques sous forme de tableau associatif.
     * 
     * @return array Tableau associatif contenant la latitude et la longitude.
     */
    
    public function toArray(): array
    {
        return [
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }

    /**
     * Récupère les coordonnées géographiques sous forme de chaîne JSON.
     * 
     * @return string Chaîne JSON contenant la latitude et la longitude.
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
