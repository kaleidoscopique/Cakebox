## A quoi servent Cakebox et le Mardambey-script ?

Le MB-script vous permet d'installer une seedbox sur votre serveur ou sur votre VPS en quelques minutes sans demander le moindre effort. Une seedbox permet de télécharger des fichiers torrent depuis une interface web très agréable et de les stocker sur un serveur distant, sans passer par votre connexion internet.

Cakebox permet de lire tous vos fichiers torrent en streaming depuis une autre interface web très pratique. En clair :

1. Vous executez le MardamBey-script, il vous installe une seedbox et Cakebox
2. Vous téléchargez tous les torrents que vous voulez sur votre seedbox, ça va très vite !
3. Vous les regardez tranquillement en streaming, comme si vous possédiez un Megaupload privé.

Deux screenshots de Cakebox : 
* [La liste de vos fichiers torrents](http://cloud.github.com/downloads/MardamBeyK/Cakebox/screen1.png)
* [La page de visionage d'un torrent](http://cloud.github.com/downloads/MardamBeyK/Cakebox/screen_2.png)

## Comment faire pour installer tout ça ?

1. Disposez d'un VPS, OS Debian ou basé Debian uniquement (très bonnes offres de serveurs offshore chez [Transip](https://www.transip.eu/vps/pricing-and-purchase/) et [Leaseweb](http://www.leaseweb.com/en/cloud-hosting/express-cloud))
2. Téléchargez le mardambey-script et executez-le :
`$ wget https://github.com/downloads/MardamBeyK/Cakebox/mardambey_script.sh`
`$ chmod +x mardambey_script.sh && ./mardambey_script.sh --enable-htaccess`
3. C'est terminé ! Téléchargez. Streamez.

## Que fait le MardamBey-Script sur mon serveur ?

Il installe un serveur web et diverses bibliothèques. Il modifie la configuration d'Apache pour activer les .htaccess si le paramètre "--enable-htaccess" est spécifié. Il crée 2 dossiers à la racine du serveur web (par défaut /var/www/ mais peut être changé avec --www-dir=/path/) pour Cakebox et Rutorrent. Il ajoute un cron qui agit toutes les 5 minutes sur le dossier "downloads" de Cakebox pour appliquer un chmod 777. Enfin, il lance rutorrent en tâche de fond avec screen.

## Remerciements

* Merci à Jeremie pour son aide sur les problèmes d'encodage et ses suggestions;
* Merci à Skalp de Plugngeek.net d'avoir soutenu le projet;
* Merci à tous ceux qui ont donné un coup de pouce et qui continuent à le faire;
* Merci à HADOPI pour l'idée;

**Auteur :** MardamBey (iam.mardaybey@gmail.com)
[Retrouvez-moi sur Twitter](http://www.twitter.com/kaleidoscopique "Follow me !")
