# 🥩 Gestionale Macelleria - Funzionalità Implementate

## 📋 Panoramica

Questo gestionale per macelleria è stato sviluppato con **Laravel 12** e **Filament 3.3**, offrendo una soluzione completa per la gestione delle vendite, inventario e margini di profitto.

## ✨ Funzionalità Principali

### 🔧 Modelli e Struttura Database

#### Product (Prodotti)
- **Gestione completa prodotti** con supporto per vendita a peso o a pezzo
- **Calcoli automatici** di margini e profitti
- **Attributi calcolati dinamici**:
  - `margin`: Percentuale di margine di profitto
  - `unit_profit`: Profitto per singola unità
  - `in_stock`: Verifica disponibilità
  - `low_stock`: Identificazione stock basso

#### Order & OrderItem (Ordini e Prodotti Venduti)
- **Calcolo automatico subtotali** al salvataggio OrderItem
- **Aggiornamento automatico totale ordine**
- **Gestione margini per item** con calcoli in tempo reale
- **Tracciamento profitti** per ordine e item

#### StockMovement (Movimenti di Magazzino)
- Tracciamento completo carichi/scarichi
- Integrazione con sistema ordini

### 🎛️ Dashboard e Statistiche

#### Widget SalesOverview
- **Vendite giornaliere, settimanali, mensili**
- **Conteggio ordini** per periodo
- **Prodotti con stock basso** (alert automatici)
- **Top prodotto del mese** con quantità vendute

#### Widget SalesChart
- **Grafico vendite ultimi 7 giorni**
- Visualizzazione trend vendite
- Interfaccia responsive

#### Widget LowStockProducts
- **Tabella prodotti con stock ≤ 5 unità**
- Azioni rapide per riordino
- Codici colore per urgenza

### 🛠️ Interfaccia Filament Avanzata

#### ProductResource - Funzionalità Avanzate
**Filtri Intelligenti:**
- Filtro per categoria
- Toggle stock basso/in stock
- Filtro margine alto (>30%)

**Azioni Personalizzate:**
- **Regolazione stock rapida** con modal
- **Aggiornamento prezzi in massa** (percentuale)
- Azioni bulk per gestione efficiente

**Visualizzazione Migliorata:**
- Colonne margini con coding colore
- Stock con indicatori visivi
- Badge per unità di misura

#### OrderResource - RelationManager
**Gestione OrderItems:**
- **Selezione prodotto con auto-completamento prezzo**
- **Calcolo automatico subtotali** in tempo reale
- **Visualizzazione margini** per ogni item
- Form reattivi con validazioni

### 🔒 Validazioni Robuste

#### Validazioni Lato Server (Product Model)
```php
- Prezzo vendita > prezzo acquisto
- Stock quantity non negativo
- Arrotondamento automatico prezzi
```

#### Validazioni Lato Client (Filament Forms)
```php
- Controlli reattivi su prezzi
- Notifiche errore in tempo reale
- Validazioni numeriche con step
```

### 📊 Calcoli Business Logic

#### Margini e Profitti
```php
// Margine percentuale
margin = ((sale_price - cost_price) / sale_price) * 100

// Profitto per unità
unit_profit = sale_price - cost_price

// Profitto totale OrderItem
total_profit = (unit_price - product.cost_price) * quantity
```

#### Automatismi
- **Subtotale OrderItem**: `quantity * unit_price`
- **Totale Order**: Somma di tutti i subtotali items
- **Aggiornamento cascata** su modifiche/eliminazioni

### 🎨 UX/UI Miglioramenti

#### Codici Colore Semantici
- 🟢 **Verde**: Margini alti (>30%), stock OK
- 🟡 **Giallo**: Margini medi (15-30%), stock basso
- 🔴 **Rosso**: Margini bassi (<15%), stock critico

#### Badge e Indicatori
- **Unità di misura** (kg/pz) con colori distintivi
- **Stato stock** con indicatori visivi
- **Contatori** per relazioni (es. prodotti per ordine)

#### Azioni Rapide
- **Quick edit** per stock e prezzi
- **Bulk actions** per operazioni massive
- **Notifiche** contestuali

### 📈 Business Intelligence

#### KPI Automatici
- **ROI per prodotto** (margine %)
- **Velocity** prodotti (vendite/periodo)
- **Alert stock** automatici
- **Trend analysis** vendite

#### Report Capabilities
- **Esportazione dati** (CSV, Excel)
- **Filtri avanzati** multi-dimensionali
- **Aggregazioni** automatiche

## 🚀 Tecnologie Utilizzate

- **Backend**: Laravel 12.x
- **Admin Panel**: Filament 3.3
- **Frontend**: TailwindCSS 4.0
- **Database**: SQLite (dev) / MySQL (prod)
- **Charts**: Chart.js integrato

## 💡 Vantaggi per la Macelleria

1. **Controllo Margini**: Visibilità immediata su profittabilità
2. **Gestione Stock**: Alert automatici per riordini
3. **Velocità Operativa**: Interfaccia ottimizzata per vendite rapide
4. **Business Intelligence**: Dashboard con KPI essenziali
5. **Scalabilità**: Architettura pronta per crescita business

## 🔧 Setup e Configurazione

```bash
# Installazione dipendenze
composer install
npm install

# Setup database
php artisan migrate
php artisan db:seed

# Avvio sviluppo
php artisan serve
npm run dev
```

## 📝 Prossimi Sviluppi

- **App Mobile** per vendite
- **Integrazione POS** hardware
- **Gestione fornitori** avanzata
- **Report fiscali** automatici
- **Integrazione e-commerce**

---

*Sviluppato con ❤️ per ottimizzare la gestione delle macellerie moderne*