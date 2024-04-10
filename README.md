Projet Symfony
Mise en place du projet Symfony :

    Monter le fichier compose.yaml pour configurer la base de données PostgreSQL.
    Une fois la base de données montée, mettre à jour les tables en base de données avec la commande :

    bash

php bin/console make:entity --regenerate

Pour initialiser le jeu de données, utiliser la commande :

bash

    php bin/console doctrine:fixtures:load

Une fois les différentes étapes réalisées, vous pouvez commencer à étudier l'API avec la documentation : localhost:8080/api/doc.
Implémentations réalisées :

    Mise en place de 4 entités :
        UserAccount
        Tweet
        Response (réponse à un tweet).
        Log

    Mise en place de l'authentification par JWT.

    Utilisation de Faker pour générer des données fictives.

    Mise en place du "safe delete".

    Chaque méthode des différents contrôleurs enregistre des logs en base de données pour assurer la traçabilité.

Modèle conceptuel des données final du projet :

![image](https://github.com/Raptoor44/HelloWorldA/assets/78044552/5aa50d6f-a213-481c-98a6-ed34b7225ca5)

Modèle logique des données final du projet :

![image](https://github.com/Raptoor44/HelloWorldA/assets/78044552/be4809f5-689d-4f18-85f6-0cab5192dc01)       
