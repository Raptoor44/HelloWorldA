Pour mettre en place le projet symfony :

  1) Il faut monter compose.yaml pour monter la base de données postegresql
  2) Une fois la base de données monté, il faut mettre à jour les tables en base de données avec la commande : php bin/console make:entity --regenerate
  3) Pour initialiser le jeu de données, taper la commande : php bin/console doctrine:fixtures:load

  Une fois les différentes étapes réalisées, vous pouvez commencez à étudiez l'api avec la doc : localhost:8080/api/doc

Implémentations réalisées :

  Mise en place de 4 entités :
    UserAccount
    Tweet
    Response (réponse à un tweet).
    Log

  Mise en place de l'authenfication par JWT.
  Mise en place de faker
  Mise en place du safe delete.
  Chaque méthode des différents controllers enregistre des logs en base de données pour la mise en place traçabilité.


Modèle conceptuel des données final du projet (Noms des attributs différent sur le projet) :

![image](https://github.com/Raptoor44/HelloWorldA/assets/78044552/5aa50d6f-a213-481c-98a6-ed34b7225ca5)



Modèle logique des données final du projet (Noms des attributs différent sur le projet) :

![image](https://github.com/Raptoor44/HelloWorldA/assets/78044552/be4809f5-689d-4f18-85f6-0cab5192dc01)



                                                                                                                                
                                                                                                                                                                                                                                                                 
