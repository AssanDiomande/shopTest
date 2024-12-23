Installation :

1) importer le fichier sql dans la base de données
2) modifier le fichier Config/database.ini pour mettre la bonne configuration

Utilisation de l'API :

1) Exécuter la requête POST vers l'endpoint /login pour récupérer un token, le corps de la requête est :
    {
        "email" : "user@test.com",
        "password" : "passwordTest"
    }
2) copier le token puis la coller comme autorisation 'Bearer <token>' lors d'une autre requête (voir la liste des endpoints disponible)

HTACCESS :
- si les instruction des fichier .htaccess ne fonctionnent pas, modifier la configuration apache2 (apache.conf) et mettre la valeur de AllowOverride à All
- permet de rediriger les requêtes vers le fichier index.php du dossier public
- permet aussi de pouvoir envoyer un token en tant qu'autorisation (Bearer token)

BDD : 
- Connexion géré via le fichier Database.php
- La configuration se trouve dans le fichier Config/database.ini, elle est utilisé dans Database.php lors d'une connexion
- Les requêtes sql sont créées dans les fichiers du dossier Repository (chaque fichier correspond à une table spécifique)
- tables créées : 'store' et 'user'

Route :
- Géré dans le fichier Router/Routes.php
- Ajout des routes dans le constructeur
- Appel du controller adéquat avec la méthode résolve

Endpoints :

    POST /login :
    - connecte un utilisateur et retourne un token
    - corps de la requête (exemple) :
        {
            "email" : "user@test.com",
            "password" : "passwordTest"
        }
    - exemple resultat :
        {
            "status": "success",
            "code": 200,
            "token": <token>
        }

    GET /stores :
    - affiche tous les magasins enregistrés
    - possibilité de trier chaque champ par ordre croissant ou décroissant (synthaxe : /store?champ=valeurTri)
    - champs disponibles pour triage : id, name, adress, owner, created_at
    - valeur des tri disponibles : 'asc' ou 'desc'
    - exemple pour trier par ordre croissant : /stores?id=asc
    - exemple pour trier par ordre décroissant : /stores?id=desc
    - exemple pour récupérer les magasins sans triage : /stores
    - exemple resultat :
        {
            "status": "success",
            "code": 200,
            "stores": [
                {
                    "store_id": 1,
                    "store_name": "store 1",
                    "store_adress": "adress 1",
                    "store_owner": "owner 1",
                    "store_created_at": "2024-07-17"
                },
                {
                    "store_id": 2,
                    "store_name": "store 2",
                    "store_adress": "adress 2",
                    "store_owner": "owner 2",
                    "store_created_at": "2024-07-17"
                }
            ]
        }

    GET /store/:id :
    - affiche un magasin en fonction de son identifiant
    - exemple /store/1
    - exemple resultat :
        {
            "status": "success",
            "code": 200,
            "stores": [
                {
                    "store_id": 1,
                    "store_name": "store 1",
                    "store_adress": "adress 1",
                    "store_owner": "owner 1",
                    "store_created_at": "2024-07-17"
                }
            ]
        }

    POST /store :
    - crée un magasin
    - champs requis : store_name, store_adress, store_owner, store_created_at
    - corps de la requête (exemple) :
        {
            "store_name": "name test",
            "store_adress": "adress test",
            "store_owner": "owner test",
            "store_created_at": "2024-07-17"
        }
    - exemple resultat :
        {
            "status": "success",
            "code": 201,
            "message": "Ressource créée"
        }

    PATCH /store/:id :
    - modifie au moins un champ d'un magasin
    - au moins un de ces champs doit être présent : store_name, store_adress, store_owner, store_created_at
    - corps de la requête (exemple) :
        {
            "store_name" : "name test",
            "store_adress": "adress test",
        }
    - exemple resultat :
        {
            {
                "status": "success",
                "code": 201,
                "message": "Ressource 1 mis à jour"
            }
        }

    PUT /store/:id :
    - modifie tous les champs d'un magasin
    - champs requis : store_name, store_adress, store_owner, store_created_at
    - corps de la requête (exemple) :
        {
            "store_name": "name test",
            "store_adress": "adress test",
            "store_owner": "owner test",
            "store_created_at": "2024-07-17"
        }
    - exemple resultat :
        {
            {
                "status": "success",
                "code": 201,
                "message": "Ressource 2 mis à jour"
            }
        }

    DELETE /store/:id :
    - supprime un magasin en fonction de son identifiant
    - exemple /store/1
    - exemple resultat :
        {
            {
                "status": "success",
                "code": 201,
                "message": "Ressource 2 supprimée"
            }
        }
