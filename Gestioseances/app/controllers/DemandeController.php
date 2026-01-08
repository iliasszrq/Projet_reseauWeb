<?php

class DemandeController extends Controller
{
    private $demandeModel;
    private $notificationModel;
    private $validationModel;
    private $pieceJointeModel;

    public function __construct()
    {
        parent::__construct();
        $this->demandeModel = new Demande();
        $this->notificationModel = new Notification();
        $this->validationModel = new Validation();
        $this->pieceJointeModel = new PieceJointe();
    }

    public function index(): void
    {
        $this->requireLogin();

        $professeurId = $this->getUserId();
        $demandes = $this->demandeModel->findByProfesseur($professeurId);

        $stats = [
            'total' => count($demandes),
            'brouillon' => $this->demandeModel->countByProfesseurAndStatut($professeurId, STATUT_BROUILLON),
            'en_attente' => $this->demandeModel->countByProfesseurAndStatut($professeurId, STATUT_EN_ATTENTE),
            'approuvees' => $this->demandeModel->countByProfesseurAndStatut($professeurId, STATUT_APPROUVEE),
            'rejetees' => $this->demandeModel->countByProfesseurAndStatut($professeurId, STATUT_REJETEE),
        ];

        $this->view('demandes/index', [
            'demandes' => $demandes,
            'stats' => $stats,
            'flash' => $this->getFlash()
        ]);
    }

    public function create(): void
    {
        $this->requireRole(ROLE_PROFESSEUR);

        $seances = $this->getSeancesProfesseur($this->getUserId());
        $salles = $this->getSalles();

        $this->view('demandes/create', [
            'seances' => $seances,
            'salles' => $salles,
            'flash' => $this->getFlash()
        ]);
    }

    public function store(): void
    {
        $this->requireRole(ROLE_PROFESSEUR);

        if (!$this->isPost()) {
            $this->redirect('/demandes/create');
        }

        $errors = $this->validateDemande($_POST);

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            $this->redirect('/demandes/create');
        }

        $data = [
            'professeur_id' => $this->getUserId(),
            'seance_id' => (int) $this->post('seance_id'),
            'type' => $this->post('type'),
            'motif' => trim($this->post('motif')),
            'nouvelle_date' => $this->post('nouvelle_date') ?: null,
            'nouvelle_heure_debut' => $this->post('nouvelle_heure_debut') ?: null,
            'nouvelle_heure_fin' => $this->post('nouvelle_heure_fin') ?: null,
            'nouvelle_salle_id' => $this->post('nouvelle_salle_id') ?: null,
            'commentaire_professeur' => trim($this->post('commentaire_professeur') ?? ''),
            'urgente' => $this->post('urgente') ? 1 : 0,
        ];

        $demandeId = $this->demandeModel->creerDemande($data);

        if (!empty($_FILES['pieces_jointes']['name'][0])) {
            $this->pieceJointeModel->uploaderMultiple($demandeId, $_FILES['pieces_jointes']);
        }

