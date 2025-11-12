# **CAHIER DES CHARGES - MVP**

## **Application de Gestion de Matériel Informatique**
### **Version 2.0 - Focus MVP**

---

## **📋 INFORMATIONS GÉNÉRALES**

**Nom du projet** : Gestion Matériel IT  
**Version** : 2.0 (MVP Optimisé)  
**Date** : 12 Novembre 2025  
**Auteur** : Équipe DSI  
**Statut** : Approuvé pour développement MVP

---

## **1. CONTEXTE ET VISION**

### **1.1 Problématiques Actuelles**

L'organisation de 40 personnes gère actuellement son parc informatique de manière manuelle (Excel, documents partagés), ce qui engendre :

- ❌ **Manque de traçabilité** : Impossible de savoir qui possède quel matériel
- ❌ **Aucune responsabilisation** : Pas de formalisation des attributions/restitutions
- ❌ **Vision inexistante** : Pas de vue d'ensemble du parc (disponibilité, état, amortissement)
- ❌ **Inefficacité** : Inventaires et audits chronophages
- ❌ **Risques** : Pertes matérielles, litiges, absence de preuves

### **1.2 Objectifs du MVP**

Le MVP vise à livrer **rapidement** (2-3 mois) une solution fonctionnelle pour :

1. ✅ Centraliser l'inventaire dans une base de données unique
2. ✅ Formaliser les attributions/restitutions via des fiches de décharge PDF signées
3. ✅ Tracer automatiquement toutes les opérations (audit log)
4. ✅ Responsabiliser les employés avec un système de preuves documentées
5. ✅ Obtenir une vision temps réel du parc (disponibilité, état, amortissement)

### **1.3 Critères de Succès du MVP**

- 📊 100% du parc informatique inventorié et accessible en ligne
- 📄 Génération automatique des fiches de décharge en moins de 30 secondes
- 🔍 Recherche d'un matériel ou d'un employé en moins de 5 secondes
- 📈 Dashboard opérationnel avec indicateurs clés visibles en un coup d'œil
- 🎯 Adoption par 100% de l'équipe DSI en 1 mois

---

## **2. STACK TECHNIQUE**

### **2.1 Technologies**

| Composant | Technologie | Version |
|-----------|-------------|---------|
| **Backend** | Laravel | 12.x |
| **Admin Panel** | Filament | v4 |
| **Frontend** | Livewire + Tailwind CSS | Latest |
| **Base de données** | MariaDB/MySQL | 10.6+ / 8.0+ |
| **PDF** | spatie/laravel-pdf ou barryvdh/laravel-dompdf | Latest |
| **Audit Log** | spatie/laravel-activitylog | Latest |
| **Import** | Filament Import Actions | Built-in |

### **2.2 Infrastructure**

- **Environnement** : LAMP/LEMP
- **Serveur web** : Apache/Nginx
- **PHP** : 8.3+
- **Stockage PDF** : Storage local (`storage/app/discharge_documents`)

---

## **3. PÉRIMÈTRE FONCTIONNEL DU MVP**

### **3.1 🎯 Fonctionnalités INCLUSES dans le MVP**

#### **Module 1 : Gestion de Base**

##### **1.1 Import Initial des Données**
- ✅ Import CSV/Excel pour **Employés**
- ✅ Import CSV/Excel pour **Services**
- ✅ Import CSV/Excel pour **Matériels**
- ✅ Validation des données avec rapport d'erreurs
- ✅ Détection et alerte des doublons (numéro de série)

