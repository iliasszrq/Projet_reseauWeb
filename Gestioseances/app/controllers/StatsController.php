<?php

class StatsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        $this->requireRole(ROLE_DIRECTEUR);

        $stats = $this->getGlobalStats();
        $monthlyStats = $this->getMonthlyStats();
        $statsByProfesseur = $this->getStatsByProfesseur();
        $statsByType = $this->getStatsByType();
        $recentActivity = $this->getRecentActivity();

        $this->view('stats/index', [
            'stats' => $stats,
            'monthlyStats' => json_encode($monthlyStats),
            'statsByProfesseur' => json_encode($statsByProfesseur),
            'statsByType' => json_encode($statsByType),
            'recentActivity' => $recentActivity,
            'flash' => $this->getFlash()
        ]);
    }

    public function exportExcel(): void
    {
        $this->requireRole(ROLE_DIRECTEUR);

        $demandes = $this->getAllDemandes();

        // Export CSV avec BOM UTF-8 pour Excel
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="demandes_' . date('Y-m-d') . '.csv"');
        header('Cache-Control: max-age=0');

        // Ouvrir la sortie
        $output = fopen('php://output', 'w');
        
        // BOM UTF-8 pour que Excel reconnaisse les accents
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // En-têtes
        fputcsv($output, ['ID', 'Professeur', 'Type', 'Matiere', 'Motif', 'Statut', 'Urgente', 'Date creation'], ';');

        // Données
        foreach ($demandes as $d) {
            fputcsv($output, [
                $d['id'],
                $d['professeur_nom'] . ' ' . $d['professeur_prenom'],
                $d['type'],
                $d['matiere_nom'] ?? 'N/A',
                $d['motif'],
                $d['statut'],
                $d['urgente'] ? 'Oui' : 'Non',
                $d['created_at']
            ], ';');
        }

        fclose($output);
        exit;
    }

    public function exportPdf(): void
    {
        $this->requireRole(ROLE_DIRECTEUR);

        $demandes = $this->getAllDemandes();
        $stats = $this->getGlobalStats();

        header('Content-Type: text/html; charset=utf-8');
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Rapport GestioSeances</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #4472C4; text-align: center; }
                h2 { color: #2d3748; border-bottom: 2px solid #4472C4; padding-bottom: 5px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th { background: #4472C4; color: white; padding: 10px; text-align: left; }
                td { padding: 8px; border: 1px solid #ddd; }
                tr:nth-child(even) { background: #f2f2f2; }
                .stats-box { display: inline-block; width: 22%; margin: 1%; padding: 15px; background: #f8f9fa; border-radius: 8px; text-align: center; }
                .stats-number { font-size: 24px; font-weight: bold; color: #4472C4; }
                .print-btn { background: #4472C4; color: white; padding: 10px 20px; border: none; cursor: pointer; margin: 10px; border-radius: 5px; }
                .print-btn:hover { background: #3730a3; }
                @media print { .no-print { display: none; } }
            </style>
        </head>
        <body>
            <div class="no-print">
                <button class="print-btn" onclick="window.print()">Imprimer / Sauvegarder PDF</button>
                <button class="print-btn" onclick="window.history.back()">Retour</button>
            </div>
            <h1>Rapport GestioSeances</h1>
            <p style="text-align:center;">Genere le ' . date('d/m/Y H:i') . '</p>
            <h2>Statistiques Globales</h2>
            <div style="text-align:center;">
                <div class="stats-box"><div class="stats-number">' . $stats['total'] . '</div><div>Total</div></div>
                <div class="stats-box"><div class="stats-number" style="color:#f6ad55;">' . $stats['en_attente'] . '</div><div>En Attente</div></div>
                <div class="stats-box"><div class="stats-number" style="color:#48bb78;">' . $stats['approuvees'] . '</div><div>Approuvees</div></div>
                <div class="stats-box"><div class="stats-number" style="color:#fc8181;">' . $stats['rejetees'] . '</div><div>Rejetees</div></div>
            </div>
            <h2>Liste des Demandes</h2>
            <table><tr><th>ID</th><th>Professeur</th><th>Type</th><th>Matiere</th><th>Statut</th><th>Urgente</th><th>Date</th></tr>';
        foreach ($demandes as $d) {
            $urgentBadge = $d['urgente'] ? '<span style="color:red;">Oui</span>' : 'Non';
            echo '<tr>
                <td>' . $d['id'] . '</td>
                <td>' . htmlspecialchars($d['professeur_nom'] . ' ' . $d['professeur_prenom']) . '</td>
                <td>' . htmlspecialchars($d['type']) . '</td>
                <td>' . htmlspecialchars($d['matiere_nom'] ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($d['statut']) . '</td>
                <td>' . $urgentBadge . '</td>
                <td>' . date('d/m/Y', strtotime($d['created_at'])) . '</td>
            </tr>';
        }
        echo '</table>
            <p style="text-align:center; color:#666; margin-top:30px;">GestioSeances - EIDIA UEMF - ' . date('Y') . '</p>
        </body></html>';
        exit;
    }

    private function getGlobalStats(): array
    {
        $db = Database::getInstance()->getConnection();
        $stats = ['total' => 0, 'en_attente' => 0, 'approuvees' => 0, 'rejetees' => 0, 'brouillon' => 0, 'validees_assistante' => 0, 'professeurs' => 0, 'assistantes' => 0];

        $sql = "SELECT COUNT(*) as total FROM demandes";
        $stmt = $db->query($sql);
        $stats['total'] = $stmt->fetch()['total'];

        $sql = "SELECT statut, COUNT(*) as count FROM demandes GROUP BY statut";
        $stmt = $db->query($sql);
        while ($row = $stmt->fetch()) {
            switch ($row['statut']) {
                case 'en_attente': $stats['en_attente'] = $row['count']; break;
                case 'approuvee': $stats['approuvees'] = $row['count']; break;
                case 'rejetee': $stats['rejetees'] = $row['count']; break;
                case 'brouillon': $stats['brouillon'] = $row['count']; break;
                case 'validee_assistante': $stats['validees_assistante'] = $row['count']; break;
            }
        }

        $sql = "SELECT COUNT(*) as count FROM users WHERE role = 'professeur' AND actif = 1";
        $stmt = $db->query($sql);
        $stats['professeurs'] = $stmt->fetch()['count'];

        $sql = "SELECT COUNT(*) as count FROM users WHERE role = 'assistante' AND actif = 1";
        $stmt = $db->query($sql);
        $stats['assistantes'] = $stmt->fetch()['count'];

        return $stats;
    }

    private function getMonthlyStats(): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total,
                SUM(CASE WHEN statut = 'approuvee' THEN 1 ELSE 0 END) as approuvees,
                SUM(CASE WHEN statut = 'rejetee' THEN 1 ELSE 0 END) as rejetees
                FROM demandes WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY month ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }

    private function getStatsByProfesseur(): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT u.nom, u.prenom, COUNT(d.id) as total FROM users u
                LEFT JOIN demandes d ON u.id = d.professeur_id WHERE u.role = 'professeur'
                GROUP BY u.id ORDER BY total DESC LIMIT 10";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }

    private function getStatsByType(): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT type, COUNT(*) as total FROM demandes GROUP BY type";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }

    private function getRecentActivity(): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT d.*, u.nom as professeur_nom, u.prenom as professeur_prenom
                FROM demandes d JOIN users u ON d.professeur_id = u.id ORDER BY d.updated_at DESC LIMIT 10";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }

    private function getAllDemandes(): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT d.*, u.nom as professeur_nom, u.prenom as professeur_prenom, m.nom as matiere_nom
                FROM demandes d JOIN users u ON d.professeur_id = u.id
                LEFT JOIN seances s ON d.seance_id = s.id LEFT JOIN matieres m ON s.matiere_id = m.id
                ORDER BY d.created_at DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }
}