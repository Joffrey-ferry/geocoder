# Geocoder Package

**Geocoder** est une classe PHP permettant d'interagir avec l'API Adresse du gouvernement français. Elle permet de rechercher des adresses, de récupérer leurs coordonnées géographiques, et d'effectuer des recherches inversées à partir de coordonnées.

## 📚 Documentation officielle de l'API
- [Documentation API Adresse](https://adresse.data.gouv.fr/api-doc/adresse)
- [Documentation générale](https://adresse.data.gouv.fr/api-doc)

---

## 🚀 Installation

### Prérequis
- PHP 8.0 ou supérieur
- Framework Laravel (facultatif, mais recommandé)

### Étapes
1. Clonez ce dépôt ou ajoutez-le à votre projet.
2. Assurez-vous que les dépendances nécessaires (comme `illuminate/http`) sont installées via Composer :
   ```bash
   composer require illuminate/http
   ```

## 🛠️ Utilisation

### 1. Recherche d'une adresse
Pour rechercher une adresse, utilisez la méthode `search()` :
```php
use JoffreyFerry\Geocoder\Geocoder;

$results = Geocoder::search('29 rue de Rivoli, Paris')->get();
```

### 2. Recherche inversée (à partir de coordonnées)
Pour trouver une adresse à partir de coordonnées géographiques :
```php
use JoffreyFerry\Geocoder\Geocoder;

$location = Geocoder::reverse(48.8566, 2.3522)->getLocation();
```

### 3. Limiter les résultats
Pour limiter le nombre de résultats retournés par l'API :
```php
use JoffreyFerry\Geocoder\Geocoder;

$results = Geocoder::search('rue de Rivoli, Paris')->limit(5)->get();
```

### 4. Filtrer par code postal ou code INSEE
Pour filtrer les résultats par code postal ou code INSEE :
```php
use JoffreyFerry\Geocoder\Geocoder;

$results = Geocoder::search('29 rue de Rivoli')
    ->postcode(75001)
    ->get();
```

### 5. Récupérer les coordonnées d'une adresse
Pour récupérer les coordonnées géographiques d'une adresse spécifique :
```php
use JoffreyFerry\Geocoder\Geocoder;

$coordinates = Geocoder::search('29 rue de Rivoli, Paris')->getCoordinates();
```

---

## ⚙️ Méthodes disponibles

### Méthodes publiques
| Méthode                  | Description                                                                                   |
|--------------------------|-----------------------------------------------------------------------------------------------|
| `search(string $query)`  | Recherche une adresse à partir d'une requête.                                                 |
| `reverse(float $lat, float $lng)` | Recherche une adresse à partir de coordonnées géographiques.                              |
| `limit(int $limit)`      | Limite le nombre de résultats retournés par l'API.                                           |
| `postcode(int $postcode)`| Filtre les résultats par code postal.                                                        |
| `citycode(int $citycode)`| Filtre les résultats par code INSEE de la ville.                                             |
| `get()`                  | Retourne la réponse complète de l'API.                                                       |
| `getCoordinates(int $index = 0)` | Récupère les coordonnées géographiques d'une adresse à partir de l'index des résultats. |
| `getLocation(int $index = 0)`    | Récupère une adresse complète à partir de l'index des résultats.                        |
| `getProperties(int $index = 0)`  | Retourne les propriétés d'une adresse à partir de l'index des résultats.                |

---

## 📄 Licence
Ce projet est sous licence MIT. Consultez le fichier `LICENSE` pour plus d'informations.

---

## 📝 Remarques
- Ce package utilise l'API Adresse du gouvernement français. Assurez-vous de respecter les conditions d'utilisation de l'API.
- En cas de problème ou de suggestion, n'hésitez pas à ouvrir une issue ou à soumettre une pull request.

---

## 👨‍💻 Auteur
Développé par **Joffrey Ferry**.

---
```

Si vous souhaitez ajouter ou modifier des sections spécifiques, faites-le-moi savoir !   