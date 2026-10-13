# Procédure de mise en production – Pepe Chicken

## Environnements

| | Dev (local) | Prod |
|---|---|---|
| URL | https://pepe-chicken.ddev.site | https://mmi.unilim.fr/~sanchezperrau1/wordpress |
| Outil | DDEV (WSL) | Serveur mmi.unilim.fr |
| Dossier | `Bureau/Pepe chicken` | `/home/etudiants/sanchezperrau1/public_html/wordpress` |
| Base de données | `db` (DDEV) | `sanchezperrau1` (phpMyAdmin) |
| Préfixe des tables | `r3sae01_` | `r3sae01_` |
| Back office | `/wp-admin` | `/wp-admin` |

Les mots de passe ne sont pas notés ici.

## Accès au serveur

- Connexion en **SFTP** avec FileZilla : hôte `sftp://mmi.unilim.fr`, port `22`, identifiant `sanchezperrau1`.
- Le FTP classique (port 21) est fermé : WordPress ne peut pas installer ou mettre à jour les extensions depuis l'admin. Tout passe par l'envoi des fichiers depuis le dev.
- Base de données : phpMyAdmin, base `sanchezperrau1`.

## Première installation (IT-1)

1. Envoyer l'archive de WordPress (fr_FR) dans `public_html` et la décompresser dans `wordpress/`.
2. Ouvrir l'URL de prod et remplir la configuration de la base :
   nom `sanchezperrau1`, identifiant `sanchezperrau1`, hôte `localhost`, préfixe `r3sae01_` → crée `wp-config.php`.
3. Installer WordPress (titre « Pepe Chicken », compte administrateur).
4. Envoyer en SFTP depuis le dev :
   - `wp-content/plugins/woocommerce`, `secure-custom-fields`, `classic-editor` → `wordpress/wp-content/plugins/`
   - `wp-content/themes/storefront` et `wp-content/themes/pepe-chicken` → `wordpress/wp-content/themes/`
5. Dans l'admin de prod : activer les 3 extensions puis le thème **Pepe Chicken**.

## Mettre à jour la prod après une modification en dev

### 1. Modifications du thème enfant
1. En dev, tester les modifications sur https://pepe-chicken.ddev.site.
2. Commit et push sur GitHub avec le tag de l'itération :
   ```bash
   cd wp-content/themes/pepe-chicken
   git add .
   git commit -m "[IT-X] description"
   git push
   ```
3. Envoyer le dossier `pepe-chicken` (sans `.git` ni `docs`) dans `wordpress/wp-content/themes/` avec FileZilla (écraser les fichiers).
   Le thème contient : `style.css`, `functions.php` et le dossier `woocommerce/` (modèles WooCommerce surchargés, ex. `archive-product.php` pour la page Boutique). Ne pas oublier ce dossier.
4. Vider le cache du navigateur (Ctrl + F5) pour voir le nouveau CSS.

### 2. Nouvelle extension ou mise à jour d'extension
1. L'installer / la mettre à jour en dev.
2. Envoyer son dossier dans `wordpress/wp-content/plugins/` avec FileZilla.
3. L'activer dans l'admin de prod.

### 3. Contenu (produits, pages, réglages)
1. Exporter la base de dev :
   ```bash
   ddev export-db --file=pepe-chicken.sql.gz
   ```
2. Dans phpMyAdmin (base `sanchezperrau1`) : **Importer** le fichier `pepe-chicken.sql.gz`.
3. Remplacer l'URL de dev par l'URL de prod :
   dans la table `r3sae01_options`, modifier `siteurl` et `home` en
   `https://mmi.unilim.fr/~sanchezperrau1/wordpress`
   (ou `wp search-replace 'https://pepe-chicken.ddev.site' 'https://mmi.unilim.fr/~sanchezperrau1/wordpress' --all-tables` si WP-CLI est disponible).
4. Envoyer `wp-content/uploads/` (images des produits) dans `wordpress/wp-content/uploads/` avec FileZilla.
5. Se reconnecter à l'admin de prod (les comptes sont ceux de la base de dev).
6. Supprimer le fichier `.sql.gz` du PC une fois l'import fait (il ne doit jamais aller sur GitHub).

### 4. Vérifications après chaque mise en prod
- Le site et `/wp-admin` s'affichent sans erreur.
- Les extensions sont actives et le thème Pepe Chicken est activé.
- Les pages concernées par l'itération fonctionnent comme en dev.
- Ajouter une ligne dans `journal-mises-en-production.md`.
