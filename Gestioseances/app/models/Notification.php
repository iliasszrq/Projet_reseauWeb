<?php

class Notification extends Model
{
    protected $table = 'notifications';

    public function findByUser(int $userId, int $limit = 50): array
    {
        $sql = "SELECT n.*, d.type AS demande_type
                FROM notifications n
                LEFT JOIN demandes d ON n.demande_id = d.id
                WHERE n.user_id = :user_id
                ORDER BY n.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findNonLues(int $userId): array
    {
        $sql = "SELECT n.*, d.type AS demande_type
                FROM notifications n
                LEFT JOIN demandes d ON n.demande_id = d.id
                WHERE n.user_id = :user_id AND n.lue = 0
                ORDER BY n.created_at DESC";

        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }

    public function countNonLues(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM notifications 
                WHERE user_id = :user_id AND lue = 0";
        return $this->db->count($sql, ['user_id' => $userId]);
    }

    public function creer(int $userId, string $titre, string $message, string $type = 'info', ?int $demandeId = null): int
    {
        return $this->create([
            'user_id' => $userId,
            'demande_id' => $demandeId,
            'titre' => $titre,
            'message' => $message,
            'type' => $type,
            'lue' => 0
        ]);
    }

    public function notifierSoumission(int $assistanteId, int $demandeId, string $professeurNom): int
    {
        return $this->creer(
            $assistanteId,
            'Nouvelle demande soumise',
            "Une nouvelle demande a été soumise par {$professeurNom}.",
            'info',
            $demandeId
        );
    }

    public function notifierValidation(int $directeurId, int $demandeId, string $professeurNom): int
    {
        return $this->creer(
            $directeurId,
            'Demande en attente d\'approbation',
            "La demande de {$professeurNom} a été validée et attend votre approbation.",
            'warning',
            $demandeId
        );
    }

    public function notifierRejetAssistante(int $professeurId, int $demandeId): int
    {
        return $this->creer(
            $professeurId,
            'Demande rejetée',
            "Votre demande a été rejetée par l'assistante.",
            'danger',
            $demandeId
        );
    }

    public function notifierApprobation(int $professeurId, int $demandeId): int
    {
        return $this->creer(
            $professeurId,
            'Demande approuvée',
            "Votre demande a été approuvée par le directeur.",
            'success',
            $demandeId
        );
    }

    public function notifierRejetDirecteur(int $professeurId, int $demandeId): int
    {
        return $this->creer(
            $professeurId,
            'Demande rejetée',
            "Votre demande a été rejetée par le directeur.",
            'danger',
            $demandeId
        );
    }

    public function marquerCommeLue(int $id): bool
    {
        return $this->update($id, [
            'lue' => 1,
            'date_lecture' => date('Y-m-d H:i:s')
        ]);
    }

    public function marquerToutesCommeLues(int $userId): bool
    {
        $sql = "UPDATE notifications 
                SET lue = 1, date_lecture = :date_lecture 
                WHERE user_id = :user_id AND lue = 0";

        return $this->db->execute($sql, [
            'user_id' => $userId,
            'date_lecture' => date('Y-m-d H:i:s')
        ]);
    }

    public function supprimerAnciennes(int $jours = 30): bool
    {
        $sql = "DELETE FROM notifications 
                WHERE lue = 1 
                AND date_lecture < DATE_SUB(NOW(), INTERVAL :jours DAY)";

        return $this->db->execute($sql, ['jours' => $jours]);
    }

    public static function getIcone(string $type): string
    {
        $icones = [
            'info' => 'bi-info-circle',
            'success' => 'bi-check-circle',
            'warning' => 'bi-exclamation-triangle',
            'danger' => 'bi-x-circle'
        ];
        return $icones[$type] ?? 'bi-bell';
    }

    public static function getTypeClass(string $type): string
    {
        $classes = [
            'info' => 'text-info',
            'success' => 'text-success',
            'warning' => 'text-warning',
            'danger' => 'text-danger'
        ];
        return $classes[$type] ?? 'text-secondary';
    }

    public static function formatDateRelative(string $date): string
    {
        $timestamp = strtotime($date);
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return "À l'instant";
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return "Il y a {$minutes} minute" . ($minutes > 1 ? 's' : '');
        } elseif ($diff < 86400) {
            $heures = floor($diff / 3600);
            return "Il y a {$heures} heure" . ($heures > 1 ? 's' : '');
        } elseif ($diff < 604800) {
            $jours = floor($diff / 86400);
            return "Il y a {$jours} jour" . ($jours > 1 ? 's' : '');
        } else {
            return date('d/m/Y', $timestamp);
        }
    }
}
