# Design System – Pepe Chicken (HTML / CSS)

Page statique regroupant les couleurs, la typographie, les espacements et les composants
du site click & collect Pepe Chicken (SAÉ 3.01). HTML et CSS uniquement, sans framework, validés W3C.

## Organisation

Deux fichiers : `index.html` et `style.css`. La feuille est découpée en couches (`@layer`), dans cet ordre :
`reset < variables < global < components < layout < utilities` (une couche plus bas l'emporte sur celles du dessus).

- **reset** : remise à zéro des styles du navigateur
- **variables** : mêmes valeurs que Figma
- **global** : styles des balises (html, body, a…)
- **components** : un bloc BEM par composant (bouton, header, carte produit…)
- **layout** : mise en page de la planche (classes `ds-…`)
- **utilities** : petites classes réutilisables (`.mt-lg`, `.bg-rouge`…)

## Conventions

- **BEM** (`bloc__element--modificateur`) avec le préfixe `pepe-`, les mêmes classes que le thème WordPress.
- **CSS natif imbriqué** : les éléments et modificateurs sont écrits dans le bloc (`&.pepe-btn--rouge`, `&:hover`).
- **Unités en rem**, sauf la valeur « pilule » (999px).
- **Variables** :

| Figma | CSS |
|---|---|
| couleur/rouge, jaune, beige, brun, blanc (+ hover) | `--color-rouge`, `--color-jaune`… |
| Tokens (fond, texte, accent) | `--color-bg`, `--color-fg`, `--color-surface`, `--color-text`, `--color-accent` |
| espace/xxs … 5xl | `--spacing-xxs` … `--spacing-5xl` |
| rayon/xs … pilule | `--radius-xs` … `--radius-pilule` |
| Typographie (Desktop / Mobile) | `--fs-h1`, `--fs-h2`, `--fs-h3`, `--fs-texte`, `--fs-petit` |
| Polices | `--ff-titre`, `--ff-texte` |