        $this->setFlash('success', 'Demande créée avec succès. Elle est en brouillon.');
        $this->redirect('/demandes/' . $demandeId);
    }

    public function show(int $id): void
    {
        $this->requireLogin();

        $demande = $this->demandeModel->findWithDetails($id);

        if (!$demande) {
            $this->setFlash('danger', 'Demande non trouvée.');
            $this->redirect('/demandes');
        }

        if (!$this->canViewDemande($demande)) {
            $this->setFlash('danger', 'Vous n\'avez pas accès à cette demande.');
            $this->redirect('/demandes');
        }

        $historique = $this->validationModel->getHistorique($id);
        $piecesJointes = $this->pieceJointeModel->findByDemande($id);

        $this->view('demandes/show', [
            'demande' => $demande,
            'historique' => $historique,
            'piecesJointes' => $piecesJointes,
            'flash' => $this->getFlash()
        ]);
    }

    public function edit(int $id): void
    {
        $this->requireRole(ROLE_PROFESSEUR);

        $demande = $this->demandeModel->find($id);

        if (!$demande) {
            $this->setFlash('danger', 'Demande non trouvée.');
            $this->redirect('/demandes');
        }

        if (!$this->demandeModel->estProprietaire($id, $this->getUserId())) {
            $this->setFlash('danger', 'Vous ne pouvez pas modifier cette demande.');
            $this->redirect('/demandes');
        }

        if ($demande['statut'] !== STATUT_BROUILLON) {
            $this->setFlash('warning', 'Seules les demandes en brouillon peuvent être modifiées.');
            $this->redirect('/demandes/' . $id);
        }

        $seances = $this->getSeancesProfesseur($this->getUserId());
        $salles = $this->getSalles();
        $piecesJointes = $this->pieceJointeModel->findByDemande($id);

        $this->view('demandes/edit', [
            'demande' => $demande,
            'seances' => $seances,
            'salles' => $salles,
            'piecesJointes' => $piecesJointes,
            'flash' => $this->getFlash()
        ]);
    }

    public function update(int $id): void
    {
        $this->requireRole(ROLE_PROFESSEUR);

        if (!$this->isPost()) {
            $this->redirect('/demandes/' . $id . '/edit');
        }

        $demande = $this->demandeModel->find($id);

        if (!$demande || !$this->demandeModel->estProprietaire($id, $this->getUserId())) {
            $this->setFlash('danger', 'Demande non trouvée ou accès refusé.');
            $this->redirect('/demandes');
        }

        if ($demande['statut'] !== STATUT_BROUILLON) {
            $this->setFlash('warning', 'Seules les demandes en brouillon peuvent être modifiées.');
            $this->redirect('/demandes/' . $id);
        }

        $errors = $this->validateDemande($_POST);

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $this->redirect('/demandes/' . $id . '/edit');
        }

        $data = [
            'seance_id' => (int) $this->post('seance_id'),
            'type' => $this->post('type'),
            'motif' => trim($this->post('motif')),
            'nouvelle_date' => $this->post('nouvelle_date') ?: null,
            'nouvelle_heure_debut' => $this->post('nouvelle_heure_debut') ?: null,
            'nouvelle_heure_fin' => $this->post('nouvelle_heure_fin') ?: null,
            'nouvelle_salle_id' => $this->post('nouvelle_salle_id') ?: null,
            'commentaire_professeur' => trim($this->post('commentaire_professeur') ?? ''),
            'urgente' => $this->post('urgente') ? 1 : 0,
        ];

        $this->demandeModel->modifierDemande($id, $data);

        $this->validationModel->enregistrerModification($id, $this->getUserId());

        if (!empty($_FILES['pieces_jointes']['name'][0])) {
            $this->pieceJointeModel->uploaderMultiple($id, $_FILES['pieces_jointes']);
        }

        $this->setFlash('success', 'Demande modifiée avec succès.');
        $this->redirect('/demandes/' . $id);
    }

    public function soumettre(int $id): void
    {
        $this->requireRole(ROLE_PROFESSEUR);

        $demande = $this->demandeModel->find($id);

        if (!$demande || !$this->demandeModel->estProprietaire($id, $this->getUserId())) {
            $this->setFlash('danger', 'Demande non trouvée ou accès refusé.');
            $this->redirect('/demandes');
        }

        if ($this->demandeModel->soumettre($id)) {
            $this->validationModel->enregistrerSoumission($id, $this->getUserId());

            $assistanteId = $this->getAssistanteId();
            if ($assistanteId) {
                $professeurNom = $_SESSION['user_nom'] . ' ' . $_SESSION['user_prenom'];
                $this->notificationModel->notifierSoumission($assistanteId, $id, $professeurNom);
            }

            $this->setFlash('success', 'Demande soumise avec succès. Elle est en attente de validation.');
        } else {
            $this->setFlash('danger', 'Impossible de soumettre cette demande.');
        }

        $this->redirect('/demandes/' . $id);
    }

    public function annuler(int $id): void
    {
        $this->requireRole(ROLE_PROFESSEUR);

        $demande = $this->demandeModel->find($id);

        if (!$demande || !$this->demandeModel->estProprietaire($id, $this->getUserId())) {
            $this->setFlash('danger', 'Demande non trouvée ou accès refusé.');
            $this->redirect('/demandes');
        }

        $ancienStatut = $demande['statut'];

        if ($this->demandeModel->annuler($id)) {
            $this->validationModel->enregistrerAnnulation($id, $this->getUserId(), $ancienStatut);
            $this->setFlash('success', 'Demande annulée.');
        } else {
            $this->setFlash('danger', 'Impossible d\'annuler cette demande.');
        }

        $this->redirect('/demandes/' . $id);
    }

    public function fileAttente(): void
    {
        $this->requireRole(ROLE_ASSISTANTE);

        $demandes = $this->demandeModel->getDemandesEnAttente();
        $urgentes = $this->demandeModel->getDemandesUrgentes();

        $this->view('demandes/file-attente', [
            'demandes' => $demandes,
            'urgentes' => $urgentes,
            'flash' => $this->getFlash()
        ]);
    }

    public function valider(int $id): void
    {
        $this->requireRole(ROLE_ASSISTANTE);

        if (!$this->isPost()) {
            $this->redirect('/demandes/' . $id);
        }

        $demande = $this->demandeModel->findWithDetails($id);

        if (!$demande || $demande['statut'] !== STATUT_EN_ATTENTE) {
            $this->setFlash('danger', 'Cette demande ne peut pas être validée.');
            $this->redirect('/demandes/file-attente');
        }

        $commentaire = trim($this->post('commentaire') ?? '');

        if ($this->demandeModel->validerParAssistante($id)) {
            $this->validationModel->enregistrerValidationAssistante($id, $this->getUserId(), $commentaire);

            $directeurId = $this->getDirecteurId();
            if ($directeurId) {
                $professeurNom = $demande['professeur_prenom'] . ' ' . $demande['professeur_nom'];
                $this->notificationModel->notifierValidation($directeurId, $id, $professeurNom);
            }

            $this->setFlash('success', 'Demande validée et transmise au directeur.');
        } else {
            $this->setFlash('danger', 'Erreur lors de la validation.');
        }

        $this->redirect('/demandes/file-attente');
    }

    public function rejeterAssistante(int $id): void
    {
        $this->requireRole(ROLE_ASSISTANTE);

        if (!$this->isPost()) {
            $this->redirect('/demandes/' . $id);
        }

        $demande = $this->demandeModel->findWithDetails($id);

        if (!$demande || $demande['statut'] !== STATUT_EN_ATTENTE) {
            $this->setFlash('danger', 'Cette demande ne peut pas être rejetée.');
            $this->redirect('/demandes/file-attente');
        }

        $commentaire = trim($this->post('commentaire') ?? '');

        if (empty($commentaire)) {
            $this->setFlash('warning', 'Veuillez indiquer un motif de rejet.');
            $this->redirect('/demandes/' . $id);
        }

        if ($this->demandeModel->rejeterParAssistante($id)) {
            $this->validationModel->enregistrerRejetAssistante($id, $this->getUserId(), $commentaire);

            $this->notificationModel->notifierRejetAssistante($demande['professeur_id'], $id);

            $this->setFlash('success', 'Demande rejetée.');
        } else {
            $this->setFlash('danger', 'Erreur lors du rejet.');
        }

        $this->redirect('/demandes/file-attente');
    }

    public function aApprouver(): void
    {
        $this->requireRole(ROLE_DIRECTEUR);

        $demandes = $this->demandeModel->getDemandesValidees();

        $this->view('demandes/a-approuver', [
            'demandes' => $demandes,
            'flash' => $this->getFlash()
        ]);
    }

    public function approuver(int $id): void
    {
        $this->requireRole(ROLE_DIRECTEUR);

        if (!$this->isPost()) {
            $this->redirect('/demandes/' . $id);
        }

        $demande = $this->demandeModel->findWithDetails($id);

        if (!$demande || $demande['statut'] !== STATUT_VALIDEE_ASSISTANTE) {
            $this->setFlash('danger', 'Cette demande ne peut pas être approuvée.');
            $this->redirect('/demandes/a-approuver');
        }

        $commentaire = trim($this->post('commentaire') ?? '');

        if ($this->demandeModel->approuverParDirecteur($id)) {
            $this->validationModel->enregistrerApprobationDirecteur($id, $this->getUserId(), $commentaire);

            $this->notificationModel->notifierApprobation($demande['professeur_id'], $id);

            $this->setFlash('success', 'Demande approuvée.');
        } else {
            $this->setFlash('danger', 'Erreur lors de l\'approbation.');
        }

        $this->redirect('/demandes/a-approuver');
    }

    public function rejeterDirecteur(int $id): void
    {
        $this->requireRole(ROLE_DIRECTEUR);

        if (!$this->isPost()) {
            $this->redirect('/demandes/' . $id);
        }

        $demande = $this->demandeModel->findWithDetails($id);

        if (!$demande || $demande['statut'] !== STATUT_VALIDEE_ASSISTANTE) {
            $this->setFlash('danger', 'Cette demande ne peut pas être rejetée.');
            $this->redirect('/demandes/a-approuver');
        }

        $commentaire = trim($this->post('commentaire') ?? '');

        if (empty($commentaire)) {
            $this->setFlash('warning', 'Veuillez indiquer un motif de rejet.');
            $this->redirect('/demandes/' . $id);
        }

        if ($this->demandeModel->rejeterParDirecteur($id)) {
            $this->validationModel->enregistrerRejetDirecteur($id, $this->getUserId(), $commentaire);

            $this->notificationModel->notifierRejetDirecteur($demande['professeur_id'], $id);

            $this->setFlash('success', 'Demande rejetée.');
        } else {
            $this->setFlash('danger', 'Erreur lors du rejet.');
        }

        $this->redirect('/demandes/a-approuver');
    }

    private function validateDemande(array $data): array
    {
        $errors = [];

        if (empty($data['seance_id'])) {
            $errors[] = 'Veuillez sélectionner une séance.';
        }

        if (empty($data['type']) || !in_array($data['type'], [TYPE_CHANGEMENT, TYPE_ANNULATION])) {
            $errors[] = 'Type de demande invalide.';
        }

        if (empty(trim($data['motif']))) {
            $errors[] = 'Le motif est obligatoire.';
        } elseif (strlen(trim($data['motif'])) < 20) {
            $errors[] = 'Le motif doit contenir au moins 20 caractères.';
        }

        if ($data['type'] === TYPE_CHANGEMENT) {
            if (empty($data['nouvelle_date']) && empty($data['nouvelle_heure_debut']) && empty($data['nouvelle_salle_id'])) {
                $errors[] = 'Pour un changement, veuillez indiquer au moins une nouvelle date, heure ou salle.';
            }
        }

        return $errors;
    }

    private function canViewDemande(array $demande): bool
    {
        $role = $this->getUserRole();
        $userId = $this->getUserId();

        if ($role === ROLE_PROFESSEUR && $demande['professeur_id'] === $userId) {
            return true;
        }

        if (in_array($role, [ROLE_ASSISTANTE, ROLE_DIRECTEUR])) {
            return true;
        }

        return false;
    }

    private function getSeancesProfesseur(int $professeurId): array
    {
        $sql = "SELECT s.*, m.nom AS matiere_nom, sal.nom AS salle_nom
                FROM seances s
                JOIN matieres m ON s.matiere_id = m.id
                JOIN salles sal ON s.salle_id = sal.id
                WHERE s.professeur_id = :professeur_id
                ORDER BY s.jour, s.heure_debut";

        return $this->db->fetchAll($sql, ['professeur_id' => $professeurId]);
    }

    private function getSalles(): array
    {
        $sql = "SELECT * FROM salles WHERE disponible = 1 ORDER BY nom";
        return $this->db->fetchAll($sql);
    }

    private function getAssistanteId(): ?int
    {
        $sql = "SELECT id FROM users WHERE role = :role AND actif = 1 LIMIT 1";
        $result = $this->db->fetch($sql, ['role' => ROLE_ASSISTANTE]);
        return $result ? (int) $result['id'] : null;
    }

    private function getDirecteurId(): ?int
    {
        $sql = "SELECT id FROM users WHERE role = :role AND actif = 1 LIMIT 1";
        $result = $this->db->fetch($sql, ['role' => ROLE_DIRECTEUR]);
        return $result ? (int) $result['id'] : null;
    }
}
