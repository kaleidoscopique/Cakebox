#!/bin/bash

 ######     ###    ##    ## ######## ########   #######  ##     ## 
##    ##   ## ##   ##   ##  ##       ##     ## ##     ##  ##   ##  
##        ##   ##  ##  ##   ##       ##     ## ##     ##   ## ##   
##       ##     ## #####    ######   ########  ##     ##    ###    
##       ######### ##  ##   ##       ##     ## ##     ##   ## ##   
##    ## ##     ## ##   ##  ##       ##     ## ##     ##  ##   ##  
 ######  ##     ## ##    ## ######## ########   #######  ##     ## 
#					par MardamBey (iam.mardambey@gmail.com)
#					https://github.com/MardamBeyK/Cakebox

# ~~~~~~~ PARAMETRES DU SCRIPT ~~~~~~~~
#
#	* 	--wwwdir=/chemin/vers/le/serveur/web 		Par défaut /var/www
#								/!\ Ne mas mettre de "/" final
#
#	* 	--enable-htaccess 				Modifie Apache2 pour activer les .htaccess
#							        /!\ Obligatoire si Apache n'est pas encore installé ou pas configuré sur votre serveur
#
# Il est complètement déconseillé d'exécuter ce script sur un serveur en production ou déjà configuré
# Ce script a été conçu pour être exécuté sur un serveur vierge.
# Vous n'avez rien à modifier avant de le lancer.
# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

# URL des fichiers à télécharger pendant l'installation
# Important : avant de changer l'URL d'un fichier, vérifiez que le dossier extrait
# porte bien le même nom que l'archive elle-même (sans .tar.gz bien entendu)
# Exemple : `tar -xvf libtorrent-0.13.0.tar.gz` donne bien un dossier `libtorrent-0.13.0`
URL_LIBTORRENT="https://github.com/MardamBeyK/Cakebox/raw/master/softwares/libtorrent-0.13.0.tar.gz"
URL_RTORRENT="https://github.com/MardamBeyK/Cakebox/raw/master/softwares/rtorrent-0.9.0.tar.gz"
URL_XMLRPC="https://github.com/MardamBeyK/Cakebox/raw/master/softwares/xmlrpc-c-1.25.17.tar.gz"
URL_RUTORRENT="https://github.com/MardamBeyK/Cakebox/raw/master/softwares/rutorrent-3.4.tar.gz"
URL_RUTORRENT_LINKCAKEBOX="https://github.com/MardamBeyK/Cakebox/raw/master/softwares/rutorrent-linkcakebox-1.0.zip"

# Liste des plugins qui seront installés avec rutorrent
# Liste entière ici : http://goo.gl/cFFNa
# Attention, certains plugins (comme erasedata) font planter la version actuelle de rutorrent (erreur 500)
PLUGINS_LIST="diskspace unpack create"

# ========================================================
# 	     NE RIEN MODIFIER A PARTIR D'ICI
# ========================================================

# Fonction de génération d'un password aléatoire pour le .htaccess
function randompass () {
        local randompassLength
        if [ $1 ]; then
                randompassLength=$1
        else
                randompassLength=8
        fi

        pass=</dev/urandom tr -dc A-Za-z0-9 | head -c $randompassLength
        echo $pass
}

# Parsing des paramètres
for i in $*
do
        case $i in
        --wwwdir=*)
                WWWDIR=`echo $i | sed 's/[-a-zA-Z0-9]*=//'`
                ;;
        --enable-htaccess)
                ENABLEHT=1
                ;;
        *)
                ;;
        esac
done

# Configuration du répertoire web public
if [ -z $WWWDIR ]
then
        WWWDIR="/var/www"
fi


# Détéction du gestionnaire de paquet à utiliser (aptitude en priorité)
if [ "`dpkg --status aptitude | grep Status:`" == "Status: install ok installed" ]
then
        packetg="aptitude"
else
        packetg="apt-get"
fi

