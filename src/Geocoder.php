<?php
declare(strict_types=1);

namespace Neoxiel\Geocoder;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Classe permettant d'utiliser l'api adresse du gouvernement
 *
 *
 * Documentation :
 * https://adresse.data.gouv.fr/api-doc/adresse
 * https://adresse.data.gouv.fr/api-doc
 */
class Geocoder
{

    private const BASE_URL = 'https://api-adresse.data.gouv.fr/';

    private const ENDPOINT_SEARCH = 'search/?';

    private const ENDPOINT_REVERSE = "reverse/?lonlat";

    private const ENDPOINT_CSV = "search/csv/";

    private const ENDPOINT_CSV_REVERSE = "reverse/csv/";

    private const ERROR_INVALID_COORDINATES = 'Les coordonnées doivent être des nombres.';

    private const ERROR_MISSING_RESULTS = 'Aucun résultat trouvé.';
    
    private const ERROR_MISSING_COORDINATES = 'Les coordonnées sont manquantes dans la réponse.';

    private const ERROR_MISSING_PROPERTIES = 'Les propriétés sont manquantes dans la réponse.';

    private static ?string $endPoint = null;

    private static ?string $limit = null;

    private static ?string $postcode = null;

    private static ?string $citycode = null;

    private static ?string $address = null;

    private static ?int $autocomplete = null;
    
    private static ?array $response = null;

    private static ?array $features = null;

    private static bool $throwException = false;

    private static array $coordinates = [0, 0]; // Coordonnées par défaut Attention à l'ordre : [latitude, longitude]

    /**
     * Récupère les coordonnées géographiques d'une adresse.
     * 
     * @param array $features Le résultat de recherche.
     * @param int $index L'index de l'adresse à récupérer.
     * @return null|Coordinates Les coordonnées géographiques de l'adresse.
     */
    public static function getCoordinates($index = 0): ?Coordinates
    {
        self::geocoder();

        return self::coordinates(self::$features, $index) ?? null;
    }

    /**
     * Récupère les coordonnées géographiques d'une adresse et ses propriétés associées.
     * 
     * @param array $features Le résultat de recherche.
     * @param int $index L'index de l'adresse à récupérer.
     * @return null|Position Les coordonnées géographiques de l'adresse + les propriétés associées.
     */
    public static function getPosition($index = 0): ?Position
    {
        self::geocoder();

        return self::position(self::$features, $index) ?? null;
    }


    /**
     * cherche les adresses à partir de ses coordonnées géographiques.
     * 
     * @param float $lat Latitude de l'adresse.
     * @param float $lng Longitude de l'adresse.
     * @return static
     * @throws \Exception Si les coordonnées ne sont pas valides.
     */
    public static function reverse(float $lat, float $lng): static
    {
        self::$endPoint = self::ENDPOINT_REVERSE;

        // Vérification des coordonnées
        if (!is_numeric($lat) || !is_numeric($lng)) {
            if(self::$throwException) {
               throw new \Exception(self::ERROR_INVALID_COORDINATES);
            }else {
                list($lat, $lng) = self::$coordinates;
            }
        }

        $coordinates = "lon=$lng&lat=$lat";        

        self::$endPoint = str_replace('lonlat', $coordinates, self::ENDPOINT_REVERSE) ;

        return new static;
    }

    /**
     * Récupère une adresse a partir de l'index de l'adresse.
     *  
     * @param int $index L'index de l'adresse à récupérer.
     * @return null|Location L'adresse récupérée.
     * @throws \Exception Si l'adresse est manquante ou si aucun résultat n'est trouvé.
     */
    public static function getLocation($index = 0): ?Location
    {
        self::geocoder();

        $features = self::$features;

        if(! count($features)){
            throw new \Exception(self::ERROR_MISSING_RESULTS);
        }

        if (! isset($features[$index]['properties'])) {
            throw new \Exception(self::ERROR_MISSING_COORDINATES);
        }

        return Location::make($features[$index]['properties']) ?? null;
    }
    
    /**
     * Limite le nombre de résultats retournés par l'API.
     * 
     * @param int $limit Le nombre de résultats à retourner.
     * @return static     
     */
    public static function limit(int $limit): static
    {
        self::$limit = "$limit";

        return new static;
    }

    /**
     * Définit le code postal pour la recherche.
     * 
     * @param int|string $postcode Le code postal à utiliser.
     * @return static
     */
    public static function postcode(int|string $postcode): static
    {
        self::$postcode = str_pad((string)$postcode, 5, '0', STR_PAD_LEFT);

        return new static;
    }

