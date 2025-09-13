<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Exemple DataTable</title>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>
<body>
  <h2>Liste des utilisateurs</h2>
  
  <table id="myTable" class="display">
    <thead>
      <tr>
        <th>Nom</th>
        <th>Email</th>
        <th>Rôle</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Alice</td>
        <td>alice@mail.com</td>
        <td>Admin</td>
      </tr>
      <tr>
        <td>Bob</td>
        <td>bob@mail.com</td>
        <td>Chauffeur</td>
      </tr>
      <tr>
        <td>Claire</td>
        <td>claire@mail.com</td>
        <td>Utilisateur</td>
      </tr>
    </tbody>
  </table>

  <script>
    $(document).ready(function() {
      $('#myTable').DataTable({
        "pageLength": 5,      // nombre d’entrées par page
        "lengthMenu": [5, 10, 20, 50], // choix du nombre d’entrées
        "language": {
          "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
        }
      });
    });
  </script>
</body>
</html>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Liste des Signalements</title>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- DataTables CSS + JS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <style>
    /* Style global */
    body {
      font-family: Arial, sans-serif;
      background: #f5f6fa;
      padding: 20px;
    }

    h2 {
      text-align: center;
      color: #cfa13b;
      margin-bottom: 20px;
    }

    /* Tableau */
    table.dataTable {
      border-collapse: collapse;
      width: 100%;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    table.dataTable thead {
      background: #cfa13b;
      color: #fff;
      text-transform: uppercase;
      font-size: 13px;
    }

    table.dataTable thead th {
      padding: 12px;
    }

    table.dataTable tbody td {
      padding: 10px;
      border-bottom: 1px solid #eee;
    }

    table.dataTable tbody tr:hover {
      background: #f9f2dc;
    }

    /* Pagination et barre de recherche */
    .dataTables_wrapper .dataTables_filter input {
      border: 1px solid #ccc;
      border-radius: 6px;
      padding: 6px;
      margin-left: 8px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
      border-radius: 6px;
      padding: 5px 10px;
      margin: 2px;
      border: 1px solid transparent;
      background: #eee;
      color: #333 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
      background: #cfa13b !important;
      color: white !important;
      border: 1px solid #cfa13b !important;
    }
  </style>
</head>
<body>

  <h2>Liste des Signalements</h2>

  <table id="signalements" class="display">
    <thead>
      <tr>
        <th>Nom utilisateur</th>
        <th>Motif</th>
        <th>Date et heure</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Alice</td>
        <td>Retard</td>
        <td>2025-09-12 09:30</td>
        <td>Camion arrivé en retard de 2 heures</td>
      </tr>
      <tr>
        <td>Bob</td>
        <td>Panne</td>
        <td>2025-09-10 14:15</td>
        <td>Panne mécanique signalée</td>
      </tr>
      <tr>
        <td>Claire</td>
        <td>Absence</td>
        <td>2025-09-08 11:00</td>
        <td>Chauffeur absent sans prévenir</td>
      </tr>
    </tbody>
  </table>

  <script>
    $(document).ready(function() {
      $('#signalements').DataTable({
        "pageLength": 5,
        "lengthMenu": [5, 10, 20, 50],
        "language": {
          "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
        }
      });
    });
  </script>

</body>
</html>
