Peerassess
=======

Nouvelle organisation des Bundle
-----------------------
Afin de permettre une plus grande flexibilité dans le futur, l'organisation des
bundle est en train de changer. L'application sera organisée en 3 bundles:
* Le `CoreBundle` contient le code qui peut-être réutilisé dans d'autre Bundle
* Le `MarketingWebsiteBundle` contient le code qui ne sert *que* pour le site vitrine de présentation du produit
* Le `ProductWebsiteBundle` contient le code qui ne sert *que* au site web du produit
* Le `AdminBundle` contient le dashboard administrateur, réservé aux employés Peerassess

Plus concrètement, les entités liées aux tests et aux utilisateurs se trouvent dans le `CodeBundle`, ainsi que les classes utilisables dans d'autres bundle (par exemple la classe qui gère l'état d'une session d'évaluation).

Par contre, l'entité qui représente un message envoyé via la page de contact du site vitrine n'est utile que dans le site vitrine. C'est donc dans le `MarketingWebsiteBundle` qu'elle se trouve.

De même, les gestionnaires d'évènements tels que le `InviteListener` ne s'applique qu'un site web du produit, donc il se trouve dans le `ProductWebsiteBundle`.

Contenu des controlleurs
-----------------------

Afin de rendre le code testable, les controlleurs doivent contenirs le minimum de code possible tel que la gestion des permissions, la gestion de formulaire HTML ou les templates.

Si un controlleurs souhaitent modifier des objets en base de donnée, envoyer un mail ou effectuer une autre action non triviale, il délègue cette tâche à un [service](http://symfony.com/doc/current/book/service_container.html). Les services doivent utiliser l'injection de dépendance ou c'est sensé afin de préserver la testabilité du-dit service.

Ancienne organisation des Bundle
-----------------------
L'application est découpée en 5 bundles :
  * Le VitrineBundle contient toutes les pages d'accueil présentant la solution
  * Le SupervisorBundle contient l'application dédiée aux recruteurs
    * Préparations de tests
    * Invitations de candidats
    * Présentation des résultats
  * Le DevBundle contient l'application dédiée aux candidats développeurs
    * Interface d'accueil & profil
    * Passage de tests : interface de pull / push du code
    * Correction des autres tests
  * Le CoreBundle contient les entités ainsi que des utilitaires (template ou services) communs à plusieurs bundles (langages, énoncé de test, changement de locale...)
  * Le UserBundle étends fosuserbundle et gére tout le système d'utilisateurs

Entités de test
---------------
À garder en tête, les entités dédiées au test :
  * Un Test (CoreBundle) correspond à l'énoncé, le sujet de test.
  * Une Evaluation (SupervisorBundle) est crée par un recruteur, et lui permet d'inviter des candidats à passer un test.
  * Une EvaluationSession (DevBundle) correspond au passage d'une évaluation, qui contient la note et le résultat.

MAJ du schema
-------------
Pour mettre à jour le schema de la base de données
  * Si des modifications ont été pullé :
  ```
	php app/console doctrine:migrations:migrate
	```
  * Si des modifications ont été faites :
	```
	php app/console doctrine:migrations:diff
	```
  * Cette commande crée un fichier qu'il faut pusher avec
  ```
	php app/console doctrine:migrations:migrate
	```

Server dependencies
-------------------

- Apache2
- PHP 5.5+
- php5-imap
