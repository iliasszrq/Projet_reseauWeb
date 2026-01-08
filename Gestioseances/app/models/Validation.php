<?php

class Validation extends Model
{
    protected $table = 'validations';

    public function getHistorique(int $demandeId): array
    {
        $sql = "SELECT 
                    v.*,
                    u.nom AS user_nom,
                    u.prenom AS user_prenom,
                    u.role AS user_role
                FROM validations v
                JOIN users u ON v.user_id = u.id
                WHERE v.demande_id = :demande_id
                ORDER BY v.created_at ASC";

        return $this->db->fetchAll($sql, ['demande_id' => $demandeId]);
    }

    public function getDerniereAction(int $demandeId): ?array
    {
        $sql = "SELECT 
                    v.*,
                    u.nom AS user_nom,
                    u.prenom AS user_prenom,
                    u.role AS user_role
                FROM validations v
                JOIN users u ON v.user_id = u.id
                WHERE v.demande_id = :demande_id
                ORDER BY v.created_at DESC
                LIMIT 1";

        return $this->db->fetch($sql, ['demande_id' => $demandeId]);
    }

    public function enregistrer(int $demandeId, int $userId, string $action, ?string $commentaire = null, ?string $ancienStatut = null, ?string $nouveauStatut = null): int
    {
        return $this->create([
            'demande_id' => $demandeId,
            'user_id' => $userId,
            'action' => $action,
            'commentaire' => $commentaire,
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $nouveauStatut
        ]);
    }

    public function enregistrerSoumission(int $demandeId, int $professeurId, ?string $commentaire = null): int
    {
        return $this->enregistrer(
            $demandeId,
            $professeurId,
            'soumise',
            $commentaire,
            STATUT_BROUILLON,
            STATUT_EN_ATTENTE
        );
    }

    public function enregistrerValidationAssistante(int $demandeId, int $assistanteId, ?string $commentaire = null): int
    {
        return $this->enregistrer(
            $demandeId,
            $assistanteId,
            'validee',
            $commentaire,
            STATUT_EN_ATTENTE,
            STATUT_VALIDEE_ASSISTANTE
        );
    }

    public function enregistrerRejetAssistante(int $demandeId, int $assistanteId, ?string $commentaire = null): int
    {
        return $this->enregistrer(
            $demandeId,
            $assistanteId,
            'rejetee',
            $commentaire,
            STATUT_EN_ATTENTE,
            STATUT_REJETEE
        );
    }

    public function enregistrerApprobationDirecteur(int $demandeId, int $directeurId, ?string $commentaire = null): int
    {
        return $this->enregistrer(
            $demandeId,
            $directeurId,
            'approuvee',
            $commentaire,
            STATUT_VALIDEE_ASSISTANTE,
            STATUT_APPROUVEE
        );
    }

    public function enregistrerRejetDirecteur(int $demandeId, int $directeurId, ?string $commentaire = null): int
    {
        return $this->enregistrer(
            $demandeId,
            $directeurId,
            'rejetee',
            $commentaire,
            STATUT_VALIDEE_ASSISTANTE,
            STATUT_REJETEE
        );
    }

    public function enregistrerAnnulation(int $demandeId, int $professeurId, string $ancienStatut, ?string $commentaire = null): int
    {
        return $this->enregistrer(
            $demandeId,
            $professeurId,
            'annulee',
            $commentaire,
            $ancienStatut,
            STATUT_ANNULEE
        );
    }

    public function enregistrerModification(int $demandeId, int $professeurId, ?string $commentaire = null): int
    {
        return $this->enregistrer(
            $demandeId,
            $professeurId,
            'modifiee',
            $commentaire,
            STATUT_BROUILLON,
            STATUT_BROUILLON
        );
    }

    public static function getActionLabel(string $action): string
    {
        $labels = [
            'soumise' => 'Demande soumise',
            'validee' => 'Validée par l\'assistante',
            'rejetee' => 'Rejetée',
            'approuvee' => 'Approuvée par le directeur',
            'annulee' => 'Annulée',
            'modifiee' => 'Modifiée'
        ];
        return $labels[$action] ?? $action;
    }

    public static function getActionIcone(string $action): string
    {
        $icones = [
            'soumise' => 'bi-send',
            'validee' => 'bi-check-circle',
            'rejetee' => 'bi-x-circle',
            'approuvee' => 'bi-check2-all',
            'annulee' => 'bi-slash-circle',
            'modifiee' => 'bi-pencil'
        ];
        return $icones[$action] ?? 'bi-circle';
    }

    public static function getActionCouleur(string $action): string
    {
        $couleurs = [
            'soumise' => 'primary',
            'validee' => 'info',
            'rejetee' => 'danger',
            'approuvee' => 'success',
            'annulee' => 'secondary',
            'modifiee' => 'warning'
        ];
        return $couleurs[$action] ?? 'secondary';
    }
}
