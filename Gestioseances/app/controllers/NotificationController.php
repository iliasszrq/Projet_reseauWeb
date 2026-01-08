<?php
/**
 * Contrôleur NotificationController
 * Gère les notifications in-app
 * 
 * Place ce fichier dans : app/controllers/NotificationController.php
 * 
 * @author Dev 2
 */

class NotificationController extends Controller
{
    private $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->notificationModel = new Notification();
    }

    /**
     * Liste des notifications de l'utilisateur
     */
    public function index(): void
    {
        $this->requireLogin();
        
        $userId = $this->getUserId();
        $notifications = $this->notificationModel->findByUser($userId);
        $countNonLues = $this->notificationModel->countNonLues($userId);

        $this->view('notifications/index', [
            'notifications' => $notifications,
            'countNonLues' => $countNonLues,
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function marquerLue(int $id): void
    {
        $this->requireLogin();
        
        $notification = $this->notificationModel->find($id);
        
        // Vérifier que la notification appartient à l'utilisateur
        if (!$notification || $notification['user_id'] !== $this->getUserId()) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Notification non trouvée.'], 404);
            }
            $this->redirect('/notifications');
        }

        $this->notificationModel->marquerCommeLue($id);

        if ($this->isAjax()) {
            $this->json(['success' => true]);
        }

        // Rediriger vers la demande si liée
        if ($notification['demande_id']) {
            $this->redirect('/demandes/' . $notification['demande_id']);
        }

        $this->redirect('/notifications');
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function marquerToutesLues(): void
    {
        $this->requireLogin();
        
        $this->notificationModel->marquerToutesCommeLues($this->getUserId());

        if ($this->isAjax()) {
            $this->json(['success' => true]);
        }

        $this->setFlash('success', 'Toutes les notifications ont été marquées comme lues.');
        $this->redirect('/notifications');
    }

    /**
     * Obtenir le nombre de notifications non lues (AJAX)
     */
    public function count(): void
    {
        $this->requireLogin();
        
        $count = $this->notificationModel->countNonLues($this->getUserId());
        
        $this->json(['count' => $count]);
    }

    /**
     * Obtenir les dernières notifications non lues (AJAX pour le dropdown)
     */
    public function recent(): void
    {
        $this->requireLogin();
        
        $notifications = $this->notificationModel->findNonLues($this->getUserId());
        
        // Limiter à 5 pour le dropdown
        $notifications = array_slice($notifications, 0, 5);
        
        // Formater pour l'affichage
        $formatted = array_map(function($notif) {
            return [
                'id' => $notif['id'],
                'titre' => $notif['titre'],
                'message' => $notif['message'],
                'type' => $notif['type'],
                'demande_id' => $notif['demande_id'],
                'date_relative' => Notification::formatDateRelative($notif['created_at']),
                'icone' => Notification::getIcone($notif['type']),
                'classe' => Notification::getTypeClass($notif['type'])
            ];
        }, $notifications);
        
        $this->json([
            'notifications' => $formatted,
            'total' => $this->notificationModel->countNonLues($this->getUserId())
        ]);
    }

    /**
     * Supprimer une notification
     */
    public function supprimer(int $id): void
    {
        $this->requireLogin();
        
        $notification = $this->notificationModel->find($id);
        
        if (!$notification || $notification['user_id'] !== $this->getUserId()) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Notification non trouvée.'], 404);
            }
            $this->setFlash('danger', 'Notification non trouvée.');
            $this->redirect('/notifications');
        }

        $this->notificationModel->delete($id);

        if ($this->isAjax()) {
            $this->json(['success' => true]);
        }

        $this->setFlash('success', 'Notification supprimée.');
        $this->redirect('/notifications');
    }
}
