# Journal des mises en production – Pepe Chicken

| Date | Itération | Ce qui a été mis en production | Méthode | Vérifié |
|---|---|---|---|---|
| 13/10/2026 | IT-1 | Installation de WordPress 7.1.3 (fr_FR) sur mmi.unilim.fr, base `sanchezperrau1`, préfixe `r3sae01_` | Archive décompressée sur le serveur + installation WordPress | Oui |
| 13/10/2026 | IT-1 | Extensions WooCommerce 11.2.1, Secure Custom Fields 6.9.5, Classic Editor 1.7.0 | Envoi SFTP depuis le dev (FileZilla) + activation dans l'admin | Oui |
| 13/10/2026 | IT-1 | Thème parent Storefront + thème enfant Pepe Chicken (commit `[IT-1]`) | Envoi SFTP depuis le dev + activation | Oui |
| 16/10/2026 | IT-1 | `wp-config.php` de prod refait manuellement à partir de `wp-config-sample.php` : base `sanchezperrau1`, hôte `localhost`, préfixe `r3sae01_`, nouvelles clés de sécurité (US-1.4) | Fichier édité en local puis envoyé en SFTP (FileZilla) dans `public_html/wordpress/` | Oui |

## Remarques
- 13/10/2026 : l'installation d'extensions depuis l'admin de prod demande un accès FTP (port 21), fermé sur le serveur. Les extensions et thèmes sont donc envoyés en SFTP (port 22) depuis le dev.
- 13/10/2026 : la base de prod vient d'une installation neuve ; l'import de la base de dev sera fait après l'IT-2 (produits).
- 16/10/2026 : premier envoi du `wp-config.php` refusé (« Erreur lors de la connexion à la base de données ») à cause d'un mauvais mot de passe ; corrigé puis renvoyé. Le changement de clés de sécurité a déconnecté les sessions, reconnexion à l'admin nécessaire.