# Début des logs (tee)
( (

# Ajoute des dépots non-free
echo "deb http://ftp2.fr.debian.org/debian/ squeeze main non-free
deb-src http://ftp2.fr.debian.org/debian/ squeeze main non-free" >> /etc/apt/sources.list

# Installation des paquets vitaux
$packetg update
$packetg install -y subversion php5 libapache2-mod-scgi php5-curl build-essential automake libtool libcppunit-dev libcurl3-dev libsigc++-2.0-dev unzip unrar curl libncurses-dev git screen

# On se place sur le serveur web
cd $WWWDIR

# Installation de Rutorrent
FILE_RUTORRENT=$(basename $URL_RUTORRENT)			# Extraction du nom de l'archive
DIR_RUTORRENT=$(echo $FILE_RUTORRENT | sed "s/.tar.gz//g")	# Extraction du nom du dossier extrait
wget --no-check-certificate $URL_RUTORRENT						# Téléchargement des sources
tar -zxvf $FILE_RUTORRENT                                       # Extraction
mv $DIR_RUTORRENT rutorrent					# Renommage en /rutorrent
chmod 777 -R rutorrent/share/					# Configuration des permissions
# Installation des plugins de Rutorrent
cd rutorrent/plugins/
for X in $PLUGINS_LIST
do
        echo "Téléchargement et installation de $X"
        svn co http://rutorrent.googlecode.com/svn/trunk/plugins/$X
done

# Installation du plugin Rutorrent-Cakebox, conçu par Magicalex et Lechatleon
wget --no-check-certificate $URL_RUTORRENT_LINKCAKEBOX
unzip rutorrent-linkcakebox-1.0.zip
rm rutorrent-linkcakebox-1.0.zip && rm -R __MACOSX

# On se replace sur le serveur web
cd $WWWDIR

# Installation de Cakebox
git clone git://github.com/MardamBeyK/Cakebox.git		# Récupération dans le dépot
mv Cakebox/ cakebox/                                            # Renommage en /cakebox
chmod 777 cakebox/downloads cakebox/data                        # Configuration des permissions
chmod +x cakebox/patch_update                                   # Exec de la MAJ possible

# Installation d'un crontab pour attribuer des droits au dossier /downloads de Cakebox
cd && crontab -l > crontab.new
echo "* * * * * chmod -R 777 $WWWDIR/cakebox/downloads" >> crontab.new
crontab crontab.new

# Installation du mode SGCI d'Apache (obligatoire pour rtorrent et rutorrent)
echo SCGIMount /RPC2 127.0.0.1:5000 >> /etc/apache2/apache2.conf

# Génération du .htpasswd
htpassword=`randompass 10`
htpasswd -mbc $WWWDIR/.htpasswd admin $htpassword

# Génération des .htaccess
echo "AuthName \"Restricted\"
AuthType Basic
AuthUserFile \"$WWWDIR/.htpasswd\"
Require valid-user" > $WWWDIR/.htaccess # Protege la racine du serveur web

echo "Satisfy Any
Options -Indexes
" > $WWWDIR/cakebox/data/.htaccess	# Protege les downloads de Cakebox (pas d'index of/)

echo "Satisfy Any
Options -Indexes
" > $WWWDIR/cakebox/downloads/.htaccess	# Protege les downloads de Cakebox (pas d'index of/)

# Configuration et activation des .htaccess dans Apache2 si l'utilisateur l'a demandé
if [ -n $ENABLEHT ]
then
        cd /etc/apache2/sites-enabled/
        sed '0,/AllowOverride None/ s//AllowOverride All/' 000-default > 000-default.1
        sed '0,/AllowOverride None/ s//AllowOverride All/' 000-default.1 > 000-default
fi

# Redémarage d'Apache pour prendre en compte la nouvelle configuration
a2enmod scgi && /etc/init.d/apache2 restart

# Installation de XMLRPC
cd                                                              # Retour à la racine
FILE_XMLRPC=$(basename $URL_XMLRPC)                             # Extraction du nom de l'archive
DIR_XMLRPC=$(echo $FILE_XMLRPC | sed "s/.tar.gz//g")            # Extraction du nom du dossier extrait
wget --no-check-certificate $URL_XMLRPC && tar -zxvf $FILE_XMLRPC                      # Téléchargement de XMLRPC-C
cd $DIR_XMLRPC                                                  # Compilation et installation
./configure --disable-cplusplus
make
make install

# Installation de la libtorrent
cd                                                              # Retour à la racine
FILE_LIBTORRENT=$(basename $URL_LIBTORRENT)                     # Extraction du nom de l'archive
DIR_LIBTORRENT=$(echo $FILE_LIBTORRENT | sed "s/.tar.gz//g")    # Extraction du nom du dossier extrait
wget --no-check-certificate $URL_LIBTORRENT && tar -zxvf $FILE_LIBTORRENT              # Téléchargement de la libtorrent
cd $DIR_LIBTORRENT                                              # Compilation et installation
./autogen.sh
./autogen.sh
./configure
make
make install

# Installation de Rtorrent
cd                                                              # Retour à la racine
FILE_RTORRENT=$(basename $URL_RTORRENT)                         # Extraction du nom de l'archive
DIR_RTORRENT=$(echo $FILE_RTORRENT | sed "s/.tar.gz//g")        # Extraction du nom du dossier extrait
wget --no-check-certificate $URL_RTORRENT && tar -zxvf $FILE_RTORRENT                 # Téléchargement de rtorrent
cd $DIR_RTORRENT                                                # Compilation et installation
./autogen.sh && ./configure --with-xmlrpc-c 
make
make install
ldconfig

# Configuration de Rtorrent
cd                                                              # Retour à la racine
echo "directory = $WWWDIR/cakebox/downloads
session = ~/session
port_range = 6890-6999
port_random = no
check_hash = yes
use_udp_trackers = yes
schedule = watch_directory,15,15,load_start=~/watch/*.torrent
dht = auto
dht_port = 6881
scgi_port = 127.0.0.1:5000" > .rtorrent.rc
mkdir session                                                   # Création d'un dossier système

# Application des droits nécessaires à Cakebox et Rutorrent
chown -R www-data:www-data $WWWDIR

# Nettoyage des fichiers d'installation
rm -R /var/www/cakebox/softwares

# Lancement de rtorrent en background
screen -fa -d -m /usr/local/bin/rtorrent

# Affichage final
echo "--"
echo "--"
echo "=========== FIN DE L'INSTALLATION ! On dirait que tout a fonctionne ! ============"
echo "Username : admin"
echo "Password : $htpassword"
echo "Vous pouvez changer vos logins dans $WWWDIR/.htpasswd ou grâce à la commande htpasswd."
echo "--"
echo "Maintenant, rendez-vous sur :"
echo "http://ip_of_your_server/cakebox/"
echo "et"
echo "http://ip_of_your_server/rutorrent/"
echo "Profitez bien !"
if [ -n $ENABLEHT ]
then
        echo "[Note : les .htaccess d'Apache2 ont été activés pendant l'installation]"
fi
echo "==========================================================================="
echo "--"
echo "--"
) 2>&1) | tee mardambey_script.log
mv mardambey_script.log $WWWDIR
