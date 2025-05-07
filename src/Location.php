<?php

namespace Neoxiel\Geocoder;


/**
 * Classe permettant de récupérer les information concernant une adresse
 * Adresse, code postal, ville, département, région, pays... 
 * ses données sont récupérées par l'API adresse du gouvernement au format geocodejson
 * 
 * @see https://github.com/geocoders/geocodejson-spec/blob/master/draft/README.md
 */
class Location
{

    /**
     * Constructeur de la classe Location.
     * 
     * @param string $address Adresse
     * @param string $postcode Code postal
     * @param string $city Ville
     * @param string $department Département
     * @param string $region Région
     * @param string $country Pays
     */
    public function __construct(
        public readonly string $address,
        public readonly string $postcode,
        public readonly string $city,
        public readonly string $department,
        // public readonly string $region,
        // public readonly string $country
    ) {
    }

    /**
     * Crée une instance de la classe Location à partir d'un tableau.
     * 
     * @param array $array Tableau contenant les informations de l'adresse.
     * @return self Instance de la classe Location.
     */
    public static function make(Array $array): self
    {
        return new self(
            $array['properties']['label'],
            $array['properties']['postcode'],
            $array['properties']['city'],
            $array['properties']['context'],
            // $array['properties']['region'],
            // $array['properties']['country']
        );
    }

    /**
     * Récupère l'adresse.
     * 
     * @return string L'adresse.
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * Récupère le code postal.
     * 
     * @return string Le code postal.
     */
    public function getPostcode(): string
    {
        return $this->postcode;
    }

    /**
     * Récupère la ville.
     * 
     * @return string La ville.
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * Récupère le département.
     * 
     * @return string Le département.
     */
    public function getDepartment(): string
    {
        return $this->department;
    }

    // /**
    //  * Récupère la région.
    //  * 
    //  * @return string La région.
    //  */
    // public function getRegion(): string
    // {
    //     return $this->region;
    // }

    // /**
    //  * Récupère le pays.
    //  * 
    //  * @return string Le pays.
    //  */
    // public function getCountry(): string
    // {
    //     return $this->country;
    // }       

    /**
     * Récupère les informations de l'adresse sous forme de tableau.
     * 
     * @return array Tableau contenant les informations de l'adresse.
     */
    public function toArray(): array    
    {
        return [
            'address' => $this->address,
            'postcode' => $this->postcode,
            'city' => $this->city,
            'department' => $this->department,
            // 'region' => $this->region,
            // 'country' => $this->country,
        ];
    }

    /**
     * Récupère les informations de l'adresse sous forme de chaîne JSON.
     * 
     * @return string Chaîne JSON contenant les informations de l'adresse.
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }


}
