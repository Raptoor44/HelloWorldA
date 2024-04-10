# API de Tweet :

Pour mettre en place le projet Symfony :
(La partie 1 n'est pas obligatoire)

    1) La partie numéro 1 n'est pas obligatoire.

    2) Monter le fichier compose.yaml pour configurer la base de données PostgreSQL.

    3) Une fois la base de données configurée, mettre à jour les tables en exécutant la commande : php bin/console make:entity --regenerate.

    4) Pour initialiser le jeu de données, exécuter la commande : php bin/console doctrine:fixtures:load.

Une fois ces étapes réalisées, vous pouvez commencer à explorer l'API via la documentation disponible à l'adresse : localhost:8080/api/doc.

Implémentations réalisées :

    Mise en place de 4 entités : UserAccount, Tweet, Response (réponse à un tweet), Log.
    Mise en place de l'authentification par JWT.
    Implémentation des rôles (ADMIN_ROLE : Droit de suppression + accès aux logs | "Sans Rôle" : tous les autres droits d'un utilisateur standard).
    Utilisation de Faker pour la génération de données factices.
    Mise en place du "safe delete".
    Enregistrement des logs en base de données pour assurer la traçabilité de chaque méthode des différents contrôleurs.
    Tentative d'implémentation du "soft delete" avec Gedmo.
    Tentative d'utilisation de "voters" pour la gestion des rôles.

Modèle conceptuel des données final du projet (Noms des attributs différent sur le projet) :

![image](https://github.com/Raptoor44/HelloWorldA/assets/78044552/90cf5a41-c567-418b-82fc-ed900f4e99f6)




Modèle logique des données final du projet (Noms des attributs différent sur le projet) :

![image](https://github.com/Raptoor44/HelloWorldA/assets/78044552/be4809f5-689d-4f18-85f6-0cab5192dc01)      
