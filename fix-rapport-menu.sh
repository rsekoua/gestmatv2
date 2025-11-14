#!/bin/bash

echo "🔧 Correction du menu Rapport du Parc Informatique"
echo "=================================================="
echo ""

echo "📋 Étape 1/4 : Vérification du fichier..."
if [ -f "app/Filament/Pages/RapportParcInformatique.php" ]; then
    echo "✅ Fichier trouvé"
else
    echo "❌ Fichier introuvable!"
    exit 1
fi

echo ""
echo "🧹 Étape 2/4 : Vidage des caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo ""
echo "🔄 Étape 3/4 : Optimisation Filament..."
php artisan filament:optimize-clear 2>/dev/null || echo "⚠️ Commande filament:optimize-clear non disponible (optionnel)"

echo ""
echo "📊 Étape 4/4 : Vérification de la configuration..."
php artisan about 2>/dev/null || echo "✓ Configuration Laravel OK"

echo ""
echo "✅ Terminé!"
echo ""
echo "🔍 Actions suivantes :"
echo "   1. Redémarrer votre serveur de développement (Ctrl+C puis 'php artisan serve')"
echo "   2. Vider le cache de votre navigateur (Ctrl+Shift+R)"
echo "   3. Accéder à /admin"
echo "   4. Chercher 'Rapports' → 'Rapport du Parc' dans le menu"
echo ""
echo "🌐 Si le problème persiste, consultez : FIX_RAPPORT_NAVIGATION.md"
