<?php
/**
 * Modèle Validation
 * Gère l'historique des actions sur les demandes (timeline)
 * 
 * Place ce fichier dans : app/models/Validation.php
 * 
 * @author Dev 2
 */

class Validation extends Model
{
    protected $table = 'validations';

    // ============================================
    // MÉTHODES DE RÉCUPÉRATION
    // ============================================

    /**
     * Récupérer l'historique complet d'une demande
     */
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

    /**
     * Récupérer la dernière action sur une demande
     */
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

    // ============================================
    // MÉTHODES D'ENREGISTREMENT
    // ============================================

    /**
     * Enregistrer une action générique
     */
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

    /**
     * Enregistrer la soumission d'une demande
     */
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

    /**
     * Enregistrer la validation par l'assistante
     */
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

    /**
     * Enregistrer le rejet par l'assistante
     */
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

    /**
     * Enregistrer l'approbation par le directeur
     */
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

    /**
     * Enregistrer le rejet par le directeur
     */
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

    /**
     * Enregistrer l'annulation par le professeur
     */
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

    /**
     * Enregistrer une modification de la demande
     */
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

    // ============================================
    // MÉTHODES UTILITAIRES
    // ============================================

    /**
     * Obtenir le libellé de l'action en français
     */
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

    /**
     * Obtenir l'icône de l'action
     */
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

    /**
     * Obtenir la couleur de l'action
     */
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
