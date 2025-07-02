<?php

namespace Neoxiel\Geocoder;

class Position
{



    /* Constructeur de la classe Coordinates.
     * 
     * @param float $lng Longitude
     * @param float $lat Latitude
     */
    public function __construct(public readonly array $coordinates, public readonly array $properties) 
    {
        $this->coordinates = [
            'lng' => $coordinates[0],
            'lat' => $coordinates[1],
        ];

        $this->properties = [            
            'score' => $properties['score'] ?? null,
            'label' => $properties['label'] ?? null,
            'street' => $properties['street'] ?? null,
            'city' => $properties['city'] ?? null,
            'citycode' => $properties['citycode'] ?? null,
            'postcode' => $properties['postcode'] ?? null,
            // 'municipalitycode' => $properties['municipalitycode'] ?? null,
            // 'departmentcode' => $properties['departmentcode'] ?? null,
        ];
    }


    /**
     * Crée une instance de la classe Coordinates à partir d'un tableau.
     * 
     * @param array $array Tableau contenant la longitude et la latitude.
     * @return self Instance de la classe Coordinates.
     */ 
    public static function make(Array $coordinates, array $properties): self    
    {
        return new self(
            $coordinates,
            $properties
        );
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getCity(): ?string
    {
        return $this->properties['city'] ?? null;
    }

    public function getStreet(): ?string
    {
        return $this->properties['street'] ?? null;
    }

    public function getPostcode(): ?string
    {
        return $this->properties['postcode'] ?? null;
    }

    public function getCitycode(): ?string
    {
        return $this->properties['citycode'] ?? null;
    }

    public function getScore(): ?float
    {
        return $this->properties['score'] ?? null;
    }

    public function getLabel(): ?string
    {
        return $this->properties['label'] ?? null;
    }



   /**
     * Récupère la latitude.
     * 
     * @return float La latitude.
     */
    public function getLat(): float
    {
        return $this->coordinates['lat'];
    }
    
    /**
     * Récupère la longitude.
     * 
     * @return float La longitude.
     */
    public function getLng(): float
    {
        return $this->coordinates['lng'];
    }

    /**
     * Récupère les coordonnées géographiques sous forme de tableau.
     * 
     * @return array Tableau contenant la latitude et la longitude.
     */
    public function getLatLng(): array
    {
        return [
            $this->coordinates['lat'],
            $this->coordinates['lng'],
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
            $this->coordinates['lng'],
            $this->coordinates['lat'],
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
            'lat' => $this->coordinates['lat'],
            'lng' => $this->coordinates['lng'],
            'score' => $this->properties['score'],
            'label' => $this->properties['label'],
            'street' => $this->properties['street'],
            'city' => $this->properties['city'],
            'citycode' => $this->properties['citycode'],
            'postcode' => $this->properties['postcode'],
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
