# Plugin jeedom pour les caméras SERCOMM

Paramétrage avancées des caméras de la marque blanche SERCOMM

Plus connu sous les marques suivantes :
- SFR (Sercomm RC8221W v2, RC8221) dans la gamme : Homescope, Home by SFR (accès partiel aux fonctionnalités)
SFR ayant doté son firmware d'un VPN la caméra n'est pas capable de communiquer avec l'extérieur, les notifications HTTP, et POST HTTP ne sont pas fonctionnel)
- FREE (Sercomm RC8310) dans la gamme : Freebox Delta
- ORANGE (Sercomm RC8110) dans la gamme : HomeLive

# Accès administrateur aux caméras

- SFR = login : administrator mot de passe : cgiconfig
- FREE = login : freeboxcam mot de passe : disponible dans l'application Freebox Home

Ce plugin utilise l'API officiel du constructeur pour accéder et modifier les paramètres non accessibles via l'interface officiel du revendeur.

Vous aurez notamment accès à :

* [MOTION] Les réglages sur la détection de mouvement
* [EVENT] Le comportement de la caméra en cas de détection d'évènement
* [NETWORK] Les configuration réseau avancées
* [HTTP] Les notification HTTP
* [RTSP_RTP] Les paramétrages des flux vidéos
* [EMAIL] L'envoi des mails
* [FTP] L'envoi vers un serveur FTP
* [AUDIO] Le paramétrage de l'audio
* [LOG] Log debug de la caméra
etc...
