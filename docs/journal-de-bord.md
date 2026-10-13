# Journal de bord – SAÉ 3.01 Pepe Chicken

## 13/10/2026 – Conception (Figma)
- Analyse de la charte Pepe Chicken et benchmark McDonald’s / KFC / Five Guys.
- Maquettes desktop : Accueil, Boutique, Fiche produit, Panier.
- Maquettes mobile : Accueil, Boutique, Fiche produit, Panier.
- Design System Figma : variables (couleurs, espacements, rayons, typo Desktop/Mobile), styles, composants.
- Prototype desktop et mobile (Accueil → Boutique → Fiche produit → Panier).

## 13/10/2026 – IT-1 : Environnements de Dev et de Prod
- US-1.1 : DDEV + WordPress 7.1.3 (fr_FR), préfixe `r3sae01_`.
- US-1.2 : WooCommerce, Storefront, Secure Custom Fields, Classic Editor installés.
  - Problème : fatal error à l'activation de WooCommerce → extension téléchargée à moitié (projet sur `/mnt/c`). Réinstallée avec `--force`.
- US-1.3 : thème enfant Storefront `pepe-chicken` suivi sur GitHub.
  - Problème : push refusé (403) → token GitHub sans le droit `repo`. Nouveau token classic avec `repo`.
- US-1.4 : WordPress installé sur mmi.unilim.fr, base `sanchezperrau1`, préfixe `r3sae01_`.
  - Problème : installation d'extensions depuis l'admin impossible (FTP port 21 fermé) → envoi des extensions et thèmes en SFTP (FileZilla, port 22).
- US-1.5 : procédure de mise en production et journal des mises en production rédigés (`docs/`).

## 13/10/2026 – IT-2 : Catalogue et produits simples
- US-2.1 : apparence générale reprise de la maquette dans le thème enfant
  (variables CSS = variables du Design System Figma, header et footer, boutons, couleurs, typo, version mobile).
- US-2.3 : page Boutique avec un titre, des onglets de catégories et une section par catégorie (`woocommerce/archive-product.php`).
- US-2.4 : fiche produit en deux colonnes (image / infos), prix en jaune, onglets dans une carte beige.
- Figma : couleurs, arrondis et espacements des maquettes reliés aux variables, styles de texte appliqués.