##### **1.2 CRUD Matériels**
- ✅ Créer, Modifier, Supprimer un matériel
- ✅ Champs obligatoires : Type, Marque, Modèle, N° série, Date d'achat, Statut
- ✅ Gestion des **statuts** : `disponible`, `attribué`, `en_panne`, `en_maintenance`, `rebuté`
- ✅ Gestion de l'**état physique** : `excellent`, `bon`, `moyen`, `mauvais`
- ✅ Badge visuel **"Amorti"** (date d'achat > 3 ans) calculé automatiquement **uniquement pour les types "Ordinateur Portable" et "Ordinateur Bureau"**

##### **1.3 CRUD Employés et Services**
- ✅ Gestion des employés (Nom, Prénom, Email, Service, Téléphone)
- ✅ Gestion des services (Nom, Code, Responsable)
- ✅ Validation unicité email

##### **1.4 Gestion des Types de Matériel**
- ✅ Types prédéfinis : Ordinateur Portable, Ordinateur Bureau, Imprimante, Écran, Smartphone, Tablette, Vidéoprojecteur, Serveur, Switch, Routeur, Autre
- ✅ CRUD Types personnalisés

##### **1.5 Gestion des Accessoires**
- ✅ Création d'une liste d'accessoires standards (Chargeur, Souris, Câble réseau, Sacoche, Documentation, etc.)
- ✅ Sélection multiple lors de l'attribution
- ✅ Suivi du statut : `fourni`, `restitué`, `manquant`

#### **Module 2 : Attributions & Restitutions**

##### **2.1 Attribution de Matériel**
- ✅ Formulaire d'attribution : Matériel → Employé
- ✅ Sélection des accessoires fournis (cases à cocher)
- ✅ Champ "Observations" optionnel
- ✅ Génération automatique du PDF de décharge d'**attribution**
- ✅ Numérotation automatique : `ATT-YYYY-NNNN`
- ✅ QR code intégré pointant vers la fiche matériel
- ✅ Changement automatique du statut matériel → `attribué`
- ✅ Action "Imprimer la fiche" disponible immédiatement

##### **2.2 Restitution de Matériel**
- ✅ Formulaire de restitution avec constat d'état
- ✅ Sélection des accessoires restitués
- ✅ Liste des accessoires manquants
- ✅ Constat d'état (général, fonctionnel, dommages)
- ✅ Décision : Remise en stock / À réparer / Mise au rebut
- ✅ Génération automatique du PDF de décharge de **restitution**
- ✅ Numérotation automatique : `RES-YYYY-NNNN`
- ✅ Changement automatique du statut matériel selon décision
- ✅ Action "Imprimer la fiche" disponible immédiatement

#### **Module 3 : Traçabilité & Historique**

##### **3.1 Audit Log Automatique**
- ✅ Enregistrement automatique de TOUTES les actions :
  - Création de matériel
  - Modification de matériel
  - Attribution à un employé
  - Restitution par un employé
  - Changement de statut
- ✅ Informations tracées : Qui, Quoi, Quand, Anciennes/Nouvelles valeurs
- ✅ Affichage de l'historique sur la fiche matériel (Timeline)
- ✅ Package : `spatie/laravel-activitylog`

##### **3.2 Stockage des PDF**
- ✅ Table `discharge_documents` pour stocker les métadonnées
- ✅ Fichiers PDF stockés dans `storage/app/discharge_documents`
- ✅ Lien vers le PDF depuis la ressource Attribution/Restitution

#### **Module 4 : Recherche & Filtres**

##### **4.1 Recherche Globale**
- ✅ Barre de recherche dans l'admin Filament
- ✅ Recherche par :
  - Numéro de série
  - Marque/Modèle
  - Nom d'employé
  - Service

##### **4.2 Filtres**
- ✅ Filtre par **Statut** (disponible, attribué, en panne, etc.)
- ✅ Filtre par **Type de matériel**
- ✅ Filtre par **Service**
- ✅ Filtre par **État physique**
- ✅ Filtre **"Amorti"** (Oui/Non)

#### **Module 5 : Dashboard MVP**

##### **5.1 Widgets Essentiels**
- ✅ **Statistiques en chiffres** :
  - Nombre total de matériels
  - Matériels disponibles
  - Matériels attribués
  - Matériels en panne
  - Ordinateurs amortis (> 3 ans)
- ✅ **Graphique de répartition** :
  - Répartition par type de matériel (Camembert)
  - Répartition par statut (Barres)
- ✅ **Alertes visuelles** :
  - Ordinateurs amortis (> 3 ans) - uniquement Ordinateur Portable et Ordinateur Bureau
  - Matériels en panne depuis > 7 jours
- ✅ **Dernières activités** : Liste des 10 dernières actions (attributions, restitutions)

#### **Module 6 : Gestion des Utilisateurs (Basique)**

##### **6.1 Authentification**
- ✅ Connexion par email/mot de passe
- ✅ Réinitialisation mot de passe

##### **6.2 Rôles Simples**
- ✅ **Super Admin** : Accès total
- ✅ **Gestionnaire de Parc** : Gestion matériel, attributions, restitutions, employés
- ✅ Package : `spatie/laravel-permission` (base)

---

### **3.2 ⏳ Fonctionnalités EXCLUES du MVP (Phases futures)**

Les fonctionnalités suivantes sont **reportées aux phases 2 et 3** :

#### **Phase 2 (Post-MVP - 3-6 mois)**
- ⏳ Gestion des incidents/pannes (déclaration, suivi, résolution)
- ⏳ Notifications in-app et par email
- ⏳ Rapports avancés (exports Excel, PDF)
- ⏳ Module de maintenance préventive

#### **Phase 3 (Nice-to-Have - 6-12 mois)**
- ⏳ Gestion des fournisseurs et garanties
- ⏳ Localisation physique du matériel (bâtiment, étage, bureau)
- ⏳ Planning de maintenance
- ⏳ Rôles avancés (Technicien, Visualisateur, Manager)
- ⏳ API REST pour intégrations tierces

---

## **4. MODÈLE DE DONNÉES MVP**

### **4.1 Architecture des Tables**

Le modèle de données est organisé en 4 axes fonctionnels :

```
📦 Organisation
├── users (Utilisateurs app)
├── employees (Employés de l'organisation)
└── services (Départements)

📦 Inventaire
├── materiels (Le matériel IT)
├── materiel_types (Types de matériel)
└── accessories (Accessoires standards)

📦 Transactions
├── attributions (Attribution employé ↔ matériel)
├── accessoire_attribution (Pivot accessoires ↔ attribution)
└── discharge_documents (Métadonnées PDF générés)

📦 Audit & Traçabilité
└── activity_log (Package spatie - automatique)
```

### **4.2 Schémas des Tables Principales**

#### **Table : `materiels`**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
materiel_type_id    BIGINT UNSIGNED NOT NULL FK(materiel_types)
nom                 VARCHAR(255) NOT NULL
marque              VARCHAR(100)
modele              VARCHAR(100)
numero_serie        VARCHAR(100) UNIQUE NOT NULL
specifications      TEXT
purchase_date       DATE NOT NULL
purchase_price      DECIMAL(10,2)
statut              ENUM('disponible','attribué','en_panne','en_maintenance','rebuté') DEFAULT 'disponible'
etat_physique       ENUM('excellent','bon','moyen','mauvais') DEFAULT 'bon'
notes               TEXT
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

**Accessors/Mutators Laravel** :
- `is_amorti` : 
```php
// Ne s'applique qu'aux ordinateurs (Portable ou Bureau)
return in_array($this->materielType->nom, ['Ordinateur Portable', 'Ordinateur Bureau']) 
    && $this->purchase_date->diffInYears(now()) >= 3;
```

#### **Table : `employees`**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
service_id          BIGINT UNSIGNED FK(services)
nom                 VARCHAR(100) NOT NULL
prenom              VARCHAR(100) NOT NULL
email               VARCHAR(255) UNIQUE NOT NULL
telephone           VARCHAR(20)
poste               VARCHAR(100)
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### **Table : `services`**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
nom                 VARCHAR(100) UNIQUE NOT NULL
code                VARCHAR(20) UNIQUE
responsable         VARCHAR(200)
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### **Table : `materiel_types`**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
nom                 VARCHAR(100) UNIQUE NOT NULL (ex: 'Ordinateur Portable')
description         TEXT
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

**Valeurs par défaut** : Ordinateur Portable, Ordinateur Bureau, Imprimante, Écran, Smartphone, Tablette, Vidéoprojecteur, Serveur, Switch, Routeur, Autre

#### **Table : `accessories`**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
nom                 VARCHAR(100) UNIQUE NOT NULL (ex: 'Chargeur')
description         TEXT
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

**Valeurs par défaut** : Chargeur/Câble alimentation, Souris, Câble réseau, Sacoche/Housse, Documentation, Clé USB, Casque audio, Webcam, Clavier externe

#### **Table : `attributions`**
```sql
id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
materiel_id             BIGINT UNSIGNED NOT NULL FK(materiels)
employee_id             BIGINT UNSIGNED NOT NULL FK(employees)
date_attribution        DATE NOT NULL
date_restitution        DATE NULL
numero_decharge_att     VARCHAR(50) UNIQUE (ATT-YYYY-NNNN)
numero_decharge_res     VARCHAR(50) UNIQUE (RES-YYYY-NNNN)
observations_att        TEXT (Observations à l'attribution)
observations_res        TEXT (Observations à la restitution)
etat_general_res        ENUM('excellent','bon','moyen','mauvais')
etat_fonctionnel_res    ENUM('parfait','defauts_mineurs','dysfonctionnements','hors_service')
dommages_res            JSON (Liste des dommages constatés)
decision_res            ENUM('remis_en_stock','a_reparer','rebut')
created_at              TIMESTAMP
updated_at              TIMESTAMP
```

#### **Table : `accessoire_attribution` (Pivot)**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
attribution_id      BIGINT UNSIGNED NOT NULL FK(attributions)
accessory_id        BIGINT UNSIGNED NOT NULL FK(accessories)
statut_att          ENUM('fourni') DEFAULT 'fourni'
statut_res          ENUM('restitué','manquant') NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### **Table : `discharge_documents`**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
attribution_id      BIGINT UNSIGNED NOT NULL FK(attributions)
type                ENUM('attribution','restitution') NOT NULL
numero_decharge     VARCHAR(50) NOT NULL
file_path           VARCHAR(500) NOT NULL
generated_at        TIMESTAMP NOT NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### **Table : `users` (Authentification)**
```sql
id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
name                VARCHAR(255) NOT NULL
email               VARCHAR(255) UNIQUE NOT NULL
password            VARCHAR(255) NOT NULL
role                VARCHAR(50) DEFAULT 'gestionnaire'
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

---

## **5. SPÉCIFICATIONS DES FICHES DE DÉCHARGE PDF**

### **5.1 Fiche d'Attribution**

#### **Contenu Minimum**
- 📄 **En-tête** : Logo organisation, Titre "FICHE DE DÉCHARGE D'ATTRIBUTION"
- 🔢 **Numéro** : ATT-YYYY-NNNN (ex: ATT-2025-0042)
- 📅 **Date** : Date d'attribution
- 👤 **Bénéficiaire** : Nom, Prénom, Service, Email
- 💻 **Matériel** : Type, Marque, Modèle, N° série, QR Code
- 📦 **Accessoires** : Liste avec cases cochées (Chargeur ✓, Souris ✓, etc.)
- 📝 **Observations** : Champ texte libre
- ⚖️ **Engagements** : Texte légal standard (utilisation conforme, restitution, etc.)
- ✍️ **Signatures** : Bénéficiaire + Responsable informatique (avec dates)
- 🔗 **Pied de page** : Date/heure génération, Référence interne

#### **Format**
- **Taille** : A4
- **Orientation** : Portrait
- **Police** : Arial ou équivalent
- **Mise en page** : Marges 2cm, Espacement lisible

### **5.2 Fiche de Restitution**

#### **Contenu Minimum**
- 📄 **En-tête** : Logo organisation, Titre "FICHE DE DÉCHARGE DE RESTITUTION"
- 🔢 **Numéro** : RES-YYYY-NNNN (ex: RES-2025-0042)
- 📅 **Date** : Date de restitution
- 👤 **Bénéficiaire** : Nom, Prénom, Service, Email
- 💻 **Matériel** : Type, Marque, Modèle, N° série, QR Code
- ⏱️ **Période** : Du [date_att] au [date_res] - Durée : X jours
- 🔍 **Constat d'état** :
  - État général : Excellent / Bon / Moyen / Mauvais
  - État fonctionnel : Parfait / Défauts mineurs / Dysfonctionnements / Hors service
  - Dommages constatés : Cases à cocher + description
- 📦 **Accessoires restitués** : Liste avec cases cochées
- ⚠️ **Accessoires manquants** : Liste si applicable
- 📝 **Observations** : Champ texte libre
- 🎯 **Décision** : Remis en stock / À réparer / Mise au rebut
- ✍️ **Signatures** : Employé + Responsable informatique (avec dates)
- 🔗 **Pied de page** : Date/heure génération, Références attribution/restitution

---

## **6. RÈGLES DE GESTION**

### **6.1 Statuts du Matériel**

| Statut | Description | Peut être attribué ? |
|--------|-------------|---------------------|
| `disponible` | Matériel en stock, prêt à l'emploi | ✅ Oui |
| `attribué` | Matériel utilisé par un employé | ❌ Non |
| `en_panne` | Matériel défectueux, en attente réparation | ❌ Non |
| `en_maintenance` | Matériel en cours de maintenance | ❌ Non |
| `rebuté` | Matériel hors service, à jeter | ❌ Non |

### **6.2 Cycle de Vie d'une Attribution**

```
1. ATTRIBUTION
   ├─ Sélection matériel (statut = disponible)
   ├─ Sélection employé
   ├─ Sélection accessoires
   ├─ Génération PDF attribution (ATT-YYYY-NNNN)
   └─ Matériel.statut → 'attribué'

2. UTILISATION
   ├─ Matériel chez l'employé
   └─ Traçabilité automatique (audit log)

3. RESTITUTION
   ├─ Constat d'état
   ├─ Vérification accessoires
   ├─ Génération PDF restitution (RES-YYYY-NNNN)
   └─ Décision finale :
      ├─ Remis en stock → Matériel.statut = 'disponible'
      ├─ À réparer → Matériel.statut = 'en_panne'
      └─ Rebut → Matériel.statut = 'rebuté'
```

### **6.3 Amortissement**

- **Règle** : Un matériel de type **"Ordinateur Portable"** ou **"Ordinateur Bureau"** est considéré "Amorti" si `purchase_date` > 3 ans
- **Périmètre** : L'amortissement automatique s'applique **UNIQUEMENT aux ordinateurs**
- **Calcul** : Automatique via Accessor Laravel `is_amorti` qui vérifie :
  1. Le type de matériel est "Ordinateur Portable" OU "Ordinateur Bureau"
  2. ET la date d'achat > 3 ans
- **Affichage** : Badge visuel dans les listes Filament (Vert "Actif" / Orange "Amorti") **uniquement pour les ordinateurs**
- **Autres équipements** : Pour les imprimantes, écrans, smartphones, etc., l'amortissement sera géré manuellement (champ dédié en Phase 2)
- **Impact** : Aucun sur les attributions (information seulement)

### **6.4 Numérotation Automatique**

#### **Fiches d'Attribution**
- **Format** : `ATT-YYYY-NNNN`
- **Exemple** : `ATT-2025-0001`, `ATT-2025-0042`
- **Logique** : Compteur annuel réinitialisé chaque année

#### **Fiches de Restitution**
- **Format** : `RES-YYYY-NNNN`
- **Exemple** : `RES-2025-0001`, `RES-2025-0042`
- **Logique** : Compteur annuel réinitialisé chaque année

### **6.5 QR Codes**

- **Contenu** : URL vers la fiche matériel dans l'admin
- **Format** : `https://gestion-materiel.local/admin/materiels/{id}`
- **Génération** : Package `simplesoftwareio/simple-qrcode`
- **Position** : Sur les PDF d'attribution et de restitution, à côté des infos matériel

### **6.6 Validation des Imports**

#### **Import Employés**
- ✅ Email unique obligatoire
- ✅ Nom et prénom obligatoires
- ✅ Service doit exister (ou créé automatiquement)

#### **Import Matériels**
- ✅ Numéro de série unique obligatoire
- ✅ Type de matériel doit exister
- ✅ Date d'achat obligatoire (format ISO 8601)
- ✅ Statut par défaut : `disponible`
- ⚠️ Alerte si doublon détecté

#### **Rapport d'Import**
- Nombre de lignes traitées
- Nombre de succès
- Nombre d'échecs avec détails des erreurs
- Export du rapport en CSV

---

## **7. INTERFACES UTILISATEUR (Filament v4)**

### **7.1 Navigation Principale**

```
📊 Dashboard
   └─ Widgets (Stats, Graphiques, Alertes, Activités récentes)

👥 Organisation
   ├─ Employés
   └─ Services

💻 Inventaire
   ├─ Matériels
   ├─ Types de Matériel
   └─ Accessoires

🔄 Transactions
   └─ Attributions (avec onglets: Actives / Historique)

⚙️ Paramètres
   └─ Utilisateurs
```

### **7.2 Vues Principales**

#### **Dashboard**
- **Widgets en chiffres** : 4 cartes avec icônes (Total, Dispo, Attribués, Pannes)
- **Graphiques** : 2 graphiques (Répartition par type, Répartition par statut)
- **Alertes** : Liste avec badges colorés (Amortis, Pannes anciennes)
- **Activités récentes** : Timeline des 10 dernières actions

#### **Liste Matériels**
- **Colonnes** : ID, Type, Nom, Marque, Modèle, N° série, Statut, État physique, Amorti, Actions
- **Badges colorés** :
  - Statut disponible : Vert
  - Statut attribué : Bleu
  - Statut en_panne : Rouge
  - Amorti : Orange (affiché uniquement pour les ordinateurs)
- **Actions** : Voir, Modifier, Supprimer, Attribuer
- **Filtres** : Statut, Type, Service, État physique, Amorti (filtre uniquement les ordinateurs)

#### **Fiche Matériel (Détail)**
- **Onglet Informations** : Toutes les données du matériel
- **Onglet Historique** : Timeline des événements (attributions, modifications)
- **Onglet Attribution active** : Si attribué, affichage de l'attribution en cours

#### **Formulaire Attribution**
- **Étape 1** : Sélection matériel (filtre automatique sur disponibles)
- **Étape 2** : Sélection employé
- **Étape 3** : Sélection accessoires (cases à cocher)
- **Étape 4** : Observations (optionnel)
- **Action finale** : "Attribuer et générer la fiche" → PDF téléchargé automatiquement

#### **Formulaire Restitution**
- **Champs pré-remplis** : Matériel, Employé, Date attribution, Durée
- **Constat d'état** : Sélecteurs (État général, État fonctionnel)
- **Dommages** : Cases à cocher + champ texte libre
- **Accessoires** : Cases à cocher (restitués) + liste manquants
- **Décision** : Radio buttons (Remis en stock / À réparer / Rebut)
- **Action finale** : "Restituer et générer la fiche" → PDF téléchargé automatiquement

---

## **8. SÉCURITÉ ET PERMISSIONS**

### **8.1 Authentification**
- ✅ Connexion par email/mot de passe
- ✅ Hachage bcrypt
- ✅ Protection CSRF
- ✅ Réinitialisation mot de passe par email

### **8.2 Rôles et Permissions (Basique)**

#### **Super Admin**
- Accès total à toutes les fonctionnalités
- Gestion des utilisateurs
- Configuration de l'application

#### **Gestionnaire de Parc**
- CRUD Matériels
- CRUD Employés et Services
- CRUD Types et Accessoires
- Attributions et Restitutions
- Consultation Dashboard et Historique
- ❌ Pas de gestion des utilisateurs

### **8.3 Protection des Données**
- ✅ Validation des inputs (Form Requests Laravel)
- ✅ Protection contre les injections SQL (Eloquent ORM)
- ✅ Stockage sécurisé des fichiers PDF (hors web root)
- ✅ Logs d'activité traçables

---

## **9. TESTS ET VALIDATION**

### **9.1 Critères d'Acceptation du MVP**

#### **Fonctionnalités Core**
- [ ] Import CSV fonctionnel pour Employés, Services, Matériels
- [ ] CRUD complet pour Matériels, Employés, Services, Types, Accessoires
- [ ] Attribution matériel → employé avec génération PDF
- [ ] Restitution matériel avec constat et génération PDF
- [ ] Numérotation automatique ATT-YYYY-NNNN et RES-YYYY-NNNN
- [ ] QR codes fonctionnels sur les PDF
- [ ] Audit log automatique de toutes les actions

#### **Interface Utilisateur**
- [ ] Dashboard opérationnel avec 4 widgets minimum
- [ ] Recherche globale fonctionnelle
- [ ] Filtres sur les listes (Statut, Type, Service)
- [ ] Badges visuels (Statut, Amorti)
- [ ] Responsive (Desktop, Tablette)

#### **Qualité et Performance**
- [ ] Temps de génération PDF < 2 secondes
- [ ] Temps de recherche < 1 seconde
- [ ] Pas d'erreurs dans les logs
- [ ] Validations des formulaires opérationnelles

### **9.2 Scénarios de Test**

#### **Scénario 1 : Import Initial**
1. Préparer 3 fichiers CSV (Employés, Services, Matériels)
2. Importer via Filament
3. Vérifier : Rapport d'import, Données en base, Alertes doublons

#### **Scénario 2 : Attribution Complète**
1. Créer un nouvel employé
2. Créer un nouveau matériel (statut = disponible)
3. Attribuer le matériel à l'employé
4. Vérifier : PDF généré, Statut = attribué, Historique mis à jour

#### **Scénario 3 : Restitution Avec Dommages**
1. Restituer un matériel attribué
2. Renseigner des dommages
3. Choisir "À réparer"
4. Vérifier : PDF généré, Statut = en_panne, Historique mis à jour

#### **Scénario 4 : Recherche et Filtres**
1. Rechercher un matériel par numéro de série
2. Filtrer les matériels amortis
3. Filtrer les matériels attribués au service "IT"
4. Vérifier : Résultats corrects, Temps < 1 seconde

---

## **10. LIVRABLES DU MVP**

### **10.1 Code Source**
- ✅ Repository Git avec historique clair
- ✅ Code commenté et structuré (PSR-12)
- ✅ Migrations de base de données
- ✅ Seeders pour données de test
- ✅ Fichier `.env.example` configuré

### **10.2 Documentation Technique**
- ✅ README.md (Installation, Configuration, Déploiement)
- ✅ Diagramme du modèle de données (ERD)
- ✅ Documentation API (si applicable)
- ✅ Guide de contribution

### **10.3 Documentation Utilisateur**
- ✅ Guide d'utilisation (PDF ou Wiki)
  - Comment importer des données
  - Comment attribuer un matériel
  - Comment restituer un matériel
  - Comment rechercher et filtrer
  - Comment interpréter le dashboard

### **10.4 Environnement de Test**
- ✅ Application déployée sur environnement de staging
- ✅ Données de test chargées (20 employés, 50 matériels)
- ✅ Accès fournis (Super Admin + Gestionnaire)

---

## **11. PLANNING ET JALONS**

### **11.1 Planning MVP (8 semaines)**

#### **Semaine 1-2 : Fondations**
- Configuration Laravel 12 + Filament v4
- Création du modèle de données complet
- Migrations et seeders

#### **Semaine 3-4 : CRUD et Import**
- Ressources Filament (Matériels, Employés, Services, Types, Accessoires)
- Actions d'import CSV
- Validation et gestion des doublons

#### **Semaine 5-6 : Attributions et PDF**
- Formulaires Attribution/Restitution
- Génération PDF avec QR codes
- Numérotation automatique
- Stockage des documents

#### **Semaine 7 : Historique et Dashboard**
- Intégration spatie/laravel-activitylog
- Dashboard avec widgets
- Recherche et filtres

#### **Semaine 8 : Tests et Documentation**
- Tests manuels
- Corrections de bugs
- Rédaction documentation
- Déploiement staging

### **11.2 Jalons de Validation**

| Jalon | Date | Critère de Validation |
|-------|------|----------------------|
| **J1** | Fin S2 | Base de données opérationnelle + Seeders OK |
| **J2** | Fin S4 | Import CSV fonctionnel + CRUD complets |
| **J3** | Fin S6 | Génération PDF Attribution/Restitution OK |
| **J4** | Fin S7 | Dashboard et Audit Log opérationnels |
| **J5** | Fin S8 | MVP validé et prêt pour production |

---

## **12. BUDGET ET RESSOURCES**

### **12.1 Ressources Humaines**

| Rôle | Charge | Mission |
|------|--------|---------|
| **Développeur Full-Stack Laravel** | 8 semaines | Développement, Tests, Documentation |
| **Référent Métier DSI** | 2 jours | Validation fonctionnelle, Recette |
| **Chef de Projet** | 1 jour/semaine | Suivi, Coordination |

### **12.2 Ressources Techniques**

- **Serveur de staging** : LAMP/LEMP (2 vCPU, 4 GB RAM)
- **Serveur de production** : LAMP/LEMP (4 vCPU, 8 GB RAM) - Post-MVP
- **Licences** : Aucune (Stack 100% open-source)

### **12.3 Coûts Estimés**

| Poste | Coût |
|-------|------|
| Développement (8 semaines) | À définir selon contexte |
| Infrastructure (Staging) | 20€/mois |
| Infrastructure (Production) | 40€/mois |
| **TOTAL MVP** | À définir selon contexte |

---

## **13. RISQUES ET MITIGATION**

| Risque | Probabilité | Impact | Mitigation |
|--------|-------------|--------|------------|
| **Retard dans les développements** | Moyenne | Élevé | Planning avec buffer de 1 semaine |
| **Données d'import incomplètes** | Élevée | Moyen | Validation stricte + rapport d'erreurs détaillé |
| **Adoption faible par les utilisateurs** | Moyenne | Élevé | Formation + Documentation claire + Support dédié |
| **Bugs en production** | Moyenne | Moyen | Tests rigoureux + Environnement de staging |
| **Performance (génération PDF)** | Faible | Moyen | Optimisation + Mise en cache si nécessaire |

---

## **14. ÉVOLUTIONS POST-MVP**

### **14.1 Phase 2 (3-6 mois après MVP)**
- Module de gestion des incidents (déclaration, suivi, résolution)
- Notifications automatiques (in-app + email)
- Rapports avancés (exports Excel, PDF)
- Dashboard enrichi (graphiques temporels, prévisions)

### **14.2 Phase 3 (6-12 mois après MVP)**
- Gestion des fournisseurs et garanties
- Localisation physique (bâtiment, étage, bureau)
- Planning de maintenance préventive
- Rôles avancés (Technicien, Visualisateur, Manager)
- API REST pour intégrations tierces

---

## **15. VALIDATION ET SIGNATURES**

### **15.1 Validation Fonctionnelle**

**Référent Métier DSI** : ___________________________  
Date : ____ / ____ / ________  
Signature :

### **15.2 Validation Technique**

**Développeur Lead** : ___________________________  
Date : ____ / ____ / ________  
Signature :

### **15.3 Validation Direction**

**Direction / Chef de Projet** : ___________________________  
Date : ____ / ____ / ________  
Signature :

---

## **ANNEXES**

### **Annexe A : Modèle Import CSV Employés**
```csv
nom,prenom,email,telephone,poste,service_code
Kouassi,Jean,jean.kouassi@example.com,+225070000001,Analyste,IT
Touré,Aminata,aminata.toure@example.com,+225070000002,Comptable,FIN
```

### **Annexe B : Modèle Import CSV Matériels**
```csv
type,nom,marque,modele,numero_serie,purchase_date,purchase_price,statut
Ordinateur Portable,PC-001,Dell,Latitude 5420,SN123456,2023-01-15,850000,disponible
Imprimante,IMP-001,HP,LaserJet Pro,SN789012,2022-06-10,250000,disponible
```

### **Annexe C : Liste des Packages Laravel Utilisés**
- `laravel/framework` : 12.x
- `filament/filament` : ^4.0
- `spatie/laravel-activitylog` : ^4.0
- `spatie/laravel-permission` : ^6.0
- `barryvdh/laravel-dompdf` ou `spatie/laravel-pdf` : Latest
- `simplesoftwareio/simple-qrcode` : ^4.0

### **Annexe D : Maquettes des Fiches de Décharge**

*(Les maquettes détaillées des fiches sont fournies dans le document original - Section 5)*

---

## **🎯 RÉCAPITULATIF DES POINTS D'AMÉLIORATION INTÉGRÉS**

Ce cahier des charges v2.0 intègre les améliorations suivantes par rapport à la v1.0 :

✅ **Dashboard simplifié** : 4 widgets de base + graphiques + alertes + activités récentes  
✅ **Statuts matériel explicites** : `disponible`, `attribué`, `en_panne`, `en_maintenance`, `rebuté`  
✅ **État physique** : `excellent`, `bon`, `moyen`, `mauvais`  
✅ **Recherche et filtres** : Recherche globale + Filtres multiples (Statut, Type, Service, Amorti)  
✅ **Validation des imports** : Règles strictes + Rapport d'erreurs détaillé  
✅ **Gestion des doublons** : Validation unicité `numero_serie` + Alertes  
✅ **Module de recherche** : Barre de recherche globale optimisée  
✅ **Planning détaillé** : 8 semaines avec jalons de validation  
✅ **Critères d'acceptation** : Liste de contrôle précise pour la recette  
✅ **Scénarios de test** : 4 scénarios couvrant les flux principaux  
✅ **Amortissement ciblé** : Calcul automatique uniquement pour les ordinateurs (Portable et Bureau), gestion manuelle pour les autres équipements  

---

**🚀 FIN DU CAHIER DES CHARGES MVP v2.0**

*Document prêt pour développement et validation*
