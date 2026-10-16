# Procédure de mise en production – Pepe Chicken

## Environnements

| | Dev (local) | Prod |
|---|---|---|
| URL | https://pepe-chicken.ddev.site | https://mmi.unilim.fr/~sanchezperrau1/wordpress |
| Outil | DDEV dans WSL (Ubuntu) + Docker Desktop | Serveur mmi.unilim.fr |
| Dossier | `Bureau/Pepe chicken` | `/home/etudiants/sanchezperrau1/public_html/wordpress` |
| Base de données | `db` (DDEV) | `sanchezperrau1` (phpMyAdmin) |
| Préfixe des tables | `r3sae01_` | `r3sae01_` |
| Back office | `/wp-admin` | `/wp-admin` |

Les mots de passe ne sont pas notés ici.

La page Design System HTML/CSS est dans `docs/design-system/` et publiée avec GitHub Pages
(réglage du dépôt : Settings → Pages → branche `main`, dossier `/docs`) :
https://loutsyam.github.io/Sa--3.01_pepechicken/design-system/

## Démarrer l'environnement de dev

1. Ouvrir **Docker Desktop** (attendre que l'icône soit verte).
2. Dans l'invite de commandes Windows :
   ```bash
   wsl
   cd "/mnt/c/Users/tsuku/Desktop/Pepe chicken"
   ddev start
   ```
3. Ouvrir https://pepe-chicken.ddev.site (admin : `/wp-admin`). Arrêter avec `ddev stop`.

Les fichiers (WordPress, extensions, thème) sont dans le dossier `Pepe chicken`, mais **la base de données est stockée dans Docker**.
Elle peut être perdue si Docker est réinitialisé : la sauvegarder régulièrement, dans `Pepe chicken` (jamais dans le thème) :
```bash
ddev export-db --file=sauvegarde-AAAA-MM-JJ.sql.gz
```
Restauration : `ddev import-db --file=sauvegarde-AAAA-MM-JJ.sql.gz`.

Le `wp-config.php` à la racine de `Pepe chicken` est celui du dev (généré par DDEV, base `db`) : il ne doit **jamais** être envoyé en prod.

## Accès au serveur

- Connexion en **SFTP** avec FileZilla : hôte `sftp://mmi.unilim.fr`, port `22`, identifiant `sanchezperrau1`.
- Le FTP classique (port 21) est fermé : WordPress ne peut pas installer ou mettre à jour les extensions depuis l'admin. Tout passe par l'envoi des fichiers depuis le dev.
- Connexion en **SSH** (même port 22) pour les commandes sur le serveur : `ssh sanchezperrau1@mmi.unilim.fr` (git, WP-CLI).
- Base de données : phpMyAdmin, base `sanchezperrau1`.

## Première installation (IT-1)

1. Envoyer l'archive de WordPress (fr_FR) dans `public_html` et la décompresser dans `wordpress/`.
2. Configurer **manuellement** `wp-config.php` (US-1.4) :
   - partir de `wp-config-sample.php` (copie de travail dans `Pepe chicken/mise-en-prod/wp-config.php`, hors GitHub) ;
   - renseigner `DB_NAME` = `sanchezperrau1`, `DB_USER` = `sanchezperrau1`, `DB_PASSWORD` (mot de passe de la base, non noté ici), `DB_HOST` = `localhost` ;
   - définir le préfixe dédié au projet : `$table_prefix = 'r3sae01_';` ;
   - remplacer les clés de sécurité (`AUTH_KEY`…) par des clés uniques ;
   - désactiver le débogage en prod (`WP_DEBUG` à `false`) ;
   - envoyer le fichier par SFTP dans `public_html/wordpress/`, puis vérifier que le site s'affiche.
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
3. Mettre à jour le thème sur le serveur **depuis le dépôt GitHub** (US-1.4), en SSH :
   ```bash
   ssh sanchezperrau1@mmi.unilim.fr
   cd ~/public_html/wordpress/wp-content/themes
   # première fois seulement (renommer d'abord l'ancien dossier envoyé en SFTP) :
   git clone https://github.com/Loutsyam/Sa--3.01_pepechicken.git pepe-chicken
   # mises à jour suivantes :
   cd pepe-chicken && git pull
   ```
   Méthode de secours (sans SSH) : envoyer le dossier `pepe-chicken` (sans `.git` ni `docs`) dans `wordpress/wp-content/themes/` avec FileZilla.
   Le thème contient : `style.css`, `functions.php` et le dossier `woocommerce/` (modèles WooCommerce surchargés, ex. `archive-product.php` pour la page Boutique). Ne pas oublier ce dossier.
4. Vider le cache du navigateur (Ctrl + F5) pour voir le nouveau CSS.
5. Si la modification touche aux menus (depuis la version 1.2.0 du thème) : dans l'admin de prod, **Apparence > Menus**, attribuer le menu du header aux emplacements Storefront **« Menu principal »** et **« Menu mobile »**. Tant qu'aucun menu n'est attribué, le thème affiche les liens par défaut (Le concept, Le menu, Où nous trouver).

> Organisation du code du thème (v1.2.0) : la structure de Storefront est conservée (menu mobile, mini-panier, liens d'accessibilité, zones de widgets du footer) et complétée par des hooks dans `functions.php`. Les classes ajoutées suivent la convention BEM avec le préfixe `pepe-` (ex. `pepe-header__actions`, `pepe-footer__colonnes`, `pepe-categories__lien--actif`), les mêmes que sur la page Design System HTML/CSS.

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
3. Remplacer l'URL de dev par l'URL de prod, en SSH avec WP-CLI (US-1.4) :
   ```bash
   ssh sanchezperrau1@mmi.unilim.fr
   cd ~/public_html/wordpress
   wp search-replace 'https://pepe-chicken.ddev.site' 'https://mmi.unilim.fr/~sanchezperrau1/wordpress' --all-tables
   ```
   Sans WP-CLI : dans phpMyAdmin, table `r3sae01_options`, modifier `siteurl` et `home` en `https://mmi.unilim.fr/~sanchezperrau1/wordpress`.
   L'import de la base ne touche pas au `wp-config.php` de prod (identifiants de la base de prod conservés).
4. Envoyer `wp-content/uploads/` (images des produits) dans `wordpress/wp-content/uploads/` avec FileZilla.
5. Se reconnecter à l'admin de prod (les comptes sont ceux de la base de dev).
6. Supprimer le fichier `.sql.gz` du PC une fois l'import fait (il ne doit jamais aller sur GitHub).

### 4. Vérifications après chaque mise en prod
- Le site et `/wp-admin` s'affichent sans erreur.
- Les extensions sont actives et le thème Pepe Chicken est activé.
- Les pages concernées par l'itération fonctionnent comme en dev.
- Ajouter une ligne dans `journal-mises-en-production.md`.
