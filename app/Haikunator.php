<?php
namespace App;

class Haikunator {
    private static array $adjectives = [
      "vieux", "antique", "automnal", "ondoyant", "amer", "noir", "bleu", "audacieux",
      "large", "brise", "calme", "froid", "frais", "cramoisi", "boucle", "humide",
      "sombre", "aurore", "delicat", "divin", "sec", "vide", "tombant", "fantaisiste",
      "plat", "fleuri", "parfume", "givre", "doux", "vert", "cache", "sacre",
      "glace", "joyeux", "tardif", "persistant", "petit", "vivant", "long", "chanceux",
      "brumeux", "matinal", "boueux", "muet", "sans nom", "bruyant", "etrange", "vieux",
      "orange", "patient", "simple", "poli", "fier", "violet", "tranquille", "rapide",
      "raque", "rouge", "agite", "rugueux", "rond", "royal", "brillant", "strident",
      "timide", "silencieux", "petit", "neigeux", "mou", "solitaire", "etincelant", "printanier",
      "carre", "raide", "immobile", "estival", "super", "doux", "palpitant", "serre",
      "minuscule", "crepusculaire", "errant", "use", "blanc", "sauvage", "hivernal", "vaporux",
      "fane", "jaune", "jeune"
];

    private static array $nouns = [
      "art", "groupe", "bar", "base", "oiseau", "bloc", "bateau", "bonus",
      "pain", "brise", "ruisseau", "buisson", "papillon", "gateau", "cellule", "cerise",
      "nuage", "credit", "obscurite", "aurore", "rosee", "disque", "reve", "poussiere",
      "plume", "champ", "feu", "luciole", "fleur", "brouillard", "foret", "grenouille",
      "gele", "clairiere", "paillette", "herbe", "salle", "chapeau", "brume", "coeur",
      "colline", "roi", "labo", "lac", "feuille", "limite", "maths", "prairie",
      "mode", "lune", "matin", "montagne", "souris", "boue", "nuit", "papier",
      "pin", "poesie", "etang", "reine", "pluie", "recette", "resonance", "riz",
      "riviere", "salade", "scene", "mer", "ombre", "forme", "silence", "ciel",
      "fumee", "neige", "flocon", "son", "etoile", "soleil", "crepuscule",
      "vague", "terme", "tonnerre", "dent", "arbre", "verite", "union", "unite",
      "violette", "voix", "eau", "cascade", "vague", "fleur sauvage", "vent", "bois"
];

    public static function setSeed(int $seed): void {
        mt_srand($seed);
    }

    public static function haikunate(): string {
        $adjective = self::$adjectives[mt_rand(0, count(self::$adjectives) - 1)];
        $noun = self::$nouns[mt_rand(0, count(self::$nouns) - 1)];
        return sprintf("%s-%s", $adjective, $noun);
    }

    public static function size(): int {
        return count(self::$adjectives) * count(self::$nouns);
    }
}
