# Guide d'importation - Employés et Matériel

## 📥 Templates disponibles

- **template_employes.csv** : Pour importer des employés
- **template_materiel.csv** : Pour importer du matériel

## 🔍 Comment utiliser les templates

### 1. Télécharger le template
Dans l'interface Filament :
- Allez sur la page **Employés** ou **Matériels**
- Cliquez sur le bouton **Importer** (icône téléchargement)
- Cliquez sur **Download example CSV** pour télécharger le template

### 2. Remplir le template
Ouvrez le fichier CSV avec Excel ou LibreOffice et remplissez les données selon les règles ci-dessous.

---

## 👥 Template Employés

### Colonnes obligatoires (❗)
- **nom** : Nom de famille (ex: DUPONT)
- **prenom** : Prénom (ex: Jean)
- **email** : Adresse email **UNIQUE** (ex: jean.dupont@mshpcmu.cd)
- **service_code** : Code du service existant (DSI, DRH, FIN)

### Colonnes optionnelles
- **telephone** : Numéro de téléphone (ex: +243 123 456 789)
- **emploi** : Type de contrat (ex: CDI, CDD, Stagiaire)
- **fonction** : Poste occupé (ex: Développeur, Comptable)

### ⚠️ Règles importantes
- ✅ L'email doit être **unique** (pas de doublon)
- ✅ Le code service doit **exister** dans la base (DSI, DRH, FIN)
- ✅ Ne pas modifier les en-têtes de colonnes
- ✅ Supprimer les lignes d'exemple avant l'import

---

## 💻 Template Matériel

### Colonnes obligatoires (❗)
- **numero_serie** : Numéro de série **UNIQUE** (ex: SN001)
- **type_materiel** : Type exact (voir liste ci-dessous)
- **statut** : État actuel (disponible, attribué, en_panne, en_maintenance, rebuté)

### Colonnes optionnelles
- **marque** : Marque du matériel (ex: Dell, HP, Lenovo)
- **modele** : Modèle exact (ex: Latitude 5420)
- **etat_physique** : État (excellent, bon, moyen, mauvais)
- **purchase_date** : Date d'achat (format: YYYY-MM-DD ou DD/MM/YYYY)
- **acquision** : Mode d'acquisition (Achat, Don, Location)
- **processor** : Processeur (pour ordinateurs uniquement)
- **ram_size_gb** : RAM en GB (pour ordinateurs uniquement)
- **storage_size_gb** : Stockage en GB (pour ordinateurs uniquement)
- **screen_size** : Taille écran en pouces (pour ordinateurs/écrans)
- **notes** : Observations diverses

### 📋 Types de matériel valides
```
Ordinateur Portable
Ordinateur Bureau
Imprimante
Écran
Smartphone
Tablette
Serveur
Switch
Routeur
Vidéoprojecteur
Autre
```

### ⚠️ Règles importantes
- ✅ Le numéro de série doit être **unique** (pas de doublon)
- ✅ Le type de matériel doit correspondre **exactement** (sensible à la casse)
- ✅ Pour les ordinateurs, remplir les spécifications techniques (processor, ram, storage, screen_size)
- ✅ Le statut doit être : disponible, attribué, en_panne, en_maintenance, ou rebuté
- ✅ Ne pas modifier les en-têtes de colonnes
- ✅ Supprimer les lignes d'exemple avant l'import

---

## 📤 Procédure d'import

### Étape 1 : Préparer votre fichier
1. Ouvrir le template CSV
2. Remplir vos données
3. **Supprimer les lignes d'exemple**
4. Sauvegarder en format CSV

### Étape 2 : Importer dans Filament
1. Aller sur la page **Employés** ou **Matériels**
2. Cliquer sur **Importer**
3. Glisser-déposer votre fichier CSV ou cliquer pour le sélectionner
4. Mapper les colonnes (vérifier la correspondance)
5. Cliquer sur **Importer**

### Étape 3 : Vérifier les résultats
- ✅ Notification de succès : Nombre d'enregistrements importés
- ❌ En cas d'erreur : Voir le rapport d'erreur détaillé
- 📊 Vérifier les données importées dans la liste

---

## ❌ Erreurs courantes

### Erreur : "Email déjà utilisé"
➡️ Solution : Chaque employé doit avoir un email unique

### Erreur : "Numéro de série déjà utilisé"
➡️ Solution : Chaque matériel doit avoir un numéro de série unique

### Erreur : "Service non trouvé"
➡️ Solution : Vérifier que le code service existe (DSI, DRH, FIN)

### Erreur : "Type de matériel invalide"
➡️ Solution : Utiliser exactement un des types listés ci-dessus

### Erreur : "Statut invalide"
➡️ Solution : Utiliser : disponible, attribué, en_panne, en_maintenance, ou rebuté

---

## 💡 Conseils

- **Tester d'abord** avec 2-3 lignes avant d'importer toute la base
- **Faire une sauvegarde** de votre base de données avant un gros import
- **Utiliser Excel/LibreOffice** pour éditer les CSV (pas Notepad)
- **Encoder en UTF-8** pour éviter les problèmes d'accents
- **Vérifier les doublons** avant l'import (email, numéro de série)

---

## 📞 Support

En cas de problème, vérifier :
1. Le format des colonnes
2. Les valeurs obligatoires
3. L'unicité des emails/numéros de série
4. L'existence des services référencés
