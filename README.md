# Pour mettre en place le projet symfony :

 Heading La partie numéro 1 n'est pas obligatoire.

  1) Il faut monter compose.yaml pour monter la base de données postegresql
  2) Une fois la base de données monté, il faut mettre à jour les tables en base de données avec la commande :  `php bin/console make:entity --regenerate`
  3) Pour initialiser le jeu de données, taper la commande : `php bin/console doctrine:fixtures:load`

  Une fois les différentes étapes réalisées, vous pouvez commencez à étudiez l'api avec la doc : `localhost:8080/api/doc`

Implémentations réalisées :

  Mise en place de 4 entités :
    UserAccount
    Tweet
    Response (réponse à un tweet).
    Log

  Mise en place de l'authenfication par JWT.
  Implémentation des rôles (ADMIN_ROLE : Droit de suppression + droit sur les logs | "Sans Role" : tous les autres droits d'un utilisateur lamda).
  Mise en place de faker.
  Mise en place du safe delete.
  Chaque méthode des différents controllers enregistre des logs en base de données pour la mise en place traçabilité.
  Tentative de mettre en place le soft delete avec Gedmo.
  Tentative de mettre d'utiliser les vooters pour la gestion des rôles.


Modèle conceptuel des données final du projet (Noms des attributs différent sur le projet) :

![image](https://github.com/Raptoor44/HelloWorldA/assets/78044552/90cf5a41-c567-418b-82fc-ed900f4e99f6)




Modèle logique des données final du projet (Noms des attributs différent sur le projet) :

![image](https://github.com/Raptoor44/HelloWorldA/assets/78044552/be4809f5-689d-4f18-85f6-0cab5192dc01)      
