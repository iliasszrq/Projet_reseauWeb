<?php

class Demande extends Model
{
    protected $table = 'demandes';

    public function findWithDetails(int $id): ?array
    {
        $sql = "SELECT 
                    d.*,
                    u.nom AS professeur_nom,
                    u.prenom AS professeur_prenom,
                    u.email AS professeur_email,
                    m.nom AS matiere_nom,
                    m.code AS matiere_code,
                    s.jour AS seance_jour,
                    s.heure_debut AS seance_heure_debut,
                    s.heure_fin AS seance_heure_fin,
                    s.groupe AS seance_groupe,
                    sal.nom AS salle_nom,
                    sal_new.nom AS nouvelle_salle_nom
                FROM demandes d
                JOIN users u ON d.professeur_id = u.id
                JOIN seances s ON d.seance_id = s.id
                JOIN matieres m ON s.matiere_id = m.id
                JOIN salles sal ON s.salle_id = sal.id
                LEFT JOIN salles sal_new ON d.nouvelle_salle_id = sal_new.id
                WHERE d.id = :id";

        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function findByProfesseur(int $professeurId): array
    {
        $sql = "SELECT 
                    d.*,
                    m.nom AS matiere_nom,
                    s.jour AS seance_jour,
                    s.heure_debut AS seance_heure_debut
                FROM demandes d
                JOIN seances s ON d.seance_id = s.id
                JOIN matieres m ON s.matiere_id = m.id
                WHERE d.professeur_id = :professeur_id
                ORDER BY d.created_at DESC";

        return $this->db->fetchAll($sql, ['professeur_id' => $professeurId]);
    }

    public function findByStatut(string $statut): array
    {
        $sql = "SELECT 
                    d.*,
                    u.nom AS professeur_nom,
                    u.prenom AS professeur_prenom,
                    m.nom AS matiere_nom,
                    s.jour AS seance_jour,
                    s.heure_debut AS seance_heure_debut
                FROM demandes d
                JOIN users u ON d.professeur_id = u.id
                JOIN seances s ON d.seance_id = s.id
                JOIN matieres m ON s.matiere_id = m.id
                WHERE d.statut = :statut
                ORDER BY d.urgente DESC, d.created_at ASC";

        return $this->db->fetchAll($sql, ['statut' => $statut]);
    }

    public function getDemandesEnAttente(): array
    {
        return $this->findByStatut(STATUT_EN_ATTENTE);
    }

    public function getDemandesValidees(): array
    {
        return $this->findByStatut(STATUT_VALIDEE_ASSISTANTE);
    }

    public function getDemandesUrgentes(): array
    {
        $sql = "SELECT 
                    d.*,
                    u.nom AS professeur_nom,
                    u.prenom AS professeur_prenom,
                    m.nom AS matiere_nom
                FROM demandes d
                JOIN users u ON d.professeur_id = u.id
                JOIN seances s ON d.seance_id = s.id
                JOIN matieres m ON s.matiere_id = m.id
                WHERE d.urgente = 1 
                AND d.statut NOT IN ('approuvee', 'rejetee', 'annulee')
                ORDER BY d.created_at ASC";

        return $this->db->fetchAll($sql);
    }

    public function creerDemande(array $data): int
    {
        $demande = [
            'professeur_id' => $data['professeur_id'],
            'seance_id' => $data['seance_id'],
            'type' => $data['type'],
            'statut' => STATUT_BROUILLON,
            'motif' => $data['motif'],
            'nouvelle_date' => $data['nouvelle_date'] ?? null,
            'nouvelle_heure_debut' => $data['nouvelle_heure_debut'] ?? null,
            'nouvelle_heure_fin' => $data['nouvelle_heure_fin'] ?? null,
            'nouvelle_salle_id' => $data['nouvelle_salle_id'] ?? null,
            'commentaire_professeur' => $data['commentaire_professeur'] ?? null,
            'urgente' => $data['urgente'] ?? 0,
        ];

        return $this->create($demande);
    }

    public function modifierDemande(int $id, array $data): bool
    {
        $demande = $this->find($id);
        if (!$demande || $demande['statut'] !== STATUT_BROUILLON) {
            return false;
        }

        $updateData = [
            'seance_id' => $data['seance_id'],
            'type' => $data['type'],
            'motif' => $data['motif'],
            'nouvelle_date' => $data['nouvelle_date'] ?? null,
            'nouvelle_heure_debut' => $data['nouvelle_heure_debut'] ?? null,
            'nouvelle_heure_fin' => $data['nouvelle_heure_fin'] ?? null,
            'nouvelle_salle_id' => $data['nouvelle_salle_id'] ?? null,
            'commentaire_professeur' => $data['commentaire_professeur'] ?? null,
            'urgente' => $data['urgente'] ?? 0,
        ];

        return $this->update($id, $updateData);
    }

    public function soumettre(int $id): bool
    {
        $demande = $this->find($id);
        if (!$demande || $demande['statut'] !== STATUT_BROUILLON) {
            return false;
        }

        return $this->update($id, [
            'statut' => STATUT_EN_ATTENTE,
            'date_soumission' => date('Y-m-d H:i:s')
        ]);
    }

    public function validerParAssistante(int $id): bool
    {
        $demande = $this->find($id);
        if (!$demande || $demande['statut'] !== STATUT_EN_ATTENTE) {
            return false;
        }

        return $this->update($id, [
            'statut' => STATUT_VALIDEE_ASSISTANTE,
            'date_validation_assistante' => date('Y-m-d H:i:s')
        ]);
    }

    public function rejeterParAssistante(int $id): bool
    {
        $demande = $this->find($id);
        if (!$demande || $demande['statut'] !== STATUT_EN_ATTENTE) {
            return false;
        }

        return $this->update($id, [
            'statut' => STATUT_REJETEE,
            'date_validation_assistante' => date('Y-m-d H:i:s')
        ]);
    }

    public function approuverParDirecteur(int $id): bool
    {
        $demande = $this->find($id);
        if (!$demande || $demande['statut'] !== STATUT_VALIDEE_ASSISTANTE) {
            return false;
        }

        return $this->update($id, [
            'statut' => STATUT_APPROUVEE,
            'date_decision_directeur' => date('Y-m-d H:i:s')
        ]);
    }

    public function rejeterParDirecteur(int $id): bool
    {
        $demande = $this->find($id);
        if (!$demande || $demande['statut'] !== STATUT_VALIDEE_ASSISTANTE) {
            return false;
        }

        return $this->update($id, [
            'statut' => STATUT_REJETEE,
            'date_decision_directeur' => date('Y-m-d H:i:s')
        ]);
    }

    public function annuler(int $id): bool
    {
        $demande = $this->find($id);
        if (!$demande) {
            return false;
        }

        $statutsNonAnnulables = [STATUT_APPROUVEE, STATUT_REJETEE, STATUT_ANNULEE];
        if (in_array($demande['statut'], $statutsNonAnnulables)) {
            return false;
        }

        return $this->update($id, [
            'statut' => STATUT_ANNULEE
        ]);
    }

    public function countByStatut(string $statut): int
    {
        return $this->countWhere('statut', $statut);
    }

    public function countByProfesseurAndStatut(int $professeurId, string $statut): int
    {
        $sql = "SELECT COUNT(*) FROM demandes 
                WHERE professeur_id = :professeur_id AND statut = :statut";
        return $this->db->count($sql, [
            'professeur_id' => $professeurId,
            'statut' => $statut
        ]);
    }

    public function getStatistiques(): array
    {
        return [
            'total' => $this->countAll(),
            'brouillon' => $this->countByStatut(STATUT_BROUILLON),
            'en_attente' => $this->countByStatut(STATUT_EN_ATTENTE),
            'validees' => $this->countByStatut(STATUT_VALIDEE_ASSISTANTE),
            'approuvees' => $this->countByStatut(STATUT_APPROUVEE),
            'rejetees' => $this->countByStatut(STATUT_REJETEE),
            'annulees' => $this->countByStatut(STATUT_ANNULEE),
        ];
    }

    public function estProprietaire(int $demandeId, int $professeurId): bool
    {
        $demande = $this->find($demandeId);
        return $demande && $demande['professeur_id'] === $professeurId;
    }

    public static function getStatutLabel(string $statut): string
    {
        $labels = [
            STATUT_BROUILLON => 'Brouillon',
            STATUT_EN_ATTENTE => 'En attente',
            STATUT_VALIDEE_ASSISTANTE => 'Validée (assistante)',
            STATUT_APPROUVEE => 'Approuvée',
            STATUT_REJETEE => 'Rejetée',
            STATUT_ANNULEE => 'Annulée',
        ];
        return $labels[$statut] ?? $statut;
    }

    public static function getStatutBadgeClass(string $statut): string
    {
        $classes = [
            STATUT_BROUILLON => 'bg-secondary',
            STATUT_EN_ATTENTE => 'bg-warning text-dark',
            STATUT_VALIDEE_ASSISTANTE => 'bg-info',
            STATUT_APPROUVEE => 'bg-success',
            STATUT_REJETEE => 'bg-danger',
            STATUT_ANNULEE => 'bg-dark',
        ];
        return $classes[$statut] ?? 'bg-secondary';
    }

    public static function getTypeLabel(string $type): string
    {
        $labels = [
            TYPE_CHANGEMENT => 'Changement',
            TYPE_ANNULATION => 'Annulation',
        ];
        return $labels[$type] ?? $type;
    }

    public function rechercher(array $filtres = []): array
    {
        $sql = "SELECT 
                    d.*,
                    u.nom AS professeur_nom,
                    u.prenom AS professeur_prenom,
                    m.nom AS matiere_nom,
                    s.jour AS seance_jour
                FROM demandes d
                JOIN users u ON d.professeur_id = u.id
                JOIN seances s ON d.seance_id = s.id
                JOIN matieres m ON s.matiere_id = m.id
                WHERE 1=1";

        $params = [];

        if (!empty($filtres['statut'])) {
            $sql .= " AND d.statut = :statut";
            $params['statut'] = $filtres['statut'];
        }

        if (!empty($filtres['type'])) {
            $sql .= " AND d.type = :type";
            $params['type'] = $filtres['type'];
        }

        if (!empty($filtres['professeur_id'])) {
            $sql .= " AND d.professeur_id = :professeur_id";
            $params['professeur_id'] = $filtres['professeur_id'];
        }

        if (isset($filtres['urgente'])) {
            $sql .= " AND d.urgente = :urgente";
            $params['urgente'] = $filtres['urgente'];
        }

        if (!empty($filtres['date_debut'])) {
            $sql .= " AND DATE(d.created_at) >= :date_debut";
            $params['date_debut'] = $filtres['date_debut'];
        }

        if (!empty($filtres['date_fin'])) {
            $sql .= " AND DATE(d.created_at) <= :date_fin";
            $params['date_fin'] = $filtres['date_fin'];
        }

        $sql .= " ORDER BY d.urgente DESC, d.created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }
}