    /**
     * Définit le code INSEE de la ville pour la recherche.
     * 
     * @param int $citycode Le code INSEE de la ville à utiliser.
     * @return static
     */
    public static function citycode(int $citycode): static
    {
        self::$citycode = "$citycode";

        return new static;
    }

    /**
     * Effectue une recherche d'adresse.
     * Il n'est pas nécessaire de spécifier le code postal ou le code INSEE de la ville,
     * car ils seront automatiquement ajoutés à la requête, lorsque les méthodes postcode() 
     * et citycode() sont utilisées pour filtrer les résultats.
     * 
     * @param string $query La requête de recherche.
     * @return static
     */
    public static function search(string $query = ''): static
    {
        self::$address = $query;
      
        return new static;
    }

    /**
     * Retourne la partie properties de la réponse qui est contenu dans les résultats de la recherche.
     * l'index par défaut est 0, ce qui signifie que la première adresse sera retournée.
     * 
     * @param int $index L'index de la propriété à récupérer.
     * @throws \Exception Si l'index est invalide ou si la propriété est manquante.
     * @return null|array Les propriétés de l'adresse.
     */
    public static function getProperties($index = 0): ?array
    {
        self::geocoder();

        $features = self::$features;

        if(count($features) && isset($features[$index]['properties'])){
            return self::$features[$index]['properties'];
        }

        if (self::$throwException) {
            throw new \Exception(self::ERROR_MISSING_PROPERTIES);
        }

        return null;
    }

    /**
     * Retourne la réponse complète de l'API.
     * 
     * @return array La réponse complète de l'API.
     */
    public static function get(): array
    {
        self::geocoder();

        return self::$response;
    }

/******************************************************************************
    Methode privée pour construire la requête API
******************************************************************************/ 

    /**
     * Effectue une requête à l'API de géocodage et gère les erreurs.
     * - Utilisation de la méthode buildQuery() pour construire la requête
     * - Utilisation de la méthode Http::get() pour effectuer la requête
     * - Gestion des erreurs avec un log d'erreur et une exception
     * 
     * @throws \Exception Si la requête échoue ou si la réponse est invalide.
     * @return void
     */
    private static function geocoder()
    {
        $query = self::buildQuery(); // Construction de la requête API
        $response = Http::get(self::buildQuery());
        
        //Log::info('Requête à l\'API de géocodage.', ['query' => $query,'response' => $response->json()]);
        
        if ($response->failed()) {
            Log::error('Erreur lors de la requête à l\'API de géocodage.', [
                'userId' => auth()->id(),
                'query' => $query,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('Erreur lors de la requête à l\'API de géocodage.');
        }

        self::$response = $response->json(); // Récupération de la réponse JSON
        self::$features = $response->json('features'); // Récupération des résultats de la recherche
    }    


    /**
     * Effectue une requête à l'API de géocodage et retourne les résultats.
     * - Utilisation de la constante BASE_URL pour l'URL de base
     * - Utilisation de la constante ENDPOINT_SEARCH pour le point d'entrée de la recherche d'adresse
     * - Construction de la requête avec les paramètres de recherche
     * - en filtrant les valeurs nulles et en utilisant http_build_query pour créer la chaîne de requête
     * 
     * @return array Les résultats de la requête.
     */
    private static function buildQuery(): string
    {
        $url = self::BASE_URL . (self::$endPoint ?? self::ENDPOINT_SEARCH);

        $query = array_filter([
            'q' => self::$address,
            'limit' => self::$limit ?? 1,
            'postcode' => self::$postcode,
            'citycode' => self::$citycode,
            'autocomplete' => self::$autocomplete,
        ]);
    
        return $url . http_build_query($query);
    }

    /**
     * Récupère les coordonnées géographiques d'un résultat de recherche.
     * 
     * @param array $feature Le résultat de recherche.
     * @param int $index L'index du résultat à récupérer.
     * @return null|Coordinates Les coordonnées géographiques.
     */
    private static function coordinates(array $features, int $index = 0): ?Coordinates
    {
        if(count($features) && isset($features[$index]['geometry']['coordinates'])){
            return Coordinates::make($features[$index]['geometry']['coordinates']);
        }

        if (self::$throwException) {
            throw new \Exception(self::ERROR_MISSING_COORDINATES);
        }

        return null;
    }

    private static function position(array $features, int $index = 0): ?Position
    {
        if(
            count($features) && 
            isset($features[$index]['geometry']['coordinates']) && 
            isset($features[$index]['properties'])
        ){
            return Position::make($features[$index]['geometry']['coordinates'], $features[$index]['properties']);
        }

        if (self::$throwException) {
            throw new \Exception(self::ERROR_MISSING_COORDINATES);
        }

        return null;
    }



}