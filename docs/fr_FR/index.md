# Plugin hplip

Plugin Permettant de se connecter sur les imprimantes de maque HP

Pour le moment le plugin récupère l'état des 4 cartouches et si l'imprimante est connectée.

L'actualisation des données peut prendre jusqu'à 30s

> Attention, le paquet HP pour la connexion aux imprimantes est assez conséquent (170Mo)

Pour l'installer:

* Activer le plugin

* Lancer l'installation des dépendances

* Créer un équipement:
  * Renseigner au minimum son adresse IP
  * L'actualisation est de base sur 5 min mais vous pouvez la changer depuis le champs "Auto Actualisation"
  * Sauvegarder l'équipement
> Cette opération peut durer jusqu'à une minute car l'imprimante est installé et les commandes créées en conséquences
