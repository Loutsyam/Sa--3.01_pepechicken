# Design System – Pepe Chicken (HTML / CSS)

Page statique regroupant les couleurs, la typographie, les espacements et les principaux composants
du site click & collect Pepe Chicken (SAÉ 3.01). HTML et CSS uniquement, sans framework, validés avec le validateur W3C.

## Conventions

- Les variables CSS reprennent celles du Design System Figma et du thème WordPress.
- Les composants sont nommés en **BEM** (`bloc__element--modificateur`) avec le préfixe `pepe-`,
  les mêmes noms que dans le thème enfant WordPress : `pepe-btn--rouge`, `pepe-carte-produit__prix`,
  `pepe-header__actions`, `pepe-footer__colonnes`…
- Les classes `ds-…` servent uniquement à la mise en page de cette planche (sections, grilles de démonstration).

## Composants

| Composant | Classes |
|---|---|
| Header | `pepe-header`, `pepe-header__nav`, `pepe-header__actions`, `pepe-burger` |
| Logo | `pepe-logo`, `pepe-logo__pepe`, `pepe-logo__chicken` |
| Boutons | `pepe-btn` + `pepe-btn--rouge` / `--beige` / `--jaune`, `pepe-btn-plus` |
| Panier | `pepe-panier`, `pepe-panier__nombre` |
| Badges | `pepe-badge` + `pepe-badge--jaune` / `--beige` |
| Options | `pepe-option`, `pepe-option--actif` |
| Carte produit | `pepe-carte-produit` (`__photo`, `__nom`, `__bas`, `__prix`) |
| Formulaire | `pepe-champ`, `pepe-case` |
| Récapitulatif | `pepe-recap`, `pepe-recap__ligne`, `pepe-recap__ligne--total` |
| Cartes | `pepe-carte` + `pepe-carte--beige` / `--blanche` |
| Typographie | `pepe-titre` + `--h1` / `--h2` / `--h3`, `pepe-texte`, `pepe-texte--petit` |
| Footer | `pepe-footer` (`__colonnes`, `__colonne`, `__titre`, `__liste`, `__lien`, `__logo`, `__sante`) |
